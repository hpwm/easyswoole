<?php


namespace App\Crontab;


class Test2Crontab extends BaseCrontab
{
    public static function getRule(): string
    {
        return '*/1 * * * *';
    }

    public static function getTaskName(): string
    {
        return  'taskTwo';
    }

    public function run(int $taskId, int $workerIndex)
    {
        echo date('Y-m-d H:i:s').'----test----'.PHP_EOL;
    }

    public function onException(\Throwable $throwable, int $taskId, int $workerIndex)
    {
        echo $throwable->getMessage();
    }
}