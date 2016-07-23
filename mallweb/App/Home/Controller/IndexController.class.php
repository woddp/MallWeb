<?php
namespace Home\Controller;

use Think\Controller;

class IndexController extends FatherController
{
   
    /**
     * 主页
     */
    public function index()
    {
        //取出banner图
        $banner_model=D('Banner');
        $banner= $banner_model->Relation('goods')->limit(0,5)->select();
        $this->assign('banner',$banner);

        //获取网站快报 id: 14
        $article_model=D('Article');
        $kb=$article_model->where("article_category_id=14")->order('id desc')->select();
        $this->assign('kb',$kb);

        //获取首发 id: 15
        $article_model=D('Article');
        $sf=$article_model->Relation(true)->where("article_category_id=15")->order('id desc')->limit(0,2)->select();
        $sf=get_img_url($sf);
        $this->assign('sf',$sf);

        //获取数码分类资讯
         $article_category_model=D('ArticleCategory');
         $id=$article_category_model->where("goods_category_id={$this->sm_id}")->getField('id');
        $suzx=$article_model->where("article_category_id={$id}")->order('id desc')->select();
        $this->assign('suzx',$suzx);
        //取出所有数码品牌
        $goods_category_model=D('GoodsCategory');
            //1.取出数码分类下的所有子分类
         $ids=$goods_category_model->margin($this->sm_id);
        $ids=array_column($ids, 'id');




         $this->display();
  }

}



