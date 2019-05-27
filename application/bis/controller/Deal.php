<?php
/**
 * Created by PhpStorm.
 * User: Hsii
 * Date: 2018/11/05
 * Time: 20:12 下午
 */
namespace app\bis\controller;

class Deal  extends Base
{
    private $obj;
    public function _initialize()
    {
        $this->obj = model('Deal');
    }

    public function index()
    {
        $deal = $this->obj->getApplyDeals();
        return $this->fetch('',[
            'deal' => $deal,
        ]);
    }

    public function add ()
    {
        $bisId = (array)unserialize($this->getLoginUser())->bis_id;
        if(request()->isPost())
        {
            $data = input('post.','','htmlentities');
            $location = model('BisLocation')->get($data['location_ids'][0]);
            $deals = [
                'bis_id' => $bisId[0],
                'name' => $data['name'],
                'image' => $data['image'],
                'category_id' => $data['category_id'],
                'se_category_id' => empty($data['category_id'])?'':implode(',',$data['se_category_id']),
                'city_id' => $data['city_id'],
                'location_ids' => empty($data['location_ids'])?'':implode(',',$data['location_ids']),
                'start_time' => strtotime($data['start_time']),
                'end_time' => strtotime($data['end_time']),
                'total_count' => $data['total_count'],
                'orgin_price' => $data['orgin_price'],
                'current_price' => $data['current_price'],
                'coupons_begin_time' => strtotime($data['coupons_begin_time']),
                'coupons_end_time' => strtotime($data['coupons_end_time']),
                'description' => $data['description'],
                'notes' => $data['notes'],
                'bis_account_id' => $bisId[0],
                'xpoint' => $location->xpoint,
                'ypoint' => $location->ypoint,
            ];
            $id = model('Deal')->add($deals);
            if($id){
                $this->success('添加成功','deal/index');
            }else{
                $this->error('添加失败');
            }
        }else{
            //获取一级城市数据
            $citys = model('City')->getNormalCitysByParentId();
            //获取一级栏目数据
            $categorys = model('Category')->getNormalCategoryByParentId();
            return $this->fetch('',[
                'citys' => $citys,
                'categorys' => $categorys,
                'bislocations' => model('BisLocation')->getNormalLocationByBisId($bisId[0]),
            ]);
        }
    }

    public function status()
    {
        $data = input('param.');

        $validate = validate('Deal');
        if(!$validate->scene('status')->check($data))
        {
            $this->error($validate->getError());
        }
        $res = $this->obj->save(['status'=>$data['status']] , ['id'=>$data['id']]);

        if($res){
            $this->success('状态更新成功');
        }else{
            $this->error('状态更新失败');
        }
    }
}