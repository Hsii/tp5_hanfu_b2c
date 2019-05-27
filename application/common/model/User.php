<?php

namespace app\common\model;

use think\Model;

class User extends Model
{
    // 查找str对应的用户
    public static function getUserByParam($str, $param)
    {
        return self::where($str, $param)->find();
    }

    // 查找所有用户
    public static function getNoramlUser()
    {
        return self::where('state', '<>', 0)->select();
    }

    // 修改用户信息
    public function saveUserByParam($id, $param)
    {
        return $this->allowField(true)->save($param, ['id' => $id]);
    }

}