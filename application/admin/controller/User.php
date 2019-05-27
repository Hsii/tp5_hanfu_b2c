<?php
/**
 * Created by PhpStorm.
 * User: Hsii
 * Date: 2018/11/23
 * Time: 15:17 下午
 */
namespace app\admin\controller;
use app\common\model\Pointlog;
use app\common\model\Moneylog;
use app\common\model\User as UserModel;
use app\common\validate\FailMessage;
use app\common\validate\SuccessMessage;
use think\Db;
use Think\Exception;
use think\Session;
use utf_pinyin;

class User extends Base
{
    public function index()
    {
        $user = UserModel::getNoramlUser();
        return $this->fetch('',[
            'userData' => $user,
            'count' => count($user)
        ]);
    }
    public function user_edit()
    {
        if (request()->isGet()) {
            $data = input('param.');
            $user = UserModel::get(function($query)use($data) {
                $query->where(['id' => $data['id'], 'code' => $data['fromto']]);
            });
            return $this->fetch('',[
                'userDataArr' => $user
            ]);
        }
    }

    public function user_money()
    {
        if (request()->isGet()) {
            $data = input('param.');
            if($data['mod'] == 'user' && $data['act']){
                $user = UserModel::get(function($query)use($data) {
                    $query->where(['id' => $data['id']]);
                });
                return $this->fetch('',[
                    'act' => $data['act'],
                    'user' => $user
                ]);
            }
        }
    }
    public function user_point()
    {
        if (request()->isGet()) {
            $data = input('param.');
            if($data['mod'] == 'user' && $data['act']){
                $user = UserModel::get(function($query)use($data) {
                    $query->where(['id' => $data['id']]);
                });
                return $this->fetch('',[
                    'act' => $data['act'],
                    'user' => $user
                ]);
            }
        }
    }
    public function save_user()
    {
        if(request()->isPost()){
            $data = input('param.');
            try{
                $map['id'] = $data['id'];
                $map['code'] = $data['code'];
                if(empty($data['user_password']) || strlen($data['user_password']) != 32){
                    unset($data['user_password']);
                }
                $user = UserModel::get($data['id']);
                $user->allowField(true)->save($data);
                if($data['type'] == 'point' && $data['action']){
                   $pointlog = new Pointlog();
                   $pointlog->savePointLog($data['action'],$data['pointlog_in'],$data['pointlog_out'],$data['user_point'],$data['pointlog_text'],$data['id'],$data['user_email']);
                }else if($data['type'] == 'money' && $data['action']){
                    $moneylog = new Moneylog();
                    $moneylog->saveMoneylog($data['action'],$data['moneylog_in'],$data['moneylog_out'],$data['user_money'],$data['moneylog_text'],$data['id'],$data['user_email']);
                }
                return new SuccessMessage();
            }catch (\Exception $e){
                return $e;
//                return new FailMessage();
            }
        }
    }
}