<?php
/**
 * Created by PhpStorm.
 * User: Hsii
 * Date: 2019/02/26
 * Time: 20:17 下午
 */
namespace app\common\controller;

use think\Controller;

class SendEmail extends Controller
{
    public function sendEmail($user)
    {
        $content = '';
        // 发送激活邮件
        $url = request()->domain().url('Register/UserConfirm',['id' => $user['id'], 'yc'=>md5($user['user_email'].$user['id']),'token' => sha1($user['code'].$user['user_email'])]);
        $title = '欢迎加入西湘记';
        $content .= "您的激活链接是:<span style='color: #FA8072;font-size: 18px;'>";
        $content .= "<a href='".$url."' target='_blank'>".$url."</a>";
        $content .= "</span></br>点击链接进行账户激活,或将链接复制到浏览器,激活后方可登录！";
        \phpmailer\Email::send($user['user_email'],$title,$content);
    }

    public function sendForgetEmail($user)
    {
        $content = '';
        // 发送重置密码邮件
        $url = request()->domain().url('Login/forget',['id' => $user['id'], 'yc'=>md5($user['user_email'].$user['id']),'token' => sha1($user['code'].$user['user_email'])]);
        $title = '这是一封修改密码的邮件';
        $content .= "您的修改链接是:<span style='color: #FA8072;font-size: 18px;'>";
        $content .= "<a href='".$url."' target='_blank'>".$url."</a>";
        $content .= "</span></br>点击链接进行账户密码修改,或将链接复制到浏览器,修改使用新密码进行登录！";
        \phpmailer\Email::send($user['user_email'],$title,$content);
    }
}