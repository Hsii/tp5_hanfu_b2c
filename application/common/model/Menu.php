<?php
/**
 * Created by PhpStorm.
 * User: Hsii
 * Date: 2019/01/25
 * Time: 13:38 下午
 */


namespace app\common\model;

use think\Model;

class Menu extends Model
{
    // 关闭自动写入时间戳
    protected $autoWriteTimestamp = false;

    public static function getMenu($id = '')
    {
        if ($id) {
            return self::order('id')->find($id);
        } else {
            return self::order('id')->order('menu_order ASC')->select();
        }
    }
    public function saveMenu($data)
    {
        if (is_array($data) && !is_array($data['menu_order'])) {
            return $this->allowField(true)->save($data['info'], ['id' => $data['id']]);
        }else if ($data['menu_order'] && is_array($data['menu_order'])) {
            // 更新排序操作
            $arr = array();
            $i = 0;
            foreach ($data['menu_order'] as $k => $v) {
                $arr[$i]['id'] = $k;
                $arr[$i]['menu_order'] = $v;
                $i++;
            }
            return $this->saveAll($arr);
        }
    }
}