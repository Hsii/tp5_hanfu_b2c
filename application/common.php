<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// 应用公共文件
use app\common\model\Image;
// 反序列化
function getUnserialize($strArr){
    $strArr = unserialize($strArr);
    foreach ($strArr as $k => $v){
        $str = $v['name'];
        $str .= '：';
        $str .= $v['value'];
    }
    return $str;
}
// product.html|detail.html->product商品详情图处理
function getProductImg($productData)
{
    $imgIdArr = explode(',',$productData);
    foreach ($imgIdArr as $key => $value){
        $imgArr[] = Image::getImageById($value);
    }
    foreach ($imgArr as $key => $value){
        foreach ($value as $data){
            $productImg[] = $data['img_url'];
        }
    }
    return $productImg;
}
// 商品评价图处理
function getCommentImg($productData)
{
    $imgIdArr = explode(',', $productData);
    foreach ($imgIdArr as $k => $v){
        $imgArr .= '<img src="__UPIMG__/products/product_Reviews/'.$v.'"  height="55" width="55">';
    }
    return $imgArr;
}
// 时间戳转换,thinkphp自动转换时间戳，故先将时间转换为时间戳再进行转换
function handleSettingTime($data){
    if(is_numeric($data)){
        $time = date("Y-m-d", $data);
    }else{
        $data = strtotime($data);
        $time = date("Y-m-d", $data);
    }
   return $time;
}
// 时间戳转换
function TimeStamp($time){
    if($time != 0) return date("Y-m-d H:i:s",$time);
    return $time;
}
/**
 * 通用的分页样式
 * @param $obj
 */
function pagination($obj)
{
    if(!$obj)
    {
        return '';
    }
    $params = request()->param();
    return '<div class="cl pd-5 bg-1 bk-gray mt-20 tp5-o2o">'.$obj->appends($params)->render().'</div>';
}
/*
 *@param $url
 *@param int $type 0 get 1 post
 *@param array $data
 **/
function doCurl($url,$type=0,$data=[])
{
    $ch = curl_init(); //初始化
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch,CURLOPT_HEADER,0);

    if($type ==1)
    {
        curl_setopt($ch,CURLOPT_POST,1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
    }
    //执行并获取内容
    $output = curl_exec($ch);
    //释放curl句柄
    curl_close($ch);
    return $output;
}
function pe_jsonshow($arr) {
    echo json_encode($arr);
    die();
}
// 设置订单号
function setOrderSn()
{
    $yCode = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
    $orderSn = $yCode[intval(date('Y')) - 2011] . strtoupper(dechex(date('m'))) . date('d') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf('%02d', rand(0, 99));
    return $orderSn;
}
// 订单状态
function choiceOrderState($order_state){
    $str = '';
    switch ($order_state){
        case 'wpay':
            $str = '<span class="corg">等待付款</span>';
            break;
        case 'wsend':
            $str = '<span class="corg">等待发货</span>';
            break;
        case 'wget':
            $str = '<span class="corg">已发货</span>';
            break;
        case 'success':
            $str = '<span class="cgreen">交易完成</span>';
            break;
        case 'close':
            $str = '<del class="c999">交易关闭</del>';
            break;
    }
    return $str;
}
//评价星级
function pe_comment($val, $width=16) {
    global $pe;
    $star_arr = array(1=>'很差', 2=>'较差', 3=>'一般', 4=>'满意', 5=>'很满意');
    for ($i=1; $i<=5; $i++) {
        if ($i <= intval($val)) {
            $html .= "<img src='__STATIC__/common/images/star-on.png' title='{$i}' style='width:{$width}px;margin-right:1px' />";
        } else {
            $html .= "<img src='__STATIC__/common/images/star-off.png' title='{$i}' style='width:{$width}px;margin-right:1px' />";
        }
    }
    return $html;
}

