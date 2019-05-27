<?php
/**
 * Created by PhpStorm.
 * User: Hsii
 * Date: 2019/01/18
 * Time: 17:03 下午
 */

namespace app\common\model;

use Home\Model\CategoryModel;

class Product extends BaseModel
{
    // 关联商品规格集表
    public function prodata()
    {
        return $this->hasMany('Prodata', 'product_id');
    }

    /**
     * 查询所有商品
     * @param string $state
     * @return false|\PDOStatement|string|\think\Collection
     */
    public static function getProductsByState()
    {
        // 下架商品
        $productData[1] = self::where('product_state', '=', 2)->select();
        // 缺货商品
        $productData[2] = self::where('product_num', '=', 0)->select();
        // 包邮商品
        $productData[3] = self::where('product_wlmoney', '=', 0)->select();
        // 推荐商品
        $productData[4] = self::where('product_istuijian', '=', 1)->select();
        // 出售中商品
        $productData[0] = self::where('product_state', '=', 1)->select();

        return $productData;
    }

    /**
     * 首页推荐商品
     * @param int $limit
     * @return false|\PDOStatement|string|\think\Collection
     */
    public static function getNoramlCommendProducts($limit = 12)
    {
        $data = [
            'product_state' => 1,
            'product_num' => ['>', 0],
            'product_istuijian' => 1,
        ];
        $order = [
            'id' => 'asc',
            'product_order' => 'asc'
        ];

        return self::where($data)
            ->order($order)
            ->limit($limit)
            ->select();
    }
    /**
     * 获取商品详情
     * @param $id
     * @return array|false|\PDOStatement|string|\think\Model
     */
    public static function getProductDetail($id)
    {
        $products = self::where('id',$id)->find();
        return $products;
    }
    /**
     * 查询分类id对应的商品
     * @param $id
     * @return false|\PDOStatement|string|\think\Collection
     */
    public static function getNoramlProductsByCategoryId($id, $order='', $map='')
    {
        if ($id == 0) {
            $where = 'product_state = 1';
        } else {
            $where = 'product_state = 1 AND category_id in (' . $id . ')';
        }
        return self::where($where)
            ->where($map)
            ->order($order)
            ->select();
    }

    public static function getProductCount()
    {
        $productData = self::getProductsByState();
        $num[0] = count($productData[0]);
        $num[1] = count($productData[1]);
        $num[2] = count($productData[2]);
        $num[3] = count($productData[3]);
        $num[4] = count($productData[4]);
        return $num;
    }

    /**
     * 更新数据
     * @param $data
     * @param $id
     * @return false|int
     */
    public function saveProductsById($data, $id)
    {
        if (is_numeric($id)) {
            return $this->allowField(true)->save($data, ['id' => $id]);
        }
    }

    /**
     * 商品排序的批量修改
     * @param $data
     * @return false|int
     */
    public function saveProductsOrder($data)
    {
        if (empty($data)) {
            exception('信息不合法', 90000);
        }
        if ($data['product_order'] && is_array($data['product_order'])) {
            // 更新排序操作
            $arr = array();
            $i = 0;
            foreach ($data['product_order'] as $k => $v) {
                $arr[$i]['id'] = $k;
                $arr[$i]['product_order'] = $v;
                $i++;
            }
            return $this->saveAll($arr);
        }

    }

    /**
     * 商品上下架更改
     * @param $data
     * @return false|int
     */
    public function saveProductState($data)
    {
        if (is_array($data['id'])) {
            $where = 'id in(' . implode(',', $data['id']) . ')';
        } else {
            $where = 'id=' . $data['id'];
        }
        return $this->allowField(true)->save(['product_state' => $data['state']], $where);
    }

    /**
     * 商品批量推荐
     * @param $id
     * @return false|int
     */
    public function saveProductIsTuijianById($data)
    {
        if (is_array($data['id'])) {
            $where = 'id in(' . implode(',', $data['id']) . ')';
        } else {
            $where = 'id=' . $data['id'];
        }
        return $this->allowField(true)->save(['product_istuijian' => $data['product_istuijian']], $where);
    }

    /**
     * 删除商品信息
     * @param $id
     * @return false|int
     */
    public function delProductById($id)
    {
        //设置更新数据
        $data['product_state'] = -1;
        $data['delete_time'] = time();
        if (is_array($id)) {
            $where = 'id in(' . implode(',', $id) . ')';
        } else {
            $where = 'id=' . $id;
        }
        return $this->allowField(true)->save($data, $where);

    }
}