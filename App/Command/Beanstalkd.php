<?php
/**
 * Created by PhpStorm
 * User: Administrator
 * Date: 2020/1/13
 * Time: 9:56
 **/

namespace App\Command;


use EasySwoole\EasySwoole\Command\CommandInterface;
use EasySwoole\EasySwoole\Command\Utility;

class Beanstalkd implements CommandInterface
{
    public function commandName(): string
    {
        return 'beanstalkd';
    }

    public function exec(array $args): ?string
    {
        //打印参数,打印测试值
        $command = array_shift($args);
        $beastalkd_path = '/usr/local/bin/beanstalkd -l 0.0.0.0 -p 11300 ';
        switch ($command){
            case 'start':
                break;
            case 'stop':
//                //先杀死进程
//                go(function ()use($beastalkd_path){
//                    $pid_command = 'ps -ef | grep beanstalkd';
//                    $pid_result = \Co::exec($pid_command);
//                    // /(%Cpu.*:)[\s]+(.*?)[\s]  root      7924  7785  0 10:49 pts/1    00:00:00 ./beanstalkd -l 0.0.0.0 -p 11300
//
//                    $pattern = '/(.*?)[\s]/';
//                    $result = explode(PHP_EOL,trim($pid_result['output']));
//                    $b_result = $result[0];
//                    $empty = preg_split($pattern,$pid_result['output']);
//
//                    var_dump($empty);
//                });
                break;
            case 'reload':

                break;
        }

        go(function ()use($beastalkd_path){
            $result = \Co::exec($beastalkd_path);
            var_dump($result);
        });
        return null;
    }

    public function help(array $args): ?string
    {
        //输出logo
        $logo = Utility::easySwooleLog();
        return $logo."this is beanstalkd";
    }
}