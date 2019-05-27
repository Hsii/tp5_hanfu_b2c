<?php
/**
 * Created by PhpStorm.
 * User: Hsii
 * Date: 2019/02/21
 * Time: 14:44 下午
 */

namespace app\user\controller;

use app\common\controller\Hash;
use app\index\controller\Base;
use app\common\model\User as UserModel;
use app\common\model\UserAddress;

class User extends BaseController
{
    public function base()
    {
        $user = $this->getLoginUser();
        return $this->fetch('', [
            'user' => $user
        ]);
    }

    public function pw()
    {
        $user = $this->getLoginUser();
        return $this->fetch('', [
            'user' => $user
        ]);
    }

    public function paypw()
    {
        $user = $this->getLoginUser();
        return $this->fetch('', [
            'user' => $user
        ]);
    }
    public function saveUser()
    {
        $user = $this->getLoginUser();
        $data = input('param.');
        if ($data) {
            $User = new UserModel();
            if ($User->saveUserByParam($user['id'], $data)) pe_jsonshow(array('result' => true, 'show' => '修改成功', 'url' => 'base'));
        }
    }

    public function address()
    {
        $user = $this->getLoginUser();
        $address = UserAddress::getUserAddressByUserId($user['id']);
        return $this->fetch('', [
            'user' => $user,
            'address' => $address,
            'count' => count($address)
        ]);
    }

    public function adduserress()
    {
        return $this->fetch();
    }

    public function editaddress()
    {
        $address_id = input('param.id');
        $address = UserAddress::getUserAddressById($address_id);
        return $this->fetch('',[
            'address' => $address
        ]);
    }
    public function saveUserPw()
    {
        $user = $this->getLoginUser();
        $data = input('param.');
        $User = new UserModel();
        $hash = new Hash();
        $pw = $hash->hash_pw($data['user_oldpw'],$user['code']);
        if($data['act'] =='pw'){
            if(UserModel::getUserByParam('user_password',$pw)){
                $map['user_password'] = $hash->hash_pw($data['user_password'],$user['code']);
                $User->saveUserByParam($user['id'],$map);
                pe_jsonshow(array('result' => true, 'show' => '修改成功'));
            }else{
                pe_jsonshow(array('result' => false, 'show' => '当前密码错误'));
            }
        }else if($data['act'] == 'paypw'){
            if(UserModel::getUserByParam('user_paypw',$pw)){
                $map['user_paypw'] = $hash->hash_pw($data['user_password'],$user['code']);
                $User->saveUserByParam($user['id'],$map);
                pe_jsonshow(array('result' => true, 'show' => '修改成功'));
            }else{
                pe_jsonshow(array('result' => false, 'show' => $pw));
//                pe_jsonshow(array('result' => false, 'show' => '当前密码错误'));
            }
        }
    }
}