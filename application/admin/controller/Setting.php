<?php
/**
 * Created by PhpStorm.
 * User: Hsii
 * Date: 2019/01/24
 * Time: 22:24 下午
 */
namespace app\admin\controller;
use app\common\model\Setting as SettingModel;
use app\common;
use think\Config;
use think\Db;

class Setting extends Base
{

    public function saveSetting()
    {
        if(request()->isPost()){
            $data = input('param.info/a');
            $setting = new SettingModel();
            Db::startTrans();
            try {
//                if(!empty($data['notice_start_time']) && !empty($data['notice_end_time'])){
//                    $data['notice_start_time'] = handleSettingTime($data['notice_start_time']);
//                    $data['notice_end_time'] = handleSettingTime($data['notice_end_time']);
//                }
                $setting->saveSetting($data);
                Db::commit();   // 提交事务
                $this->handleSettingImg($data['web_logo']);
                $this->handleSettingImg($data['web_qrcode']);
                return '<script language="javascript">alert("修改成功");window.location.href="http://58.87.89.241//admin/setting/index"</script>';
            } catch (\Exception $e) {
                Db::rollback();  // 回滚事务
                return "<script>alert('修改失败');window.location.href='http://58.87.89.241//admin/setting/index';</script>";
            }
        }
    }

    public function index()
    {
        $settingData = SettingModel::getSeeting();
        $info = array();
        foreach ($settingData as $key => $value){
            $info[$value['setting_key']]['setting_value'] = $value['setting_value'];
        }
        return $this->fetch('',[
            'info' => $info
        ]);
    }
    public function setting_email()
    {
        $settingData = SettingModel::getSeeting();
        $info = array();
        foreach ($settingData as $key => $value){
            $info[$value['setting_key']]['setting_value'] = $value['setting_value'];
        }
        Config::set('default_ajax_return','html');
        return $this->fetch('',[
            'info' => $info
        ]);
    }
    public function setting_user()
    {
        $settingData = SettingModel::getSeeting();
        $info = array();
        foreach ($settingData as $key => $value){
            $info[$value['setting_key']]['setting_value'] = $value['setting_value'];
        }
        Config::set('default_ajax_return','html');
        return $this->fetch('',[
            'info' => $info
        ]);
    }
    public function setting_point()
    {
        $settingData = SettingModel::getSeeting();
        $info = array();
        foreach ($settingData as $key => $value){
            $info[$value['setting_key']]['setting_value'] = $value['setting_value'];
        }
        Config::set('default_ajax_return','html');
        return $this->fetch('',[
            'info' => $info
        ]);
    }
    public function setting_notice()
    {
        $settingData = SettingModel::getSeeting();
        $info = array();
        foreach ($settingData as $key => $value){
            $info[$value['setting_key']]['setting_value'] = $value['setting_value'];
        }
        Config::set('default_ajax_return','html');
        return $this->fetch('',[
            'info' => $info,
        ]);
    }
    public function setting_sms()
    {
        $settingData = SettingModel::getSeeting();
        $info = array();
        foreach ($settingData as $key => $value){
            $info[$value['setting_key']]['setting_value'] = $value['setting_value'];
        }
        Config::set('default_ajax_return','html');
        return $this->fetch('',[
            'info' => $info
        ]);
    }
    /**
     * 将获取到的图片一移动到public/upload/web_setting/文件夹下
     * @param $banner_id
     * @param $data
     */
    public function handleSettingImg($data)
    {
        if (!empty($data)) {
            $__TEMP__ = ROOT_PATH . 'runtime/temp/uploads/';
            $__LOGO__ = ROOT_PATH . 'public/uploads/web_setting/';
            if (file_exists($__LOGO__)) {
                rename($__TEMP__ . $data, $__LOGO__  . $data);
            } else {
                mkdir($__LOGO__);
                rename($__TEMP__ . $data, $__LOGO__  . $data);
            }
        }
    }
}