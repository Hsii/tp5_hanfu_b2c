<?php
/**
 * Created by PhpStorm.
 * User: Hsii
 * Date: 2018/10/21
 * Time: 20:58 下午
 */
namespace app\bis\controller;
use think\Controller;

class Register extends Controller
{
    public function index()
    {
        //获取一级城市数据
        $citys = model('City')->getNormalCitysByParentId();
        //获取一级栏目数据
        $categorys = model('Category')->getNormalCategoryByParentId();

        return $this->fetch('',[
            'citys' => $citys,
            'SecondaryCity' => $categorys,
        ]);
    }

    public function add()
    {
        if (!request()->isPost()) {
            $this->error('请求错误');
        }
        $data = input('post.','','htmlentities');
//        print_r($data['licence_logo']);
        $validata = validate('Bis');
        if(!$validata->scene('add')->check($data)) {
            $this->error($validata->getError());
        }
        //获取经纬度
        $lnglat = \Map::getLngLat($data['address']);
        if (empty($lnglat) || $lnglat['status'] != 0 || $lnglat['result']['precise'] != 1)
        {
            $this->error('无法获取数据或匹配的数据不精确');
        }
        $accountResult = Model('BisAccount')->get(['username'=>$data['username']]);
        if($accountResult)
        {
            $this->error('该用户存在');
        }
        //基本信息入库
        $bisData = [
            'name' => $data['name'],
            'city_id' => $data['city_id'],
            'city_path' => empty($data['se_city_id']) ? $data['city_id']:$data['city_id'].','.$data['se_city_id'],
            'logo' => $data['logo'],
            'licence_logo' => $data['licence_logo'],
            'description' => empty($data['description'])?'':$data['description'],
            'bank_info' => $data['bank_info'],
            'bank_name' => $data['bank_name'],
            'bank_user' => $data['bank_user'],
            'faren' => $data['faren'],
            'faren_tel' => $data['faren_tel'],
            'email' => $data['email'],
        ];
        $bisId = model('Bis')->add($bisData);
//        print_r($bisData['licence_logo']);
        //总店相关信息检验/入库
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
            'is_main' => 1,
            'xpoint' => empty($lnglat['result']['location']['lng'])?'':$lnglat['result']['location']['lng'],
            'ypoint' => empty($lnglat['result']['location']['lat'])?'':$lnglat['result']['location']['lat'],
        ];
        $locationId = model('BisLocation')->add($locationData);
        //账户相关信息
        //密码加盐字符串
        $data['code'] = mt_rand(100,1902849);
        $accountData = [
            'bis_id' => $bisId,
            'username' => $data['username'],
            'code' => $data['code'],
            'password' => md5($data['password'].$data['code']),
            'is_main' => 1,

        ];
        $accountId = model('BisAccount')->add($accountData);
        if(!$accountId)
        {
            $this->error('申请失败');
        }
        //发送邮件
        $url = request()->domain().url('bis/register/waiting',['id' => $bisId]);
        $title = '欢迎'.$data['faren'].'加入小杰瑞';
        $content = "大家好！我是小猪佩奇。这是我的弟弟，小公猪。这是我的妈妈，老母猪。这是我的爸爸，猪刚鬣！</br><a href='".$url."' target='_blank'>查看链接</a>";
        \phpmailer\Email::send($data['email'],$title,$content);
//        $this->success('申请成功','register/waiting',['id'=>$bisId]);
        $this->success('申请成功',url('register/waiting',['id'=>$bisId]));
    }

    public function waiting($id)
    {
        if(empty($id))
        {
            $this->error('error');
        }
        $detail = model('Bis')->get($id);
        return $this->fetch('',[
            'detail' => $detail,
        ]);
//        print_r($detail['status']);
    }
}
