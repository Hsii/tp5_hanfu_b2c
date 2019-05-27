<?php
/**
 * Created by PhpStorm.
 * User: Hsii
 * Date: 2019/01/24
 * Time: 23:12 下午
 */
namespace app\admin\controller;
use app\admin\validate\SuccessRedirectUrl as RedirectUrl;
use app\common\model\Menu as MenuModel;
use app\common\model\Category as CategoryModel;
use app\lib\exception\SuccessMessage;
use think\Db;
use Think\Exception;

class Menu extends Base
{
    public function index()
    {
        $menu = MenuModel::getMenu();
        return $this->fetch('',[
            'menu' => $menu,
        ]);
    }
    public function menu_edit()
    {
        if (request()->isGet()) {
            $id = input('param.id');
            $menuById = MenuModel::getMenu($id);
            $category = CategoryModel::getCategory();
            return $this->fetch('',[
                'vo' => $menuById,
                'category' => $category
            ]);
        }elseif(request()->isPost()){
            $data = input('param.');
            if($data['info']['menu_type'] !== 'diy') $data['info']['menu_url'] = $data['info']['menu_type']; unset($data['info']['menu_type']);
            $menu = new MenuModel();
            Db::startTrans();
            try{
                $menu->saveMenu($data);
                Db::commit();   // 提交事务
                return '<script language="javascript">alert("修改成功");window.location.href="http://58.87.89.241//admin/menu"</script>';
            }catch (Exception $e){
                Db::rollback();  // 回滚事务
                $this->redirect('menu/index');
            }
        } else{
            $this->redirect('menu/index');
        }
    }

    public function SaveMenuOrder()
    {
        if (request()->isPost()) {
            $data = input('param.');
            $menu = new MenuModel();
            Db::startTrans();
            try {
                $menu->saveMenu($data);
                Db::commit();   // 提交事务
                $this->redirect('menu/index');
            } catch (Exception $e) {
                Db::rollback();  // 回滚事务
                return $e;
//                $this->redirect('menu/index');
            }
        }
    }
}