<?php
/**
 * Created by PhpStorm.
 * User: Hsii
 * Date: 2019/01/07
 * Time: 15:51 下午
 */

namespace app\common\model;
class Banner extends BaseModel
{
    protected $hidden = ['create_time', 'update_time', 'delete_time'];

    // 获取Banner
    public static function getBanner($id = '')
    {
        if ($id) {
            return self::where('status <> -1 AND id = ' . $id)->select($id);
        } else {
            return self::select();
        }
    }

    // 获取前五条Banner
    public static function getBannerByIndex($limit = 5)
    {
//        $banner = self::where('status','<>',-1)->limit($limit)->order('listorder ASC')->find();
        $banner = self::with(['image'])->where('status', '<>', -1)->limit($limit)->order('listorder ASC')->select();
        return json($banner);
    }

    public function saveBanner($id = '', $data)
    {
        if ($id) {
            return $this->allowField(true)->save($data, ['id' => $id]);
        } else {
            // 新增操作
            return $this->allowField(true)->save($data);
        }
    }

    public function saveBannerStatus($data)
    {
        return $this->save(['status' => $data['state']], ['id' => $data['id']]);
    }

    public function saveBannerOrder($data)
    {
        if ($data['banner_order'] && is_array($data['banner_order'])) {
            // 更新排序操作
            $arr = array();
            $i = 0;
            foreach ($data['banner_order'] as $k => $v) {
                $arr[$i]['id'] = $k;
                $arr[$i]['listorder'] = $v;
                $i++;
            }
            return $this->saveAll($arr);
        }
    }

    public function delBannerById($id)
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