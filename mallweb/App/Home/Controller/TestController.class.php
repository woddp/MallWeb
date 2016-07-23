<?php
/**
 * Created by PhpStorm.
 * User: woddp
 * Date: 2016/7/14
 * Time: 13:13
 */

namespace Home\Controller;


use Think\Controller;

class TestController extends Controller
{
    function test(){
        echo phpinfo();
    }



    function  search($word='3'){
        //引入中文全文搜素的类
        Vendor('sphinxapi.sphinxapi');
        //实例化列
        $sphinx=new \SphinxClient();
        //设置search的主机名和tcp客服端
        $sphinx->SetServer('127.0.0.1',9312);
        //搜素
        $rst=$sphinx->Query($word);

        var_dump($rst);

    }

    function curl(){
        Vendor('simple_html_dom.simple_html_dom');
       header('Content-type:text/html;charset="utf8"');

        //获取当前网址源码
        $data=$this->get_html('http://www.id97.com/');
        //解析url 获取所有url
        $url=$this->simple_url($data);

        $ur=[];
        foreach ($url as $item){
            $data1=$this->get_html($item);
            $ur[]=$this->simple_url($data1);
        }
        dump($ur);

    }

    //curl连接网页 获取资源
    function  get_res($url){
        //创建curl资源
        $ur=curl_init();
        curl_setopt($ur, CURLOPT_URL,$url);
        curl_setopt($ur,CURLOPT_RETURNTRANSFER,1); //以源码显示
        return $ur;
    }
    //解析读取网页 获取html
    function  get_html($url){
        $ur=$this->get_res($url);
        $data=curl_exec($ur);
        return $data;
    }

    //dom解析 获取url
    function  simple_url($data){
        $html=new  \simple_html_dom();
        $html->load($data);
        $res=$html->find('a');
        $url=array();
        //获取所有url地址
        foreach ($res as $item){
            $url[]=$item->href;
            echo  $item->title.'<br>';
        }
        return $url;

    }

}

class  pthreads extends \Thread{

    protected $url;
    public  function __construct($url)
    {
           $this->url=$url;
    }

    public  function run()
    {
        $i=0;
             echo microtime().'<br>';
//        dump(file_get_contents($this->url));

    }
}

