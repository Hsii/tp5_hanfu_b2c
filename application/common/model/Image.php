<?php
/**
 * Created by PhpStorm.
 * User: Hsii
 * Date: 2019/01/07
 * Time: 19:17 下午
 */
namespace app\common\model;
use think\Model;

class Image extends BaseModel
{
    protected $hidden = ['id','create_time','update_time', 'delete_time'];

    public static function getImageById($id)
    {
        $result = self::where('id','=',$id)->select();
        return $result;
    }

    public function saveImage($id='',$data)
    {
        if($id){
            $success = $this->save(['img_url' => $data],['id' => $id]);
        }else{
            $success = $this->save(['img_url' => $data]);
        }
        return $success;
    }
}