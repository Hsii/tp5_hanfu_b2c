<?php
/**
 * Created by PhpStorm.
 * User: Hsii
 * Date: 2019/01/07
 * Time: 15:45 下午
 */

namespace app\admin\controller;

use app\common\controller\DelDirAndFile;
use app\admin\validate\FailMessage;
use app\admin\validate\SuccessMessage;
use app\common\model\Banner as BannerModel;
use app\common\model\Image as ImageModel;
use think\Db;

class Banner extends Base
{
    public function index()
    {
        $img = new ImageModel();
        $banner = BannerModel::getBanner();
        foreach ($banner as $key => $value){
            $banner[$key]['image'] = $img->getImageById($value['img_id']);
        }
        return $this->fetch('', [
            'banner' => $banner,
            'count' => count($banner),
            'ROOT_PATH' => ROOT_PATH
        ]);
    }

    public function banner_add()
    {
        if (request()->isPost()) {
            $banner = new BannerModel();
            $img = new ImageModel();
            $data = input('param.');
            Db::startTrans();
            try {
                $img->saveImage('',$data['img']);
                $data['img_id'] = $img->getLastInsID();
                $banner->saveBanner('',$data);
                $id = $banner->getLastInsID();
                if ($id) {
                    Db::commit();   // 提交事务
                    $this->handleBannerImg($id, $data['img']);
                    return new SuccessMessage();
                } else {
                    return new FailMessage();
                }
            } catch (\Exception $e) {
                Db::rollback();  // 回滚事务
                return new FailMessage();
            }
        } else {
            return $this->fetch();
        }
    }

    public function banner_edit()
    {
        $img = new ImageModel();
        $banner = new BannerModel();
        if (request()->isPost()) {
            $data = input('param.');
            Db::startTrans();
            try {
                $result = $img->getImageById($data['img_id']);
                foreach ($result as $k => $v){
                    if($v['img_url'] == $data['img']){
                        unset($data['img']);
                    } else{
                        $img->saveImage($data['img_id'],$data['img']);
                    }
                }
                $banner->allowField(true)->saveBanner($data['id'],$data['info']);
                Db::commit();   // 提交事务
                $this->handleBannerImg($data['id'], $data['img']);
                return new SuccessMessage();
            } catch (\Exception $e) {
                Db::rollback();  // 回滚事务
//                return $banner->getLastSql();
                return new FailMessage();
            }
        } else if (request()->isGet()) {
            $id = input('param.id');
            if (!$id) {
                $this->redirect('banner/index');
            }
            $banner = BannerModel::getBanner($id);
            foreach ($banner as $key => $value){
                $banner[$key]['image'] = $img->getImageById($value['img_id']);
            }
            return $this->fetch('', [
                'banner' => $banner
            ]);
        }
    }

    public function saveBanner()
    {
        if (request()->isPost()) {
            $banner = new BannerModel();
            $data = input('param.');
            Db::startTrans();
            try {
                $banner->saveBannerStatus($data);
                Db::commit();   // 提交事务
                return new SuccessMessage();
            } catch (\Exception $e) {
                Db::rollback();  // 回滚事务
                return $banner->getLastSql();
//                return new FailMessage();
            }
        }
    }

    public function delBannerById()
    {
        if (request()->isPost()) {
            $banner = new BannerModel();
            $id = input('param.id/a');
            Db::startTrans();
            try {
                $banner->delBannerById($id);
                Db::commit();   // 提交事务
                return new SuccessMessage();
            } catch (\Exception $e) {
                Db::rollback();  // 回滚事务
                return new FailMessage();
            }
        }
    }

    public function saveBannerStatus()
    {
        $banner = new BannerModel();
        if (request()->isGet()) {
            $data = input('param.');
            Db::startTrans();
            try {
                $banner->saveBannerStatus($data);
                Db::commit();   // 提交事务
                $this->redirect('banner/index');
//                return new SuccessMessage();
            } catch (\Exception $e) {
                Db::rollback();  // 回滚事务
//                return $banner->getLastSql();
                $this->redirect('banner/index');
            }
        }
    }

    public function saveBannerOrder()
    {
        if (request()->isPost()) {
            $banner = new BannerModel();
            $data = input('param.');
            Db::startTrans();
            try {
                $banner->saveBannerOrder($data);
                Db::commit();   // 提交事务
                $this->redirect('banner/index');
            } catch (\Exception $e) {
                Db::rollback();  // 回滚事务
//                return $banner->getLastSql();
                $this->redirect('banner/index');
            }
        }
    }

    /**
     * 将获取到的图片一移动到public/upload/banners/banner_id/文件夹下
     * @param $banner_id
     * @param $data
     */
    public function handleBannerImg($banner_id, $data)
    {
        if (!empty($data)) {
            $delDirAndFile = new DelDirAndFile();
            $__TEMP__ = ROOT_PATH . 'runtime/temp/uploads/';
            $__LOGO__ = ROOT_PATH . 'public/uploads/banners/';
            if (file_exists($__LOGO__ . $banner_id)) {
                $delDirAndFile->deleFile($__LOGO__ . $banner_id);
                mkdir($__LOGO__ . $banner_id);
                rename($__TEMP__ . $data, $__LOGO__ . $banner_id . '/' . $data);
            } else {
                mkdir($__LOGO__ . $banner_id);
                rename($__TEMP__ . $data, $__LOGO__ . $banner_id . '/' . $data);
            }
        }
    }

}