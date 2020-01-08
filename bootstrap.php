<?php
use EasySwoole\EasySwoole\Command\CommandContainer;
//注册自定义命令

CommandContainer::getInstance()->set(new \App\Command\Test());