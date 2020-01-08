<?php


namespace App\Event;


use EasySwoole\Component\Container;
use EasySwoole\Component\Singleton;

class Test extends Container
{
    use Singleton;

    public function set($key, $item)
    {
        if (is_callable($item)){
            return parent::set($key, $item);
        }else{
            if(class_exists($item)){
                $class = new $item();
                return parent::set($key, $class);
            }else{
                return false;
            }
        }
    }

    public function hook($event,...$arg){
        $call = $this->get($event);
        if (is_callable($call)){
            return call_user_func($call,...$arg);
        }else if(is_object($call)){
            return call_user_func([$call,'test'],...$arg);
        }else{
            return null;
        }
    }
}