<?php

use AlexeyYashin\SeedFaker\SeedFaker;
use AlexeyYashin\ModelFaker\ModelFaker;

require __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/DemoModel.php';

$faker = new ModelFaker();

/** Set filling rules */
$faker
    // Set property value depending on its name
    ->rulePropertyName('name', fn() => (new SeedFaker())->sentence())

    // Call setter method and pass value depending on its name
    ->ruleMethodName('setDescription', fn() => (new SeedFaker())->text())

    // Call setter method and pass value depending on first parameter type
    ->ruleAcceptType(DateTime::class, fn() => new DateTime());


/**
 * Example: fillExplicitly() — manually fills only explicitly specified fields by rules
 * @see ModelFaker::rulePropertyName()
 * @see ModelFaker::ruleMethodName()
 * @see ModelFaker::ruleAcceptType()
 */
echo 'fillExplicitly()' . PHP_EOL;
$model1 = new DemoModel();

$count1 = $faker->fillExplicitly($model1);
echo 'Filled fields: ' . $count1 . PHP_EOL;
var_dump($model1);

// Example: fillSetters() — automatically fills all public setter methods based on parameter types
echo 'fillSetters()' . PHP_EOL;
$model2 = new DemoModel();

$count2 = $faker->fillSetters($model2);
echo 'Setters filled: ' . $count2 . PHP_EOL;
var_dump($model2);

// Example: fillProperties() — automatically fills all public properties based on their type constraints using the same instance
echo 'fillProperties()' . PHP_EOL;
$model3 = new DemoModel();

$count3 = $faker->fillProperties($model3);
echo 'Properties filled: ' . $count3 . PHP_EOL;
var_dump($model3);

// Example: fillAll() — automatically fills both public properties and setter methods in one call
$model4 = new DemoModel();

$count4 = $faker->fillAll($model4);
echo 'All filled: ' . $count4 . PHP_EOL;
var_dump($model4); 
