<?php
/**
 * Created by PhpStorm.
 * User: Hsii
 * Date: 2019/02/24
 * Time: 18:37 下午
 */
namespace app\admin\controller;
use think\Controller;
use Think\Exception;
use think\Session;
use app\common\model\Staff;
class Login extends Controller
{
    public function login()
    {
        $data = input('post.');
        if ($data['user_id']) {
            $user = Staff::getUserByUserId($data['user_id']);
            if(!$user) pe_jsonshow(array('result'=>false,'show'=>'帐号不存在'));
            if (md5($data['password'].$user->code) != $user->password) pe_jsonshow(array('result'=>false,'show'=>'密码错误'));
            $staff = new Staff();
            $map['last_login_ip'] = $data['last_login_ip'];
            $map['last_login_time'] = time();
            try{
                $staff->saveUser($map,$data['user_id']);
                Session::delete('Staff.token');
                Session::delete('Staff.id');
                Session::set('Staff.id', $data['user_id']);
                Session::set('Staff.token', md5($data['user_id'].$user->code));
                pe_jsonshow(array('result'=>true,'show'=>'登陆成功'));
            }catch (Exception $e){
                pe_jsonshow(array('result'=>false,'show'=>'系统出了一点小问题'));
            }
        }else{
            return $this->fetch();
        }
    }
    public function logout()
    {
        if(Session::get('Staff.token')){
            Session::delete('Staff.token');
            Session::delete('Staff.id');
            // 跳出
            pe_jsonshow(array('result'=>true));
        }else{
            $this->redirect('user/login');
        }
    }
//    public function register()
//    {
//        $data = input('post.');
//        if($data){
//            $Utils = new utf_pinyin\CUtf8_PY();
//            $result =  str_split(str_shuffle($Utils->encode($data['username'])),3);
//            $code = '';
//            for ($i = 1; $i <= 1; $i++) {
//                $code .= chr(rand(97, 122));
//            }
//            $oei = str_split($result[0], 1);
//            foreach ($oei as $key => $value) {
//                if ($value == ' ') {
//                    unset($oei[$key]);
//                    $oei[] = $code;
//                }
//            }
//            $oei = implode('',$oei);
//            $time_only = str_split(str_shuffle(time()),7);
//            $data['usernameid'] = $oei.$time_only[0];
//            $data['code'] = mt_rand(100,10000);
//            $data['password'] = md5($data['password'].$data['code']);
//            try{
//                $res = model('Staff')->add($data);
//            }catch (\Exception $e) {
//                return '910';
////                $this->error($e->getMessage());
//            }
//            if($res)
//            {
//                // 发送激活邮件
//                $url = request()->domain().url('user/waiting',['id' => md5($data['usernameid'].time()),'username' => $data['username']]);
//                $title = '欢迎'.$data['username'].'加入西湘记!';
//                $content = "您的员工id是:<span style='color: #FA8072;font-size: 18px;'>".$data['usernameid']."</span>,这是您的唯一登录凭证,请妥善保管！</br>点击链接进行账户激活，激活后方可登录！</br><a href='".$url."' target='_blank'>查看链接</a></br>
//                            或将链接复制到浏览器".$url;
//                \phpmailer\Email::send($data['email'],$title,$content);
//                // 返回注册成功状态码
//                return '401';
//            }else{
//                return '402';
//            }
//        }else{
//            return $this->fetch();
//        }
//    }
//    //重置密码
//    public function resetpassword()
//    {
//        $data = input('post.');
//        if ($data) {
//            try {
//                $user = model('Staff')->getUserByUsernameid($data['usernameid']);
//            } catch (\Exception $e) {
//                return '910';
//            }
//            if (!$user) {
//                return '303';
//            }elseif ($user->status != 1){
//                return '305';
//            }elseif (md5($data['password'] . $user->code) != $user->password) {
//                return '302';
//            }
//            Db::name('Staff')->where('usernameid',$user['usernameid'])->update(['password' => md5($data['newpassword'].$user['code']),'status' => -1]);
//            // 发送通知邮件
//            $url = request()->domain().url('user/valida',['id' => md5($user['usernameid'].time()),'username' => $user['username'],'uscode'=>$user['code']]);
//            $title = '亲爱的'.$user['username'].',您的密码刚刚进行了修改!';
//            $content = "点击链接进行验证，验证后请使用新密码进行登录！</br><a href='".$url."' target='_blank'>查看链接</a></br>
//                            或将链接复制到浏览器".$url."</br>如果您并没有进行过密码修改，请及时联系管理人员";
//            \phpmailer\Email::send($user['email'],$title,$content);
//            return '309';
//
//        }
//
//        return $this->fetch();
//    }
//
//    public function waiting()
//    {
//        if(!request()->isGet()){
//            $this->redirect('user/login');
//        }else{
//            $data = input('param.username');
//            $user = model('Staff')->getUserByUsername($data);
//            Db::name('Staff')->where('usernameid',$user['usernameid'])->update(['status' => 1]);
//            return '已经成功了';
//        }
//
//    }
//
//    public function valida()
//    {
//        if(!request()->isGet()){
//            $this->redirect('user/login');
//        }else{
//            $data = input('param.uscode');
//            $user = model('Staff')->getUserByUserCode($data);
//            Db::name('Staff')->where('usernameid',$user['usernameid'])->update(['status' => 1]);
//            return '已经成功了';
//        }
//    }

}