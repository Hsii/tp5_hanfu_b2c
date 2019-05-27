<?php
/**
 * Created by PhpStorm.
 * User: Hsii
 * Date: 2019/02/24
 * Time: 12:02 下午
 */


namespace app\admin\controller;
use app\common\model\Article as ArticleModel;
use app\common\validate\FailMessage;
use app\common\validate\SuccessMessage;
use think\Exception;

class Article extends Base
{
    public function index()
    {
        $Article = ArticleModel::getArticle();
        return $this->fetch('',[
            'count' => count($Article),
            'article' => $Article
        ]);
    }

    public function addArticle()
    {
        $info = input('param.info/a');
        if($info){
            $art = new ArticleModel();
            $art->saveArticle('',$info);
        }else{
            return $this->fetch();
        }
    }

    public function editArticle()
    {
        $data = input('param.');
        if($data['info']){
            $art = new ArticleModel();
            $art->saveArticle($data['id'],$data['info']);
        }else{
            $Article = ArticleModel::getArticleDetails($data['id']);
            return $this->fetch('',[
                'article' => $Article
            ]);
        }
    }

    public function delArticle()
    {
        $id = input('param.');
        $art = new ArticleModel();
        try{
            $art->where('article_id',$id)->delete();
            return new SuccessMessage();
        }catch (Exception $e){
            return new FailMessage();
        }
    }
}