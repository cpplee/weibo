<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/20
 * Time: 20:54
 */
class IndexAction extends CommonAction
{

    public function index(){

        $this->display();

    }

    public function copy(){

        $db = M('user');
        $this->user = $db->count();
        $this->lock = $db->where(array('lock' => 1))->count();

        $db = M('weibo');
        $this->weibo = $db->where(array('isturn' => 0))->count();
        $this->turn = $db->where(array('isturn' => array('GT', 0)))->count();
        $this->comment = M('comment')->count();


        $this->display();

    }








    public function loginOut(){


        session_unset();
        session_destroy();
        redirect(U(GROUP_NAME.'/Login/index'));
    }



    public function test(){

        $this->display();
    }



}