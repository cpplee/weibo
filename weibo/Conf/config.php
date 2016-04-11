<?php
return array(

    /* 数据库设置 */
    'DB_TYPE'               => 'mysql',     // 数据库类型
    'DB_HOST'               => 'localhost', // 服务器地址
    'DB_NAME'               => 'weibo',          // 数据库名
    'DB_USER'               => 'root',      // 用户名
    'DB_PWD'                => '',          // 密码
    'DB_PORT'               => '',        // 端口
    'DB_PREFIX'             => 'hd_',    // 数据库表前缀
    'DB_FIELDTYPE_CHECK'    => false,       // 是否进行字段类型检查
    'DB_FIELDS_CACHE'       => true,        // 启用字段缓存
    'DB_CHARSET'            => 'utf8',      // 数据库编码默认采用utf8
    'DB_DEPLOY_TYPE'        => 0, // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
    'DB_RW_SEPARATE'        => false,       // 数据库读写是否分离 主从式有效
    'DB_MASTER_NUM'         => 1, // 读写分离后 主服务器数量
    'DB_SLAVE_NO'           => '', // 指定从服务器序号
    'DB_SQL_BUILD_CACHE'    => false, // 数据库查询的SQL创建缓存
    'DB_SQL_BUILD_QUEUE'    => 'file',   // SQL缓存队列的缓存方式 支持 file xcache和apc
    'DB_SQL_BUILD_LENGTH'   => 20, // SQL缓存的队列长度
    'DB_SQL_LOG'            => false, // SQL执行日志记录



    //'DEFAULT_THEME'=>'default',


    //'配置项'=>'配置值'
    'APP_GROUP_LIST'=>'Index,Admin',
    'DEFAULT_GROUP'=>'Index',
    'APP_GROUP_MODE'=>1,
    'APP_GROUP_PATH'=>'Modules',

    'LOAD_EXT_CONFIG'=>'verify,water,system',

     'ENCRYPTION_KEY'=>'www.houdunwang.com',

    'AUTO_LOGIN_TIME'=>time()+3600*24*7,
//图片上传
    'UPLOAD_MAX_SIZE' => 2000000,	//最大上传大小
    'UPLOAD_PATH' => './Uploads/',	//文件上传保存路径
    'UPLOAD_EXTS' => array('jpg', 'jpeg', 'gif', 'png'),	//允许上传文件的后缀


   // 'URL_MODEL'=>2,
    'URL_ROUTER_ON'=>true,
    'URL_ROUTE_RULES'=>array(
        ':uid\d'=>'Index/User/index',
        'follow/:uid\d'=> array('Index/User/followList','type=1'),
        'fans/:uid\d'=> array('Index/User/followList','type=0'),
    ),




    'DATA_CACHE_SUBDIR'=>true, //开启哈希形式生成缓存目录
      'DATA_PATH_LEVEL'=>2,
    'DATA_CACHE_TYPE' =>'Memcache',
    'MEMCACHE_HOST'=>'127.0.0.1',
    'MEMCACHE_PORT' =>11211,





);
?>