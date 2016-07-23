<?php
/**  
 *===========================================
 * 文章模型
 *=========================================== 
 */

namespace Admin\Model;


use Think\Model;
use Think\Page;

class ArticleModel extends Model\RelationModel
{

    // 是否批处理验证
    protected $patchValidate    =   true;
    // 自动验证定义
    protected $_validate        =   array(
        //文章不能为空
        array('name','require','名称不能为空'),
        //文章不能重复
        array('name','','文章分类已存在','','unique','insert'),
        //简介不能小于5
        array('intro','5,50','文章简介不能小于5个字符','','length'),
        //排序必须为数字
        array('sort','number','排序必须为数字'),
    );
    // 自动完成定义
    protected  $_auto=array(
         array('inputtime','time',3,'function'),
    );
    /**
     * @var array 返回数组关联模型
     */
    protected $_link = array(
        //文章分类  一对一
        'ArticleCategory' => array(
            'mapping_type' => self::BELONGS_TO,
            'class_name' => 'ArticleCategory',
//            'mapping_name' => 'article_category_id',
            //当前表的那个字段关联文章分类表的id
            'foreign_key' => 'article_category_id',

        ),
        //文章内容 一对一
        'ArticleContent' => array(
            'mapping_type' => self::HAS_ONE,
            'class_name' => 'ArticleContent',
//            'foreign_key' => 'article_id',
            'mapping_name' => 'ArticleContent',
        ),

    );

 /**
 * ====================================
 *     分页
 * ===================================
 */

    function article_page(){
        $search=I('get.search');
        $data='';
        //查询
        if(!empty($search)){
            $data['name']=['like',"%{$search}%"];
        }

        //获取总页数
        $lisrows= $this->relation(true)->count();
        //每页显示几条数据
        $listRows=C('LISTROWS');
        $rows=$this->relation (true)->page (I('get.p'),$listRows)->where ($data)->order('id')->select ();
        //实例化分页
        $page=new Page($lisrows,$listRows);
        $page->setConfig ('theme',C('PAGE_CONFIG')['theme']);
        $html= $page->show ();

       return compact ('rows','html');
    }

/**
 * ====================================
 *     添加数据
 * ===================================
*/
    function  add_rows(){
        //开启事务
        $this->startTrans ();
        //介绍内容
        $content=I('post.content');
        //实例化文章内容模型
        $article_content=M('ArticleContent');
        $id=$this->add ();
        if ($id===false) {
           $this->rollback ();
           return false;
        }
        //  添加文章内容
        $ac=array(
            'content'=>$content,
            'article_id'=>$id,
        );
        if ($article_content->add ($ac)===false) {
            $this->rollback ();
            return false;
        }
        $this->commit ();
        return true;
    }





}