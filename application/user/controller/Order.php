<?php
/**
 * Created by PhpStorm.
 * User: Hsii
 * Date: 2019/02/20
 * Time: 22:56 下午
 */

namespace app\user\controller;
use app\common\controller\Comment;
use app\common\model\Comment as CommentModel;
use app\common\model\Product;
use app\common\model\Collect;
use app\common\model\Order as OrderModel;
use app\index\controller\Base;

class Order extends BaseController
{
    public function index()
    {
        $user_id = $this->getLoginUser()->id;
        $order = OrderModel::getOrderByUserId($user_id);
        return $this->fetch('',[
            'count' => count($order),
            'order' => $order
        ]);
    }
    // 我的评价
    public function comment()
    {
        $id = $this->getLoginUser()->id;
        $orderform = new CommentModel();
        $comment = $orderform->getCommentByUserId($id);
        foreach ($comment as $k => $v){
            $comment[$k]['product'] = Product::getProductDetail($v['product_id']);
        }
        return $this->fetch('',[
            'count' => count($comment),
            'comment' => $comment
        ]);
    }
    // 商品收藏
    public function collect()
    {
        $user = $this->getLoginUser();
        $collect = Collect::getCollectByUserId($user['id']);
        foreach ($collect as $k => $v){
            $collect[$k]['product'] = Product::getProductDetail($v['product_id']);
        }
        return $this->fetch('',[
            'count' => count($collect),
            'collect' => $collect
        ]);
    }
    // 订单详情
    public function view()
    {
        $order_id = input('param.order_id');
        $order = OrderModel::getOrderByOrderId($order_id);
        return $this->fetch('',[
            'order' => $order
        ]);
    }
    // 商品评价
    public function order_comment()
    {
        $data = input('param.');
        if($data['act'] == 'reviews'){
            $comment = new Comment();
            $data['user_id'] = $this->getLoginUser()->id;
            $comment->Reviews($data);
        }else{
            $order = OrderModel::getOrderByOrderId($data['id']);
            return $this->fetch('',[
                'order' => $order
            ]);
        }
    }
    // 确认收货
    public function confirm()
    {
        $data = input('param.');
        $comment = new Comment();
        $comment->Confirm($data);
    }
}