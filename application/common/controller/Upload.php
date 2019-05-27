<?php
/**
 * Created by PhpStorm.
 * User: Hsii
 * Date: 2018/10/14
 * Time: 17:53 下午
 */
namespace app\common\controller;
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
//        $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
        // 上传到temp临时文件夹
        $info = $file->rule('uniqid')->move(ROOT_PATH . 'runtime/temp/' . DS . 'uploads',true,true);
        if ($info) {
            // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpgs
//            $path = $info->getRealPath();
//            $path = 'uploads/'.$info->getSaveName();
            $path = $info->getFilename();
            // 成功上传后 返回上传信息
            return json(array('code' => 1, 'path' => $path));
        } else {
            // 上传失败返回错误信息
            return json(array('code' => 0, 'errmsg' => '上传失败'));
        }
    }

    public function move_upload_file($data)
    {
        if($data){
            move_uploaded_file($data, ROOT_PATH . 'public' . DS . 'uploads/');
        }
    }
}