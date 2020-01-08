<?php


namespace App\HttpController\Api;


use App\Model\Task as TaskModel;
use App\Task\Tasks;
use App\Tool\Code;
use App\Tool\Task;
use EasySwoole\EasySwoole\Core;
use EasySwoole\EasySwoole\ServerManager;
use EasySwoole\EasySwoole\Task\TaskManager;
use EasySwoole\Http\AbstractInterface\AnnotationController;
use EasySwoole\Http\Exception\ParamAnnotationValidateError;
use EasySwoole\EasySwoole\Trigger;
use EasySwoole\Http\Message\Status;

class Base extends AnnotationController
{
    protected $params;

    public function index()
    {

        $this->actionNotFound('index');
    }

    public function task($method,$task_type)
    {
        $data = $this->params;
        $taskData = [
            'method'=>$method,
        ];
        $task_id = TaskModel::create([
            'name'=>'创建数据库',
            'type'=>$task_type,
            'params'=>json_encode($data,JSON_UNESCAPED_UNICODE),
            'create_time'=>date('Y-m-d H:i:s'),
            'update_time'=>date('Y-m-d H:i:s'),
        ])->save();
        $data['task_id'] = $task_id;
        $taskData['data'] = $data;
        $taskClass = new Tasks($taskData);
        TaskManager::getInstance()->async($taskClass);
        return true;
    }

    /**
     * 验证签名
     * @param string|null $action
     * @return bool|null
     */
    public function onRequest(?string $action): ?bool
    {
        $method = $this->request()->getMethod();
        $params = $this->request()->getBody()->__toString();
        if(!$params){
            $data = [
                "code" => Status::CODE_BAD_REQUEST,
                "result" => [],
                "msg" => '验证失败'
            ];
            $this->writeJson(200,$data,'缺少参数！');
            return false;
        }
        $this->params = json_decode($params,true);
        //return false;
//        if (!parent::onRequest($action)) {
//            return false;
//        };
        return true;
    }

    /**
     * 获取用户的真实IP
     * @param string $headerName 代理服务器传递的标头名称
     * @return string
     */
    protected function clientRealIP($headerName = 'x-real-ip')
    {
        $server = ServerManager::getInstance()->getSwooleServer();
        $client = $server->getClientInfo($this->request()->getSwooleRequest()->fd);
        $clientAddress = $client['remote_ip'];
        $xri = $this->request()->getHeader($headerName);
        $xff = $this->request()->getHeader('x-forwarded-for');
        if ($clientAddress === '127.0.0.1') {
            if (!empty($xri)) {  // 如果有xri 则判定为前端有NGINX等代理
                $clientAddress = $xri[0];
            } elseif (!empty($xff)) {  // 如果不存在xri 则继续判断xff
                $list = explode(',', $xff[0]);
                if (isset($list[0])) $clientAddress = $list[0];
            }
        }
        return $clientAddress;
    }

    protected function input($name, $default = null)
    {
        $value = $this->request()->getRequestParam($name);
        return $value ?? $default;
    }

    //公共响应
    protected function commonResponse($data=[],$msg='ok',$code=Code::CODE_SUCCESS)
    {
        return $this->writeJson($code,$data,$msg);
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

    protected function onException(\Throwable $throwable): void
    {
        if ($throwable instanceof ParamAnnotationValidateError) {
            $msg = $throwable->getValidate()->getError()->getErrorRuleMsg();
            $this->writeJson(400, null, "{$msg}");
        } else {
            if (Core::getInstance()->isDev()) {
                $this->writeJson(500, null, $throwable->getMessage());
            } else {
                Trigger::getInstance()->throwable($throwable);
                $this->writeJson(500, null, '系统内部错误，请稍后重试');
            }
        }
    }
}