<?php
/**
 * Created by PhpStorm
 * User: Administrator
 * Date: 2020/1/8
 * Time: 16:03
 **/

namespace App\Process;


use App\Tool\Utility\MyQueue;
use EasySwoole\Component\Process\AbstractProcess;
use EasySwoole\Queue\Job;

class QueueProcess extends AbstractProcess
{
    protected function run($arg)
    {
        go(function (){
//            go(function (){
//                MyQueue::getInstance()->consumer()->listen(function (Job $job){
//                    var_dump($job->toArray());
//                    \Co::sleep(1);
//                    echo 'queue customer';
//                });
//            });
            for($i=0;$i<100;$i++){
                go(function (){
                    MyQueue::getInstance()->consumer()->listen(function (Job $job){
                        $jobArray = $job->toArray();
                        \Co::sleep(1);
                        echo 'queue custome'.$jobArray['jobId'].PHP_EOL;
                    });
                });
            }
        });
    }
}