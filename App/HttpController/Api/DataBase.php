<?php


namespace App\HttpController\Api;

use App\Model\Task as TaskModel;
use App\Tool\Code;
use App\Tool\Task;

class DataBase extends Base
{
    public function create()
    {
        try{
            $this->task(Task::CREATE_DATABASES,TaskModel::TASK_CREATE_DATABASES);
            return $this->commonResponse([],'添加任务成功!',Code::CODE_SUCCESS);
        }catch (\Exception $exception){
            return $this->commonResponse([],$exception->getMessage(),Code::CODE_EXCEPTION);
        }

    }
}