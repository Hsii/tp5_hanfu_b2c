<?php

namespace app\index\controller;
use app\common\model\Cart as CartModel;
use app\common\model\Product;
use app\common\model\Prodata;
use app\common\model\Orderdata;
use app\common\model\User as UserModel;
use app\common\model\UserAddress;
use app\common\model\Pointlog;
use app\common\model\Moneylog;
use app\common\model\Order as OrderModel;
use app\common\validate\FailMessage;
use app\common\validate\SuccessMessage;
use think\Db;
use Think\Exception;

class Order extends Base
{
    public function index()
    {
        $data = input('param.');
        // 检测商品库存
        if ($data['mod'] == 'order') {
            switch ($data['act']) {
                case 'add':
                case 'buy':
                    $product_id = intval($data['id']);
                    $prodata_id = intval($data['guid']);
                    $product_num = intval($data['num']);
                    //获得购买商品信息
                    $productData = Product::getProductDetail($product_id);
                    //获得购买商品规格信息
                    $proData = Prodata::getProDatasByGuid($prodata_id);
                    if($proData){
                        if ($proData['product_num'] < $product_num) pe_jsonshow(array('result' => false, 'show' => "库存仅剩{$proData['product_num']}件"));
                    }else{
                        if ($productData['product_num'] < $product_num) pe_jsonshow(array('result' => false, 'show' => "库存仅剩{$productData['product_num']}件"));
                        $proData['product_money'] = $productData['product_money'];
                        $proData['product_rule_data'] = '';
                    }
                    if (!$productData) pe_jsonshow(array('result' => false, 'show' => '商品下架或失效'));
                    if ($data['type'] == 'confirm') {
                        //下订单
                        $order = new OrderModel();
                        $orderdata = new Orderdata();
                        $useraddress = new UserAddress();
                        $user = $this->getLoginUser();
                        $address = $useraddress->getUserAddressById($data['address_id']);
                        $ordata['order_id'] = setOrderSn();
                        $ordata['order_money'] = $product_num * $proData['product_money'];
                        $ordata['order_paid_money'] = $product_num * $proData['product_money'] + $productData['product_wlmoney'] - $data['order_point_use'] / $this->getWebSetting()['point_money']['setting_value'];
                        $ordata['order_point_get'] = $productData['product_point'] * $product_num;
                        $ordata['order_point_use'] = $data['order_point_use'];
                        $ordata['order_point_money'] = $data['order_point_use'] / $this->getWebSetting()['point_money']['setting_value'];
                        $ordata['order_text'] = $data['order_text'];
                        $ordata['order_wl_money'] = $productData['product_wlmoney'];
                        $ordata['user_id'] = $user['id'];
                        $ordata['user_name'] = $user['user_email'];
                        $ordata['user_tname'] = $address['user_tname'];
                        $ordata['user_phone'] = $address['user_phone'];
                        $ordata['user_address'] = $address['address_province'] . $address['address_city'] . $address['address_area'] . $address['address_text'];
                        $order->PlaceOrder($ordata);
                        $orderArr['order_id'] = $ordata['order_id'];
                        $orderArr['product_guid'] = $prodata_id;
                        $orderArr['product_id'] = $product_id;
                        $orderArr['product_name'] = $productData['product_name'];
                        $orderArr['product_rule'] = $proData['product_rule_data'];
                        $orderArr['product_logo'] = $productData['product_logo'];
                        $orderArr['product_money'] = $proData['product_money'] * $product_num;
                        $orderArr['product_num'] = $product_num;
                        $orderdata->PlaceOrderData($orderArr);
                        if ($ordata['order_paid_money'] == 0) {
                            // 更改订单状态
                            $p_order = new OrderModel();
                            $map['order_state'] = 'wsend';
                            $map['order_pstate'] = 1;
                            $map['order_atime'] = time();
                            $map['order_ptime'] = time();
                            $p_order->PlaceOrder($map, ['order_id' => $ordata['order_id']]);
                            // 系统记录扣除积分
                            $Pay_pointlog = new Pointlog();
                            $Pay_point_now = $user['user_point'] - $ordata['order_point_use'];
                            $Pay_pointlog->savePointLog('paypoint', 0, $ordata['order_point_use'], $Pay_point_now, '订单支付抵现，单号【'. $ordata['order_id'] .'】', $user->id, $user->user_email);
                            // 修改商品库存

                            $PRODUCT = new Product();
                            $P_map['product_num'] = $productData['product_num'] - $product_num;
                            $PRODUCT->saveProductsById($P_map, $product_id);
                            if($proData){
                                $PRODATA = new Prodata();
                                $P_data_map['product_num'] = $proData['product_num'] - $product_num;
                                $PRODATA->saveProdatasByGuid($prodata_id, $P_data_map);
                            }
                            // 修改积分
                            $UserPayPoint = new UserModel();
                            $payP['user_point'] = $user['user_point'] - $ordata['order_point_use'];
                            $UserPayPoint->saveUserByParam($user->id, $payP);
                            // 跳转订单页面
                            pe_jsonshow(array('result' => true, 'url' => 'user/order?uid=' . $ordata['order_id']));
                        } else if ($ordata['order_paid_money'] > 0) {
                            $a_order = new OrderModel();
                            $map['order_atime'] = time();
                            $a_order->PlaceOrder($map, ['order_id' => $ordata['order_id']]);
                            // 跳转支付页面
                            pe_jsonshow(array('result' => true, 'url' => url('order/pay') . '?act=pay&uid=' . $ordata['order_id']));
                        }
                    }
                    pe_jsonshow(array('result' => true));
                    break;
            }
        }
    }


    public function confirm()
    {
        $param = input('param.');
        // 获取用户信息
        $user = $this->getLoginUser();
        $address = UserAddress::getUserAddressByUserId($user->id);
        if ($param['mod'] == 'order' && $param['act'] == 'buy') {
            $product_id = intval($param['id']);
            $prodata_id = intval($param['guid']);
            $product_num = intval($param['num']);
            //获得购买商品信息
            $productData = Product::getProductDetail($product_id);
            //获得购买商品规格信息
            $proData = Prodata::getProDatasByGuid($prodata_id);
            if(!$proData){
                $proData['product_money'] = $productData['product_money'];
                $proData['product_rule_data'] = '';

            }
            return $this->fetch('', [
                'User' => $user,
                'UserAddress' => $address,
                'Product' => $productData,
                'product_num' => $product_num,
                'guid' => $prodata_id,
                'prodata_rule' => unserialize($proData['product_rule_data']),
                'prodata_money' => $proData['product_money'],
                'order_money' => $proData['product_money'] * $product_num,
                'order_paid_money' => $proData['product_money'] * $product_num + $productData['product_wlmoney'],
                'setting_point_money' => $this->getWebSetting()['point_money']['setting_value'],
                'use_point_money' => $user['user_point'] / $this->getWebSetting()['point_money']['setting_value']
            ]);
        }elseif ($param['mod'] == 'cart' && $param['act'] == 'buy'){
            $cart_id = explode(',', $param['id']);
            $order_wl_money = $order_money = 0;
            foreach ($cart_id as $k => $v){
                $cart[] = CartModel::getCartByCartId($v);
                $order_money += $cart[$k]->product_num * $cart[$k]->product_money;
            }
            foreach ($cart_id as $key => $value){
                $pro['id'][] = $cart[$key]->product_id;
                $pro['num'][] = $cart[$key]->product_num;
                $pro['guid'][] = $cart[$key]->product_guid;
            }
            foreach ($pro['id'] as $key => $value){
                $product = Product::getProductDetail($value);
                $order_wl_money += $product->product_wlmoney;
            }
            return $this->fetch('', [
                'Type' => 'cart',
                'User' => $user,
                'UserAddress' => $address,
                'Cart' => $cart,
                'order_money' => $order_money,
                'order_wl_money' => $order_wl_money,
                'order_paid_money' => $order_money + $order_wl_money,
                'setting_point_money' => $this->getWebSetting()['point_money']['setting_value'],
                'use_point_money' => $user['user_point'] / $this->getWebSetting()['point_money']['setting_value']
            ]);
        }
    }

    public function pay()
    {
        $data = input('param.');
        $user = $this->getLoginUser();
        //获得订单orderd
        $order = OrderModel::getOrderByOrderId($data['uid']);
        //获得购买商品规格信息
//        $proData = Prodata::getProDatasByGuid($orderdata['product_guid']);
//        if(!$proData){
//            $proData['product_num'] = $proDuct['product_num'];
//        }
        if ($data['act'] == 'pay' && $data['uid']) {
            return $this->fetch('', [
                'order' => $order,
                'User' => $user
            ]);
        } else if ($data['qw']) {
            if ($this->payConfirm($data['qw'],$user)) {
                if ($user->user_money < $order->order_paid_money) return new FailMessage();
                Db::startTrans();
                try {
                    // 系统记录扣除余额
                    $moneylog = new Moneylog();
                    $pointlog = new Pointlog();
                    $Pay_money_log = $user->user_money - $order['order_paid_money'];
                    $Pay_point_log = $user->user_point - $order['order_point_use'];
                    $moneylog->saveMoneylog('paymoney', 0, $order['order_paid_money'], $Pay_money_log, '支付订单扣除，单号【'. $order['order_id'] .'】', $user->id, $user->user_email);
                    if ($order->order_point_use != 0) {
                        // 系统记录扣除积分
                        $pointlog->savePointLog('paypoint', 0, $order['order_point_use'], $Pay_point_log, '订单支付抵现，单号【'. $order['order_id'] .'】', $user->id, $user->user_email);
                    }
                    $USER = new UserModel();
                    // 扣除余额
                    $par['user_money'] = $Pay_money_log;
                    if ($order['order_point_use'] != 0) {
                        // 扣除积分
                        $par['user_point'] = $Pay_point_log;
                    }
                    // 记录消费总额
                    $par['user_money_cost'] = $user->user_money_cost + $order['order_paid_money'];
                    $USER->saveUserByParam($user->id,$par);
                    // 修改商品库存
                    // 查询Orderdata详细信息
                    $O_ordata = Orderdata::getOrderDataByOrderId($data['uid']);
                    $Product = new Product();
                    $Prodata = new Prodata();
                    //获得购买商品信息
                    foreach ($O_ordata as $k => $v){
                        $P_duct[] = Product::getProductDetail($v->product_id);
                        if($v->product_guid == 0){
                            $P_Num[$k]['product_num'] = $P_duct[$k]['product_num'] - $v['product_num'];
                            $Product->saveProductsById($P_Num[$k], $v['product_id']);
                        }else{
                            $P_Num[$k]['product_num'] = Prodata::getProDatasByGuid($v->product_guid)->product_num - $v['product_num'];
                            $Prodata->saveProdatasByGuid($v->product_guid, $P_Num[$k]);
                        }
                    }
//                    var_dump($P_Num);
////                    $Product = new Product();
////                    $duct_map['product_num'] = $proDuct['product_num'] - $orderdata['product_num'];
////                    $Product->saveProductsById($duct_map, $orderdata['product_id']);
////                    if($proData){
////                        $Prodata = new Prodata();
////                        $data_map['product_num'] = $proData['product_num'] - $orderdata['product_num'];
////                        $Prodata->saveProdatasByGuid($orderdata['product_guid'], $data_map);
////                    }
                    // 跳转订单页面
                    $order = new OrderModel();
                    $map['order_state'] = 'wsend';
                    $map['order_pstate'] = 1;
                    $map['order_ptime'] = time();
                    $order->PlaceOrder($map, ['order_id' => $data['uid']]);
                    Db::commit();   // 提交事务
                    return new SuccessMessage();
                } catch (Exception $e) {
                    Db::rollback();  // 回滚事务
                    return new FailMessage();
                }
            } else {
                return new FailMessage();
            }
        }
    }

    public function payConfirm($qw,$user)
    {
        $code = $user->code;
        if($user->user_paypw == md5($qw.$code)){
            return true;
        }else{
            return false;
        }
    }
}