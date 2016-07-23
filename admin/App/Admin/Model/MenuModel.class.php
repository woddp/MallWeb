<?php
/**
 * ==================================
 *菜单 模型
 * ==================================
 */

namespace Admin\Model;


use Admin\Logic\DbSqlLogic;
use Admin\Service\NestedSets;
use Think\Model;

class MenuModel extends Model
{
    // 是否批处理验证
    protected $patchValidate = true;
    // 自动验证定义
    protected $_validate = array(
        //菜单名不为空
        array('name', 'require', '菜单名不能为空'),
        //菜单名不能重复
        array('name', '', '菜单名已经存在', '', 'unique', 1),


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
     * 添加菜单
     */
    public function addMenu()
    {
        //获取所有数据
        $data = I('post.');
        $this->startTrans();
        //添加菜单  嵌套集合
        $dbsql = new DbSqlLogic();
        $nested = new NestedSets($dbsql, $this->getTableName(), 'lft', 'rght', 'parent_id', 'id', 'level');
        if ($nested->insert($this->data['parent_id'], $this->data, 'bottom') === false) {
            $this->rollback();
            $this->error = '菜单添加失败1';
            return false;
        }
        $menu_id = $this->getLastInsID();
        //添加菜单权限关联表
        $admin_permission_model = M('MenuPermission');
        $admin_permission_data = array();
        foreach ($data['permission_id'] as $item) {
            $admin_permission_data[] = array(
                'menu_id' => $menu_id,
                'permission_id' => $item,
            );
        }
        //如果没有选择所属权限组不添加
        if (!empty($admin_permission_data)) {
            if ($admin_permission_model->addAll($admin_permission_data) === false) {
                $this->rollback();
                $this->error = '菜单添加失败2';
                return false;
            }
        }

        $this->commit();
        return true;
    }

    /**
     * 修改菜单
     */

    public function updateMenu()
    {
        //获取所有数据
        $row = I('post.');

        $this->startTrans();
        //获取当前数据库的parent_id和当前的parent_id是否相等

        //获取原来的父级菜单,要使用getFieldById 因为find会将数据放到data属性中 data会被重置
        $old_parent_id = $this->getFieldById($row['id'], 'parent_id');

        //如果菜单不同才修改 才使用嵌套更新
        if ($old_parent_id != $row['parent_id']) {
            //添加菜单  嵌套集合
            $dbsql = new DbSqlLogic();
            $nested = new NestedSets($dbsql, $this->getTableName(), 'lft', 'rght', 'parent_id', 'id', 'level');
            //moveUnder只计算左右节点和层级，不保存其它数据 且不能移动到自身下 会返回false
            $status = $nested->moveUnder($this->data['id'], $this->data['parent_id'], 'bottom');
            if ($status === false) {
                $this->rollback();
                $this->error = '不能移动到自身下';
                return false;
            }
        }

        if ($this->save($this->data()) === false) {
            $this->rollback();
            $this->error = '菜单修改失败2';
            return false;
        }

        $admin_permission_model = M('MenuPermission');
        //先删除菜单权限关联表的数据
        if ($admin_permission_model->where("menu_id={$row['id']}")->delete() === false) {
            $this->rollback();
            $this->error = '菜单修改失败';
            return false;
        }
        //添加菜单权限关联表
        $admin_permission_data = array();
        foreach ($row['permission_id'] as $item) {
            $admin_permission_data[] = array(
                'menu_id' => $row['id'],
                'permission_id' => $item,
            );
        }

        //如果没有选择所属权限组不添加
        if (!empty($admin_permission_data)) {
            if ($admin_permission_model->addAll($admin_permission_data) === false) {
                $this->rollback();
                $this->error = '菜单修改失败2';
                return false;
            }
        }

        $this->commit();
        return true;
    }


    //删除deleteMenu
    /**
     * 删除menu 菜单
     *
     * 删除菜单权限对应的数据
     */
    public function deleteMenu($value)
    {

        $id = $value;
        $this->startTrans();


        //计算左右边界包涵那些子级元素

        //获取左边界
        $lft = $this->getFieldById($id, 'lft');
        //获取右边界
        $rght = $this->getFieldById($id, 'rght');
        $cond = array(
            'lft' => ['egt', $lft],
            'rght' => ['elt', $rght],
        );
        //取出当前边界内的所有id
        $menu_id = $this->where($cond)->field('id')->select();
        //如果不为空才执行删除
        if (!empty($menu_id)) {
            //二维变一维数组
            $ids = array_column($menu_id, 'id');
            //删除菜单权限对应的数据
            $menu_permission_model = M('MenuPermission');
          $status= $menu_permission_model->where(array('menu_id' =>array('in',$ids)))->delete() ;
            if ($status=== false) {
                $this->rollback();
                $this->error = '菜单删除失败2';

                return false;
            }
        }

        //删除菜单表的数据  计算嵌套层级
        $dbsql = new DbSqlLogic();
        $nested = new NestedSets($dbsql, $this->getTableName(), 'lft', 'rght', 'parent_id', 'id', 'level');
        if ($nested->delete($id) === false) {
            $this->rollback();
            $this->error = '菜单删除失败2';
            return false;
        }
          $this->commit();
        return true;
    }
}