<?php
/**
 * Created by PhpStorm.
 * User: Hsii
 * Date: 2019/02/23
 * Time: 14:05 下午
 */
namespace app\common\model;
use app\common\model\Product;
use app\common\model\Order;

class Comment extends BaseModel
{
    protected $updateTime = false;

    public function addOrderForm($data)
    {
        if(is_array($data)){
            $success = $this->allowField(true)->save($data);
            if($success){
                $p_su = $this->addProductCommentnum($data['product_id']);
                if($p_su){
                    $this->saveOrderState($data['order_id']);
                    return true;
                }
            }
        }else{
            return false;
        }
    }
    // 所有评价
    public static function getComment($where='')
    {
        return self::where($where)->order('create_time')->select();
    }
    // 查找商品评价
    public static function getCommentByProductId($product_id)
    {
        return self::where('product_id',$product_id)->order('create_time')->select();
    }
    // 查找用户评价
    public static function getCommentByUserId($user_id)
    {
        return self::where('user_id',$user_id)->order('create_time')->select();
    }
    public function addProductCommentnum($product_id)
    {
        $product = new Product();
        return $product->where('id',$product_id)->setInc('product_commentnum');
    }

    public function saveOrderState($order_id)
    {
        $order = new Order();
        $map['order_state'] = 'close';
        $map['order_ftime'] = time();
        $where['order_id'] = $order_id;
        return $order->PlaceOrder($map,$where);
    }

    public function delComment($id)
    {
        return $this->where('comment_id',$id)->delete();
    }
}