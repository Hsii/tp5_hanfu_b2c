<?php
/**
 * Created by PhpStorm.
 * User: Hsii
 * Date: 2018/12/03
 * Time: 16:40 下午
 */
namespace app\common\model;

class DealAttr extends BaseModel
{
    // 添加属性
    public function addDalAttr($data)
    {
        return $this->allowField(true)->save($data);
    }

    public function getDealAttr($id)
    {
        return $this->order('id asc')->where('deal_id',$id)->select();
    }
}