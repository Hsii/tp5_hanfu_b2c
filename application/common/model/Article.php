<?php
/**
 * Created by PhpStorm.
 * User: Hsii
 * Date: 2019/02/24
 * Time: 12:04 下午
 */
namespace app\common\model;

class Article extends BaseModel
{
    public static function getArticle()
    {
        return self::order('create_time')->select();
    }

    public static function getArticleDetails($article_id)
    {
        return self::where('article_id',$article_id)->find();
    }

    public function saveArticle($id='',$data)
    {
        if(intval($id)){
            $where = ['article_id'=>$id];
        }else{
            $where = '';
        }
        return $this->allowField(true)->save($data,$where);
    }
}