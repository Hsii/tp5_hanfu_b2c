<?php
/**
 * Created by PhpStorm.
 * User: Hsii
 * Date: 2019/02/21
 * Time: 18:19 下午
 */


namespace app\common\controller;


class Hash
{
    public function hash_pw($pw,$code)
    {
        return md5(md5($pw).$code);
    }
}