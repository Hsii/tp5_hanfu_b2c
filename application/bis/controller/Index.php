<?php
/**
 * Created by PhpStorm.
 * User: Hsii
 * Date: 2018/10/14
 * Time: 17:53 下午
 */
namespace app\bis\controller;
class Index extends Base
{
    public function index()
    {
        return $this->fetch();
    }
}