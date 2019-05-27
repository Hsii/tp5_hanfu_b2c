<?php
/**
 * Created by PhpStorm.
 * User: Hsii
 * Date: 2018/11/14
 * Time: 18:56 下午
 */
namespace app\index\controller;
use app\common\model\Collect;
use app\common\model\Comment;
use app\common\model\Orderdata;
use app\common\model\Product;
use app\common\model\Prodata;
use app\common\validate\SuccessMessage;
use Think\Exception;

class Detail extends Base
{
    public function index($id)
    {
        if(!intval($id))
        {
            $this->redirect('product/index');
        }
        $productData = Product::getProductDetail($id);
        if($productData){
            $productImg = getProductImg($productData['image_id']);
            $proData = Prodata::getProDatasById($id);
            $productData['product_rule'] = unserialize($productData['product_rule']);
            foreach ($proData as $k => $val) {
                $proData[$k]['product_rule_data'] = unserialize($val['product_rule_data']);
                $proData[$k]['product_rule_name_arr'] = explode(',', $proData[$k]['product_rule_name']);
            }
            $comment = Comment::getCommentByProductId($id);
            foreach ($comment as $k => $v){
                $or_rule = Orderdata::getOrderDataByOrderId($v['order_id']);
                foreach ($or_rule as $key => $value){
                    $comment[$k]['product_rule'] = unserialize($value['product_rule']);
//                    var_dump($comment[$k]['product_rule']);
                }
            }
            return $this->fetch('',[
                'productData' => $productData,
                'productImg' => $productImg,
                'proData' => $proData,
                'comment' => $comment,
                'count' => count($comment)
            ]);
        }else{
            return $this->redirect('/product/');
        }
        
    }
    // 商品收藏
    public function product_collect()
    {
        $map['product_id'] =  input('param.id');
        $map['user_id'] = $this->getLoginUser()->id;
        $collect = new Collect();
        try{
            $collect->addCollect($map);
            return new SuccessMessage();
        }catch (Exception $e){
            pe_jsonshow(array('result'=>false));
        }
    }
//    public function test($id){
//        $productData = Product::getProductDetail($id);
//        if($productData) {
//            $productImg = getProductImg($productData);
//            $proData = Prodata::getProDatasById($id);
//            // 处理product_rule字段
//            foreach ($productData as $k => $val) {
//                // 字符串反序列化成数组
//                $productData[$k]['product_rule'] = unserialize($val['product_rule']);
//            }
//            foreach ($proData as $k => $val) {
//                $proData[$k]['product_rule_data'] = unserialize($val['product_rule_data']);
//                $proData[$k]['product_rule_name_arr'] = explode(',', $proData[$k]['product_rule_name']);
//            }
//        }
//    }

//    public function index($id)
//    {
//        if(!intval($id))
//        {
//            $this->error('ID不合法');
//        }
//        // 根据id查询商品数据
//        $deal = model('Deal')->get($id);
////        var_dump($deal->image);exit;
//        if(!$deal || $deal->status!=1)
//        {
//            $this->error('该商品不存在');
//        }
//        // 获取分类信息
//        $category = model('Category')->get($deal->category_id);
//        // 获取分店信息
//        $location = model('BisLocation')->getNormalLocationsInId($deal->location_ids);
////        var_dump($location[0]['xpoint']);
//
//        $flag = 0;
//        if($deal->start_time > time()){
//            $flag = 1;
//            $timedate = '';
//            $dtime = $deal->start_time-time();
//            $d_day = floor($dtime/(3600*24));
//            if($d_day){
//                $timedate .= $d_day."天";
//            }
//            $d_hours = floor($dtime%(3600*24)/3600);
//            if($d_hours){
//                $timedate .= $d_hours."小时";
//            }
//            $d_minutes
//                = floor($dtime%(3600*24)%3600/60);
//            if($d_minutes){
//                $timedate .= $d_minutes."分";
//            }
//            $this->assign('timedate',$timedate);
//        }
////        var_dump($deal->id);
//        return $this->fetch('',[
//            'title' => $deal->name,
//            'category' => $category,
//            'location' => $location,
//            'deal' => $deal,
//            // 剩余优惠
//            'overplus' => $deal->total_count - $deal->buy_count,
//            // 优惠倒计时
//            'flag' => $flag,
//            // 经纬度
//            'mapstr' => $location[0]['xpoint'].','.$location[0]['ypoint'],
//        ]);
//    }
}