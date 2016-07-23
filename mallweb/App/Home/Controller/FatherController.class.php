<?php
/**
 * Created by PhpStorm.
 * User: woddp
 * Date: 2016/7/9
 * Time: 12:44
 */

namespace Home\Controller;


use Think\Controller;

class FatherController extends Controller
{

    protected  $sign_sm='sm';
    protected  $sm_id;

    public function _initialize()
    {

        //判断是否是首页
        if (ACTION_NAME == 'index') {
            //设置菜单为false
            $show_menu = false;
        } else {
            $show_menu = true;
        }

        $this->assign('show_menu', $show_menu);

        //获取所有商品分类
        $goods_Category_model = D('GoodsCategory');

        //使用数据缓存
        //如果数据为空则查询数据库添加缓存
        if(!$rows=S('rows')){
            $rows = $goods_Category_model->field('id,name,parent_id,sign')->where('status=1')->select();
            S('rows',$rows,3600);
        }
        $this->assign('rows', $rows);

        //获取商品
        $goods_model = D('Goods');
        //精品
        $best = $goods_model->goods_status(1, 'id,name,logo,shop_price');
        $this->assign('best', $best);
        //热销
        $hot = $goods_model->goods_status(2, 'id,name,logo,shop_price');
        $this->assign('hot', $hot);
        //新品
        $new = $goods_model->goods_status(4, 'id,name,logo,shop_price');
        $this->assign('new', $new);


        //获取所有的数码   sm是标识
        $id=$goods_Category_model->sign_id($this->sign_sm);
        $this->sm_id=$id;
        //根据标识取出当前数码id
        $pc_bg=category_list($rows,$id);
         
        $this->assign('pc_bg', $pc_bg);
        $pc_bg_best=$goods_model->category_goods_status(1,'id,name,logo,shop_price',$id,8);
        $this->assign('pc_bg_best', $pc_bg_best);
        $pc_bg_hot=$goods_model->category_goods_status(2,'id,name,logo,shop_price',$id,8);
        $this->assign('pc_bg_hot', $pc_bg_hot);
        $pc_bg_new=$goods_model->category_goods_status(4,'id,name,logo,shop_price',$id,8);
        $this->assign('pc_bg_new', $pc_bg_new);
       


        //取出所有文章
        $article_model=D('ArticleCategory');

        //如果数据为空则查询数据库添加缓存
        if(!$article_category=S('article_category')){
            $article_category = $article_model->get_article_category();
            S('article_category',$article_category,3600);
        }


        $this->assign('article_category',$article_category);

      
    }

}