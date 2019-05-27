<?php
/**
 * Created by PhpStorm.
 * User: Hsii
 * Date: 2019/02/23
 * Time: 11:14 上午
 */
namespace app\common\controller;
use app\common\model\Comment as CommentModel;
use app\common\model\Order;
use app\common\model\Pointlog;
use app\common\model\Product;
use app\common\model\User;
use app\index\controller\Base;
use think\Db;
use think\Exception;

class Comment extends Base
{
    // 确认收货
    public function Confirm($data)
    {
        Db::startTrans();
        try{
            $user = new User();
            $order = new Order();
            $product = new Product();
            $pointlog = new Pointlog();
            $map['order_state'] = 'success';
            $succ = $order->PlaceOrder($map,['order_id' => $data['order_id']]);
            Db::commit();   // 提交事务
            if($succ){
                $product->where('id',$data['product_id'])->setInc('product_sellnum');
//                 积分到账
                $m_order = Order::getOrderByOrderId($data['order_id']);
                $user->where('id',$m_order['user_id'])->setInc('user_point',$m_order['order_point_get']);
                // pointlog记录
                $m_order['pointlog_now'] = User::getUserByParam('id',$m_order['user_id'])->user_point;
                $pointlog->savePointLog('givepoint',$m_order['order_point_get'],0,$m_order['pointlog_now'],' 	交易完成获得，单号【'.$data['order_id'].'】',$m_order['user_id'],$m_order['user_name']);
                pe_jsonshow(array('result' => true,'show'=>'感谢您的支持!'));
            }else{
                pe_jsonshow(array('result' => false,'show'=>'系统出现了一点问题哟~'));
            }
        }catch (Exception $e){
            Db::rollback();  // 回滚事务
            pe_jsonshow(array('result' => false,'show'=>'系统出现了一点问题哟~'));
        }
    }
    // 商品评价
    public function Reviews($data)
    {
        if(is_array($data)){
            $user = User::getUserByParam('id',$data['user_id']);
            // 获取订单
            $s_order = Order::getOrderByOrderId($data['order_id']);
            $s_product = Product::getProductDetail($data['product_id']);
            if(!$s_order) pe_jsonshow(array('result' => false,'show'=>'订单不存在'));
            if(!$s_product) pe_jsonshow(array('result' => false,'show'=>'商品不存在'));
            $map['comment_star'] = $data['score'];
            $map['comment_text'] = $data['comment_text'];
            if(!empty($data['comment_img'])){
                $map['comment_img'] = implode(',',$data['comment_img']);
                $this->handleProductReviewsImg($data['comment_img']);
            }
            $map['comment_text'] = $data['comment_text'];
            $map['order_id'] = $data['order_id'];
            $map['product_id'] = $data['product_id'];
            $map['product_name'] = $s_product['product_name'];
            $map['product_logo'] = $s_product['product_logo'];
            $map['user_id'] = $data['user_id'];
            $map['user_name'] = $user['user_name'];
            $map['user_email'] = $user['user_email'];
            $map['user_logo'] = $user['user_logo'];
            try{
                $orderform = new CommentModel();
                $orderform->addOrderForm($map);
                // 积分到账
                $user->where('id',$s_order['user_id'])->setInc('user_point',$s_order['order_point_get']);
                // pointlog记录
                $pointlog = new Pointlog();
                $info = $this->getWebSetting();
                $m_pointlog['pointlog_now'] = $user->user_point;
                $m_pointlog['pointlog_in'] = $info['point_reg']['setting_value'];
                $pointlog->savePointLog('givepoint',$m_pointlog['pointlog_in'],0,$m_pointlog['pointlog_now'],'发表评价获得，单号【'.$s_order['order_id'].'】',$s_order['user_id'],$s_order['user_name']);
                pe_jsonshow(array('result' => true,'show'=>'评价成功!'));
            }catch (\Exception $e){
                pe_jsonshow(array('result' => false,'show'=>'系统出现了一点问题哟~'));
            }
        }
    }
    /**
     * 将获取到的图片一移动到public/upload/product/product_Reviews/商品编号/文件夹下
     */
    public function handleProductReviewsImg($data)
    {
        if (!empty($data)) {
            $__TEMP__ = ROOT_PATH . 'runtime/temp/uploads/';
            $__LOGO__ = ROOT_PATH . 'public/uploads/products/product_Reviews/';
            if (file_exists($__LOGO__ )) {
                for ($i = 0; $i < count($data); $i++) {
                    rename($__TEMP__ . $data[$i], $__LOGO__  . '/' . $data[$i]);
                }
            } else {
                mkdir($__LOGO__ );
                for ($i = 0; $i < count($data); $i++) {
                    rename($__TEMP__ . $data[$i], $__LOGO__  . '/' . $data[$i]);
                }
            }
        }
    }
}