<?php


namespace App\Task;

use App\Service\TaskService;

class Tasks extends TaskBase
{

    public function run(int $taskId, int $workerIndex)
    {
        var_dump($taskId,$workerIndex);
        $service = new TaskService();
//        $method = 'pullSvnCode';
        $method = $this->method;
        //$service->$method($this->params);
        call_user_func_array([$service,$method],[$this->params]);
        return 'success';
    }

}