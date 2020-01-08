<?php


namespace App\Tool;


class Code
{
    const CODE_SUCCESS = 0;//请求成功
    const CODE_FALI = 1;//失败
    const CODE_REQUEST_NOT_FOUND = 2;//路由不存在
    const CODE_SIGN_ERROR = 3;//验证签名失败
    const CODE_LOGIN_EXPIRE = 4;//登录失效
    const CODE_EXCEPTION = 5;

}