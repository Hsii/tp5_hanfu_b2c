<?php
/**
 * Created by PhpStorm.
 * User: Hsii
 * Date: 2019/01/18
 * Time: 17:03 下午
 */


namespace app\admin\controller;

use app\admin\validate\FailMessage;
use app\admin\validate\SuccessMessage;
use app\common\model\Image;
use app\common\model\Prodata;
use app\common\model\Product as ProductModel;
use app\common\model\Category as CategoryModel;
use app\common\model\Rule as RuleModel;
use app\common\model\Prodata as ProdataModel;
use think\Db;
use Library\alphaID;
use Think\Exception;

class Product extends Base
{
    public function index()
    {
        $categoryData = CategoryModel::getCategory();
        $count = ProductModel::getProductCount();
        $productData = ProductModel::getProductsByState();
        return $this->fetch('', [
            'productData' => $productData,
            'categoryData' => $categoryData,
            'count' => $count,
            'ROOT_PATH' => ROOT_PATH
        ]);

    }

    // 添加商品渲染
    public function product_add()
    {
        $categoryData = CategoryModel::getCategory();
        $ruleData = RuleModel::getRuleNature();
        return $this->fetch('', [
            'categoryData' => $categoryData,
            'ruleData' => $ruleData,
        ]);
    }

    // 修改商品渲染
    public function product_edit()
    {
        if (request()->isGet()) {
            $id = input('param.id');
            $productData = ProductModel::getProductDetail($id);
            $productImg = getProductImg($productData);
            $categoryData = CategoryModel::getCategory();
            $ruleData = RuleModel::getRuleNature();
            $proData = Prodata::getProDatasById($id);
            // 处理product_rule字段
            foreach ($productData as $k => $val) {
                // 字符串反序列化成数组
                $productData[$k]['product_rule'] = unserialize($val['product_rule']);
            }
            foreach ($proData as $k => $val) {
                $proData[$k]['product_rule_data'] = unserialize($val['product_rule_data']);
                $proData[$k]['product_rule_name_arr'] = explode(',', $proData[$k]['product_rule_name']);
            }
            // 判断proData
            return $this->fetch('', [
                'productData' => $productData,
                'productImg' => $productImg,
                'categoryData' => $categoryData,
                'ruleData' => $ruleData,
                'proData' => $proData,
            ]);
        } else {
            $this->redirect('product/index');
        }
    }

    // 修改商品数据处理
    public function editProduct()
    {
        if (request()->isPost()) {
            $data = input('param.');
//            var_dump($data);
            $productData['product_name'] = $data['info']['product_name'];
            $productData['product_wlmoney'] = ($data['info']['product_wlmoney']);
            $productData['product_point'] = $data['info']['product_point'];
            $productData['category_id'] = $data['info']['category_id'];
            // 商品规格数据集处理
            $productData['product_rule'] = $prodata = array();
            foreach ($data['rule_id'] as $k => $v) {
                $productData['product_rule'][$k]['id'] = $v;
            }
            foreach ($data['rule_name'] as $k => $v) {
                $productData['product_rule'][$k]['name'] = $v;
            }
            // 商品规格集数据
            for ($i = 0; $i < count($data['ruleDataNatureid']); $i++) {
                // 数据集product_rule_id字段
                $prodata[$i]['product_rule_id'] = $data['ruleDataNatureid'][$i];
                // 数据集product_rule_name字段
                $prodata[$i]['product_rule_name'] = $data['ruleDataNatureName'][$i];
                // 数据集product_rule_data字段
                for ($x = 0; $x < count($data['rule_name']); $x++) {
                    $prodata[$i]['product_rule_data'][$x]['name'] = $data['rule_name'][$x];
                    $prodata[$i]['product_rule_data'][$x]['value'] = explode(',', $data['ruleDataNatureName'][$i])[$x];
                }
                $prodata[$i]['product_rule_data'] = serialize($prodata[$i]['product_rule_data']);
                // 数据集product_money字段
                $prodata[$i]['product_money'] = $data['product_money'][$i];
                // 数据集product_num字段
                $prodata[$i]['product_num'] = $data['product_num'][$i];
                // 数据集product_order字段
                $prodata[$i]['product_order'] = $i + 1;
            }
//                // 序列化成字符串
            $productData['product_rule'] = serialize($productData['product_rule']);
//                // JSON编码数组成字符串
//                $productData['product_rule'] = json_encode($productData['product_rule']);
            if (empty($prodata)) {
                $productData['product_money'] = $data['info']['product_money'];
                $productData['product_num'] = $data['info']['product_num'];
            } else {
                $productData['product_money'] = $this->handleMoney($data['product_money']);
                $productData['product_num'] = $this->handleNum($data['product_num']);
            }

//            var_dump($productData);
//            var_dump($prodata);
//            // JSON解码成数组
////            var_dump(json_decode($productData['product_rule'], true));
//            // 反序列化成数组
////            var_dump(unserialize($productData['product_rule']));
//            var_dump($productData);
            Db::startTrans();
            try {
                $product = new ProductModel();
                // 更新Product表数据
                $product->saveProductsById($productData, $data['info']['id']);
                $product = ProductModel::get($data['info']['id']);
                // 更新商品规格表prodata数据
                $product->prodata()->delete();
                $product->prodata()->saveAll($prodata);
                // 更新img表数据
//                $product->imgs()->delete();
                $IMG = new Image();
                foreach ($data['product_album_image'] as $k => $v) {
                    $IMG->saveImage('',$v);
                }
                Db::commit();   // 提交事务
                $this->handleProductLogoImg($data['product_logo_image']);
                $this->handleProductAlbumImg($data['product_album_image']);
                $this->redirect('product/index');
            } catch (Exception $e) {
                Db::rollback();  // 回滚事务
//                return Db::getLastSql();
                return $e;
//                $this->redirect('product/index');
            }
        }
    }

    /**
     * 添加商品数据处理
     */
    public function addProduct()
    {
        if (request()->isPost()) {
            $data = input('param.');
            $product = new ProductModel();
//            var_dump($data);
            $productData['product_name'] = $data['info']['product_name'];
            $productData['product_mark'] = $this->getProductNumber($data['info']['category_id']);
            if (empty($data['product_logo_image'])) {
                unset($productData['product_logo']);
            } else {
//                $productData['product_logo'] = $data['product_logo_image'];
                $productData['product_logo'] = implode('',$data['product_logo_image']);
            }

            $productData['product_wlmoney'] = ($data['info']['product_wlmoney']);
            $productData['product_point'] = $data['info']['product_point'];
            $productData['category_id'] = $data['info']['category_id'];
            $productData['insert_time'] = time();
            if (empty($data['info']['product_money']) && empty($data['info']['product_num'])) {
                $productData['product_rule'] = $prodata = array();
                foreach ($data['rule_id'] as $k => $v) {
                    $productData['product_rule'][$k]['id'] = $v;
                }
                foreach ($data['rule_name'] as $k => $v) {
                    $productData['product_rule'][$k]['name'] = $v;
                }
                // 商品规格集数据
                for ($i = 0; $i < count($data['ruleDataNatureid']); $i++) {
                    // 数据集product_rule_id字段
                    $prodata[$i]['product_rule_id'] = $data['ruleDataNatureid'][$i];
                    // 数据集product_rule_name字段
                    $prodata[$i]['product_rule_name'] = $data['ruleDataNatureName'][$i];
                    // 数据集product_rule_data字段
                    for ($x = 0; $x < count($data['rule_name']); $x++) {
                        $prodata[$i]['product_rule_data'][$x]['name'] = $data['rule_name'][$x];
                        $prodata[$i]['product_rule_data'][$x]['value'] = explode(',', $data['ruleDataNatureName'][$i])[$x];
                    }
                    $prodata[$i]['product_rule_data'] = serialize($prodata[$i]['product_rule_data']);
                    // 数据集product_money字段
                    $prodata[$i]['product_money'] = $data['product_money'][$i];
                    // 数据集product_num字段
                    $prodata[$i]['product_num'] = $data['product_num'][$i];
                    // 数据集product_order字段
                    $prodata[$i]['product_order'] = $i + 1;
                }
//                // 序列化成字符串
                $productData['product_rule'] = serialize($productData['product_rule']);
//                // JSON编码数组成字符串
//                $productData['product_rule'] = json_encode($productData['product_rule']);
                $productData['product_money'] = $this->handleMoney($data['product_money']);
                $productData['product_num'] = $this->handleNum($data['product_num']);
//
            } else {
                $productData['product_money'] = $data['info']['product_money'];
                $productData['product_num'] = $data['info']['product_num'];
                $productData['product_rule'] = '';
                $prodata = '';
            }
            // JSON解码成数组
//            var_dump(json_decode($productData['product_rule'], true));
            // 反序列化成数组
//            var_dump(unserialize($productData['product_rule']));
            try {
                for ($i=0;$i<count($data['product_album_image']);$i++){
                    $ws['img_url'] = $data['product_album_image'][$i];
                    $img_id[] = Db::table('hanfu_image')->insertGetId($ws);
                }
                $productData['image_id'] = implode(',',$img_id);
//                var_dump($productData);
                $product->save($productData);
                $product->prodata()->saveAll($prodata);
                $this->handleProductLogoImg($productData['product_logo']);
                $this->handleProductAlbumImg($data['product_album_image']);
                $this->redirect('product/index');
            } catch (Exception $e) {
                $this->redirect('product/product_add');
            }
        }
    }
    // 批量修改商品上下架
    public function saveProductState()
    {
        $data = input('param.');
        $product = new ProductModel();
        Db::startTrans();
        try {
            $product->saveProductState($data);
            Db::commit();   // 提交事务
            $this->redirect('product/index');
        } catch (Exception $e) {
            Db::rollback();  // 回滚事务
//            return $product->getLastSql();
            $this->redirect('product/index');
        }
    }

    // 批量修改商品推荐
    public function saveProductIsTuijian()
    {
        $data = input('param.');
        $product = new ProductModel();
        Db::startTrans();
        try {
            $product->saveProductIsTuijianById($data);
            Db::commit();   // 提交事务
            return new SuccessMessage();
        } catch (Exception $e) {
            Db::rollback();  // 回滚事务
            return new FailMessage();
        }
    }

    // 批量修改商品排序
    public function saveProductOrder()
    {
        if (request()->isPost()) {
            $product = new ProductModel();
            $data = input('param.');
            Db::startTrans();
            try {
                $product->saveProductsOrder($data);
                Db::commit();   // 提交事务
                $this->redirect('product/index');
                return new SuccessMessage();
            } catch (Exception $e) {
                Db::rollback();  // 回滚事务
//                return $product->getLastSql();
                $this->redirect('product/index');
                return new FailMessage();
            }
        } else {
            $this->redirect('product/index');
        }

    }

    // 删除商品
    public function destoryProductById()
    {
        if (request()->isPost()) {
            $product = new ProductModel();
            $id = input('param.id');
            Db::startTrans();
            try {
                $product->delProductById($id);
                Db::commit();   // 提交事务
                return new SuccessMessage();
            } catch (\Exception $e) {
                Db::rollback();  // 回滚事务
                return new FailMessage();
//                return $product->getLastSql();
            }
        }
    }

    /**
     * 新增商品生成货号
     * 分类id转换的字母+年份(末位2位)+月份+time(最后2位)+Unix 时间戳(浮点数.后)
     */
    public function getProductNumber($category_id)
    {
        $alpha = new alphaID();
        $mark = $alpha->decimal2ABC($category_id) . substr(date('Y'), 2) . date('m') . substr(time(), 8, 2) . explode('.', microtime(true))[1];
        return $mark;
    }

    /**
     * 计算商品规格集商品库存总量
     * @param $data
     * @return string
     */
    public function handleNum($data)
    {
        $sum = '';
        if ($data) {
            foreach ($data as $value) {
                $sum += $value;
            }
            return $sum;
        }
    }

    /**
     * 计算商品规格集中最低的值
     * @param $data
     * @return mixed
     */
    public function handleMoney($data)
    {
        if ($data) {
            $min = $data[0];//默认情况下数组的第一个值是最小的
            for ($i = 0; $i < count($data); $i++) {//循环遍历数组的每一个值
                if ($min > $data[$i]) {//将第一个默认为最小的值和数组中的所有值比较，如果默认的最小值比其他的值大，那叫交换，最终遍历完后，$min中存储的就是数组中的最小的值
                    $min = $data[$i];
                }
            }
            return $min;
        }
    }

    /**
     * 将图片数组转换成字符串,以;分割
     * @param $data
     */
//    public function handleImg($data)
//    {
//        if (!empty($data)) {
//            $result = implode(";", $data);
//            return $result;
//        }
//        return $data;
//    }

    /**
     * 将获取到的图片一移动到public/upload/product/product_logo/商品编号/文件夹下
     * @param $product_mark
     * @param $data
     */
    public function handleProductLogoImg($data)
    {
        if (!empty($data)) {
            $__TEMP__ = ROOT_PATH . 'runtime/temp/uploads/';
            $__LOGO__ = ROOT_PATH . 'public/uploads/products/product_logo/';
            if (file_exists($__LOGO__)) {
                rename($__TEMP__ . $data, $__LOGO__ . '/' . $data);
            } else {
                mkdir($__LOGO__);
                rename($__TEMP__ . $data, $__LOGO__ . '/' . $data);
            }
        }

    }
    /**
     * 将获取到的图片一移动到public/upload/product/product_album/商品编号/文件夹下
     */
    public function handleProductAlbumImg($data)
    {
    if (!empty($data)) {
        $__TEMP__ = ROOT_PATH . 'runtime/temp/uploads/';
        $__LOGO__ = ROOT_PATH . 'public/uploads/products/product_album/';
        if (file_exists($__LOGO__)) {
            for ($i = 0; $i < count($data); $i++) {
                rename($__TEMP__ . $data[$i], $__LOGO__ . '/' . $data[$i]);
            }
        } else {
            mkdir($__LOGO__);
            for ($i = 0; $i < count($data); $i++) {
                rename($__TEMP__ . $data[$i], $__LOGO__ . '/' . $data[$i]);
            }
        }
    }
    }
}