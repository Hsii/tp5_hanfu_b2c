<?php
/**
 * Created by PhpStorm.
 * User: Hsii
 * Date: 2019/02/15
 * Time: 14:51 下午
 */


namespace app\admin\controller;

use app\common\model\Moneylog as MoneylogModel;

class Moneylog extends Base
{
    public function index()
    {
        $data = input('get.');
        if ($data['mod'] == 'money') {
            $moneylog_type = $data['type'];
            $user_name = $data['user_name'];
            $moneylog = MoneylogModel::getMoneylog($user_name, $moneylog_type);
            $count = MoneylogModel::get()->count();
            return $this->fetch('', [
                'moneylog' => $moneylog,
                'count' => $count
            ]);
        } else {
            $moneylog = MoneylogModel::getMoneylog();
            $count = MoneylogModel::get()->count();
            return $this->fetch('', [
                'moneylog' => $moneylog,
                'count' => $count
            ]);
        }

    }

}