<?php


namespace App\Lib\Exception;


class ParamException extends BaseException
{
    public $code = 400;
    public $msg = 'undefined params';
    public $errorCode = 10000;
}