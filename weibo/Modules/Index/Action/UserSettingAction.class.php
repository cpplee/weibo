<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/9
 * Time: 9:19
 */

class UserSettingAction extends CommonAction
{



    public function index(){

        $where = array('uid'=>$_SESSION['uid']);
       // $field = array('username','truename','sex','location','constellation','intro');
        $user =M('userinfo')->where($where)->find();
        $this->assign('user',$user);

        $this->display();


    }


    public function editBasic(){


        if(!IS_POST){

            halt('页面不存在');

        }

       $data = array(
           'username'=>$this->_post('nickname'),
           'truename'=>$this->_post('truename'),
           'sex'=>I('sex','','intval'),
           'location'=>$this->_post('province').' '.$this->_post('city'),
           'constellation'=>$this->_post('night'),
           'intro'=>$this->_post('intro'),
       );

       $result=M('userinfo')->where(array('uid'=>session('uid')))->data($data)->save();

        if($result){
            $this->success('修改成功!',U('index'));
        }else{
            $this->error('修改失败,请重新填写');
        }


    }

    public function editFace(){
        if(!IS_POST){
            $this->error('页面不存在');
        }

       $db = M('userinfo');

        $where = array('uid'=>session('uid'));
        $field = array('face50','face80','face180');
        $old = $db->where($where)->field($field)->find();
         if($db->where($where)->save($_POST)){

             if(!empty($old['face180'])){
                 @unlink('./Uploads/Face/'.$old['face180']);
                 @unlink('./Uploads/Face/'.$old['face80']);
                 @unlink('./Uploads/Face/'.$old['face50']);
             }
             $this->success('修改成功',U(GROUP_NAME.'/UserSetting/index'));

         }else{

             $this->error('修改失败!');
         }

    }

    public function editPwd(){
        if(!IS_POST){
            $this->error('页面不存在');
        }

        $db = M('user');
        $where = array('id'=>$_SESSION['uid']);
        $old = $db->where($where)->getField('password');

        if($this->_post('old','md5')!=$old){
            $this->error('旧密码错误');
        }


        if($this->_post('new')!=$this->_post('newed')){
            $this->error('两次密码不一致');
        }

        $newPwd = $this->_post('new','md5');
        $data = array(
            'id'=>$_SESSION['uid'],
            'password'=>$newPwd,

        );

        if($db->save($data)){
            $this->success('修改成功',U(GROUP_NAME.'/UserSetting/index'));

        }else{
            $this->error('修改失败');
        }

    }


}