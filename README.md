pipeline
========

Create a task pipline for seperating complex operations into smaller testable units

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

//execute
$pipeline->execute($workload, new Context());

var_dump($workload);
```