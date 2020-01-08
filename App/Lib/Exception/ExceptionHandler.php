<?php


namespace App\Lib\Exception;
use App\Tool\Code;
use EasySwoole\Http\Message\Status;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;

class ExceptionHandler
{
    public static function handle( \Throwable $exception, Request $request, Response $response )
    {
        if ($exception instanceof BaseException)
        {
            //如果是自定义异常，则控制http状态码，不需要记录日志
            //因为这些通常是因为客户端传递参数错误或者是用户请求造成的异常
            //不应当记录日志
            $code = $exception->errorCode;
            $status_code = $exception->code;
            $msg = $exception->msg;
            //$this->data = $e->data;
        }
        else{
            $code = Code::CODE_EXCEPTION;
            $status_code = Status::CODE_BAD_REQUEST;
            $msg = '服务器繁忙';
            echo $exception->getMessage();
        }
        if (!$response->isEndResponse()) {
            $data = Array(
                "code" => $code,
                "data" => [],
                "msg" => $msg
            );
            $response->write(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
            $response->withHeader('Content-type', 'application/json;charset=utf-8');
            $response->withStatus($status_code);
            return true;
        } else {
            return false;
        }
    }
}