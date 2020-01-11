<?php
/**
 * Created by PhpStorm
 * User: Administrator
 * Date: 2020/1/11
 * Time: 15:59
 **/

namespace App\Service;
use EasySwoole\RedisPool\Redis;

class PoolService
{
    public function redis_defer()
    {
        $redis = Redis::defer('redis');//字段回收对象
        $value = $redis->lPop('es_key');
        return $value;
    }

    public function redis_invoke()
    {
        Redis::invoker('redisCluster', function (\EasySwoole\Redis\Redis $redis) {
            $value = $redis->set('es_key', 1);
        });
    }


    public function redis_obj()
    {
        $redisPool  = Redis::getInstance()->get('redis');
        $redis = $redisPool->getObj();
        $value = $redis->lPop('es_key');
        $redisPool->recycleObj($redis);#回收
        return $value;
    }

    //mysql_defer
    public function mysql_defer()
    {

    }


    //mysql_obj
    public function mysql_obj()
    {

    }
}