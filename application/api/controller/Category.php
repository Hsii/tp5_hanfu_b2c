<?php
/**
 * Created by PhpStorm.
 * User: Hsii
 * Date: 2018/10/23
 * Time: 17:53 下午
 */


namespace app\api\controller;
use think\Controller;

class Category extends Controller
{
    private $obj;

    public function _initialize()
    {
        $this->obj = model('Category');
    }

    public function getCategoryByParentId()
    {
        $id = input('param.id',0,'intval');
        if (!$id) {
            $this->error('ID不合法');
        }
        //通过id获取二级城市
        $categorys = $this->obj->getNormalCategoryByParentId($id);
        if(!$categorys){
            return show(0,'error');
        }
        return show(1,'success',$categorys);

    }
}