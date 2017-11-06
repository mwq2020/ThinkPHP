<?php
namespace app\index\controller;

class Test extends \think\Controller
{
    public function index()
    {
        return $this->fetch();
    }
}
