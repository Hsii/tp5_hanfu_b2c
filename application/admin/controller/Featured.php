<?php
/**
 * Created by PhpStorm.
 * User: Hsii
 * Date: 2018/11/09
 * Time: 16:33 下午
 */


namespace app\admin\controller;

class Featured extends Base
{
    private $obj;

    public function _initialize()
    {
        $this->obj = model('Featured');
    }

    public function index()
    {
        $types = config('featured.featured_type');
        $type = input('get.type','');

        $results = $this->obj->getFeaturedsByType($type);
        return $this->fetch('',[
            'types' => $types,
            'results' => $results,
        ]);
    }

    public function add()
    {
        if(request()->isPost())
        {
            $data = input('post.');
            $id = model('Featured')->add($data);
            if(!$id)
            {
                $this->error('添加失败');
            }else{
                $this->success('添加成功');
            }
        }
        $types = config('featured.featured_type');
        return $this->fetch('',[
            'types' => $types,
        ]);
    }
}
