<?php

namespace app\common\model;

use think\Model;
use Library\CategoryTree;

class Category extends Model
{
    protected $hidden = ['status', 'create_time', 'delete_time', 'update_time'];

    /**
     * @return false|\PDOStatement|string|\think\Collection
     */
    public static function getNoramlCategory()
    {
        return self::where('status', 0)->order(['id' => 'asc', 'listorder' => 'asc'])->select();
    }

    /**
     * 获取所有分类,返回无限极分类树型图
     * @return array
     */
    public static function getCategory()
    {
        $Tree = new CategoryTree();
        $category = self::where('status', '=', 0)->order(['listorder' => 'asc', 'id' => 'asc'])->select();
        return $Tree->gettree($category);
    }
    /**
     * 获取id对应的分类信息
     * @param $id
     * @param string $pid
     * @return array|false|\PDOStatement|string|\think\Collection|Model
     */
    public static function getCategoryById($id, $pid = '')
    {
        if (((floor($id) - $id) != 0) || $id == 0) {
            exception('信息不合法', 90000);
        }
        // 查询一级分类下的二级分类
        if ($pid) {
            return self::where('status = 0 AND (id = ' . $id . ' OR  pid =' . $id . ')'
            )->select();
        } else {
            // 查询一级分类
            $where = [
                'id' => $id,
                'status' => 0
            ];
            return self::where($where)->find();
        }
    }

    /**
     * 分类信息的添加、修改
     * @param $data
     * @return false|int
     */
    /**
     * @param $data
     * @return array|false|int
     */
    public function saveCategory($data)
    {
        if (empty($data)) {
            exception('信息不合法', 90000);
        }
        if ($data['id']) {
            // 更新操作
            $where = [
                'id' => $data['id']
            ];
            return $this->allowField(true)->save($data, $where);
        } elseif ($data['category_order'] && is_array($data['category_order'])) {
            // 更新排序操作
            $arr = array();
            $i = 0;
            foreach ($data['category_order'] as $k => $v) {
                $arr[$i]['id'] = $k;
                $arr[$i]['listorder'] = $v;
                $i++;
            }
            return $this->saveAll($arr);
        } else {
            // 新增操作
            return $this->allowField(true)->save($data);
        }

    }

    /**
     * 删除分类信息
     * @param $id
     * @return false|int
     */
    public function delCategory($id)
    {
        //设置更新数据
        $data['status'] = -1;
        $data['delete_time'] = time();
        if (is_array($id)) {
            $where = 'id in(' . implode(',', $id) . ')';
        } else {
            $where = 'id=' . $id;
        }
        return $this->allowField(true)->save($data, $where);

    }

}