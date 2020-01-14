<?php
/**
 * Created by PhpStorm
 * User: Administrator
 * Date: 2020/1/9
 * Time: 10:14
 **/

namespace App\Queue;


use EasySwoole\Component\Singleton;
use EasySwoole\Queue\QueueDriverInterface;

class Beanstalkd
{
    use Singleton;

    public function __construct()
    {

    }
}