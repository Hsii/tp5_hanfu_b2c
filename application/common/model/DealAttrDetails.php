<?php
/**
 * Created by PhpStorm.
 * User: Hsii
 * Date: 2018/12/03
 * Time: 21:14 下午
 */
namespace app\common\model;

class DealAttrDetails extends BaseModel
{
    public function addDalAttrDetails($th_value)
    {
        $data = [
            'deal_attr_details' => $th_value
        ];
        return $this->allowField(true)->save($data);
    }
    public function getDalAttrDetails($value)
    {
        if(empty($value)){
            return $this->select();
        }else{
            return $this->where('deal_id',$value)->select();
        }
    }
}