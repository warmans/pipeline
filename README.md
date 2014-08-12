pipeline
========

[![Build Status](https://travis-ci.org/warmans/pipeline.svg?branch=master)](https://travis-ci.org/warmans/pipeline)
[![Code Coverage](https://scrutinizer-ci.com/g/warmans/pipeline/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/warmans/pipeline/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/warmans/pipeline/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/warmans/pipeline/?branch=master)

Create a task pipline for seperating complex operations into smaller testable units.

Sample useage:

```php

use Pipeline\Pipeline;
use Pipeline\Workload;
use Pipeline\Workload\Task;
use Pipeline\Stage\CallbackStage;

//setup two tasks
$workload = new Workload();
$workload->addTask(new Workload\Task('foo'));
$workload->addTask(new Workload\Task('bar'));

//setup a pipeline
$pipeline = new Pipeline();

//setup two stages
$pipeline->addStage(new CallbackStage('first-stage', function (Task $task) {
    $task->setMeta('done-first', true);
}));
$pipeline->addStage(new CallbackStage('second-stage', function (Task $task) {
    $task->setMeta('done-second', true);
}));

//setup the context to enable logging
$context = new Context();
$context->setLogger(function($msg, $writeLn=true) {
    echo $msg . ($writeLn ? "\n" : "");
});

//execute
$pipeline->execute($workload, $context);

var_dump($workload);
```