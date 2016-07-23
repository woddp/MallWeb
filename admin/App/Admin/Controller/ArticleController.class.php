<?php
/**
 *===========================================
 * 文章控制器
 *===========================================
 */
namespace Admin\Controller;


use Admin\Model\ArticleModel;
use Think\Controller;

class ArticleController extends PublicController
{
    /**
     * @var  \Admin\Model\ArticleModel
     */
    public  function index(){
        $article=D('Article');
        $data=$article->article_page();
        $this->assign('data',$data)->display();
    }
    public  function store(){
        if(IS_POST){
             $article=D('Article');
           
            //插入的时候才验证
            if ($article->create ('', 'insert') === false) {
                $this->error (get_error ($article));
            }
            if($article->add_rows()===false){
                $this->error ('添加失败');
            }
            $this->success ('添加成功', U ('Article/index'));
        }else{
            //查询出所有分类
            $category=M('ArticleCategory');
            $this->assign('article_category',$category->select ());
            $this->display();
        }
    }

    public function edit(){
        $article=D('Article');
        $article_content=D('ArticleContent');
        if(IS_POST){

            $data=I('post.');
            unset($data['content']);
            $id=$data['id'];

            $result=$article->where("id={$id}")->save($data);
            //更新内容
            $data=[
                'content'=>I('post.content')
            ];

            $con=$article_content->where("article_id={$id}")->save($data);
            if($result===false&&$con===false){
                $this->error('更新失败');
            }

            $this->success ('更新成功', U ('Article/index'));

        }else{
            $rows= $article->relation(true)->find(I('get.id'));
            //查询出所有分类
            $category=M('ArticleCategory');
            $this->assign('article_category',$category->select ());
            $this->assign('rows',$rows)->display();
        }
    }


    public  function destroy($id){
        $article=D('Article');
          $result=$article->relation('ArticleContent')->delete($id);
        if($result===false){
            $this->error('删除失败');
        }
        $this->success ('删除成功', U ('Article/index'));

    }


}