<?php
/**
 * Created by PhpStorm.
 * User: Hsii
 * Date: 2019/02/18
 * Time: 15:39 下午
 */


namespace app\common\model;


class UserAddress extends BaseModel
{
    // 获取user_id地址
    public static function getUserAddressByUserId($id)
    {
        $map['user_id']= $id;
        $map['address_default'] = array('neq',-1);
        return self::where($map)->order('create_time','desc')->select();
    }
    // 获取id地址
    public static function getUserAddressById($id)
    {
        return self::where('address_id',$id)->find();
    }
    // 添加地址
    public function saveUserAddress($data)
    {
        return $this->allowField(true)->save($data);
    }
    // 修改地址
    public function updateUserAddressByUserId($address_id,$data)
    {
        $this->isDefault($data['user_id']);
        return $this->allowField(true)->save($data,['address_id'=>$address_id]);
    }
    // 查找默认地址
    public function isDefault($user_id)
    {
        $map['user_id'] = $user_id;
        $map['address_default'] = 1;
        $result = $this->where($map)->find();
        if($result['address_id']){
            $data['address_default'] = 0;
            return $this->allowField(true)->save($data,['address_id'=>$result['address_id']]);
        }else{
            return true;
        }
    }
}