<?php
/**
 * Created by PhpStorm.
 * User: woddp
 * Date: 2016/7/20
 * Time: 20:19
 */

namespace Home\Model;


use Think\Model\RelationModel;

class ArticleModel extends RelationModel
{

    protected $_link=array(
        'article_content'=>array(
            'mapping_type'=>self::HAS_ONE,
            'class_name'=>'ArticleContent',
            'parent_id'=>'article_id'
        ),

    );
}