<?php
/**
 * Created by PhpStorm.
 * User: Hsii
 * Date: 2019/02/15
 * Time: 15:13 下午
 */
// 字符长度
function NumCount($str,$length = 25){
    if(mb_strlen($str) > $length) return mb_substr($str,0,$length);
}
// pointlog|moneylog 判断时间是否是当天
function isNow($data){
    $str = '';
    $time = substr($data,0,10);
    $now = date('Y-m-d');
    if($time == $now){
        $str .= '<span class="cred">';
        $str .= $data;
        $str .= '</span>';
    }else{
        $str = $data;
    }
    return $str;
}

// pointlog->pointlog_type|moneylog->moneylog_type转换
function switchLogType($type)
{
    switch ($type) {
        case 'add':
            // 系统充值
            $str = '系统充值';
            break;
        case 'del':
            // 系统扣除
            $str = '系统扣除';
            break;
        case 'give':
            // 系统赠送
            $str = '系统赠送';
            break;
        case 'order_pay':
            // 抵现扣除
            $str = '订单支付';
            break;
        case 'back':
            // 系统退还
            $str = '系统退还';
            break;
    }
    return $str;
}
?>
