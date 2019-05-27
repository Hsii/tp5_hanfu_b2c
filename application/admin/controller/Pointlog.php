<?php
/**
 * Created by PhpStorm.
 * User: Hsii
 * Date: 2019/02/15
 * Time: 14:51 下午
 */


namespace app\admin\controller;

use app\common\model\Pointlog as PointlogModel;

class Pointlog extends Base
{
    public function index()
    {
        $data = input('get.');
        if ($data['mod'] == 'pointlog') {
            $pointlog_type = $data['type'];
            $user_name = $data['user_name'];
            $pointlog = PointlogModel::getPointlog($user_name, $pointlog_type);
            $count = PointlogModel::get()->count();
            return $this->fetch('', [
                'pointlog' => $pointlog,
                'count' => $count
            ]);
        } else {
            $pointlog = PointlogModel::getPointlog();
            $count = PointlogModel::get()->count();
            return $this->fetch('', [
                'pointlog' => $pointlog,
                'count' => $count
            ]);
        }

    }

}