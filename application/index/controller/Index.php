<?php
namespace app\index\controller;

use app\common\model\Banner as BannerModel;
use app\common\model\Image as ImageModel;

class Index extends Base
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
            'count' => count($banner)
        ]);
    }
}
