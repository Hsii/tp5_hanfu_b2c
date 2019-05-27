<?php

namespace app\index\controller;
use app\common\validate\FailMessage;
use app\common\validate\SuccessMessage;
use app\common\Model\User as UserModel;
use app\common\model\UserAddress;
use Think\Exception;
use think\Session;
use app\common\controller\Hash;

class User extends Base
{
    public function login()
    {
        if (request()->isPost()) {
            $user = new UserModel();
            $data = input('post.');
            $result = UserModel::getUserByParam('user_email',$data['user_email']);
            if($result->state == -1) return new FailMessage();
            try {
                $hash = new Hash();
                $map['user_email'] = $data['user_email'];
                $map['user_password'] = md5($data['user_password'].$result->code);
                $data = $user->where($map)->find();
                if ($data) {
                    $user->save(['user_ltime' => time()], ['id' => $data['id']]);
                    Session::delete('User.user_id');
                    Session::set('User.user_id', $data['id']);
                    return new SuccessMessage();
                } else {
                    return new FailMessage();
                }
            } catch (\Exception $e) {
                return new FailMessage();
            }
        }
    }
    public function logout()
    {
        if (request()->isPost()) {
            $user = new UserModel();
            $data = input('post.');
            try {
                $data = $user->where($data)->find();
                if ($data) {
                    Session::delete('User.user_id');
                    return new SuccessMessage();
                } else {
                    return new FailMessage();
                }
            } catch (\Exception $e) {
                return new FailMessage();
            }

        }
    }

    public function useraddress()
    {
        $param = input('param.') ;
        $useraddress = new UserAddress();
        // 获取用户信息
        $user= $this->getLoginUser();
        if($param['mod'] == 'useraddr'){
            switch ($param['act']){
                case 'add':
                    return $this->fetch();
                    break;
                case 'save':
                    $param['info']['user_id'] = $user->id;
                    $param['info']['user_email'] = $user->user_email;
                    $param['info']['user_tname'] = $param['user_tname'];
                    $param['info']['user_phone'] = $param['user_phone'];
                    $param['info']['address_province'] = $param['address_province'];
                    $param['info']['address_city'] = $param['address_city'];
                    $param['info']['address_area'] = $param['address_area'];
                    $param['info']['address_text'] = htmlspecialchars($param['address_text']);
                    if(empty($param['address_default'])){
                        $param['info']['address_default'] = 0;
                    }else{
                        $param['info']['address_default'] = $param['address_default'];
                    }
                    try{
                        $useraddress->isDefault($param['info']['user_id']);
                        $address = new UserAddress();
                        $success = $address->saveUserAddress($param['info']);
                        if($success){
                            pe_jsonshow(array('result' => true));
                        }
                    }catch (Exception $e){
                        return new FailMessage();
                    }
                    break;
                case 'edit':
                    $param['user_id'] = $user->id;
                    $useraddress->updateUserAddressByUserId($param['address_id'],$param);
                    pe_jsonshow(array('result' => true));
                    break;
                case 'del':
                    $param['address_default'] = -1;
                    try{
                        $useraddress->updateUserAddressByUserId($param['id'],$param);
                        return new SuccessMessage();
                    }catch (Exception $e){
                        return new FailMessage();
                    }
                    break;
            }
        }
    }
}
