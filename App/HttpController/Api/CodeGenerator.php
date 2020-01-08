<?php


namespace App\HttpController\Api;


use App\Model\Task as TaskModel;
use App\Tool\Code;
use App\Tool\Task;


class CodeGenerator extends Base
{

    public function pull()
    {
        try{
            $this->task(Task::PULL_SVN_CODE,TaskModel::TASK_PULL_CODE);
            return $this->commonResponse([],'添加任务成功!',Code::CODE_SUCCESS);
        }catch (\Exception $exception){
            return $this->commonResponse([],$exception->getMessage(),Code::CODE_EXCEPTION);
        }
    }
}