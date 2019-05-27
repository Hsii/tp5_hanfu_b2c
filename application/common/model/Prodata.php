<?php
/**
 * Created by PhpStorm.
 * User: Hsii
 * Date: 2019/01/23
 * Time: 18:12 下午
 */


namespace app\common\model;
use think\Model;

class Prodata extends Model
{
    protected $autoWriteTimestamp = false;

    protected $hidden = ['id'];
    // 查找商品id对应的商品规格集数据
    public static function getProDatasById($id)
    {
        return self::where('product_id',$id)->order('product_order','asc')->select();
    }
    // 查找guid对应的商品规格信息
    public static function getProDatasByGuid($id){
        return self::where('id',$id)->find();
    }
    // 修改库存
    public function saveProdatasByGuid($id,$data)
    {
        return $this->allowField(true)->save($data,['id'=>$id]);
    }
}