<?php
/**
 * Created by PhpStorm.
 * User: Hsii
 * Date: 2019/02/24
 * Time: 16:47 下午
 */


namespace app\admin\controller;

use app\common\model\Staff as StaffModel;
use Think\Exception;

class Staff extends Base
{
    public function index()
    {
        $staff = StaffModel::getUser();
        return $this->fetch('', [
            'count' => count($staff),
            'staff' => $staff
        ]);
    }

    public function addstaff()
    {
        return $this->fetch();
    }

    public function editstaff()
    {
        $id = input('param.id');
        $staff = StaffModel::getUserByUserId($id);
        return $this->fetch('', [
            'staff' => $staff
        ]);
    }

    public function saveStaff()
    {
        $info = input('param.');
        $s_save = new StaffModel();
        if ($info['act'] == 'add') {
            $info['code'] = rand(0, 9999999999);
            $info['password'] = md5($info['password'].$info['code']);
            try {
                $s_save->saveUser($info);
                pe_jsonshow(array('result' => true, 'show' => '添加成功'));
            } catch (Exception $e) {
                pe_jsonshow(array('result' => false, 'show' => '系统出了一点小问题'));
            }
        } elseif ($info['act'] == 'edit') {
            $staff = StaffModel::getUserByUserId($info['user_id']);
            try {
                if (empty($info['password'])) {
                    pe_jsonshow(array('result' => true, 'show' => '修改成功'));
                } else {
                    $info['password'] = md5($info['password'].$staff['code']);
                    $s_save->saveUser($info, $info['user_id']);
                }
                pe_jsonshow(array('result' => true, 'show' => '修改成功'));
            } catch (Exception $e) {
                pe_jsonshow(array('result' => false, 'show' => '系统出了一点小问题'));
            }
        }elseif ($info['act'] == 'del'){
            try {
                $s_save->where('user_id',$info['id'])->delete();
                pe_jsonshow(array('result' => true, 'show' => '删除成功'));
            } catch (Exception $e) {
                pe_jsonshow(array('result' => false, 'show' => '系统出了一点小问题'));
            }
        }
    }
}