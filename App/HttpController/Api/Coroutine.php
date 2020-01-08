<?php


namespace App\HttpController\Api;
use EasySwoole\Component\Context\ContextManager;
use EasySwoole\EasySwoole\ServerManager;
use App\Event\Test as TestEvent;
/**
 * 协程控制器
 * @Annotation
 * 需要向上方这样使用Annotation标记这是一个注解提示类
 */
class Coroutine extends Base
{
    /**
     * 括号内会提示这些字段
     * @var string
     */
    protected $name;

    /**
     * 括号内会提示这些字段
     * @var string
     */
    protected $column;

    /**
     * 括号内会提示这些字段
     * @var string
     */
    protected $alias;

    public function onRequest(?string $action): ?bool
    {
        return true;
    }

    /**
     * @Coroutine(column="",name="",alias="")
     */
    public function goTest()
    {

        TestEvent::getInstance()->hook('test');


        //var_dump(ServerManager::getInstance()->getSwooleServer());
//        go(function (){
//            ContextManager::getInstance()->set('key','key in parent');
//            go(function (){
//                ContextManager::getInstance()->set('key','key in sub');
//                var_dump(ContextManager::getInstance()->get('key')." in");
//            });
//            \co::sleep(1);
//            var_dump(ContextManager::getInstance()->get('key')." out");
//
//            go(function (){
//                //ContextManager::getInstance()->get('key','key in sub');
//                var_dump(ContextManager::getInstance()->get('key')." where");
//            });
//
//            //协程隔离，变量没有被污染
//            //string(13) "key in sub in"
//            //string(17) "key in parent out"
//            //string(6) " where"
//        });

        go(function (){
            echo time();echo PHP_EOL;
            for($i=0;$i<10;$i++){
                if($i==3){
                    go(function () use($i){
                        echo $i;echo PHP_EOL;
                        //\co::sleep(1);
                        sleep(3);
                    });
                }elseif($i == 4){
                    go(function () use($i){
                        echo $i;echo PHP_EOL;
                        //\co::sleep(1);
                        sleep(3);
                    });
                }else{
                    go(function () use($i){
                        echo $i;echo PHP_EOL;
                        \co::sleep(1);
                    });
                }
            }
            echo time();echo PHP_EOL;
        });


        //go([$this, 'test1']);
       // go([$this, 'test2']);
    }


    public function test1()
    {
        $csp = new \EasySwoole\Component\Csp();
        $csp->add();
    }

    public function test2()
    {
        \Co::sleep(2);
        echo 'test2';
    }
}