<?php
/**
 * ================================================
 * 注册
 * ================================================
 */
namespace Home\Model;

use Org\Util\String;
use Think\Verify;


class MemberModel extends \Think\Model
{
    // 是否批处理验证
    protected $patchValidate = true;
    // 自动验证定义
    protected $_validate = array(
        //用户名不为空
        array('username', 'require', '用户名不能为空'),
        //用户名不能重复
        array('username', '', '用户名以存在', '', 'unique', 'reg'),
        //邮箱不能为空
        array('email', '', '邮箱已经存在', '', 'unique', 'reg'),
        array('email', 'require', '邮箱不能为空'),
        array('tel', 'require', '手机号不能为空'),
        array('tel', '', '手机号码已经存在', '', 'unique', 'reg'),
        //密码不为空
        array('password', 'require', '密码不能为空'),
        //密码不一致
        array('repassword', 'password', '密码不一致', self::EXISTS_VALIDATE, 'confirm', 'reg'),
//        //手机验证
//        array('captcha', 'require', '手机验证不能为空'),
//
//        array('captcha', 'tel_verify', '手机验证错误', self::EXISTS_VALIDATE, 'callback', 'reg'),

        //验证码verify
//        array('verify', 'require', '图片验证码不能为空'),
//        array('verify','check_verify','图片验证码错误',self::EXISTS_VALIDATE,'callback'),
    );

    //判断手机验证码是否正确
    function tel_verify($code)
    {
        if (session('tel_code') != $code) {
            return false;
        } else {
            return true;
        }

    }

    //判断图片验证码是否正确
    function check_verify($code)
    {
        $verify = new Verify();
        return $verify->check($code);
    }

    // 自动完成定义
    protected $_auto = array(
        array('add_time', 'time', 'reg', 'function'),
        //盐
        array('salt', 'add_salt', 'reg', 'callback'),
        //用户状态为0 位验证
        array('status', '0', 'reg'),

        //邮箱发送事件 验证密码
        array('send_time', 'time', 'reg', 'function'),
        array('token', 'add_token', 'reg', 'callback'),
    );

    function add_salt()
    {
        $str = new String();
        return $str->randString(6);
    }

    //token
    function add_token()
    {
        $str = new String();
        return $str->randString(40);
    }

    //添加会员
    public function addMember()
    {

        //密码加密加盐
        $this->data['password'] = get_md5($this->data['password'], $this->data['salt']);
        $user_info = $this->data;
        //添加用户
        if ($this->add() === false) {
            $this->error = '注册失败';
            return false;
        }
        //邮箱验证
//        $row = $this->email_verify($user_info);
//
//        if ($row['status'] === '0') {
//            $this->error = '激活邮件发送失败';
//            return false;
//        }
        return true;


    }

    //邮箱验证
    function email_verify($data)
    {
        $user_info = $data;
        //注册成功则发送激活邮件
        //注册邮箱
        $useremail = $user_info['email'];
        //标题
        $title = '欢迎注册woddp 邮箱验证';
        //获取注册用户名
        $name = $user_info['username'];
        $url = U('Member/verify_email', ['email' => $useremail, 'token' => $user_info['token']], true, true);
        //发送内容
        $content = "<center>{$name}欢迎注册</center>
     <a href='{$url}'>点击</a>马上激活  <br>
               如果无法点击 请直接复制地址到浏览器 {$url}         
";

        return send_email($useremail, $title, $content);
    }


    //用户登录验证
    function verify_member()
    {
        $login_info=I('post.');

        $data = $this->data;

        //查找用户名是否匹配
        $username = $data['username'];
        $info = $this->where(array("username" => $username))->find();
        if (empty($info)) {
            $this->error = '用户不存在!';
            return false;
        }
        //比较密码
        $cur_password = get_md5($data['password'], $info['salt']);
        //密码不匹配
        if ($cur_password != $info['password']) {
            $this->error = '密码错误';
            return false;
        }
        $updata=[
            'id'=>$info['id'],
            'last_login_time'=>time(),
            'last_login_ip'=>get_client_ip(),
        ];
        //更新最后ip 地址
        $this->save($updata);
//=======================================
        //并把cookie的购物数据存入数据库
        //实例化订单库
        $shop_model = D('ShoppingCar');
        //获取cookie的订单存入数据库
        $str = cookie('shop_car');
        //如果cookie有订单
        if ($str) {
            //把cookie里的值添加数据库
            $shop_model->cookie_add($info['id']);
        }
//=======================================
        //并且把用户信息存入seesion
        session('user',$info);
         
        //如果勾选保存登录信息
        if($login_info['is_on']){
            cookie('user',$login_info);
        }else{
            cookie(null);
        }
        return true;
    }
}