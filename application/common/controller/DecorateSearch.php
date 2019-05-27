<?php
/**
 * Created by PhpStorm.
 * User: Hsii
 * Date: 2019/02/24
 * Time: 11:13 上午
 */


namespace app\common\controller;


class DecorateSearch
{
    // 中文分词搜索
    public function decorateSearch($words)
    {
        $tempArr = str_split($words);
        $wordArr = array();
        $temp = '';
        $count = 0;
        $chineseLen = 3;
        foreach($tempArr as $word){
            if ($count == $chineseLen){
                $wordArr[] = $temp;
                $temp = '';
                $count = 0;
            }
            // 中文
            if(ord($word) > 127){
                $temp .= $word;
                ++$count;
            }else if (ord($word) != 32){
                $wordArr[] = $word;
            }
        }

        if ($count == $chineseLen){
            $wordArr[] = $temp;
        }

        return '%'.implode($wordArr, '%').'%';
    }
}