<?php
/**
 * Created by PhpStorm.
 * User: woddp
 * Date: 2016/7/2
 * Time: 11:03
 */

namespace Admin\Model;


use Org\Util\String;
use Think\Model;
use Think\Page;
use Think\Verify;

class AdminModel extends Model
{
    // 是否批处理验证
    protected $patchValidate = true;
    // 自动验证定义

    protected $_validate = array(
        //用户名唯一
        array('username', '', '用户名已存在', '', 'unique', 1),
        //用户名不为空
        array('username', 'require', '用户名不能为空'),
        //邮箱唯一
        array('email', '', '邮箱已存在', '', 'unique', 1),
        //邮箱不为空
        array('email', 'require', '邮箱不能为空'),
        //两次密码不正确

        array('repassword', 'password', '确认密码不正确', 0, 'confirm', 1),
        //密码长度为6 - 16
        array('password', '6,16', '确认长度必须大于6小于16', 0, 'length', 1),
        array('password', 'require', '密码不能为空'),
        //验证码验证
//        array('verify','check_code','验证码不正确',0,'callback','login'),
    );

    //自动完成
    protected $_auto = array(
        //添加时间
        array('add_time', 'time', 1, 'function'),
        //加盐
        array('salt', 'randSalt', 1, 'callback'),

    );

    //关联模型


    //生产盐
    function randSalt()
    {
        $model = new String();
        return $model->randString(4);
    }

    //验证码判断
    function check_code($code)
    {
        $verify = new Verify();
        if (!$verify->check($code)) {
            return false;
        }
        return true;
    }

    //分页数据
    public function show_page()
    {
        $search = I('get.search');
        //查询status不是-1的数据
        $data = array();
        //不为空
        if (!empty($search)) {
            $data['username'] = ['like', "%{$search}%"];
        }
        //共有多少条数据
        $listAll = $this->where($data)->count('id');
        //实例化分页
        $page = new Page($listAll, C('LISTROWS'));
        //设置分页样式
        $page->setConfig('theme', C('PAGE_CONFIG')['theme']);
        $html_page = $page->show();

        //查询数据
        $admin = $this->where($data)->page(I('get.p'), C('LISTROWS'))->select();

        //如果没查到数据
        return compact('admin', 'html_page');
    }

    //添加管理员
    public function addAdmin()
    {
        $this->startTrans();
        $role_id = I('post.role_id');
        $data = $this->data();
        //密码加盐加密
        $data['password'] = get_md5($data['password'], $data['salt']);

        //添加管理员 取得id
        if (($id = $this->add($data)) === false) {
            $this->error = '管理员添加失败';
            $this->rollback();
            return false;
        }

        //添加管理员角色关联
        $admin_role = array();
        foreach ($role_id as $item) {
            $admin_role[] = array(
                'role_id' => $item,
                'admin_id' => $id,
            );
        }

        $model = M('AdminRole');
        //角色关联数据不为空才添加
        if (!empty($admin_role)) {
            if ($model->addAll($admin_role) === false) {
                $this->error = '管理员角色添加失败';
                $this->rollback();
                return false;
            }
        }

        $this->commit();
        return true;
    }

    /**
     * 获取当前id的角色权限
     *
     */

    function get_admin_role($id)
    {
        $admin_role_model = M('AdminRole');
        $rows = $admin_role = $admin_role_model->where("admin_id={$id}")->select();

        $data = [];
        foreach ($rows as $item) {
            $data[] = array(
                'id' => $item['role_id'],
            );
        }
        return $data;
    }

    /**
     * 修改管理员
     */
    function updateAdmin()
    {
        $this->startTrans();
        $data = I('post.');
        //修改管理员.
        if ($this->save() === false) {
            $this->error = '管理员保存失败';
            $this->rollback();
            return false;
        }
        //=====修改管理员角色
        $admin_role_model = M('AdminRole');
        //删除所有当前管理员的角色关联
        if ($admin_role_model->where("Admin_id={$data['id']}")->delete() === false) {
            $this->error = '管理员角色修改失败';
            $this->rollback();
            return false;
        }
        $admin_role = array();
        foreach ($data['role_id'] as $item) {
            $admin_role[] = array(
                'admin_id' => $data['id'],
                'role_id' => $item,
            );
        }
        //添加管理员角色关联信息
        if ($admin_role_model->addAll($admin_role) === false) {
            $this->error = '管理员角色修改失败';
            $this->rollback();
            return false;
        }

        $this->commit();
        return true;

    }

    /**
     * 删除管理员
     * 删除管理员-角色
     */

    public function deleteAdmin($id)
    {

        $this->startTrans();
        //删除管理员-角色关联
        $admin_role_model = M('AdminRole');
        if ($admin_role_model->where("admin_id={$id}")->delete() === false) {
            $this->error = '管理员角色关联失败';
            $this->rollback();
            return true;
        }

        if ($this->delete($id) === false) {
            $this->error = '管理员删除失败';
            $this->rollback();
            return true;
        }

        $this->commit();
        return true;

    }

    /**
     * 重置密码
     */

    function resetting()
    {
        //接受表单密码
        $pwd = I('post.password');
        //获取盐
        $salt = $this->randSalt();
        //密码加盐加密
        $data = array(
            'id' => session('id'),
            'password' => get_md5($pwd, $salt),
            'salt' => $salt,
        );

        if ($this->save($data) === false) {
            $this->error = '密码重置失败';
            return false;
        }

        return true;
    }


    /**
     * 登录验证
     */

    function loginCheck()
    {
        //接受用户数据
        $data = I('post.');
        //判断用户是否存在
        if (!($user_info = $this->where("username='{$data['username']}'")->select())) {
            $this->error = '用户名错误';
            //不存在 返回false
            return false;
        }

        //取出盐
        $salt = $user_info[0]['salt'];
        //结合盐和用户输入的密码 判断是否正确
        $password = get_md5($data['password'], $salt);
        //判断密码是否正确
        if ($password !== $user_info[0]['password']) {
            //密码错误
            $this->error = '密码错误';
            return false;
        }
        //判断用户是否是自动登录 勾选
         if(!empty($data['remember'])){
            //如果自动登录则保存当前用户id和随机token
             $this->auto_token($user_info[0]['id']);
         }else{ //未勾选 则清空cookie
             cookie('user_info',null);
         }
        //更新最后登录时间ip 和 获取 权限地址
        $this->last_path($user_info[0]['id']);
        //保存session
        user_session($user_info[0]);
        return true;
    }


    //更新最后登录时间 登录ip  获取 权限_path
    function last_path($id)
    {
        $data = array(
            'id' => $id,
            'last_login_time' => time(),
            'last_login_ip' => get_client_ip(),
        );
        //变更时间ip
        $this->save($data);
        $cond = array(
            'admin_id' => $id,
        );
        //获取当前用户所拥有的所有权限
        $admin_model = M('AdminRole');
        $rows = $admin_model->field('id,name,path,parent_id')->distinct(true)->join('__ROLE_PERMISSION__ ON __ADMIN_ROLE__.role_id=__ROLE_PERMISSION__.role_id')->join('__PERMISSION__ ON __ROLE_PERMISSION__.permission_id=__PERMISSION__.id')->where($cond)->select();
        //把结果转换成一维数组存入session
        $user_path = array_column($rows, 'path');
        //保存用户可访问的地址
        user_path($user_path);
        //保持菜单信息
    
        return true;
    }


    //保存自动登录token 信息
    function auto_token($id){
       $data=array(
           'admin_id'=>$id,
           'token'=>String::randString('38'),
       );
        //保存token信息
        $admin_token_model=M('AdminToken');
        //当重新登录时删除当前用户token在重新添加
        $admin_token_model->where("admin_id={$id}")->delete();
        //删除cookie
        cookie('user_info',null);
        //并重新保存cookie
       cookie('user_info',$data,604800);
        //添加用户session信息

        //添加数据
       return $admin_token_model->add($data);
    }

    //自动验证
    function auto_login($data){
        $admin_id=$data['admin_id'];
        $token=$data['token'];

        $admin_token_model=M('AdminToken');
        $cond=array(
            'admin_id'=>$admin_id,
        );
        //判断当前的toke信息是否和数据库相同
        $row=$admin_token_model->where($cond)->select();

        if($row[0]['token']!=$token){//不匹配 验证失败
            cookie('user_info',null);
            session(null);
            return false;
        }
        //获取用户信息
        $row=$this->find($admin_id);
        user_session($row);

        //更新最后登录时间ip 和 获取 权限地址
        $this->last_path($admin_id);
        //重新保存数据库
        $this->auto_token($admin_id);
        return true;
    }
}