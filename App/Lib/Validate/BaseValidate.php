<?php


namespace App\Lib\Validate;


use App\Lib\Exception\ParamException;
use EasySwoole\Http\Request;
use EasySwoole\Validate\Validate;

class BaseValidate extends Validate
{
    protected $sceneField = null;

    //定义规则
    protected $rule = [];

    //提示
    protected $message = [];

    //验证场景
    protected $scene = [];

    protected $needField = [];

    public function sceneField($field)
    {
        $this->sceneField = $field;
        return $this;
    }



    public function goCheck(Request $request)
    {
        $params = $request->getRequestParam();
        if(!$this->rule) return true;
        $this->needField = array_keys($this->rule);
        if($this->sceneField){
            if(!isset($this->scene[$this->sceneField])){
                throw new ParamException(['code'=>200,'msg'=>'未定义'.$this->sceneField.'验证场景','errorCode'=>1000]);
            }
            $this->needField = $this->scene[$this->sceneField];
        }
        foreach ($params as $k=>$v){
            if(!isset($this->needField[$k])) continue;
            $rule = $this->rule[$k];
            $rules = explode('|',$rule);
            foreach ($rules as $_k=>$_v){
//                $this->addColumn($k)->$_v
            }
        }


    }



}