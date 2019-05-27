<?php
namespace app\index\controller;

class Lists extends Base
{
    public function index()
    {
        return $this->fetch();
    }
}
