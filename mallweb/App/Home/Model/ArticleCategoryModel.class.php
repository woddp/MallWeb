<?php
/**
 * Created by PhpStorm.
 * User: woddp
 * Date: 2016/7/7
 * Time: 19:16
 */

namespace Home\Model;


use Think\Model;

class ArticleCategoryModel extends Model
{

  

    public function get_article_category()
    {
        //    SELECT article_category.id,article_category.`name`,article.* from
        // article_category JOIN article on article_category.id=article.article_category_id
//找出文章分类 和 文章
//      $rows = $this->alias('ac')->field('ac.*,a.id as aid,a.name as aname')->join('__ARTICLE__ AS a ON ac.id=a.article_category_id')->where(array('ac.is_help'=>1,'ac.status'=>1))->select();

        //找到所有分类
        $category = $this->where("is_help=1")->select();

        //在每个分类下的所有文章
        $article_model = M('Article');
        $arr = array();
        foreach ($category as $item) {

            $arr[$item['name']] =  $article_model->where("article_category_id={$item['id']}")->select();

        }
        return $arr;
    }
}