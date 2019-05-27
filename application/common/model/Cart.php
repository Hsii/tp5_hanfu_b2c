<?php
/**
 * Created by PhpStorm.
 * User: Hsii
 * Date: 2019/02/25
 * Time: 17:36 下午
 */
namespace app\common\model;

class Cart extends BaseModel
{
    protected $autoWriteTimestamp = false;

    public static function getCart($user_id)
    {
        return self::where('user_id',$user_id)->select();
    }

    public static function getCartByCartId($cart_id)
    {
        return self::where('cart_id',$cart_id)->find();
    }
    public function saveCart($data,$id='')
    {
        if(intval($id)){
            $success = $this->allowField(true)->save($data,['cart_id'=>$id]);
        }else{
            $success = $this->allowField(true)->save($data);
        }
        return $success;
    }
}