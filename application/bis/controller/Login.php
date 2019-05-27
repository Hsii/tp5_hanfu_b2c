<?php
/**
 * Created by PhpStorm.
 * User: Hsii
 * Date: 2018/10/21
 * Time: 21:03 下午
 */
namespace app\bis\controller;
use think\Controller;
use think\Session;
class Login extends Controller
{
    public function index()
    {
//        return $this->fetch();
        if(request()->isPost())
        {
            //登陆逻辑
            $data = input('post.');

            //通过用户名获取用户相关信息
            //严格判定
            $ret = model('BisAccount')->get(['username'=>$data['username']]);
            if(!$ret || $ret->status != 1)
            {
                $this->error('该用户不存在或未获得管理员权限');
            }
            if($ret->password != md5($data['password'].$ret->code))
            {
                $this->error('密码不匹配');
            }
            model('BisAccount')->updateById(['last_login_time'=>time()],$ret->id);
            // 保存用户信息
            session('bisAccount',$ret,'bis');
//            var_dump(session('bisAccount','','bis'));
//            var_dump($_SESSION['bis_id']);
//            print_r($ret['username']);
            $this->success('登陆成功','index/index');
        }else{
            // 获取session
            $account = Session::get('bisAccount','bis');
            if($account)
            {
                $this->redirect('index/index');
            }

            return $this->fetch();
        }
    }
    public function logout()
    {
        // 清除session作用域
        session(null,'bis');
        // 跳出
        $this->redirect('login/index');
    }
}