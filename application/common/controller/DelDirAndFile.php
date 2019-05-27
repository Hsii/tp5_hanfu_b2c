<?php
/**
 * Created by PhpStorm.
 * User: Hsii
 * Date: 2019/01/21
 * Time: 16:59 下午
 */
namespace app\common\controller;

use think\Controller;

class DelDirAndFile extends Controller
{
    function deleFile($path)
    {
        if (!$handle = @opendir($path)) {
            return false;
        }
        while (false !== ($file = readdir($handle))) {
            if ($file !== "." && $file !== "..") {       //排除当前目录与父级目录
                $file = $path . '/' . $file;
                if (is_dir($file)) {
                    $this->deleFile($file);
                } else {
                    @unlink($file);
                }
            }

        }
        @rmdir($path);
    }
}