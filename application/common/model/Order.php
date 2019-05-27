<?php
namespace app\common\model;
use think\Model;

class Order extends BaseModel
{
    // 关联data表
    public function orderdata()
    {
        return $this->hasMany('Orderdata', 'order_id');
    }
    // 提交订单
    public function PlaceOrder($data,$where=''){
        return $this->allowField(true)->save($data,$where);
    }
    // 根据order_id查找订单
    public static function getOrderByOrderId($id)
    {
        return self::with('orderdata')->where('order_id',$id)->find();
    }
    // 根据user_id查找订单
    public static function getOrderByUserId($id)
    {
        return self::with('orderdata')->where('user_id',$id)->where('order_state','<>',-1)->order('order_atime','desc')->select();
    }
    // 获取所有订单
    public static function getOrderByState()
    {
        // 全部订单
        $order[0] = self::with('orderdata')->where('order_state','<>',-1)->select();
        // 等待付款
        $order[1] = self::with('orderdata')->where('order_state', '=', 'wpay')->select();
        // 等待发货
        $order[2] = self::with('orderdata')->where('order_state', '=', 'wsend')->select();
        // 已经发货
        $order[3] = self::with('orderdata')->where('order_state', '=', 'wget')->select();
        // 交易完成
        $order[4] = self::with('orderdata')->where('order_state', '=', 'success')->select();
        // 交易关闭
        $order[5] = self::with('orderdata')->where('order_state', '=', 'close')->select();
        return $order;
    }
    public static function getOrderCount()
    {
        $order = self::getOrderByState();
        $num[0] = count($order[0]);
        $num[1] = count($order[1]);
        $num[2] = count($order[2]);
        $num[3] = count($order[3]);
        $num[4] = count($order[4]);
        $num[5] = count($order[5]);
        return $num;
    }
}