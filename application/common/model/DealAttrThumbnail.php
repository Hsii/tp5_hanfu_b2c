<?php
/**
 * Created by PhpStorm.
 * User: Hsii
 * Date: 2018/12/03
 * Time: 16:40 下午
 */
namespace app\common\model;

class DealAttrThumbnail extends BaseModel
{
    public function addDalAttrThumbnail($value)
    {
        $data['deal_attr_thumbnail'] = $value;
        return $this->allowField(true)->save($data);
    }
    public function getDalAttrThumbnail($value)
    {
        if(empty($value)){
            return $this->select();
        }else{
            return $this->where('deal_id',$value)->select();
        }
    }

}