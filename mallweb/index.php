<?php

if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');
//开启debug模式
define('APP_DEBUG',true);
//定义一个项目目录常量
define('ROOT_PATH',  __DIR__ . '/');
//定义应用目录
define('APP_PATH',ROOT_PATH.'App/');
//定义前后台台
define('BIND_MODULE','Home');
//取出文件路径部分

include dirname(ROOT_PATH).'/ThinkPHP/ThinkPHP.php';