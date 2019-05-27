<?php
/**
 * Created by PhpStorm.
 * User: Hsii
 * Date: 2019/02/20
 * Time: 21:37 下午
 */
namespace app\user\controller;

use app\index\controller\Base;

class BaseController extends Base
{
    public function _initialize()
    {
        parent::_initialize();
        $this->checklogin();
    }

    public function checklogin()
    {
        if(!$this->getLoginUser()){
            $this->redirect('http://www.hanfu11.top/');
        }
    }
}