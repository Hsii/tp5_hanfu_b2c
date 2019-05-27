<?php
/**
 * Created by PhpStorm.
 * User: Hsii
 * Date: 2019/02/23
 * Time: 16:33 下午
 */
namespace app\admin\controller;
use app\common\model\Comment as CommentModel;

class Comment extends Base
{
    public function index()
    {
        $comment = CommentModel::getComment();
        return $this->fetch('',[
            'comment' => $comment,
            'count' => count($comment)
        ]);
    }

    public function delComment()
    {
        $id = input('param.id');
        if(intval($id)){
            try{
                $comment = new CommentModel();
                $comment->delComment($id);
                pe_jsonshow(array('result' => true ,'show' => '删除成功'));
            }catch (\Exception $e){
                pe_jsonshow(array('result' => false ,'show' => '服务器出了一点小问题哟'));
            }

        }
    }
}