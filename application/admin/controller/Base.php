<?php
/**
 * Created by PhpStorm.
 * User: Hsii
 * Date: 2018/10/15
 * Time: 17:08 下午
 */
namespace app\admin\controller;
use think\Controller;
use think\Session;
use app\common\model\Setting;
use think\View;

class Base extends Controller
{
    public $user = '';
    public function _initialize()
    {
        $this->user_checklogin();
        // 网站设置数据
        $this->assign('info', $this->getWebSetting());
//        if (!Session::has('staffid','hanfu')) {
//            $this->redirect('user/login');
//        }else{
        /* 当前控制器名称 */
        $this->assign('controller', request()->controller());
        // 用户数据
        View::share('Staff',$this->getLoginStaff());
    }
    //检测登陆
    public function user_checklogin() {
        $user = $this->getLoginStaff();
        if ($user) return true;
        $this->redirect('login/login');
    }
    /* 获取登录用户*/
    public function getLoginStaff()
    {
        if (!$this->user) {
            $data['user_id'] = Session::get('Staff.id');
            $User = new \app\common\model\Staff();
            $this->user = $User->where($data)->find();
        }
        return $this->user;
    }
    /**
     * 获取网站设置数据
     * @return array
     */
    public function getWebSetting()
    {
        $settingData = Setting::getSeeting();
        $info = array();
        foreach ($settingData as $key => $value) {
            $info[$value['setting_key']]['setting_value'] = $value['setting_value'];
        }
        return $info;
    }
}