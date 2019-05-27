<?php
/**
 * Created by PhpStorm.
 * User: Hsii
 * Date: 2018/10/14
 * Time: 17:53 下午
 */
namespace app\bis\controller;

class Location extends Base
{
    private $obj;
    public function _initialize()
    {
        $this->obj = model('BisLocation');
    }
    public function index()
    {
        $location = $this->obj->getNormalLocationByBisId();
//        print_r($location);
        return $this->fetch('',[
            'location' => $location,
        ]);
    }
    public function add()
    {
        if(request()->isPost())
        {
             $data = input('post.');
             // session的反序列化处理
            $bisId = (array)unserialize($this->getLoginUser())->bis_id;
            // 门店入库
            //总店相关信息检验/入库
            //获取经纬度
            $lnglat = \Map::getLngLat($data['address']);
            if (empty($lnglat) || $lnglat['status'] != 0 || $lnglat['result']['precise'] != 1)
            {
                $this->error('无法获取数据或匹配的数据不精确');
            }
            $data['cat'] = '';
            if(!empty($data['se_category_id']))
            {
                $data['cat'] = implode('|',$data['se_category_id']);
            }

            $locationData = [
                'bis_id' => $bisId,
                'name' => $data['name'],
                'tel' => $data['tel'],
                'contact' => $data['contact'],
                'category_id' => $data['category_id'],
                'category_path' => $data['category_id'].','.$data['cat'],
                'city_id' => $data['city_id'],
                'city_path' => empty($data['se_city_id'])?$data['city_id']:$data['city_id'].','.$data['se_city_id'],
                'address' => $data['address'],
                'open_time' => $data['open_time'],
                'content' => empty($data['content'])?'':$data['content'],
                'is_main' => 0,
                'xpoint' => empty($lnglat['result']['location']['lng'])?'':$lnglat['result']['location']['lng'],
                'ypoint' => empty($lnglat['result']['location']['lat'])?'':$lnglat['result']['location']['lat'],
            ];
            $locationId = model('BisLocation')->add($locationData);
            if($locationId)
            {
                $this->success('门店申请成功');
            }else{
                $this->error('申请失败');
            }
        }else{
            //获取一级城市数据
            $citys = model('City')->getNormalCitysByParentId();
            //获取一级栏目数据
            $categorys = model('Category')->getNormalCategoryByParentId();
            return $this->fetch('',[
                'citys' => $citys,
                'categorys' => $categorys,
            ]);
        }
    }
}