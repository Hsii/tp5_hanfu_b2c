<?php
/**
 * Created by PhpStorm.
 * User: Hsii
 * Date: 2019/01/25
 * Time: 11:30 上午
 */


namespace app\common\model;


use think\Model;

class Setting extends Model
{
    // 关闭自动写入时间戳
    protected $autoWriteTimestamp = false;

    public static function getSeeting()
    {
        return self::select();
    }

    public function saveSetting($data)
    {
        foreach ($data as $key => $value) {
            $success = $this->allowField(true)->save(['setting_value' => $value], ['setting_key' => $key]);
        }
        if ($success) {
            return true;
        } else {
            return false;
        }
    }
}