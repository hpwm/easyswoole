<?php


namespace App\Traits;


trait Params
{
    protected $params;
    public function setParams($params)
    {
        $this->params = $params;
    }

    protected function getParams()
    {
        return $this->params;
    }
}