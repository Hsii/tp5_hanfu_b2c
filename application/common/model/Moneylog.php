<?php
/**
 * Created by PhpStorm.
 * User: Hsii
 * Date: 2019/02/14
 * Time: 20:51 下午
 */
namespace app\common\model;

class Moneylog extends BaseModel
{
    public static function getMoneylog($user_email='',$moneylog_type='')
    {
        $user_email ? $map['user_email'] = $user_email : '';
        $moneylog_type ? $map['moneylog_type'] = $moneylog_type : '';
        return self::order('create_time','desc')->where($map)->select();
    }
    public function saveMoneylog($moneylog_type,$moneylog_in,$moneylog_out,$moneylog_now,$moneylog_text,$user_id,$user_email)
    {
        $data['moneylog_in'] = $moneylog_in;
        $data['moneylog_out'] = $moneylog_out;
        $data['moneylog_now'] = $moneylog_now;
        $data['moneylog_text'] = $moneylog_text;
        $data['user_id'] = $user_id;
        $data['user_email'] = $user_email;
        switch ($moneylog_type){
            case 'addmoney':
                // 系统充值
                $data['moneylog_type'] = 'add';
                break;
            case 'delmoney':
                // 系统扣除
                $data['moneylog_type'] = 'del';
                break;
            case 'paymoney':
                // 订单支付
                $data['moneylog_type'] = 'order_pay';
                break;
            case 'backmoney':
                // 系统退还
                $data['moneylog_type'] = 'back';
                break;
        }
        return $this->allowField(true)->save($data);
    }
}