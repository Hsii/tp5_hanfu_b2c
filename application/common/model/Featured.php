<?php
namespace app\common\model;

class Featured extends BaseModel
{
    /**
     * 根据类型来获取列表数据
     * @param $type
     */
    public function getFeaturedsByType($type=[]) {
        $data = [
            'type' => $type,
            'status' => ['neq', -1],
        ];

        $order = ['id'=>'desc'];

        $result = $this->where($data)
            ->order($order)
            ->paginate();
//        echo $this->getLastSql();
        return $result;
    }

}