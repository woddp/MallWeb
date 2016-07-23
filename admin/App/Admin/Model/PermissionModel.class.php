<?php
/**
 * ==================================
 *权限 模型
 * ==================================
 */
namespace Admin\Model;


use Think\Model\RelationModel;
use Think\Page;

class PermissionModel extends RelationModel
{
    // 是否批处理验证
    protected $patchValidate = true;
    // 自动验证定义
    protected $_validate = array(
        array('name', 'require', '请输入权限名称'),
        array('name', '', '权限已存在', '0', 'unique', 1),
        array('parent_id', 'require', '请选择父级分类'),
    );


    /**
     * 获取所有url
     */
    public function getPath($rows)
    {
        $urls = array();
        foreach ($rows as $row) {
            $urls[]['url'] = $row['module'] . '/' . $row['controller'] . '/' . $row['action'];
        }
        return $urls;

    }

    /**
     * 添加数据
     */
    public function addPermission()
    {

        //计算左右边界 深度

        $dbSql = new \Admin\Logic\DbSqlLogic();

        $nested = new \Admin\Service\NestedSets($dbSql, $this->getTableName(), 'lft', 'rght', 'parent_id', $this->getPk(), 'level');

        $rows = $nested->insert(I('post.parent_id'), I('post.'), 'bottom');

        if ($rows === false) {
            $this->error = '添加失败';
            return false;
        }
        return true;
    }

    /**
     * 获取分页
     */
    public function getPage()
    {
        $condition = array(
            'status' => '1',
        );
//        //获取数据总页数
//        $totalRows = $this->where($condition)->count();
//        $page = new Page($totalRows, C('LISTROWS'));
//        $page->setConfig('theme', C('PAGE_CONFIG')['theme']);
//        $permission = $this->page(I('get.p'), C('LISTROWS'))->order('lft')->where($condition)->select();
        $permission = $this->order('lft')->where($condition)->select();
//        $html = $page->show();
        return compact('permission');
    }

    /**
     * 修该数据
     */
    public function updatePermission()
    {
        $this->startTrans();
        //如果当前parent_id等于数据表未修改的parent_id 则层级该 普通修该
        $parent_id = $this->getFieldById(I('post.id'), 'parent_id');
        if ($parent_id != I('post.parent_id')) {
            //计算左右边界 深度
            $dbSql = new \Admin\Logic\DbSqlLogic();
            $nested = new \Admin\Service\NestedSets($dbSql, $this->getTableName(), 'lft', 'rght', 'parent_id', 'id', 'level');
            $rows = $nested->moveUnder($this->data['id'], $this->data['parent_id'], 'bottom');
            if ($rows === false) {
                $this->error = '修改失败';
                $this->rollback();
                return false;
            }
        }
        if ($this->save() === false) {
            $this->error = '修改失败';
            $this->rollback();
            return false;
        }
        $this->commit();
        return true;
    }

    /**
     * 删除数据
     * 删除权限和其后代元素
     * 如果权限已经不存在,那么角色关联也要删除
     */

    public function deletePermission()
    {
        $id = I('get.id');

        $this->startTrans();
        //获取后代权限
        $permission_info = $this->field('lft,rght')->find($id);
        $condition = [
            'lft'=>['egt',$permission_info['lft']],
            'rght'=>['elt',$permission_info['rght']],
        ];
        //获取当前范围内的权限id
        $rows=$this->where($condition)->getField('id',true);;
        //删除角色关联
        $role_permission=M('RolePermission');
        if($role_permission->where(['permission_id'=>['in',$rows]])->delete()===false){
            $this->error='删除角色权限失败';
            $this->rollback();
            return false;
        }
        //删除菜单权限关联
        $menu_permission=M('MenuPermission');
        if($menu_permission->where(['permission_id'=>['in',$rows]])->delete()===false){
            $this->error='删除角色权限失败';
            $this->rollback();
            return false;
        }

        //删除当前权限
        $dbSql = new \Admin\Logic\DbSqlLogic();
        $nested = new \Admin\Service\NestedSets($dbSql, $this->getTableName(), 'lft', 'rght', 'parent_id', 'id', 'level');
        if($nested->delete($id)===false){
            $this->error='删除权限失败';
            $this->rollback();
            return false;
        }
        $this->commit();
        return true;

    }


}