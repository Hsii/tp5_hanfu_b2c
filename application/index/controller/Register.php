<?php
/**
 * Created by PhpStorm.
 * User: Hsii
 * Date: 2019/02/07
 * Time: 21:10 下午
 */

namespace app\index\controller;

use app\common\model\User as UserModel;
use app\common\validate\FailMessage;
use app\common\validate\SuccessMessage;
use think\Db;
use think\Exception;
use app\common\controller\SendEmail;

class Register extends Base
{
    // 填写注册信息页面
    public function index()
    {
        return $this->fetch();
    }

    // 确认注册信息页面
    public function reg_activation()
    {
        $param = input('param.');
        if($param['t']){
            $data = UserModel::getUserByParam('user_email', $param['t']);
            return $this->fetch('', [
               'RegData' => $data,
            ]);
        }
    }

    // 完善信息页面
    public function reg_perfect()
    {
        $id = input('param.t');
        $data = UserModel::getUserByParam('id', $id);
        return $this->fetch('', [
            'RegData' => $data,
        ]);
    }
    // 用户注册
    public function save_user()
    {
        $data = input('post.');
        $user = new UserModel();
        if ($data['id']) {
            // 完善信息
            Db::startTrans();
            try {
                $data['user_paypw'] = md5($data['user_paypw'].$data['code']);
                $user->allowField(true)->save($data, ['id' => $data['id'],'code'=>$data['code']]);
                Db::commit();   // 提交事务
                $this->handleUserImg($data['user_logo']);
                return new SuccessMessage();
            } catch (\Exception $e) {
                Db::rollback();  // 回滚事务
                return new FailMessage();
            }
        } else {
            if(UserModel::getUserByParam('user_email',$data['user_email'])) return new FailMessage();
            $data['code'] = self::getCode();
            $data['user_password'] = md5($data['user_password'].$data['code']);
            Db::startTrans();
            try {
                $user->save($data);
                Db::commit();   // 提交事务
                $token = $user->user_email;
                $sendEmail = new SendEmail();
                $sendEmail->sendEmail($user);
                return $token;
            } catch (\Exception $e) {
                Db::rollback();  // 回滚事务
                return new FailMessage();
            }
        }

    }

    public function UserConfirm()
    {
        $param = input('param.');
        $user = UserModel::getUserByParam('id',$param['id']);
        if($param['yc'] == md5($user['user_email'].$user['id']) && $param['token'] == sha1($user['code'].$user['user_email'])){
            $u = new UserModel();
            $u->saveUserByParam($param['id'],['state' => 1]);
            $this->redirect('Register/reg_activation',array('t'=>$user['user_email']));
        }
    }
    public static function getCode()
    {
        $str1 = '!@#$%^&*';
        $str2 = $str3 = $str4 = '';
        foreach (range('A', 'Z') as $v) {
            $str2 .= $v;
        }
        foreach (range('a', 'a') as $v) {
            $str3 .= $v;
        }
        foreach (range(0, 9) as $v) {
            $str4 .= $v;
        }

        $code = md5(str_shuffle($str1 . $str2 . $str3 . $str4));
        return $code;
    }
    /**
     * 将获取到的图片一移动到public/upload/web_setting/文件夹下
     * @param $banner_id
     * @param $data
     */
    public function handleUserImg($data)
    {
        if (!empty($data)) {
            $__TEMP__ = ROOT_PATH . 'runtime/temp/uploads/';
            $__LOGO__ = ROOT_PATH . 'public/uploads/user/logo/';
            if (file_exists($__LOGO__)) {
                rename($__TEMP__ . $data, $__LOGO__  . $data);
            } else {
                mkdir($__LOGO__);
                rename($__TEMP__ . $data, $__LOGO__  . $data);
            }
        }
    }

    public function test()
    {
        $user = UserModel::getUserByParam('id',25);
        $content = '';
        // 发送激活邮件
        $url = request()->domain().url('Register/UserConfirm',['id' => $user['id'], 'yc'=>md5($user['user_email'].$user['id']),'token' => sha1($user['code'].$user['user_email'])]);
        $title = '欢迎加入西湘记';
        $content .= "您的激活链接是:<span style='color: #FA8072;font-size: 18px;'>";
        $content .= "<a href='".$url."' target='_blank'>查看链接</a>";
        $content .= "</span></br>点击链接进行账户激活,或将链接复制到浏览器,激活后方可登录！</br>";
        \phpmailer\Email::send($user['user_email'],$title,$content);
//        $sendEmail = new SendEmail();
//        $sendEmail->sendEmail($user);
    }
}