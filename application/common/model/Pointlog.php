<?php
/**
 * Created by PhpStorm.
 * User: Hsii
 * Date: 2019/02/14
 * Time: 20:51 下午
 */


namespace app\common\model;


class Pointlog extends BaseModel
{
    public static function getPointlog($user_email='',$pointlog_type='')
    {
        $user_email ? $map['user_email'] = $user_email : '';
        $pointlog_type ? $map['pointlog_type'] = $pointlog_type : '';
        return self::order('create_time','desc')->where($map)->select();
    }
    public function savePointLog($pointlog_type,$pointlog_in,$pointlog_out,$pointlog_now,$pointlog_text,$user_id,$user_email)
    {
        $data['pointlog_in'] = $pointlog_in;
        $data['pointlog_out'] = $pointlog_out;
        $data['pointlog_now'] = $pointlog_now;
        $data['pointlog_text'] = $pointlog_text;
        $data['user_id'] = $user_id;
        $data['user_email'] = $user_email;
        switch ($pointlog_type){
            case 'addpoint':
                // 系统充值
                $data['pointlog_type'] = 'add';
                break;
            case 'delpoint':
                // 系统扣除
                $data['pointlog_type'] = 'del';
                break;
            case 'givepoint':
                // 系统赠送
                $data['pointlog_type'] = 'give';
                break;
            case 'paypoint':
                // 抵现扣除
                $data['pointlog_type'] = 'order_pay';
                break;
            case 'backpoint':
                // 系统退还
                $data['pointlog_type'] = 'back';
                break;
        }
        return $this->allowField(true)->save($data);
    }
}