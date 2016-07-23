<?php
/**
 *====================================
 *文章分类控制器
 *==================================
 *
 */

namespace Admin\Controller;


use Think\Controller;

class ArticleCategoryController extends PublicController
{

    private $article_category = null;

    function _initialize ()
    {
        parent::_initialize();
        $this->article_category = D('ArticleCategory');
    }

    function index ()
    {
        //获取数据
        $article_categorys = $this->article_category->show_page ();
        //显示数据
        $this->assign ('article_categorys', $article_categorys)->display ();
    }

    function store ()
    {
        if (IS_POST) {
            //插入的时候才验证
            if ($this->article_category->create ('', 'insert') === false) {
                $this->error (get_error ($this->article_category));
            }
            if ($this->article_category->add () === false) {
                $this->error (get_error ($this->article_category));
            }
            $this->success ('添加成功', U ('index'));
        } else {
            //获取商品分类
            $goodscategory_model = D('GoodsCategory');
            $goodscategory = $goodscategory_model->field('id,name,parent_id')->select();
            //转换成json
            $goodscategory = json_encode($goodscategory);
            $this->assign('goodscategory', $goodscategory);
        $this->display ();
       }
    }

    function edit ()
    {
        if (IS_POST) {
            if ($this->article_category->create() === false) {
                $this->error (get_error ($this->article_category));
            }
            if ($this->article_category->save() === false) {
                $this->error (get_error ($this->article_category));
            }
            $this->success ('修改成功', U ('index'));
        } else {
            //获取需要修改的数据
            $articlecategory = $this->article_category->find (I ('get.id'));
            //获取商品分类
            $goodscategory_model = D('GoodsCategory');
            $goodscategory = $goodscategory_model->field('id,name,parent_id,sign')->select();
         
            //转换成json
            $goodscategory = json_encode($goodscategory);

            $this->assign('goodscategory', $goodscategory);
            $this->assign ('articlecategory', $articlecategory);
            $this->display ();
        }
    }

    function destroy ($id)
    {
        if (IS_GET) {
            //根据id找到字段 把statu更改为-1 并把字段name添加字符串后缀_del
            $data = array('status' => '-1', 'name' => ['exp', 'concat(name,"_del")']);
            if ($this->article_category->where ("id={$id}")->setField ($data) === false) {
                $this->error ('删除失败');
            }
            $this->success ('删除成功');
        }
    }


}