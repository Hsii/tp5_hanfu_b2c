<?php
/**
 * Created by PhpStorm.
 * User: Hsii
 * Date: 2018/10/30
 * Time: 21:56 下午
 */
namespace app\bis\controller;
use think\Controller;

class Base extends Controller
{
    public $account;
    public function _initialize()
    {
        //判定用户是否登陆
        $isLogin = $this->isLogin();
        if(!$isLogin)
        {
             $this->redirect('login/index');
        }

    }
    // 判定用户是否登陆
    public function isLogin()
    {
        // 获取session
        $user = $this->getLoginUser();
        if($user)
        {
            return true;
        }
        return false;
    }

    public function getLoginUser()
    {
        if(!$this->account)
        {
            $this->account = serialize(session('bisAccount','','bis'));
        }
//        $bis_id = (array)unserialize($this->account)->bis_id;
//        var_dump($bis_id);
//        print_r((array)unserialize($this->account)->bis_id);
        return $this->account;
    }
}
