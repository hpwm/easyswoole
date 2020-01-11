<?php
namespace EasySwoole\EasySwoole;


use App\Crontab\TestCrontab;
use App\Crontab\Test2Crontab;
use App\Lib\Exception\ExceptionHandler;
use App\Process\QueueProcess;
use App\Process\TestProcess;
use App\Tool\Utility\LogPusher;
use App\Tool\Utility\MyQueue;
use App\WebSocket\WebSocketParser;
use EasySwoole\Component\Di;
use EasySwoole\Component\Timer;
use EasySwoole\Console\Console;
use EasySwoole\EasySwoole\Swoole\EventRegister;
use EasySwoole\EasySwoole\AbstractInterface\Event;
use EasySwoole\Http\Message\Status;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use EasySwoole\ORM\Db\Connection;
use EasySwoole\ORM\DbManager;
use App\Event\Test as TestEvent;
use EasySwoole\Queue\Driver\Redis;
use EasySwoole\Queue\Job;
use EasySwoole\Socket\Dispatcher;
use App\Rpc\Goods;
use EasySwoole\Redis\Config\RedisConfig;
use EasySwoole\RedisPool\RedisPool;
use EasySwoole\Rpc\NodeManager\RedisManager;
use EasySwoole\Rpc\Config as RpcConfig;
use EasySwoole\Rpc\Rpc;
use EasySwoole\EasySwoole\Crontab\Crontab;
use EasySwoole\Component\Process\Config as ProcessConfig;
use EasySwoole\AtomicLimit\AtomicLimit;
/**
 * Class EasySwooleEvent
 * @Annotation
 * @package EasySwoole\EasySwoole
 */
class EasySwooleEvent implements Event
{

    public static function initialize()
    {
        // TODO: Implement initialize() method.
        date_default_timezone_set('Asia/Shanghai');
        //注册自定义异常
        Di::getInstance()->set(SysConst::HTTP_EXCEPTION_HANDLER,[ExceptionHandler::class,'handle']);

        //自定义事件注册
//        TestEvent::getInstance()->set('test', function () {
//            echo 'test event';
//        });
        //TestEvent::getInstance()->set('test','\App\Service\TaskService');//相当于注册一个容器

    }

    public static function mainServerCreate(EventRegister $register)
    {

//        //注册mysql mysql_config
        $mysql_config = Config::getInstance()->getConf('MYSQL');//var_dump($mysql_config);
        $config = new \EasySwoole\ORM\Db\Config($mysql_config);
        DbManager::getInstance()->addConnection(new Connection($config));

        //监听子服务
//        $subPort = ServerManager::getInstance()->getSwooleServer()->addListener('0.0.0.0',9503,SWOOLE_TCP);
//        $subPort->on('connect', function ($serv, $fd){
//            echo "Client:Connect.\n";
//        });
//        $subPort->on('receive',function (\swoole_server $server, int $fd, int $reactor_id, string $data){
//            var_dump($data);
//        });
//
//        $subPort1 = ServerManager::getInstance()->getSwooleServer()->addListener('0.0.0.0',9504,SWOOLE_TCP);
//        $subPort1->on('connect', function ($serv, $fd){
//            echo "Client:Connect.\n";
//        });
//        $subPort1->on('receive',function (\swoole_server $server, int $fd, int $reactor_id, string $data){
//            var_dump($data);
//        });

        /**
         * **************** websocket控制器 **********************
         */
//        // 创建一个 Dispatcher 配置
//        $conf = new \EasySwoole\Socket\Config();
//        // 设置 Dispatcher 为 WebSocket 模式
//        $conf->setType(\EasySwoole\Socket\Config::WEB_SOCKET);
//        // 设置解析器对象
//        $conf->setParser(new WebSocketParser());
//        // 创建 Dispatcher 对象 并注入 config 对象
//        $dispatch = new Dispatcher($conf);
//        // 给server 注册相关事件 在 WebSocket 模式下  on message 事件必须注册 并且交给 Dispatcher 对象处理
//        $register->set(EventRegister::onMessage, function (\swoole_websocket_server $server, \swoole_websocket_frame $frame) use ($dispatch) {
//            $dispatch->dispatch($server, $frame->data, $frame);
//        });


        /*
       * 定义节点Redis管理器
       */
//        $redisPool = new RedisPool(new RedisConfig([
//            'host'=>'127.0.0.1'
//        ]));
//        $manager = new RedisManager($redisPool);
//        //配置Rpc实例
//        $config = new RpcConfig();
//        //这边用于指定当前服务节点ip，如果不指定，则默认用UDP广播得到的地址
//        //$config->setServerIp('127.0.0.1');
//        $config->setServerIp('185.207.153.185');
//        $config->setNodeManager($manager);
////        $config->getBroadcastConfig()->setEnableBroadcast(true);//启用广播
////        $config->getBroadcastConfig()->setEnableListen(true);   //启用监听
//        /*
//         * 配置初始化
//         */
//        Rpc::getInstance($config);
//        //添加服务
//        Rpc::getInstance()->add(new Goods());
//        Rpc::getInstance()->attachToServer(ServerManager::getInstance()->getSwooleServer());

        //定时器
//        $register->add(EventRegister::onWorkerStart, function (\swoole_server $server, $workerId) {
//            //如何避免定时器因为进程重启而丢失
//            //例如在第一个进程 添加一个10秒的定时器
//            if ($workerId == 0) {
//                \EasySwoole\Component\Timer::getInstance()->loop(10 * 1000, function () {
//                    // 从数据库，或者是redis中，去获取下个就近10秒内需要执行的任务
//                    $redis = new \Redis();
//                    $redis->connect('127.0.0.1', 6379);
//                    $key = 'test_work';
//                    $length = $redis->lLen($key);var_dump($length);
//                    if(!$length){
//                        $num = 100;
//                        for($i=0;$i<$num;$i++){
//                            $redis->lPush($key,$i);
//                        }
//                        echo '已添加100条记录'.PHP_EOL;
//                    }
//                    // 例如:2秒后一个任务，3秒后一个任务 代码如下
//                    \EasySwoole\Component\Timer::getInstance()->after(2 * 1000, function ()use ($redis,$key) {
//                        //为了防止因为任务阻塞，引起定时器不准确，把任务给异步进程处理
//                        //Logger::getInstance()->console("time 2", false);
//                        echo 'delay2s_'.$redis->lPop($key).PHP_EOL;
//                    });
//                    \EasySwoole\Component\Timer::getInstance()->after(3 * 1000, function ()use ($redis,$key) {
//                        //为了防止因为任务阻塞，引起定时器不准确，把任务给异步进程处理
//                        //Logger::getInstance()->console("time 3", false);
//                        echo 'delay3s_'.$redis->lPop($key).PHP_EOL;
//                    });
//                });
//            }
//        });

        // 开始一个定时任务计划
        //Crontab::getInstance()->addTask(TestCrontab::class);
        // 开始一个定时任务计划
        //Crontab::getInstance()->addTask(Test2Crontab::class);


        //$processConfig = new ProcessConfig();
        //$processConfig->setProcessName('testProcess');
        //ServerManager::getInstance()->getSwooleServer()->addProcess((new TestProcess($processConfig))->getProcess());


//        ServerManager::getInstance()->addServer('consoleTcp','9600',SWOOLE_TCP,'0.0.0.0',[
//            'open_eof_check'=>false
//        ]);
//        $consoleTcp = ServerManager::getInstance()->getSwooleServer('consoleTcp');
//        /**
//        密码为123456
//         */
//        $console = new Console("MyConsole",'123456');
//        /*
//         * 注册日志模块
//         */
//        $console->moduleContainer()->set(new LogPusher());
//        $console->protocolSet($consoleTcp)->attachToServer(ServerManager::getInstance()->getSwooleServer());
//        /*
//         * 给es的日志推送加上hook
//         */
//        Logger::getInstance()->onLog()->set('remotePush',function ($msg,$logLevel,$category)use($console){
//
////            if(Config::getInstance()->getConf('logPush')){
////                /*
////                 * 可以在 LogPusher 模型的exec方法中，对loglevel，category进行设置，从而实现对日志等级，和分类的过滤推送
////                 */
////                foreach ($console->allFd() as $item){
////                    $console->send($item['fd'],$msg);
////                }
////            }
//            foreach ($console->allFd() as $item){
//                $console->send($item['fd'],$msg);
//            }
//        });

        //queue

        //redis pool使用请看redis 章节文档
        //redis pool使用请看redis 章节文档
//        $config = new RedisConfig([
//            'host'=>'127.0.0.1'
//        ]);
//        $redis = new RedisPool($config);
//        $driver = new Redis($redis);
//        MyQueue::getInstance($driver);
//        //注册一个消费进程
//        ServerManager::getInstance()->addProcess(new QueueProcess());
        //模拟生产者，可以在任意位置投递
//        $register->add($register::onWorkerStart,function ($ser,$id){
//            if($id == 0){
//                Timer::getInstance()->loop(3000,function (){
//                    $job = new Job();
//                    $job->setJobData(['time'=>\time()]);
//                    MyQueue::getInstance()->producer()->push($job);
//                });
//            }
//        });


//        AtomicLimit::getInstance()->addItem('default')->setMax(200);
//        AtomicLimit::getInstance()->addItem('api')->setMax(2);
//        AtomicLimit::getInstance()->enableProcessAutoRestore(ServerManager::getInstance()->getSwooleServer(),10*1000);

        //redis 链接池

        //redis连接池注册(config默认为127.0.0.1,端口6379)
        \EasySwoole\RedisPool\Redis::getInstance()->register('redis',(new RedisConfig(Config::getInstance()->getConf('REDIS'))));




    }

    public static function onRequest(Request $request, Response $response): bool
    {
        //跨域处理
        $response->withHeader('Access-Control-Allow-Origin', '*');
        $response->withHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
        $response->withHeader('Access-Control-Allow-Credentials', 'true');
        $response->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
        if ($request->getMethod() === 'OPTIONS') {
            $response->withStatus(Status::CODE_OK);
            return false;
        }
        return true;
    }

    public static function afterRequest(Request $request, Response $response): void
    {
        // TODO: Implement afterAction() method.
    }
}