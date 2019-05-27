<?php
/**
 * Created by PhpStorm.
 * User: Hsii
 * Date: 2018/10/14
 * Time: 17:53 下午
 */
namespace app\admin\controller;
use think\Controller;

class Upload extends Controller
{
    public function uploadimg()
    {
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('file');
        // 图片压缩
        $fd = new \ImgManage();
        $fd->ImageResize($file,'','','');
        // 移动到框架应用根目录/public/uploads/ 目录下
        $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
//        $info = $file->move('../public/uploads');
        if ($info) {
            // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpgs
            $path = 'uploads/'.$info->getSaveName();
            // 成功上传后 返回上传信息
            return json(array('state' => 1, 'path' => $path));
        } else {
            // 上传失败返回错误信息
            return json(array('state' => 0, 'errmsg' => '上传失败'));
        }
    }
}