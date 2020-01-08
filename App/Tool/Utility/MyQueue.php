<?php
/**
 * Created by PhpStorm
 * User: Administrator
 * Date: 2020/1/8
 * Time: 16:04
 **/

namespace App\Tool\Utility;

use EasySwoole\Component\Singleton;
use EasySwoole\Queue\Queue;

class MyQueue extends Queue
{
    use Singleton;
}