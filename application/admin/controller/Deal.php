<?php
/**
 * Created by PhpStorm.
 * User: Hsii
 * Date: 2018/11/25
 * Time: 22:41 下午
 */

namespace app\admin\controller;

use think\Db;

class Deal extends Base
{
    public function index()
    {
        // 获取所有一级分类
        $categoryId = $subclass = $subclassDeal = array();
        $subclass = '';
        $category = model('Category')->field('id')->where('status', 1)->order('listorder asc')->select();
        // 取出category中所有id值，存入数组$categoryId
        foreach ($category as $value) {
            $categoryId[] = $value->id;
        }
        // 循环$categoryId数据长度
        // 一级类目所有值按id ASC排列存为数组，获取数组长度，-1即为数组最后一位下标，获得数组最后一位下标值，即为一级类目最大的id
        for ($c = 1; $c <= $categoryId[count($categoryId) - 1]; $c++) {
            $map['category_id'] = $c;
            $map['status'] = 1;
            // 取出CategorySubclass中所有值
            $subclass[$c] = model('CategorySubclass')->field('id,name,category_id')->where($map)->order('category_id asc')->select();
            if (empty($subclass[$c])) {
                $subclass[$c]['id'] = $c;
                $subclass[$c]['name'] = '';
            }
        }

        return $this->fetch('', [
            // $categoryId,所有category id值
            'categoryId' => $categoryId,
            // 所有的sublcass结果,id、name、category_id
            'subclass' => $subclass
        ]);
    }

    // 添加商品属性
    public function addDeal()
    {
        if (request()->isPost()) {
            $data = input('param.');
            if ($data['name'] && $data['category_subclass_id'] && $data['status'] && $data['orgin_price'] && $data['total_count'] && $data['thumbnail_attr'] && $data['details_attr']) {
                // category_subclass_id{1,1},.前为一级类目,.后为二级类目
                $category_id = explode(",", $data['category_subclass_id']);    // 一级类目id
                // 生成唯一编号,商品编号生成算法{1字节，一级类目序列号}{2字节，二级类目序列号}{14字节，年月日时分秒}{4字节，毫秒}{5字节,自增序列号}
                $getLastInsID = model('Deal')->getLastInsID() + 1;
                $data['code'] = $category_id[0] . $category_id[1] . date('Ymdhis') . explode('.', microtime(true))[1] . $getLastInsID;
                try {
                    // 更新Deal数据
                    $res = model('Deal')->addDeal($data);   // 返回主键值
                    $res_id = model('Deal')->getLastInsID();
                } catch (\Exception $e) {
//                    $this->error($e->getMessage());
                    return '910';
                }
                // deal插入成功了
                $ThumbnailId = array();
                $DetailsId = array();
                // 缩略图,返回主键ID
                $thumbnailAttr = explode(',', $data['thumbnail_attr']);
                foreach ($thumbnailAttr as $th_value) {
                    // 插入数据库deal_attr_thumbnail,$ThumbnailId数组存入主键id
                    $sql = "INSERT INTO hanfu_deal_attr_thumbnail (deal_id,deal_attr_thumbnail) VALUES ('" .$res_id. "','" . addslashes($th_value) . "')";
                    Db::execute($sql);
                    $ThumbnailId[] = Db::name('DealAttrThumbnail')->getLastInsID();
                }
                // 细节图,返回主键ID
                $detailsAttr = explode(',', $data['details_attr']);
                foreach ($detailsAttr as $de_value) {
                    // 插入数据库hanfu_deal_attr_details,$DetailsId数组存入主键id
                    $sql = "INSERT INTO hanfu_deal_attr_details (deal_id,deal_attr_details) VALUES ('" .$res_id. "','" . addslashes($de_value) . "')";
                    Db::execute($sql);
                    $DetailsId[] = Db::name('DealAttrDetails')->getLastInsID();
                }
                // 插入成功了
                // 主键id
                $deal_attr_thumbnailId = implode(",", $ThumbnailId);
                $deal_attr_detailsId = implode(",", $DetailsId);
                // 进行数据处理
                if ($res) {
                    // deal_attr_size_id
                    $deal_attr['deal_attr_size_id'] = $data['deal_attr_size_id'];
                    // deal_attr, deal_id => deal主键id
                    $deal_attr['deal_id'] = $res_id;
                    // deal_attr thumbnail,即deal_attr_thumbnail主键id
                    $deal_attr['thumbnail'] = $deal_attr_thumbnailId;
                    // deal_attr details,即deal_attr_details主键id
                    $deal_attr['details'] = $deal_attr_detailsId;
                    try {
                        $DeatAtrr_res = model('DealAttr')->addDalAttr($deal_attr);
                    } catch (\Exception $e) {
                        $this->error($e->getMessage());
                    }
                }
                if ($res && $DeatAtrr_res) {
                    return '201';
                }
            } else {
                // 表单内容缺失
                return '205';
            }
        } else {
            // 获取所有一级分类
            $getCategory = model('Category')->getCategory();
            // 获取所有二级分类
            $getCategorySubclass = model('CategorySubclass')->getCategorySubclass();
            return $this->fetch('', [
                'getCategory' => $getCategory,
                'getCategorySubclass' => $getCategorySubclass
            ]);
        }
    }
    // 商品列表分页返回Json
    public function getDeal()
    {
        $id = input('param.id');
        if ($id) {
            $deal_subclass_id = $id; //获取分类id
        } else {
            $deal_subclass_id = 0;
        }
        $limit = $this->request->param('limit');
        $page = $this->request->param('page');
        $begin = ($page - 1) * $limit;
        // 获取总条数
        $list = model('Deal')->getDeal($deal_subclass_id);
        $count = count($list);
        /* 获取所有商品 */
        $list = model('Deal')->getDeal($deal_subclass_id, $begin, $limit);
        // 获取所有商品属性
        foreach ($list as $vl){
            $details[] = model('DealAttrDetails')->getDalAttrDetails($vl->id);
            $thumbnail[] = model('DealAttrThumbnail')->getDalAttrThumbnail($vl->id);
        }
//        foreach ($details as $key => $value){
//            foreach ($value as $k => $v){
//               $detailsId[] = $v->id;
//               $detailsDealId[] = $v->deal_id;
//               $detailsAttr[] = $v->deal_attr_thumbnail;
//            }
//        }
        foreach ($thumbnail as $key => $value){
            foreach ($value as $k => $v){
                $thumbnailId[] = $v->id;
                $thumbnailDealId[] = $v->deal_id;
                $thumbnailAttr[] = array($v->deal_id => $v->deal_attr_thumbnail);
            }
        }
        //获取每页显示的条数
        foreach ($list as $k => $v) {
            $map['id'] = $v['id'];
            $map['status'] = ['<>', -1];
            $map['category_subclass_id'] = '';
            if ($deal_subclass_id != 0) {
                $map['category_subclass_id'] = $deal_subclass_id;
            }else{
                unset($map['category_subclass_id']);
            }
            $cat = Db::name('Deal')->where($map)->find();
            $list[$k]['id'] = $cat['id'];
            $list[$k]['name'] = $cat['name'];
            foreach ($thumbnailAttr as $value){
                foreach ($value as $id => $img){
                    if($cat['id'] == $id){
                        $list[$k]['thumbnail'] = $img;
                    }
                }
            }
            $list[$k]['status'] = $cat['status'];
            $res = model('CategorySubclass')->where('id',explode(',',$cat['category_subclass_id'])[1])->find()->name;
            $list[$k]['category_subclass_id'] = $res;
            $list[$k]['orgin_price'] = $cat['orgin_price'];
            $list[$k]['total_count'] = $cat['total_count'];
        }
        $data['code'] = 0;
        $data['msg'] = "";
        $data['count'] = $count;
        $data['data'] = $list;
        //有的需要json处理
        return json($data);
    }
    public function delDeal()
    {
        if (request()->isPost()) {
            $id = input('post.id');
            if ($id) {
                try {
                    $res = model('Deal')->delDeal($id);
                } catch (\Exception $e) {
                    return '910';
                }
                if ($res) {
                    return '203';
                }
            } else {
                return '204';
            }
        }
    }

    public function updateDeal()
    {
        if (request()->isPost()) {
            $data = input('param.');
            if ($data) {
                try {
                    $res = model('Deal')->updateDeal($data);
                } catch (\Exception $e) {
                    $this->error($e->getMessage());
//                    return '910';
                }
                if ($res) {
                    return '202';
                }
            } else {
                return '911';
            }
        }else{
            return '911';
        }
    }
}