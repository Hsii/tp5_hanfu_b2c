<?php
/**
 * Created by PhpStorm.
 * User: Hsii
 * Date: 2019/02/07
 * Time: 22:37 下午
 */


namespace app\index\controller;


class Copyright extends Base
{
    public function index()
    {
        return $this->fetch();
    }
    // 商城协议
    public function disclaimer()
    {
        return $this->fetch();
    }
    // 新手指南
    public function helper()
    {
        return $this->fetch();
    }
}