<?php
/**
 * Created by PhpStorm.
 * User: Hsii
 * Date: 2019/02/12
 * Time: 22:35 下午
 */
namespace app\index\controller;
use app\common\model\User;
use app\common\validate\FailMessage;
use app\common\validate\SuccessMessage;
use think\Db;
use app\common\controller\SendEmail;
use think\Exception;

class Login extends Base
{
    public function index()
    {
        return $this->fetch();
    }
    // 忘记密码
    public function forget()
    {
        $param = input('param.');
        if($param['act'] == 'forget'){
            $user_email = $param['user_email'];
            $user = User::getUserByParam('user_email',$user_email);
            if($user){
                // 发送邮件操作
                $sendEmail = new SendEmail();
                $sendEmail->sendForgetEmail($user);
                return new SuccessMessage();
            }else{
                return new FailMessage();
            }
        }else if($param['yc']){
            $user = User::getUserByParam('id',$param['id']);
            if($param['yc'] == md5($user['user_email'].$user['id']) && $param['token'] == sha1($user['code'].$user['user_email'])){
                return $this->fetch('',[
                    'token' => sha1($user->code),
                    'data' => $user->id
                ]);
            }
        }else{
            return $this->fetch();
        }
    }

    public function save_user()
    {
        $data = input('post.');
        if ($data['id']) {
            // 完善信息
            Db::startTrans();
            try {
                $user = User::getUserByParam('id',$data['id']);
                $map['user_password'] = md5($data['user_password'].$user['code']);
                $s_user = new User();
                $s_user->allowField(true)->save($map, ['id' => $data['id'], 'code' => $user['code']]);
                Db::commit();   // 提交事务
                return new SuccessMessage();
            } catch (Exception $e) {
                Db::rollback();  // 回滚事务
                return new FailMessage();
            }
        }
    }
}