<?php


namespace App\HttpController\Api;


use App\Lib\Validate\BasicCheck;
use App\Service\PoolService;
use App\Tool\Utility\MyQueue;
use EasySwoole\AtomicLimit\AtomicLimit;
use EasySwoole\EasySwoole\Core;
use EasySwoole\EasySwoole\Task\TaskManager;
use EasySwoole\EasySwoole\Trigger;
use EasySwoole\FastCache\Cache;
use EasySwoole\Http\AbstractInterface\AnnotationController;

use EasySwoole\Http\Exception\ParamAnnotationValidateError;
use EasySwoole\Http\Request;
use  EasySwoole\EasySwoole\Logger;
use EasySwoole\Queue\Job;
use EasySwoole\Smtp\Mailer;
use EasySwoole\Smtp\MailerConfig;
use EasySwoole\Smtp\Message\Html;
use EasySwoole\VerifyCode\Conf;

use EasySwoole\Rpc\Response;
use EasySwoole\Rpc\Rpc;
use EasySwoole\Rpc\Config;
use EasySwoole\RedisPool\RedisPool;
use EasySwoole\Redis\Config\RedisConfig;
use EasySwoole\Rpc\NodeManager\RedisManager;
use EasySwoole\Component\Context\ContextManager;
class Test extends AnnotationController
{
    public function index()
    {
        // TODO: Implement index() method.
    }

    public function test()
    {
        echo 22;
        (new BasicCheck())->sceneField('name')->goCheck($this->request());
    }

    public function verify()
    {
        $config = new Conf();
        $code = new \EasySwoole\VerifyCode\VerifyCode($config);
        $code = $code->DrawCode()->getImageBase64();
        return $this->writeJson(200,['code'=>$code],'success');
    }


    public function rpc()
    {

        $config = new Config();
        /*
         * 定义一个节点管理器
         */
        $host = '185.207.153.185';
        $redisPool = new RedisPool(new RedisConfig([
            'host'=>$host
        ]));
        $manager = new RedisManager($redisPool);
        $config->setNodeManager($manager);
        $rpc = new Rpc($config);
        $ret = [];
        //$client = Rpc::getInstance()->client();
        $client = $rpc->client();
        $client->addCall('goods','list')
            ->setOnSuccess(function (Response $response)use(&$ret){
                $ret['list'] = $response->toArray();
            })->setOnFail(function (Response $response)use(&$ret){
                $ret['list'] = $response->toArray();
            });

        $client->exec(5);

        $this->writeJson(200,$ret);
    }





    public function writeJson($statusCode = 200, $result = null, $msg = null)
    {
        if (!$this->response()->isEndResponse()) {
            $data = Array(
                "code" => $statusCode,
                "data" => $result,
                "msg" => $msg
            );
            $this->response()->write(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
            $this->response()->withHeader('Content-type', 'application/json;charset=utf-8');
            $this->response()->withStatus($statusCode);
            return true;
        } else {
            return false;
        }
    }


    public function waitGroup()
    {
        go(function (){
            $ret = [];

            $wait = new \EasySwoole\Component\WaitGroup();

            $wait->add();
            go(function ()use($wait,&$ret){
                \co::sleep(0.1);
                $ret[] = time();
                $wait->done();
            });

            $wait->add();
            go(function ()use($wait,&$ret){
                \co::sleep(2);
                $ret[] = time();
                $wait->done();
            });

            $wait->wait();

            var_dump($ret);
        });
    }


    public function contextManage()
    {
        go(function (){
            ContextManager::getInstance()->set('key','key in parent');
            go(function (){
                ContextManager::getInstance()->set('key','key in sub');//这里只在当前协程有效，不会覆盖上面协程数据
                var_dump(ContextManager::getInstance()->get('key')." in");
            });

            go(function (){
                //ContextManager::getInstance()->get('key','key in second');//这里只在当前协程有效，不会覆盖上面协程数据
                var_dump(ContextManager::getInstance()->get('key')." in");
            });
            \co::sleep(1);
            var_dump(ContextManager::getInstance()->get('key')." out");
        });
    }


    public function event()
    {
        \App\Event\Test::getInstance()->hook('test','我是自定义进程！');
    }

    //进程 mainSerive启动即出发，测试不了
    public function process()
    {
        //TaskManager::getInstance()->async('testProcess');
    }

    public function task()
    {
        TaskManager::getInstance()->async('App\Task\TestTask',function(){
            echo '我是回调';
        });
    }


    public function log()
    {
        throw new \Exception('我是异常');
        Logger::getInstance()->log('日志内容');
    }

    //console
    public function console()
    {

    }


    //日志记录
    protected function onException(\Throwable $throwable): void
    {
        Logger::getInstance()->onLog()->set('myHook',function ($msg,$logLevel,$category){
            //增加日志写入之后的回调函数
            //file_put_contents('hook.json',$msg);
            //var_dump($msg,$logLevel,$category);
        });
        //触发throwable触发
        Trigger::getInstance()->onException()->set('myHook',function (){
            //file_put_contents('my_hook_exception.json','my_hook_exception');
        });
        Trigger::getInstance()->throwable($throwable);
        //error方法回调
        Trigger::getInstance()->onError()->set('myHook',function (){
            //当发生error时新增回调函数
            //file_put_contents('my_hook_error.json','my_hook_error');
        });
        //记录错误信息,等级为FatalError
        Trigger::getInstance()->error($throwable->getMessage().'666');

    }


    public function evals()
    {
        $string = "beautiful";
        $time = "winter";

        $str = 'This is a $string $time morning!';
        echo $str. PHP_EOL;

        //eval("\$str = \"$str\";");
        eval($str.";");
        echo $str;

    }

    //queue
    public function queue()
    {
//        $job = new Job();
//        for($i=0;$i<100;$i++){
//            $job->setJobData(['time'=>time()]);
//            MyQueue::getInstance()->producer()->push($job);
//        }
//        echo '添加任务成功';
        echo '进入了';
    }


    public function cacheQueue()
    {
        Cache::getInstance();
    }

    public function pool()
    {
        go(function (){
            $redisPool = new \App\Pool\RedisPool(new \EasySwoole\Pool\Config(), new \EasySwoole\Redis\Config\RedisConfig(\EasySwoole\EasySwoole\Config::getInstance()->getConf('REDIS')));
            $redisPool->invoke(function (\EasySwoole\Redis\Redis $redis){
                var_dump($redis->echo('仙士可'));
            });
        });
    }


    public function mail()
    {


        go(function (){
//            $config = new MailerConfig();
//            $config->setServer('smtp.163.com');
//            $config->setSsl(false);
//            $config->setUsername('18715160785@163.com');
//            //$config->setPassword('HPpin10167113');
//            $config->setPassword('hp8023wm');//！！！！！这里不是邮箱密码而是网易邮箱的客户端授权码
//            $config->setMailFrom('18715160785@163.com');
//            $config->setTimeout(10);//设置客户端连接超时时间
//            $config->setMaxPackage(1024*1024*5);//设置包发送的大小：5M
//
//            //设置文本或者html格式
//            $mimeBean = new Html();
//            $mimeBean->setSubject('Hello Word!');
//            $mimeBean->setBody('<h1>Hello Word</h1>');
//
//            //添加附件
//            //$mimeBean->addAttachment(Attac::create('./test.txt'));
//
//            $mailer = new Mailer($config);
//            $mailer->sendTo('1278077589@qq.com', $mimeBean);

//            $config = new MailerConfig();
//            $config->setServer('smtp.qq.com');
//            $config->setSsl(false);
//            $config->setUsername('1278077589@qq.com');
//            //$config->setPassword('HPpin10167113');
//            $config->setPassword('wshglgshiqxnhgab');//！！！！！这里不是邮箱密码而是qq邮箱的客户端授权码（可多个）
//            $config->setMailFrom('1278077589@qq.com');
//            $config->setTimeout(10);//设置客户端连接超时时间
//            $config->setMaxPackage(1024*1024*5);//设置包发送的大小：5M
//
//            //设置文本或者html格式
//            $mimeBean = new Html();
//            $mimeBean->setSubject('Hello Word!');
//            $mimeBean->setBody('<h1>Hello Word</h1>');
//
//            //添加附件
//            //$mimeBean->addAttachment(Attac::create('./test.txt'));
//
//            $mailer = new Mailer($config);
//            $mailer->sendTo('18715160785@163.com', $mimeBean);
        });

    }

    public function onRequest(?string $action): ?bool
    {
        return true;
//        if(AtomicLimit::isAllow('api')){
//            echo 'api success';
//            return true;
//        }else{
//            echo 'api error';
//            return false;
//        }

    }

    public function addRedis()
    {
        $redis = new \Redis();
        $redis->connect('127.0.0.1','6379');
        $num = 100;
        for($i=0;$i<$num;$i++){
            $redis->lPush('es_key',$i);
        }
        echo 'redis add success';

    }

    public function redisPool()
    {
//        $redis = new \EasySwoole\Redis\Redis(new \EasySwoole\Redis\Config\RedisConfig([
//            'host' => '127.0.0.1',
//            'port' => '6379',
//            'auth' => '',
//            'serialize' => \EasySwoole\Redis\Config\RedisConfig::SERIALIZE_NONE
//        ]));
//        $value = $redis->lPop('es_key');

//        echo $value;
        //var_dump('111');

//            //defer方式获取连接
//            $redis = \EasySwoole\RedisPool\Redis::defer('redis');//字段回收对象
//            $value = $redis->lPop('es_key');
//            echo $value;


        //这样获取链接必须回收
//        $redisPool  = \EasySwoole\RedisPool\Redis::getInstance()->get('redis');
//        $redis = $redisPool->getObj();
//        $value = $redis->lPop('es_key');
//         echo $value;
//        $redisPool->recycleObj($redis);#回收
        //$i = 50;
        //echo 'request entry';
//        go(function (){
//            $pool = new PoolService();
//            $data = $pool->redis_defer();
//            //$data = $pool->redis_obj();
//            $this->writeJson(200,['value'=>$data],'success');
//        });
//        go(function (){
//            $pool = new PoolService();
//            $data = $pool->redis_defer();
//            //$data = $pool->redis_obj();
//            $this->writeJson(200,['value'=>$data],'success');
//        });
//        go(function (){
//
//        });

//        $pool = new PoolService();
//        $data = $pool->redis_defer();
//        //$data = $pool->redis_obj();
//        //$this->writeJson(200,['value'=>$data],'success');
//
//        $pool = new PoolService();
//        $data = $pool->redis_defer();
//        //$data = $pool->redis_obj();
//        //$this->writeJson(200,['value'=>$data],'success');
//
//        $pool = new PoolService();
//        $data = $pool->redis_defer();
//        //$data = $pool->redis_obj();
//        $this->writeJson(200,['value'=>$data],'success');

    }


    public function mysqlPool()
    {

    }


}