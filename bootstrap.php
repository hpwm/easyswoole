<?php
use EasySwoole\EasySwoole\Command\CommandContainer;
use App\Command\Test;
use App\Command\Beanstalkd;
//注册自定义命令

CommandContainer::getInstance()->set(new Test());

CommandContainer::getInstance()->set(new Beanstalkd());