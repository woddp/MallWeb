<?php
return array(
	//'配置项'=>'配置值'
    'TMPL_L_DELIM'          =>  '{{',            // 模板引擎普通标签开始标记
    'TMPL_R_DELIM'          =>  '}}',            // 模板引擎普通标签结束标记

    //根目录地址
    'ROOT' =>__ROOT__,
    /* 数据库设置 */
    'DB_TYPE'               =>  'mysql',     // 数据库类型
    'DB_HOST'               =>  '127.0.0.1', // 服务器地址
    'DB_NAME'               =>  'mallweb',          // 数据库名
    'DB_USER'               =>  'root',      // 用户名
    'DB_PWD'                =>  '',          // 密码
    'DB_PORT'               =>  '',        // 端口
    'DB_PREFIX'             =>  '',    // 数据库表前缀
    'DB_PARAMS'          	=>  array(), // 数据库连接参数
    'DB_DEBUG'  			=>  TRUE, // 数据库调试模式 开启后可以记录SQL日志
    'DB_FIELDS_CACHE'       =>  false,        // 启用字段缓存
    'DB_CHARSET'            =>  'utf8',      // 数据库编码默认采用utf8
    'DB_DEPLOY_TYPE'        =>  0, // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
    'DB_RW_SEPARATE'        =>  false,       // 数据库读写是否分离 主从式有效
    'DB_MASTER_NUM'         =>  1, // 读写分离后 主服务器数量
    'DB_SLAVE_NO'           =>  '', // 指定从服务器序号

    //分页设置
    'LISTROWS'=>5, // 列表每页显示行数
    // 分页显示定制
    'PAGE_CONFIG'=> array(
        'header' => '<span class="rows">共 %TOTAL_ROW% 条记录</span>',
        'prev'   => '<<',
        'next'   => '>>',
        'first'  => '1...',
        'last'   => '...%TOTAL_PAGE%',
        'theme'  => '%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%',
    ),
    'UPLOAD_CONFIG'=>array(
        'maxSize'   =>  0 ,// 设置附件上传大小
        'exts'      =>    array('jpg', 'gif', 'png', 'jpeg'),// 设置附件上传类型
        'rootPath'  =>     'Public/Uploads/', // 设置附件上传根目录
        'savePath'  =>      'img/',// 设置附件上传（子）目录
        'mimes'         =>  array('image/png','image/gif','image/jpg','image/jpeg'), //允许上传的文件MiMe类型
        'driver'        =>  'Qiniu', // 文件上传驱动
        'driverConfig'  =>  array(
            'secretKey'      => 'l9FRyKHG9J0DXoMLzkZqMHv5VMn9p5KNc0KXxjNe', //七牛服务器
            'accessKey'      => 'vtt_WAhFfoie3-5tKUYG0fIe3HX4jfZemj2G7NrF', //七牛用户
            'domain'         => '7xpyb8.com1.z0.glb.clouddn.com', //七牛密码
            'bucket'         => 'woddp', //空间名称
            'timeout'        => 300, //超时时间
        ), // 上传驱动配置

    ),



);