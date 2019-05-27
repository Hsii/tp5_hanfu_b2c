<?php
/**
 * Created by PhpStorm.
 * User: Hsii
 * Date: 2018/10/30
 * Time: 17:17 下午
 */
namespace app\admin\validate;
use think\Validate;

class Bis extends Validate
{
    protected $rule = [
        ['id','number'],
        ['status','number|in:-1,0,1','状态必须是数字|状态范围不合法'],
        ['listorder','number']
    ];
    /*场景设置*/
    protected $scene = [
        'listorder' => ['id','listorder'],  //排序
        'status' => ['id', 'status'],
    ];
}