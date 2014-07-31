pipeline
========

Create a task pipline for seperating complex operations into smaller testable units

Sample useage:

```php

//setup two tasks
$workload = new Workload();
$workload->addTask(new Workload\Task('foo'));
$workload->addTask(new Workload\Task('bar'));

//setup a pipeline
$pipeline = new \Pipeline\Pipeline();

//setup two stages
$pipeline->addStage(new CallbackStage('first-stage', function (Workload\Task $task) {
    $task->setMeta('done-first', true);
}));
$pipeline->addStage(new CallbackStage('second-stage', function (Workload\Task $task) {
    $task->setMeta('done-second', true);
}));

//execute
$pipeline->execute($workload, new Context());

var_dump($workload);
```