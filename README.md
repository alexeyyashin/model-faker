# model-faker

A flexible and extensible PHP library for automatically populating object models with dummy data, built on top of seed-faker.

## Requirements

- PHP 8.0 or higher
- Composer

## Installation

```bash
composer require alexeyyashin/model-faker
```

## Usage

See the `examples/demo.php` file for a complete demonstration:

```php
use AlexeyYashin\ModelFaker\ModelFaker;
use DemoModel;

$faker = new ModelFaker();

// Configure rules (optional)
$faker
    ->rulePropertyName('name', fn() => 'John Doe')
    ->ruleMethodName('setCreatedAt', fn() => new \DateTime())
    ->ruleAcceptType(\DateTime::class, fn() => new \DateTime());

$model = new DemoModel();
$faker->fillAll($model);

var_dump($model);
```

## API Reference

- `ruleAcceptType(string $type, callable $rule): static` — Register a generator for parameter types.
- `ruleMethodName(string $method, callable $rule): static` — Register a generator for specific setter methods.
- `rulePropertyName(string $property, callable $rule): static` — Register a generator for specific properties.
- `fillExplicitly(object $model, bool $existing = true): int` — Fill explicitly named fields and setters.
- `fillSetters(object $model): int` — Fill all public setters based on parameter types.
- `fillProperties(object $model): int` — Fill all public properties based on type declarations.
- `fillAll(object $model): int` — Fill both public properties and methods based on type declarations and parameter types.
