<?php
/**
 * ==================================
 *角色模型
 * ==================================
 */
namespace Admin\Model;


use Think\Model\RelationModel;
use Think\Page;

class RoleModel extends RelationModel
{

    // 是否批处理验证
    protected $patchValidate = true;
    // 自动验证定义
    protected $_validate = array(
        array('name', 'require', '请输入角色名称'),
        array('name', '', '角色已存在', '0', 'unique', 1),

    );

    //关联角色权限表
    protected $_link = array(

        'roelPerssion' => array(
            'mapping_type' => self::HAS_MANY,
            'class_name' => 'RolePermission',
        ),
    );

    /**
     * 数据展示并分页
     *
     */
    /**
     * 获取分页
     */
    public function getPage()
    {
        $condition = array(
            'status' => '1',

        );
        if (!empty(I('get.search'))) {
            $condition[] = array(
                'name' => ['like', "%{I('get.search')}%"],
            );
        }
        //获取数据总页数
        $totalRows = $this->where($condition)->count();
        $page = new Page($totalRows, C('LISTROWS'));
        $page->setConfig('theme', C('PAGE_CONFIG')['theme']);
        $role = $this->page(I('get.p'), C('LISTROWS'))->where($condition)->select();
        $html = $page->show();
        return compact('html', 'role');
    }


    /**
     * 添加角色
     */
    function addRole()
    {
        $this->startTrans();
        $data = I('post.');
        //保存角色数据
        $id = $this->add();
        if (!$id) {
            $this->error = '角色保存失败';
            $this->rollback();
            return false;
        }

        //保存角色权限数据
        $role_date = array();
        foreach ($data['permission_id'] as $item) {
            $role_date[] = array(
                'role_id' => $id,
                'permission_id' => $item,
            );
        }
        $role_permission = M('RolePermission');
        if (!$role_permission->addAll($role_date)) {
            $this->error = '角色权限保存失败';
            $this->rollback();
            return false;
        }
        $this->commit();
        return true;
    }

    /**
     * 修改数据
     */
    function updateRole(){
        $this->startTrans();
        $data = I('post.');
         //修改角色数据
        if ( $this->save($data)===false) {
            $this->error = '角色修改失败';
            $this->rollback();
            return false;
        }
/*
 * ============================================
  */
        $role_permission = M('RolePermission');
        //移除角色表当前id所有权限数据

          $status=$role_permission->where("role_id={$data['id']}")->delete();
       if($status===false){ //删除失败
           $this->error = '角色权限修改失败1';
           $this->rollback();
           return false;
       }
        //修改角色权限数据
        $role_date = array();
        foreach ($data['permission_id'] as $item) {
            $role_date[] = array(
                'role_id' => $data['id'],
                'permission_id' => $item,
            );
        }
      if(!empty($role_date)){
        if (!$role_permission->addAll($role_date)) {
            $this->error = '角色权限修改失败2';
            $this->rollback();
            return false;
        }
      }
        $this->commit();
        return true;
    }
}