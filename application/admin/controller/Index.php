<?php
/**
 * Created by PhpStorm.
 * User: Hsii
 * Date: 2018/10/14
 * Time: 17:53 下午
 */

namespace app\admin\controller;

use app\common\controller\Count;
use app\common\model\User;
use app\common\model\Order;
use app\common\model\Orderdata;
class Index extends Base
{
    public function index()
    {
        $order = new Order();
        $count = new Count();
        $orderdata = new Orderdata();
        $result = $count->Sales();
        $user = new User();
        $c = $user->whereTime('create_time', 'm')->select();
        $M_user = $user->whereTime('create_time', 'm')->where('state',1)->limit(5)->order('user_money_cost','desc')->select();
        foreach ($M_user as $k => $v){
            $M_user[$k]['order_num'] = count($order->where('user_id',$v['id'])->select());
        }
        $O_product = $orderdata->whereTime('create_time', 'm')->field('*,count(product_id)')->group('product_id')->select();
        foreach ($O_product as $k => $v){
            $O_product[$k]['o_num'] = $orderdata->where('product_id',$v['product_id'])->sum('product_num');
            $O_product[$k]['o_money'] = $orderdata->where('product_id',$v['product_id'])->sum('product_money');
        }
        return $this->fetch('', [
            'Sales' => $result,
            'countUser' => count($c),
            'M_user' => $M_user,
            'O_product' => $O_product
        ]);
    }


}