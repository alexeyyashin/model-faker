<?php

namespace AlexeyYashin\ModelFaker;

use AlexeyYashin\SeedFaker\SeedFaker;
use DateTime;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;
use Throwable;

class ModelFaker
{
    protected SeedFaker $seedFaker;

    protected array $acceptTypeRules = [];
    protected array $methodNameRules = [];
    protected array $propertyNameRules = [];

    public function __construct(string $seed = null)
    {
        $this->seedFaker = new SeedFaker($seed);
    }

    public function ruleAcceptType(string $type, callable $rule): static
    {
        $this->acceptTypeRules[$type] = $rule;
        return $this;
    }

    public function ruleMethodName(string $method, callable $rule): static
    {
        $this->methodNameRules[$method] = $rule;
        return $this;
    }

    public function rulePropertyName(string $property, callable $rule): static
    {
        $this->propertyNameRules[$property] = $rule;
        return $this;
    }

    protected function getValueByType(?string $type): mixed
    {
        if ($type === null) {
            return null;
        }

        if (array_key_exists($type, $this->acceptTypeRules)) {
            return $this->acceptTypeRules[$type]();
        }

        switch ($type) {
            case 'int':
                return $this->seedFaker->integer();
            case 'float':
                return $this->seedFaker->float();
            case 'bool':
                return $this->seedFaker->boolean();
            case 'string':
                return $this->seedFaker->sentence();
            case DateTime::class:
                return (new DateTime())->setTimestamp($this->seedFaker->integer(strtotime('-1 year'), time()));
        }

        return null;
    }

    public function fillExplicitly(object $model, bool $existing = true): int
    {
        $reflection = new ReflectionClass($model);

        $i = 0;

        if ($existing) {
            $properties = $reflection->getProperties(ReflectionProperty::IS_PUBLIC);
            foreach ($properties as $property) {
                if (array_key_exists($property->getName(), $this->propertyNameRules)) {
                    $model->{$property->getName()} = $this->propertyNameRules[$property->getName()]();
                    $i++;
                }
            }
        } else {
            foreach ($this->propertyNameRules as $property => $rule) {
                $value = $rule();
                try {
                    $model->$property = $value;
                    $i++;
                } catch (Throwable $throwable) {}
            }
        }

        if ($existing) {
            $setters = array_filter(
                $reflection->getMethods(ReflectionMethod::IS_PUBLIC),
                fn($method) => str_starts_with($method->getName(), 'set') && !$method->isStatic()
            );

            foreach ($setters as $setter) {
                if (array_key_exists($setter->getName(), $this->methodNameRules)) {
                    $model->{$setter->getName()}($this->methodNameRules[$setter->getName()]());
                    $i++;
                }
            }
        } else {
            foreach ($this->methodNameRules as $method => $rule) {
                $value = $rule();
                try {
                    $method->{$method}($value);
                    $i++;
                } catch (Throwable $throwable) {}
            }
        }

        return $i;
    }

    public function fillSetters(object $model): int
    {
        $reflection = new ReflectionClass($model);

        $i = 0;

        foreach ($reflection->getMethods(ReflectionProperty::IS_PUBLIC) as $method) {
            if (str_starts_with(strtolower($method->getName()), 'set')) {

                if ($method->isStatic()) {
                    continue;
                }

                if (array_key_exists($method->getName(), $this->methodNameRules)) {
                    $model->{$method->getName()}($this->methodNameRules[$method->getName()]());
                    $i++;
                } else {
                    $accepts = $method->getParameters()[0]->getType()?->getName();

                    $value = $this->getValueByType($accepts);

                    if ($value !== null) {
                        $model->{$method->getName()}($value);
                        $i++;
                    }
                }
            }
        }

        return $i;
    }

    public function fillProperties(object $model): int
    {
        $reflection = new ReflectionClass($model);
        $i = 0;

        foreach ($reflection->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            if ($property->isStatic()) {
                continue;
            }

            $model->{$property->getName()} = $this->getValueByType($property->getType());
            $i++;
        }

        return $i;
    }

    public function fillAll(object $model): int
    {
        return $this->fillProperties($model) + $this->fillSetters($model);
    }
}
