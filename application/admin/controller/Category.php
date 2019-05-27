<?php

namespace app\admin\controller;
use app\admin\validate\FailMessage;
use app\admin\validate\SuccessMessage;
use app\common\model\Category as CategoryModel;
use think\Db;
use Think\Exception;
use Library\CategoryTree;

class Category extends Base
{
    public function index()
    {
        return $this->fetch('', [
            'count' => CategoryModel::where('status', '<>', -1)->count(),
            'categoryData' => CategoryModel::getCategory()
        ]);
    }

    public function category_add()
    {
        return $this->fetch('', [
            'categoryData' => CategoryModel::getCategory()
        ]);
    }

    /**
     * @return mixed
     */
    public function category_edit()
    {
        if (request()->isGet()) {
            $id = input('get.id');
            $Tree = new CategoryTree();
            $CategoryTree = CategoryModel::getCategory();
            //不允许移动到的分类id数组
            $category_noid = $Tree->getcid_arr($CategoryTree, $id);
            $category_noid[] = $id;
            return $this->fetch('', [
                'CategoryData' => CategoryModel::getCategoryById($id),
                'CategoryTree' => $CategoryTree,
                'CategoryNoid' => $category_noid
            ]);
        }
    }

    /**
     * @return FailMessage|SuccessMessage
     */
    public function SaveCategory()
    {
        if (request()->isPost()) {
            $category = new CategoryModel();
            $data = input('param.');
            try {
                $category->saveCategory($data);
                return new SuccessMessage();
            } catch (Exception $e) {
                return new FailMessage();
            }
        }else{
            $this->redirect('category/index');
        }
    }

    public function destoryCategory()
    {
        if (request()->isPost()) {
            $category = new CategoryModel();
            $data = input('param.id/a');
            Db::startTrans();
            try {
                $category->delCategory($data);
                Db::commit();   // 提交事务
                return new SuccessMessage();
            } catch (\Exception $e) {
                Db::rollback();  // 回滚事务
                return new FailMessage();
//                return $category->getLastSql();
            }
        }
    }
    // 一级分类分页返回Json
//    public function getCategory()
//    {
//        $limit = $this->request->param('limit');
//        $page = $this->request->param('page');
//        $begin = ($page - 1) * $limit;
//        // 获取总条数
//        $list = model('Category')->getCategory();
//        $count = count($list);
//        /* 获取所有一级分类 */
//        $list = Db::table('hanfu_category')->order('listorder asc')->limit($begin, $limit)->where('status', '<>', -1)->select();
//
//        //获取每页显示的条数
//        foreach ($list as $k => $v) {
//            $cat = Db::name('category')->where('id', $v['id'])->find();
//            $list[$k]['name'] = $cat['name'];
//            $list[$k]['status'] = $cat['status'];
//            $list[$k]['isgender'] = $cat['isgender'];
//            $list[$k]['listorder'] = $cat['listorder'];
//            $list[$k]['create_time'] = $cat['create_time'];
//            $list[$k]['update_time'] = $cat['update_time'];
//        }
//        $data['code'] = 0;
//        $data['msg'] = "";
//        $data['count'] = $count;
//        $data['data'] = $list;
//        //有的需要json处理
//        return json($data);
//    }

    // 获取二级分类
//    public function getCategorySubclass()
//    {
//        $id = input('param.id');
//        if ($id) {
//            $category_id = $id;
//        } else {
//            $category_id = 0;
//        }
////        echo $category_id;exit;
//        $limit = $this->request->param('limit');
//        $page = $this->request->param('page');
//        $begin = ($page - 1) * $limit;
//
//        // 获取总条数
//        $list = model('CategorySubclass')->getCategorySubclass($category_id);
//        $count = count($list);
////        echo $count;exit();
//        /* 获取所有二级分类 */
//        $list = model('CategorySubclass')->getCategorySubclass($category_id, $begin, $limit);
//
////        return json($list);exit();
//        // 获取每页显示的条数
//        foreach ($list as $k => $v) {
//            $map['id'] = $v['id'];
//            $map['status'] = ['<>', -1];
//            $map['category_id'] = '';
//            if ($category_id != 0) {
//                $map['category_id'] = $category_id;
//            }else{
//                unset($map['category_id']);
//            }
//            $cat = Db::name('CategorySubclass')->where($map)->find();
//            $list[$k]['name'] = $cat['name'];
//            $list[$k]['status'] = $cat['status'];
//            $list[$k]['category_id'] = $cat['category_id'];
//            $list[$k]['listorder'] = $cat['listorder'];
//            $list[$k]['create_time'] = $cat['create_time'];
//            $list[$k]['update_time'] = $cat['update_time'];
//        }
//
//        $data['code'] = 0;
//        $data['msg'] = "";
//        $data['count'] = $count;
//        $data['data'] = $list;
//        return json($data);
//    }

    // 二级分类categorySubclass增加分类
//    public function addCategorySubclass()
//    {
//        if (request()->isPost()) {
//            $data = input('post.');
//            if ($data) {
//                try {
//                    $res = model('CategorySubclass')->addCategorySubclass($data);
//                } catch (\Exception $e) {
//                    return '910';
////                    $this->error($e->getMessage());exit;
//                }
//                if ($res) {
//                    return '101';
//                }
//            }
//        } else {
//            $category = model('Category')->getCategory();
//            return $this->fetch('', [
//                'category' => $category,
//            ]);
//        }
//    }

}
