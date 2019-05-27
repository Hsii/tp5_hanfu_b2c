<?php
namespace app\common\model;

class Bis extends BaseModel
{

    protected $autoWriteTimestamp = true;
    /**
     * 通过状态获取商家数据
     * @param $status
     */
    public function getBisByStatus($status=0) {
        $order = [
            'id' => 'desc',
        ];

        $data = [
            'status' => $status,
        ];
        $result = $this->where($data)
            ->order($order)
            ->paginate();
        return $result;
    }
//
//    public function add($data)
//    {
//        $data['status'] = 1;
//        $this->save($data);
//        return $this->id;
//    }
}