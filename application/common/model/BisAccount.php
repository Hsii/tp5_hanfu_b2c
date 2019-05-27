<?php
namespace app\common\model;
 class BisAccount extends BaseModel
{
    public function updateById($data, $id) {
        // allowField 过滤data数组中非数据表中的数据
        return $this->allowField(true)->save($data, ['id'=>$id]);
        // post数组中只有name和email字段会写入
//        return $this->allowField(['username','password'])->save($data,['id'=>$id]);
    }
}