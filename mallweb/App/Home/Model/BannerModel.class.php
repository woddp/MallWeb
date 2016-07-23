<?php
/**
 * Created by PhpStorm.
 * User: woddp
 * Date: 2016/7/19
 * Time: 13:38
 */

namespace Home\Model;


use Think\Model;

class BannerModel extends Model\RelationModel
{

    protected $_link=array(

        'goods'=>array(
          'mapping_type'=>self::BELONGS_TO,
            'class_name'=>'Goods',
            'mapping_fields'=>'id,logo',
        ),

    );


}