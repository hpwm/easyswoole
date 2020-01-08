<?php


namespace App\Model;


class Task extends Base
{
    protected $tableName = 'swoole_task';
    const TASK_PULL_CODE = 1;
    const TASK_CREATE_DATABASES = 2;
}