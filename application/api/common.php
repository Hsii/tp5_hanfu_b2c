<?php
/**
 * Created by PhpStorm.
 * User: Hsii
 * Date: 2018/10/23
 * Time: 17:28 下午
 */


function show($status,$message='',$data=[])
{
    return [
        'status' => intval($status),
        'message' => $message,
        'data' => $data,
    ];
}