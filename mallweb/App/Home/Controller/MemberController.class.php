<?php
/**
 * ================================================
 * 注册控制器
 * ================================================
 */

namespace Home\Controller;


use Org\Util\String;
use Think\Controller;

class MemberController extends FatherController
{
    /**
     * @var null Admin\Model\PermissionModel;
     */
    private $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = D('Member');
        //不需要验证
        $action=array(
            'register',
            'mes',
            'verify_email',
            'reverify',
            'actual',
            'get_user_info',
            'login',
            'logout',
        );

        //获取当前用户收货地址
        $id = session('user');

        //如果用户未登录就跳到登录页面

        if (empty($id)) {
            if(in_array(ACTION_NAME,$action)){

            }else{
                $this->error('你没登录', U('Member/login'));
            }
        }
        ;


    }

    public function register()
    {
        if (IS_POST) {
            if ($this->model->create('', 'reg') === false) {
                $this->error(get_error($this->model));
            }
            if ($this->model->addMember() === false) {
                $this->error(get_error($this->model));
            }
            $this->success('注册成功!激活邮件已经发送', U('Index/index'), 4);
        } else {

            $this->display();
        }
    }

    //短信发送
    public function mes($tel, $name)
    {
        //实例化api类
        $api_mes = new ApiController();
        //随机验证码
        $rand = new String();
        $code = $rand->randNumber(10000, 99999);
        //把验证码存入session 方便验证
        session('tel_code', $code);
        //$content 设置发送内容
        $contetn = array(
            'name' => $name,
            'code' => $code
        );
        $rows = $api_mes->message($tel, json_encode($contetn));
    }

    //邮箱验证
    public function verify_email()
    {
        //通过邮箱 toke 邮件时间
        $cond = array(
            'email' => I('get.email'),
            'token' => I('get.token'),
        );
        if (($info = $this->model->where($cond)->find()) === false) {
            $this->error('邮箱验证失败', U('register'));

        }
        //判断邮箱验证时间是否过期
        // 当前时间 大于 注册时间加上86400 则验证链接失效
        if (($info['send_time'] + 86400) < time()) {
            $this->error('验证链接失效,请重新验证', U('reverify'));
        }

        //如果验证成功把会员状态改为1;
        $data = array(
            'id' => $info['id'],
            'status' => '1',
        );
        $this->model->save($data);

        $this->success('验证成功', U('Index/index'));
    }

    //验证链接过期 重新验证

    function reverify()
    {
        if (IS_POST) {
            $data = I('post.');
            //查找到用户
            $cond = array(
                'email' => $data['email'],
                'username' => $data['username'],
            );
            $info = $this->model->where($cond)->find();
            if (empty($info)) {
                $this->error('用户未注册,请注册', U('register'));
            }
            //更新邮件发送事件
            $reslut = array(
                'id' => $info['id'],
                'send_time' => time(),
            );
            $this->model->save($reslut);

            //验证邮箱
            if ($this->model->email_verify($info) === false) {
                $this->error('验证邮箱发送失败,请重试', U('reverify'));
            }
            $this->success('验证邮件发送成功', U('Index/index'));
            exit();
        }
        $this->display();
    }

    //ajax验证
    function actual()
    {
        $con = I('get.');

        $row = $this->model->where($con)->find();
        if (!$row) {
            $this->ajaxReturn(true);
        } else {
            $this->ajaxReturn(false);
        }
    }

    //ajax获取用户信息
    function get_user_info()
    {
        //用户信息
        $user = session('user');
        //如果用户信息为空
        if (empty($user)) {
            $this->ajaxReturn(array('status' => '0'));
        } else {
            $this->ajaxReturn(array('status' => '1', 'name' => $user['username']));
        }

    }

    //会员登录
    function login()
    {
        if (IS_POST) {
            if ($this->model->create() === false) {
                $this->error(get_error($this->model));
            }
            if ($this->model->verify_member() === false) {
                $this->error(get_error($this->model));
            }

            $this->success("登录成功", U('Index/index'));
        } else {
            $this->display();
        }
    }

    //会员退出
    function logout()
    {
        session(null);
        cookie(null);
        $this->success('退出成功', U('Index/index'));
    }

    /*
     * ==================================================
     * 个人中心
     * ==================================================
     */

    //收货地址
    function address()
    {
        //获取三级联动地址
        $locations_model = D('Locations');
        $address_model = D('Address');
        if (IS_AJAX) {
            $id = I('get.id');
            $rows = $locations_model->get_address($id);
            $this->ajaxReturn($rows);
            exit();
        }
        //保存收货信地址
        if (IS_POST) {


            if ($address_model->create() === false) {
                $this->error(get_error($address_model));
            }
            if ($address_model->save_address() === false) {
                $this->error(get_error($address_model));
            }

            $this->success('收货地址添加成功', U('address'));
            exit();
        }
        //获取省份地址
        $rows = $locations_model->get_address();
        $this->assign('rows', $rows);
        //获取当前用户收货地址
        $id = session('user');

        $id = $id['id'];
        $user_address = $address_model->where("member_id=$id")->select();
        $this->assign('user_address', $user_address);
        $this->display();
    }

    //修改收货地址
    /**
     * @param $cur_id 需要获取的id
     */
    function editaddress($id)
    {

        //获取三级联动地址
        $locations_model = D('Locations');
        $address_model = D('Address');
        //获取省份地址
        $rows = $locations_model->get_address();

        $this->assign('rows', $rows);
        //获取当前用户收货地址
        $cur_id = session('user');
        //如果用户未登录就跳到登录页面
        if (empty($cur_id)) {
            $this->error('你没登录', U('Member/login'));
        }
        //获取当前用户id
        $cur_id = $cur_id['id'];
        $cond = [
            //会员
            'member_id' => $cur_id,
            //id等于当前id
            'id' => $id,
        ];
        $user_address = $address_model->where($cond)->find();
        $this->assign('user_address', $user_address);
        if (empty($user_address)) {
            $this->error('么有这个地址');
        }
        $this->display();
    }

    //保存修改的地址
    function update()
    {


        $address_model = D('Address');
        if ($address_model->create() === false) {
            $this->error(get_error($address_model));
        }
        if ($address_model->update_address() === false) {
            $this->error(get_error($address_model));
        }

        $this->success('收货地址修改成功', U('address'));

    }


    //用户信息
    function userinfo(){
        $this->display();
    }

}