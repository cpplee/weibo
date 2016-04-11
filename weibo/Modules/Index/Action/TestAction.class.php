<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/19
 * Time: 15:23
 */
class TestAction extends Action
{



    public function index(){




        p('Test控制器下index方法...');

       $memcache = new memcache();

        $memcache->connect('127.0.0.1',11211);


        $memcache->set('webname','测试');

        p($memcache->get('webname'));
        p($memcache->get('test'));
        $this->display();
    }


}