<?php
namespace app\common\model;
class Department extends BaseModel
{
    /**
     * 根据员工ID获取用户所属部门
     * @param $usernameid
     */
    public function getUserPersonalByDepartment($departmentid)
    {
        if (!$departmentid) {
            exception('信息不合法');
        }
        $data = ['id' => $departmentid];

        return $this->where($data)->find();
    }
    /* 查询所有部门 */
    public function getDepartment()
    {
        $order =[
            'listorder' => 'desc',
            'id' => 'asc',
        ];

        return $this->order($order)
            ->select();
    }
}