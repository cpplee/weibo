<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/6
 * Time: 18:16
 */

    return array(

        'TMPL_PARSE_STRING'=>array(
            '__PUBLIC__' => __ROOT__. '/' . APP_NAME . '/Modules/' . GROUP_NAME . '/Tpl/Public'
        ),


        'URL_HTML_SUFFIX'=>'',




//        'APP_AUTOLOAD_PATH'=>'@.TagLib',
//        'TAGLIB_BUILD_IN'=>'Cx,Hd',
//静态缓存方式
//        'HTML_CACHE_ON'=>true,
//        'HTML_CACHE_RULES'=>array(
//          'Show:index'=>array(
//              '{:module}_{:action}_{id}',
//              10,
//          ),
//
//        ),
//动态缓存方式
//    'DATA_CACHE_TYPE'=>'Redis',
//       'MEMCACHE_HOST'=>'127.0.0.1',
//        'MEMCACHE_PORT'=>112211
    //    'DATA_CACHE_TYPE'=>'File',

        'TAGLIB_LOAD'=>true,
        'APP_AUTOLOAD_PATH'=>'@.TagLib',
        'TAGLIB_BUILD_IN'=>'Cx,Hd',


    );