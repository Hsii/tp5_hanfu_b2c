<?php
/**
 * Created by PhpStorm.
 * User: Hsii
 * Date: 2019/02/18
 * Time: 14:08 下午
 */


namespace app\common\model;


class Orderdata extends BaseModel
{
    // 查询id对应信息
    public static function getOrderDataById($id)
    {
        return self::where('orderdata_id', $id)->find();
    }
    // 查询订单id对应信息
    public static function getOrderDataByOrderId($order_id)
    {
        return self::where('order_id',$order_id)->select();
    }

    public function PlaceOrderData($data)
    {
        $orderdata_id = $this->allowField(true)->save($data);
        return $orderdata_id;
    }
}