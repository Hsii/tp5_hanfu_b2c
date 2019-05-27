<?php
namespace app\common\model;

class Staff extends BaseModel
{
    public function saveUser($data,$id='')
    {
        if($id){
            $where = ['user_id' => $id];
        }else{
            $where = '';
        }
        return $this->allowField(true)->save($data,$where);
    }

    public static function getUser()
    {
        return self::select();
    }
    public static function getUserByUserId($user_id)
    {
        if (!$user_id) {
            exception('信息不合法');
        }
        return self::where('user_id',$user_id)->find();
    }
}