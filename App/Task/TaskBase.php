<?php


namespace App\Task;


use EasySwoole\Task\AbstractInterface\TaskInterface;

class TaskBase implements TaskInterface
{
    public $taskData = null;

    protected $method = null;

    protected $params = null;
    public function __construct($taskData)
    {
        $this->taskData = $taskData;
        $this->method = $this->taskData['method'];
        $this->params = $this->taskData['data'];
    }

    public function run(int $taskId, int $workerIndex)
    {

    }

    public function onException(\Throwable $throwable, int $taskId, int $workerIndex)
    {
        echo $throwable->getMessage();
    }
}
