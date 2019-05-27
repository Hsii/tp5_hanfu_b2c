<?php
/**
 * Created by PhpStorm.
 * User: Hsii
 * Date: 2019/02/21
 * Time: 13:48 下午
 */

namespace app\user\controller;
use app\index\controller\Base;
use app\common\model\Pointlog;
use app\common\model\Moneylog;
class Finance extends BaseController
{
    public function pointlog()
    {
        $user_email = $this->getLoginUser()->user_email;
        $pointlog = Pointlog::getPointlog($user_email);
        return $this->fetch('',[
            'count' => count($pointlog),
            'pointlog' => $pointlog
        ]);
    }
    public function moneylog()
    {
        $user_email = $this->getLoginUser()->user_email;
        $moneylog = Moneylog::getMoneylog($user_email);
        return $this->fetch('',[
            'count' => count($moneylog),
            'moneylog' => $moneylog
        ]);
    }
}