<?php
return array(
    //'配置项'=>'配置值'
    'TMPL_L_DELIM' => '{{',            // 模板引擎普通标签开始标记
    'TMPL_R_DELIM' => '}}',            // 模板引擎普通标签结束标记
//'配置项'=>'配置值'
    'URL_MODEL' => 0,       // URL访问模式,可选参数0、1、2、3,代表以下四种模式：

    /* 数据库设置 */
    'DB_TYPE' => 'mysql',     // 数据库类型
    'DB_HOST' => '127.0.0.1', // 服务器地址
    'DB_NAME' => 'mallweb',          // 数据库名
    'DB_USER' => 'root',      // 用户名
    'DB_PWD' => '',          // 密码
    'DB_PORT' => '',        // 端口
    'DB_PREFIX' => '',    // 数据库表前缀
    'DB_PARAMS' => array(), // 数据库连接参数
    'DB_DEBUG' => TRUE, // 数据库调试模式 开启后可以记录SQL日志
    'DB_FIELDS_CACHE' => false,        // 启用字段缓存
    'DB_CHARSET' => 'utf8',      // 数据库编码默认采用utf8
    'DB_DEPLOY_TYPE' => 0, // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
    'DB_RW_SEPARATE' => false,       // 数据库读写是否分离 主从式有效
    'DB_MASTER_NUM' => 1, // 读写分离后 主服务器数量
    'DB_SLAVE_NO' => '', // 指定从服务器序号

    //分页设置
    'LISTROWS' => 5, // 列表每页显示行数
    // 分页显示定制
    'PAGE_CONFIG' => array(
        'header' => '<span class="rows">共 %TOTAL_ROW% 条记录</span>',
        'prev' => '<<',
        'next' => '>>',
        'first' => '1...',
        'last' => '...%TOTAL_PAGE%',
        'theme' => '%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%',
    ),
    'UPLOAD_CONFIG' => array(
        'maxSize' => 0,// 设置附件上传大小
        'exts' => array('jpg', 'gif', 'png', 'jpeg'),// 设置附件上传类型
        'rootPath' => 'Public/Uploads/', // 设置附件上传根目录
        'savePath' => 'img/',// 设置附件上传（子）目录
        'mimes' => array('image/png', 'image/gif', 'image/jpg', 'image/jpeg'), //允许上传的文件MiMe类型
        //'driver'        =>  'Qiniu', // 文件上传驱动
        'driverConfig' => array(
            'secretKey' => 'l9FRyKHG9J0DXoMLzkZqMHv5VMn9p5KNc0KXxjNe', //七牛服务器
            'accessKey' => 'vtt_WAhFfoie3-5tKUYG0fIe3HX4jfZemj2G7NrF', //七牛用户
            'domain' => '7xpyb8.com1.z0.glb.clouddn.com', //七牛密码
            'bucket' => 'woddp', //空间名称
            'timeout' => 300, //超时时间
        ), // 上传驱动配置
    ),

    //验证码长度
    'VERIFY_CODE_LENGTH' => array(
        'length' => 4,
        'fontSize' => 40,              // 验证码字体大小(px)
        'useCurve' => false,            // 是否画混淆曲线
        'useNoise' => false,            // 是否添加杂点
    ),

    //阿里大鱼 短信服务配置
    'ALIDAYU' => array(
        'appkey' => '23400196',
        'secret' => 'ea5938021da3077baa594c52e99e4fe5',
    ),


    //后台地址
    'ADMIN' => 'http://admin.mallweb.com/',

    //静态缓存
    'HTML_CACHE_ON' => false, // 开启静态缓存
    'HTML_CACHE_TIME' => 60,   // 全局静态缓存有效期（秒）
    'HTML_FILE_SUFFIX' => '.shtml', // 设置静态缓存文件后缀
    // 定义静态缓存规则
    'HTML_CACHE_RULES' => array(
        //主页缓存
        'Index:index' => array('{:controller}_{:action}', 60),
        //商品页面缓存
        'Index:goods' => array('{:controller}_{:action}id_{id}', 60),
    ),

    //session 存入redis
    //Redis Session配置
//    'SESSION_AUTO_START' => false,    // 是否自动开启Session
//    'SESSION_TYPE' => 'Redis',    //session类型
//    'SESSION_PERSISTENT' => 1,        //是否长连接(对于php来说0和1都一样)
//    'SESSION_CACHE_TIME' => 1,        //连接超时时间(秒)
//    'SESSION_EXPIRE' => 0,        //session有效期(单位:秒) 0表示永久缓存
//    'SESSION_PREFIX' => 'sess_',        //session前缀
//    'SESSION_REDIS_HOST' => '127.0.0.1', //分布式Redis,默认第一个为主服务器
//    'SESSION_REDIS_PORT' => '6379',           //端口,如果相同只填一个,用英文逗号分隔
//    'SESSION_REDIS_AUTH' => '',    //Redis auth认证(密钥中不能有逗号),如果相同只填一个,用英文逗号分隔

    //数据缓存存入redis
//    'DATA_CACHE_TYPE' => 'Redis',//数据缓存机制
//    'REDIS_HOST' => '127.0.0.1',//Redis服务器地址
//    'REDIS_PORT' => '6379',//Redis端口


    //点击量使用mysql或redis存储
    'CLICK_NUM'=>false, //true 是redis存储

    //cookie的一个bug
    'COOKIE_PREFIX'         =>  'w',      // Cookie前缀 避免冲突

);