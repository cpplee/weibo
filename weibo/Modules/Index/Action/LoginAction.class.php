<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/7
 * Time: 20:01
 */
class LoginAction extends Action
{


    public function index(){


        $this->display();
    }


    public function register(){
        if (!C('REGIS_ON')) {
            $this->error('网站暂停注册', U('index'));
        }
        $this->display('register');
    }


    public function runRegis(){

        if(!IS_POST) $this->error('非法提交');

       if($_SESSION['verify']!=I('verify','','strtolower')){
           $this->error('验证码错误');
       }
        if($_POST['pwd']!=$_POST['pwded']){
            $this->error('两次密码不一致');
        }

        $data = array(
            'account'=>I('account','','htmlspecialchars'),
            'password'=>I('pwd','','htmlspecialchars,md5'),
            'registime'=>$_SERVER['REQUEST_TIME'],
            'userinfo'=>array(
                'username'=>$this->_post('uname'),
            )
        );

       $id = D('UserRelation')->insert($data);

        if($id){
            session('uid',$id);
            $this->success('注册成功,正在为您跳转...',U(GROUP_NAME.'/Index/index'));
        }else{
            $this->error('注册失败,请重试...');
        }

        

    }



    public function verify(){

        import('Class.Image',APP_PATH);
        Image::verify();

    }

    public function checkAccount(){

        if(!IS_AJAX) $this->error('非法访问');
       $account = I('account','','htmlspecialchars');
        $where = array('account'=>$account);
        if(M('user')->where($where)->getField('id')){
            echo 'false';

        }else{
            echo 'true';
        }

    }


    public function checkUname(){


        if(!IS_AJAX) $this->error('禁止非法访问');
        $username = I('uname','','htmlspecialchars');
        $where = array('username'=>$username);

        if(M('userinfo')->where($where)->getField('username')){


            echo 'false';
        }else{
            echo  'true';
        }

    }


    public function checkVerify(){

        if(!IS_AJAX) $this->error('非法登录');

        $verify =I('verify');
        if($_SESSION['verify']!=$verify){
            echo 'false';
        }else{
            echo 'true';
        }
    }

    public function login(){

       if(!IS_POST) $this->error('禁止非法登录',U(GROUP_NAME.'/Login/index'));


        $account = $this->_post('account');
        $pwd = $this->_post('pwd','md5');


        $where = array('account'=>$account);
        $user = M('user')->where($where)->find();
        p($user);
        if(!$user || $user['password']!=$pwd){
            $this->error('用户名或者密码不正确');
        }
        if($user['lock']){
            $this->error('用户被锁定');
        }


        //处理下一次自动登录

        if(isset($_POST['auto'])){

            $account =$user['account'];
            $ip = get_client_ip();
            $value = $account.'|'.$ip;
             $value=encryption($value);
          //  $value = encryption($value,1);

            @setcookie('auto',$value,C('AUTO_LOGIN_TIME'),'/');

        }








        session('uid',$user['id']);

        redirect(__APP__,3,'登录成功,正在为您跳转');

    }










}