<?php
/**
 * Created by PhpStorm
 * User: Administrator
 * Date: 2020/1/7
 * Time: 18:28
 **/

namespace App\Process;


use App\Service\TaskService;
use EasySwoole\Component\Process\AbstractProcess;

class TestProcess extends AbstractProcess
{
    protected function run($arg)
    {
        //当进程启动后，会执行的回调
        $service = new TaskService();
        $service->test('sss');
    }
}