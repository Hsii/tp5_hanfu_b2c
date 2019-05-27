<?php
/**
 * Created by PhpStorm.
 * User: Hsii
 * Date: 2019/02/20
 * Time: 21:32 下午
 */
namespace app\user\controller;

use app\index\controller\Base;
use app\common\model\Order;
class Index extends BaseController
{
    public function index()
    {
        $user = $this->getLoginUser();
        $order = Order::getOrderByUserId($user['id']);
        $wpay_order = new Order();
        $wpay_num = $wpay_order->where('order_state','wpay')->where('user_id',$user->id)->select();
        $wsend_order = new Order();
        $wsend_num = $wpay_order->where('order_state','wsend')->where('user_id',$user->id)->select();
        return $this->fetch('',[
            'order' => $order,
            'wpay_num' => count($wpay_num),
            'wsend_num' => count($wsend_num),
        ]);
    }

}