<?php
/**
 * Created by PhpStorm.
 * User: Hsii
 * Date: 2019/01/14
 * Time: 20:49 下午
 */


namespace app\common\model;


class RuleNature extends BaseModel
{
    protected $autoWriteTimestamp = false;
    public function rule()
    {
        return $this->hasOne('Rule');
    }
}