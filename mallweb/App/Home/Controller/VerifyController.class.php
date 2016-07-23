<?php
/**
 * Created by PhpStorm.
 * User: woddp
 * Date: 2016/7/5
 * Time: 16:19
 */

namespace Home\Controller;


use Think\Controller;
use Think\Verify;

class VerifyController extends FatherController
{
    public  function verify(){
        $config=C('VERIFY_CODE_LENGTH');
        $verify=new Verify($config);
        $verify->entry();
    }

}