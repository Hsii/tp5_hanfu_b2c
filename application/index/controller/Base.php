<?php
namespace app\index\controller;
use app\common\model\Setting;
use app\common\model\Menu as MenuModel;
use think\Controller;
use app\common\model\User as UserModel;
use app\common\model\Category as CategoryModel;
use app\common\model\Product as ProductModel;
use think\Session;
use think\View;
use think\Request;

class Base extends Controller
{
    public $user = '';
    public function _initialize()
    {
        //检测
//        if (!$this->user_checkguest()) pe_jsonshow(array('result'=>false, 'show'=>'请先登录'));
        //检测游客购买
        View::share('user_checkguest',$this->user_checkguest());
        // 用户数据
        View::share('User',$this->getLoginUser());
//        $this->assign('user_email',$this->getLoginEmail());
//        var_dump($this->getLoginUser());
//        print_r(input('param.city'));
        // 导航数据
        $menu = MenuModel::getMenu();
        $this->assign('menu',$menu);
        // 分类数据
        $this->assign('NormalCategory',$this->getNoramlCategory());
        // 推荐数据->index.html
        $this->assign('products',ProductModel::getNoramlCommendProducts());
        // 商品总量-> detail.html
        $this->assign('countProducts',count(ProductModel::getNoramlProductsByCategoryId(0)));
        // 购物车内物品
        $this->assign('cartNum',$this->getCart());
        // 公共数据,获取控制器名称
        $this->assign('controller',strtolower(request()->controller()));
        // 数据为空时
        $this->assign('empty','<div class="nodata">还没有内容呢 ╮(╯▽╰)╭</div>');
        // 网站设置数据
        $this->assign('info',$this->getWebSetting());

        // 测试
    }
    public function getLoginUser()
    {
        if(!$this->user)
        {
            $data['id'] = Session::get('User.user_id');
            $User = new UserModel();
            $this->user = $User->where($data)->find();
        }
        return $this->user;
    }
    // 购物车数量统计
    public function getCart()
    {
        $num = 0;
        $cart = \app\common\model\Cart::getCart($this->getLoginUser()->id);
        foreach ($cart as $k => $v){
            $num += $v['product_num'];
        }
        return $num;
    }
    /**
     * 获取网站设置数据
     * @return array
     */
    public function getWebSetting()
    {
        $settingData = Setting::getSeeting();
        $info = array();
        foreach ($settingData as $key => $value){
            $info[$value['setting_key']]['setting_value'] = $value['setting_value'];
        }
        return $info;
    }
    //检测游客购买
    public function user_checkguest() {
        $info = $this->getWebSetting();
        $user = $this->getLoginUser();
        if ($user or $info['web_guestbuy']['setting_value']) return true;
        return false;
    }
    /**
     * 首页推荐二级分类数据
     */
    public function getNoramlCategory()
    {
        $cat = CategoryModel::getNoramlCategory();
        foreach ($cat as $k => $v) {
            if ($v['pid'] == 0) {
                $dosql[] = $v;
            }
        }

        foreach($dosql as $k=>$v){
            $SecondaryCategory = CategoryModel::getCategoryById($v['id'],'pid');
            if($SecondaryCategory){
                $dosql[$k]['list'] = $SecondaryCategory;
            }
        }
        return $dosql;
    }
}
