<?php
/**
 * Created by PhpStorm.
 * User: Hsii
 * Date: 2019/02/24
 * Time: 15:00 下午
 */
namespace app\index\controller;
use app\common\model\Article as ArticleModel;

class Article extends Base
{
    public function index()
    {
        $Article = ArticleModel::getArticle();
        return $this->fetch('',[
            'article' => $Article
        ]);
    }

    public function details($id)
    {
        if(intval($id)){
            $Article = ArticleModel::getArticleDetails($id);
            return $this->fetch('',[
                'article' => $Article
            ]);
        }
    }
}