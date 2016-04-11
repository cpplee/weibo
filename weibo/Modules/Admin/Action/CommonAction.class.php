<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/21
 * Time: 12:01
 */
class CommonAction extends Action
{

    public function _initialize(){

        if(!isset($_SESSION['uid'])||!isset($_SESSION['username'])){

            redirect(U(GROUP_NAME.'/Login/index'));
        }
    }



}