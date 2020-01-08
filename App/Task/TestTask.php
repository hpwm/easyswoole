<?php
/**
 * Created by PhpStorm
 * User: Administrator
 * Date: 2020/1/8
 * Time: 10:03
 **/

namespace App\Task;


use EasySwoole\Task\AbstractInterface\TaskInterface;

class TestTask implements TaskInterface
{
    function run(int $taskId, int $workerIndex)
    {
        echo '哈啥时哈';
    }


    public function onFinish()
    {
        echo '我是回调';
    }

    public function onException(\Throwable $throwable, int $taskId, int $workerIndex)
    {
        echo $throwable->getMessage();
    }
}