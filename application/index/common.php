<?php

use app\common\model\Product;
use app\common\model\Category;

/**
 * Created by PhpStorm.
 * User: Hsii
 * Date: 2019/02/10
 * Time: 22:13 下午
 */
// 分类及子类
function getCategorysId($id)
{
    if (!is_numeric($id) || empty($id)) {
        $id = 0;
    } else {
        $category = Category::getCategoryById($id, true);
        if (!empty($category)) {
            $Arr = array();
            foreach ($category as $k => $v) {
                if ($v['pid'] !== 0) {
                    $Arr[] = $v['id'];
                    $idArr = implode(',', $Arr);
                }
            }
            if(!empty($idArr)){
                $id = $id . ',' . $idArr;
            }
        } else {
            $id = '';
        }
    }
    return $id;
}

//product.html 包邮
function isWuLiu($data)
{
    $str = '';
    if ($data == 0) {
        $str .= '<i class="icon bg_purple mal5">包邮</i>';
    }
    return $str;
}
//detail.html页面商品分类count
// 分类商品数量
function countProductsByCategoryId($id)
{
    $id = getCategorysId($id);
    return count(Product::getNoramlProductsByCategoryId($id, '', ''));
}
?>
