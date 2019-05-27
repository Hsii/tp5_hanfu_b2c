<?php
/**
 * Created by PhpStorm.
 * User: Hsii
 * Date: 2019/02/22
 * Time: 11:32 上午
 */


namespace app\common\model;
use app\common\model\Product;

class Collect extends BaseModel
{
    // 关闭自动写入update_time字段
    protected $updateTime = false;
    // 查找用户收藏
    public static function getCollectByUserId($user_id)
    {
        return self::where('user_id',$user_id)->order('create_time')->select();
    }
    // 统计商品收藏量
    public static function getCollectByProductId($product_id)
    {
        $num = self::where('product_id',$product_id)->select();
        return count($num);
    }
    // 商品收藏
    public function addCollect($data)
    {
        $map['product_id'] = $data['product_id'];
        $map['user_id'] = $data['user_id'];
        $success = $this->where($map)->find();
        if($success){
            // 取消商品收藏
            $this->where($map)->delete();
            // 修改商品收藏数量
            return $this->reviseProductCollectNum($map['product_id'],'reduce');
        }else{
            // 添加商品收藏
            $this->allowField(true)->save($map);
            // 修改商品收藏数量
            return $this->reviseProductCollectNum($map['product_id'],'add');

        }
    }

    public function reviseProductCollectNum($id,$act)
    {
        $product = new Product();
        if($act == 'add'){
            $product->where('id',$id)->setInc('product_collectnum');
        }elseif ($act == 'reduce'){
            $product->where('id',$id)->setDec('product_collectnum');
        }
    }
}