<?php
/**
 *==========================
 * 短信  邮件 接口控制器
 * ==========================
 */

namespace Home\Controller;

use Think\Controller;

class ApiController extends FatherController
{
    public function message($tel,$conten)
    {
        //秘钥配置
        $config = C('ALIDAYU');
        //引入alidayu
        Vendor('Alidayu.TopSdk');
        $c = new \TopClient;
        $c->appkey = $config['appkey'];
        $c->secretKey = $config['secret'];
        $req = new \AlibabaAliqinFcSmsNumSendRequest;
        $req->setExtend("");
       //短信类型
        $req->setSmsType("normal");
        //发送名称
        $req->setSmsFreeSignName("夙沙飞飞");
        //内容
//        $req->setSmsParam("{name:'wo0dp',code:'1235'}");
        $req->setSmsParam($conten);
        //发送到的手机号
        $req->setRecNum($tel);
        $req->setSmsTemplateCode("SMS_11535392");
         $resp = $c->execute($req);
        return $resp;
    }

}