<?php
 /* Created by PhpStorm.
 * User: Hsii
 * Date: 2019/02/19
 * Time: 13:41 下午
 */
namespace app\index\controller;
use app\common\model\Orderdata;
use app\common\model\Product;
use app\common\model\Prodata;
use app\common\model\Cart as CartModel;
use app\common\model\Order;
use app\common\model\UserAddress;
use Think\Exception;

class Cart extends Base
{
    public function cart()
    {
        $param = input('param.');
        $cart = new CartModel();
        if($param['mod'] == 'order' && $param['act'] == 'add'){
            $product = Product::getProductDetail($param['id']);
            if(intval($param['guid'])){
                $prodata = Prodata::getProDatasByGuid($param['guid']);
                $map['product_guid']= $param['guid'];
                $map['product_rule'] = $prodata['product_rule_data'];
                $map['product_money'] = $prodata['product_money'];
            }else{
                unset($param['guid']);
                $map['product_money'] = $product['product_money'];
            }
            $map['cart_atime'] = time();
            $map['product_id'] = $param['id'];
            $map['product_name'] = $product['product_name'];
            $map['product_logo'] = $product['product_logo'];
            $map['product_num'] = $param['num'];
            $map['user_id'] = $this->getLoginUser()->id;
            try{
                $cart->saveCart($map);
                pe_jsonshow(array('result' => true));
            }catch (Exception $e){
                return $e;
            }
        }else if ($param['mod'] == 'cart' && $param['act'] == 'edit'){
            if($param['num'] == 0){
                $cart->where('cart_id',$param['id'])->delete();
                pe_jsonshow(array('result' => true));
            }else{
                $s['product_num'] = $param['num'];
                $cart->allowField(true)->saveCart($s,$param['id']);
                $c_cart = $cart->getCartByCartId($param['id'])->product_num;
                pe_jsonshow(array('result' => true,'cart_num' => $c_cart,'num' => $c_cart));
            }
        }else{
            $result = $cart->getCart($this->getLoginUser()->id);
            return $this->fetch('',[
                'result' => $result
            ]);
        }
    }
    // 结算->提交订单前的确认
    public function buy()
    {
        $param = input('param.');
        if ($param['mod'] = 'cart' && $param['act'] = 'confirm') {
            $pro = $this->ConfirmProduct($param['cart_id']);
            pe_jsonshow(array('result' => $pro,'id'=>$param['cart_id']));
        }
    }
    public function confirm()
    {
        $param = input('param.');
//        var_dump($param);
        if($param['mod'] == 'cart' && $param['act'] == 'buy' && $param['type'] == 'confirm'){
            $cart_id = explode(',', $param['id']);
            if (is_array($cart_id)){
                $or['order_id'] = setOrderSn();
                $or['order_money'] = $this->handleCartArr($cart_id);
                $or['order_point_get'] = $this->handlePointArr($cart_id);
                if(!empty($param['order_point_use'])){
                    $or['order_point_use'] = $param['order_point_use'];
                    $or['order_point_money'] = $param['order_point_use'] / $this->getWebSetting()['point_money']['setting_value'];
                }else{
                    $or['order_point_use'] = $or['order_point_money'] = 0;
                }
                $or['order_wl_money'] = $this->handleWlmoneyArr($cart_id);
                $or['order_paid_money'] = $or['order_money'] + $or['order_wl_money'] - $or['order_point_money'];
                $or['order_atime'] = time();
                $or['order_text'] = $param['order_text'];
                $user = $this->getLoginUser();
                $or['user_id'] = $user->id;
                $or['user_name'] = $user->user_email;
                $useraddress = UserAddress::getUserAddressById($param['address_id']);
                $or['user_tname'] = $useraddress->user_tname;
                $or['user_phone'] = $useraddress->user_phone;
                $or['user_address'] = $useraddress->address_province.$useraddress->address_city.$useraddress->address_area;
//                var_dump($or);
                $order = new Order();
                try{
                    $order->PlaceOrder($or);
                    $this->PlaceOrderData($cart_id,$or['order_id']);
                    // 跳转支付页面
                    pe_jsonshow(array('result' => true, 'url' => url('order/pay') . '?act=pay&uid=' . $or['order_id']));
                }catch (Exception $e){
                    return $e;
                }
            }
        }
    }

    public function test()
    {
        $param = [
            0 => 5,
            1 => 4,
            2 => 6
        ];
//        $pro = $this->getProduct($param);
//        foreach ($pro['guid'] as $k => $v){
//            if(empty($v)) {
//                $Prodata_Arr[$k] = Product::getProductDetail($pro['id'][$k]);
//            }else{
//                $Prodata_Arr[$k] = Prodata::getProDatasByGuid($v);
//            }
//        }
//        foreach ($param as $key => $value) {
//            $cart[] = \app\common\model\Cart::getCartByCartId($value);
//            if($cart[$key]->product_num <= $Prodata_Arr[$key]->product_num){
////                var_dump($cart[$key]->product_num);
////                var_dump($Prodata_Arr[$key]->product_num);
//            }else{
//                echo $Prodata_Arr[$key].'多了';
//            }
//        }
        $pro = $this->ConfirmProduct($param);
    }
    public function handleCartArr($cart_id)
    {
        $order_money = 0;
        if(is_array($cart_id)){
            foreach ($cart_id as $key => $value){
                $cart = \app\common\model\Cart::getCartByCartId($value);
                $order_money += $cart->product_money * $cart->product_num;
            }
        }
        return $order_money;
    }

    public function handlePointArr($cart_id){
        $order_point = 0;
        if(is_array($cart_id)){
            $pro = $this->getProduct($cart_id);
            foreach ($pro['id'] as $key => $value){
                $product = Product::getProductDetail($value);
                $order_point += $product->product_point * $pro['num'][$key];
            }
            return $order_point;
        }
    }
    public function handleWlmoneyArr($cart_id){
        $order_wl_money = 0;
        $pro = $this->getProduct($cart_id);
        foreach ($pro['id'] as $key => $value){
            $product = Product::getProductDetail($value);
            $order_wl_money += $product->product_wlmoney;
        }
        return $order_wl_money;
    }

    public function PlaceOrderData($cart_id,$order_id)
    {
        $orderdata = new Orderdata();
        foreach ($cart_id as $key => $value){
            $cart = \app\common\model\Cart::getCartByCartId($value);
            $map[$key]['order_id'] =$order_id;
            $map[$key]['product_id'] = $cart['product_id'];
            $map[$key]['product_guid'] = $cart['product_guid'];
            $map[$key]['product_name'] = $cart['product_name'];
            $map[$key]['product_rule'] = $cart['product_rule'];
            $map[$key]['product_logo'] = $cart['product_logo'];
            $map[$key]['product_money'] = $cart['product_money'];
            $map[$key]['product_num'] = $cart['product_num'];
        }
        $orderdata->saveAll($map);
    }

    public function getProduct($cart_id)
    {
        if(is_array($cart_id)){
            foreach ($cart_id as $key => $value){
                $cart = \app\common\model\Cart::getCartByCartId($value);
                $pro['id'][] = $cart->product_id;
                $pro['num'][] = $cart->product_num;
                $pro['guid'][] = $cart->product_guid;
            }
        }
        return $pro;
    }

    public function ConfirmProduct($cart_id)
    {
        $result = true;
        $pro = $this->getProduct($cart_id);
        foreach ($pro['guid'] as $k => $v){
            if(empty($v)) {
                $Prodata_Arr[$k] = Product::getProductDetail($pro['id'][$k]);
            }else{
                $Prodata_Arr[$k] = Prodata::getProDatasByGuid($v);
            }
        }
        foreach ($cart_id as $key => $value) {
            $cart[] = \app\common\model\Cart::getCartByCartId($value);
            if($cart[$key]->product_num > $Prodata_Arr[$key]->product_num){
                $result = false;
            }
        }
        return $result;
    }
}