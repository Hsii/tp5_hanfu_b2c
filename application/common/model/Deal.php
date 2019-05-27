<?php
namespace app\common\model;

class Deal extends BaseModel
{
//    // 一对一关联:DealAttr
//    public function DealAttr()
//    {
//        return $this->hasOne('DealAttr','deal_id','id');
//    }
    // 获取所有商品 $deal_id默认为空值，即为查询所有商品
    public function getDeal($deal_id='',$begin='',$limit='')
    {
        $map['category_subclass_id']  = $deal_id;
        $map['status']  = ['<>',-1];
        if(empty($deal_id)){
            unset($map['category_subclass_id']);
            return $this->order('id asc')->limit($begin, $limit)->where($map)->select();
        }else{
            return $this->order('id asc')->limit($begin, $limit)->where($map)->select();
        }
    }
    public function addDeal($data)
    {
        if(!array($data)){
            exception('信息不合法');
        }
        return  $this->allowField(true)->save($data);
    }

    public function delDeal($id)
    {
        if(empty($id)){
            exception('信息不合法');
        }
        //设置更新条件
        $where = ['id' => $id];
        //设置更新数据
        $data['status'] = -1;
        $data['delete_time'] = time();
        return $this->allowField(true)->save($data,$where);
    }

    public function updateDeal($data)
    {
        if(!array($data)){
            exception('信息不合法');
        }
        $map = [
            'status' => $data['status']
        ];
        return $this->allowField(true)->save($map,['id'=>$data['id']]);
    }
}