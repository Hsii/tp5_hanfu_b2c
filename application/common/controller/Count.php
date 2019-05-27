<?php
/**
 * Created by PhpStorm.
 * User: Hsii
 * Date: 2019/02/25
 * Time: 13:17 下午
 */


namespace app\common\controller;


use app\common\model\Order;
use think\Controller;

class Count extends Controller
{
    public function pv()
    {
//        $ip = $this->get_client_ip();
//        $count = 0;
//        $todayStart= strtotime(date('Y-m-d 00:00:00', time()));
//        $todayEnd= strtotime(date('Y-m-d 23:59:59', time()));
//        if($todayEnd >= time() && time() >= $todayStart){
//            if (is_file("__PUBLIC__/pv.txt")) {//有
//                //取文件里面的值
//                $count = file_get_contents("__PUBLIC__/pv.txt");
//                $time = date('Y-m-d',time());
//                file_put_contents("__PUBLIC__/pv.txt", $time);
//                file_put_contents("__PUBLIC__/pv.txt", $ip);
//                //累加
//                $count++;
//                //累加后的值存进文件
//                file_put_contents("__PUBLIC__/pv.txt", $count);
//                //输出pv数
//                return $count;
//            } else {//没有统计的文件
//                //创建文件 同时给文件里一个初始值
//                $time = date('Y-m-d',time());
//                file_put_contents("__PUBLIC__/pv.txt", $time);
//                file_put_contents("__PUBLIC__/pv.txt", $ip);
//                file_put_contents("__PUBLIC__/pv.txt", 1);
//                return $count;
//            }
//        }

    }

    public function Sales()
    {
        $beginThismonth = mktime(0, 0, 0, date('m'), 1, date('Y'));
        $endThismonth = mktime(23, 59, 59, date('m'), date('t'), date('Y'));
        $order = new Order();
        $result = $order->field("COUNT(*)  number,SUM(order_money)  account")->where("order_ftime >='$beginThismonth' AND order_ftime <='$endThismonth' AND order_state <> -1")->find();
        return $result;
    }

    public function get_client_ip()
    {
        $checks = array(
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'HTTP_VIA',
            'HTTP_X_COMING_FROM',
            'HTTP_COMING_FROM',
            'HTTP_CLIENT_IP',
            'X-Real-IP',
            'HTTP_X_REAL_IP',
        );

        foreach ($checks as $check) {
            if (isset($_SERVER[$check])) {
                $ip = $_SERVER[$check];

                if (($pos = strpos($ip, ',')) != FALSE) {
                    $ip = substr($ip, 0, $pos);
                }

                $ipnum = ip2long($ip);

                if ($ipnum !== -1 && $ipnum !== FALSE && long2ip($ipnum) === $ip) {
                    if (($ipnum < 167772160 || $ipnum > 184549375) && ($ipnum < -1408237568 || $ipnum > -1407188993) && ($ipnum < -1062731776 || $ipnum > -1062666241))
                        return $ip;
                }

            }
        }

        return $_SERVER['REMOTE_ADDR'];
    }
}