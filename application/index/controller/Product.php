<?php
/**
 * Created by PhpStorm.
 * User: Hsii
 * Date: 2019/01/09
 * Time: 22:33 下午
 */

namespace app\index\controller;
use app\common\controller\DecorateSearch;
use app\common\model\Category;
use app\common\model\Product as ProductModel;

class Product extends Base
{
    public function index($id = 0)
    {
        $id = getCategorysId($id);
        if (request()->isGet()) {
            $order = input('param.orderby');
            $order = self::handleOrder($order);
            $where['key'] = input('param.key');
            $where['product_money'] = input('param.pr');
            $where['product_num'] = input('param.nb');
            $where['product_wlmoney'] = input('param.fs');
            $map = self::handleWhere($where);
        }
        $products = ProductModel::getNoramlProductsByCategoryId($id, $order, $map);
        return $this->fetch('', [
            'productsItem' => $products,
        ]);
    }
    public static function handleOrder($order)
    {
        switch ($order) {
            case 'new':
                $order = [
                    'insert_time' => 'desc',
                    'update_time' => 'desc'
                ];
                break;
            case 'hot':
                $order = [
                    'product_sellnum' => 'asc',
                    'product_clicknum' => 'asc',
                    'product_collectnum' => 'asc',
                ];
                break;
            case 'price0':
                $order = [
                    'product_money' => 'asc'
                ];
                break;
            case 'price1':
                $order = [
                    'product_money' => 'desc'
                ];
                break;
            case 'key':
                break;
            default:
                $order = [
                    'id' => 'asc',
                    'product_order' => 'asc'
                ];
        }
        return $order;
    }

    public static function handleWhere($where)
    {
        if ($where['product_money']) {
            $where['product_money'] = explode('~', trim($where['product_money']));
            $FirstCondition = ['egt', $where['product_money'][0]];
            $MinorCondition = $where['product_money'][1] ? ['elt', $where['product_money'][1]] : false;
            if($MinorCondition){
                $map['product_money'] = array($FirstCondition, $MinorCondition, 'AND');
            }else{
                $map['product_money'] = array($FirstCondition);
            }
        }
        if ($where['product_num']) {
            $map['product_num'] = $where['product_num'] ? array('neq', 0) : false;
        }
        if ($where['product_wlmoney']) {
            $map['product_wlmoney'] = $where['product_wlmoney'] ? array('eq', 0) : false;
        }
        if ($where['key']) {
            $decorateSearch = new DecorateSearch();
            $map['product_name'] = $where['key'] ? array('like','%'.$decorateSearch->decorateSearch($where['key']).'%') : false;
        }
        return $map;
    }
}