<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/20
 * Time: 21:51
 */
class LoginAction extends Action
{


    public function index(){

        $this->display();
    }



    public function login(){


        if(!IS_POST){

            $this->error('非法提交...');
        }


        if(!isset($_POST['submit'])){


            return false;
        }

        if($_SESSION['verify']!=$_POST['verify']){

            $this->error('验证码错误');
        }

        $name = $this->_post('uname');
        $pwd  = $this->_post('pwd','md5');
        $db= M('admin');
        $user = $db->where(array('username'=>$name))->find();

        if(!$user || $user['password']!=$pwd){
            $this->error('账号或密码错误');
        }

        if($user['lock']){
            $this->error('账号被锁定');
        }

        $data = array(
            'id'=>$user['id'],
            'logintime'=>time(),
            'loginip'=>get_client_ip()
        );

       $db->save($data);

        session('uid',$user['id']);
        session('username',$user['username']);
        session('logintime',date('Y-m-d H:i:s',$user['logintime']));
        session('now',date('Y-m-d H:i:s',time()));
        session('loginip',$user['loginip']);
        session('admin',$user['admin']);

        $this->success('正在登录...',U(GROUP_NAME.'/Index/index'));
    }


    public function verify(){

        import('Class.Image',APP_PATH);

        Image::verify(1);


    }


}