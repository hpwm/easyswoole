<?php


namespace App\HttpController;

use EasySwoole\Http\AbstractInterface\AbstractRouter;
use FastRoute\RouteCollector;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;

class Router extends AbstractRouter
{
    function initialize(RouteCollector $routeCollector)
    {
        $this->setGlobalMode(true);
        //拉取代码
        $routeCollector->post('/swoole/code/pull', '/Api/CodeGenerator/pull');
        //创建数据库
        $routeCollector->post('/swoole/database/create', '/Api/DataBase/create');
        $routeCollector->addGroup('/co',function ($route){
            $route->get('/tco','/Api/Coroutine/goTest');
        });

        $routeCollector->addGroup('/test',function ($route){
            $route->get('/verify','/Api/Test/verify');
            $route->get('/rpc','/Api/Test/rpc');//rpc
            $route->get('/waitGroup','/Api/Test/waitGroup');//waitGroup
            $route->get('/contextManage','/Api/Test/contextManage');
            $route->get('/event','/Api/Test/event');
            $route->get('/process','/Api/Test/process');
            $route->get('/task','/Api/Test/task');
            $route->get('/log','/Api/Test/log');
            $route->get('/evals','/Api/Test/evals');
            $route->get('/queue','/Api/Test/queue');
            $route->get('/cacheQueue','/Api/Test/cacheQueue');
            $route->get('/mail','/Api/Test/mail');
            $route->get('/addRedis','/Api/Test/addRedis');
            $route->get('/redisPool','/Api/Test/redisPool');
            $route->get('/mysqlPool','/Api/Test/mysqlPool');
            $route->get('/beanstalkd','/Api/Test/beanstalkd');
        });

        $routeCollector->post('/test/test', '/Api/Test/test');


        $this->setMethodNotAllowCallBack(function (Request $request,Response $response){
            $response->write('未找到处理方法');
            return false;//结束此次响应
        });
        $this->setRouterNotFoundCallBack(function (Request $request,Response $response){
            $response->write('未找到路由匹配');
            return false;
        });

    }
}