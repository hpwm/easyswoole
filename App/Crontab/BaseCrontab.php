<?php


namespace App\Crontab;


use EasySwoole\EasySwoole\Crontab\AbstractCronTask;

class BaseCrontab extends AbstractCronTask
{
    public static function getRule(): string
    {
        return '*/1 * * * *';
    }

    public static function getTaskName(): string
    {
        return  'taskOne';
    }

    public function run(int $taskId, int $workerIndex)
    {
        echo date('Y-m-d H:i:s');
    }

    public function onException(\Throwable $throwable, int $taskId, int $workerIndex)
    {
        echo $throwable->getMessage();
    }
}