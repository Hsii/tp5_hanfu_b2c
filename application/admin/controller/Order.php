<?php
/**
 * Created by PhpStorm.
 * User: Hsii
 * Date: 2019/02/15
 * Time: 16:14 下午
 */


namespace app\admin\controller;

use app\common\model\Order as OrderModel;
use app\common\model\Orderdata;
use app\common\model\User as UserModel;
use app\common\model\Moneylog;
use app\common\model\Pointlog;
use app\common\validate\FailMessage;
use app\common\validate\SuccessMessage;
use Think\Exception;

class Order extends Base
{
    public function index()
    {
        $order = OrderModel::getOrderByState();
        $count = OrderModel::getOrderCount();
        return $this->fetch('', [
            'order' => $order,
            'count' => $count
        ]);
    }
    public function order_edit()
    {
        $id = input('param.id');
        $order = OrderModel::getOrderByOrderId($id);
        $orderdata = Orderdata::getOrderDataByOrderId($id);
        if($order && $orderdata){
            return $this->fetch('',[
                'order' => $order,
                'orderdata' => $orderdata
            ]);
        }

    }
    public function saveOrderState()
    {
        $param = input('param.');
        if ($param['mod'] == 'order' && $param['act'] && $param['id']) {
            $success = OrderModel::getOrderByOrderId($param['id']);
            $order = new OrderModel();
            if ($success) {
                switch ($param['act']) {
                    case 'success':
                        try {
                            $map['order_state'] = 'success';
                            $map['order_ftime'] = time();
                            $order->allowField(true)->save($map, ['order_id' => $param['id']]);
                            // 赠送积分到账
                            $this->order_givepoint($param['id']);
                            return new SuccessMessage();
                        }catch (Exception $e){
                            return new FailMessage();
                        }
                        break;
                    case 'del':
                        try {
                            if($success['order_state'] == 'close' && $success['order_pstate'] == 0 && $success['order_sstate'] == 0){
                                $map['order_state'] = '-1';
                                $order->allowField(true)->save($map, ['order_id' => $param['id']]);
                                return new SuccessMessage();
                            }
                            return new FailMessage();
                        }catch (Exception $e){
                            return new FailMessage();
                        }
                        break;
                }
            }
        }
    }
    // 确认收货获得积分
    public function order_givepoint($order_id)
    {
        $order = OrderModel::getOrderByOrderId($order_id);
        $user = UserModel::getUserByParam('id',$order['user_id']);
        if($order['order_point_get'] > 0){
            $map['user_point'] = $user['user_point'] + $order['order_point_get'];
            $user->allowField(true)->save($map,['user_email' => $order['user_name']]);;
            // 系统记录
            $pointlog = new Pointlog();
            $pointlog->savePointLog('givepoint',$order['order_point_get'],0,$map['user_point'],'发表评价获得，单号【'.$order_id.'】',$order['user_id'],$order['user_name']);
        }
    }
    public function order_close()
    {
        $param = input('param.');
        $order = OrderModel::getOrderByOrderId($param['id']);
        if($param['order_closetext']){
            try {
                // 取消订单
                $order = new OrderModel();
                $map['order_state'] = 'close';
                $map['order_pstate'] = $map['order_sstate'] = 0;
                $map['order_closetext'] = $param['order_closetext'];
                $order->allowField(true)->save($map, ['order_id' => $param['id']]);
                // 退款
                if($order['order_state'] != 'wpay' && $order['order_pstate'] = 1){
                    $this->order_refund($param['id']);
                }
                return new SuccessMessage();
            }catch (Exception $e){
                return new FailMessage();
            }
        }else{
            return $this->fetch('', [
                'order' => $order,
            ]);
        }
    }
    // 退款
    public function order_refund($order_id)
    {
        $order = OrderModel::getOrderByOrderId($order_id);
        $user = UserModel::getUserByParam('id',$order['user_id']);
        if($order['order_point_use'] > 0 && $order['order_state'] == 'success'){
            // 退积分
            $map['user_point'] = $user['user_point'] + $order['order_point_use'];
            $user->allowField(true)->save($map,['user_email' => $order['user_name']]);;
            // 系统记录
            $pointlog = new Pointlog();
            $pointlog->savePointLog('backpoint',$order['order_point_use'],0,$map['user_point'],'订单取消返还，单号【'.$order_id.'】',$order['user_id'],$order['user_name']);
        }
        if($order['order_paid_money'] != 0 && $order['order_pstate'] = 1){
            // 退余额
            $data['user_money'] = $user['user_money'] + $order['order_paid_money'];
            $data['user_money_cost'] = $user['user_money_cost'] - $order['order_paid_money'];
            $user->allowField(true)->save($data,['user_email' => $order['user_name']]);;
            // 系统记录
            $moneylog = new Moneylog();
            $moneylog->saveMoneylog('backmoney',$order['order_paid_money'],0,$data['user_money'],'订单退款返还，单号【'.$order_id.'】',$order['user_id'],$order['user_name']);
        }
    }
    // 发货
    public function order_send()
    {
        $param = input('param.');
        if($param['order_wl_id'] && $param['order_wl_name']){
            $order = new OrderModel();
            $param['order_stime'] = time();
            $param['order_state'] = 'wget';
            $param['order_sstate'] = 1;
            $order->allowField(true)->save($param, ['order_id' => $param['uid']]);
        }else{
            $order = OrderModel::getOrderByOrderId($param['id']);
            $info = $this->getWebSetting();
            $wlname = $info['web_wlname']['setting_value'];
            return $this->fetch('', [
                'order' => $order,
                'wlname' => explode('，', $wlname)
            ]);
        }
    }
    // 修改地址
    public function order_address()
    {
        $param = input('param.');
        if($param['act'] = 'address' && $param['order_id']){
            $order = new OrderModel();
            $order->allowField(true)->save($param, ['order_id' => $param['order_id']]);
        }else{
            $order = OrderModel::getOrderByOrderId($param['id']);
            $info = $this->getWebSetting();
            $wlname = $info['web_wlname']['setting_value'];
            return $this->fetch('', [
                'order' => $order,
                'wlname' => explode('，', $wlname)
            ]);
        }
    }
    // 发货单|快递单
    public function order_print_product()
    {
        $id = input('param.id');
        $type = input('param.act');
        $product_allmoney = $product_allnum = 0;
        $order = OrderModel::getOrderByOrderId($id);
        $orderdata = Orderdata::getOrderDataByOrderId($id);
        $info = $this->getWebSetting();
        $webtitle = $info['web_title']['setting_value'];
        foreach ($orderdata as $k => $v){
            $product_allmoney += $v['product_money'];
            $product_allnum += $v['product_num'];
        }
        return $this->fetch('', [
            'type' => $type,
            'order' => $order,
            'orderdata' => $orderdata,
            'product_allmoney' => $product_allmoney,
            'product_allnum' => $product_allnum,
            'webtitle' => $webtitle
        ]);
    }

}