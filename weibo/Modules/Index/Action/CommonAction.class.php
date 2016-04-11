<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/8
 * Time: 15:34
 */
class CommonAction extends Action
{


    public function _initialize(){



        if(isset($_COOKIE['auto']) && !isset($_SESSION['uid'])){

            $value = encryption($_COOKIE['auto'],1);

           $value =explode('|',encryption($_COOKIE['auto'],1));

          // p($value);
            $ip = get_client_ip();
           // p($ip);

            if($ip == $value[1]){
                $account = $value[0];
                $where = array('account'=>$account);
                $user = M('user')->where($where)->field(array('id','lock'))->find();

                if($user && !$user['lock']){
                   session('uid',$user['id']);
               }
            }



        }

       if(!isset($_SESSION['uid'])){

           $this->error('非法登录!',U(GROUP_NAME.'/Login/index'));

       }

    }

    public function uploadFace(){
        if (!$this->isPost()) {
            halt('页面不存在');
        }
        $upload = $this->_upload('Face', '180,80,50', '180,80,50');
        echo json_encode($upload);
    }

    public function uploadPic(){

        if(!IS_POST)$this->error('非法操作');

        $upload = $this->_upload('Pic','800,300,120','800,300,120');
        echo json_encode($upload);

    }

    public function _upload($path, $width, $height){

        import('ORG.Net.UploadFile');	//引入ThinkPHP文件上传类
        $obj = new UploadFile();	//实例化上传类
        $obj->maxSize = C('UPLOAD_MAX_SIZE');	//图片最大上传大小
        $obj->savePath = C('UPLOAD_PATH') . $path . '/';	//图片保存路径
        $obj->saveRule = 'uniqid';	//保存文件名
        $obj->uploadReplace = true;	//覆盖同名文件
        $obj->allowExts = C('UPLOAD_EXTS');	//允许上传文件的后缀名
        $obj->thumb = true;	//生成缩略图
        $obj->thumbMaxWidth = $width;	//缩略图宽度
        $obj->thumbMaxHeight = $height;	//缩略图高度
        $obj->thumbPrefix = 'max_,medium_,mini_';	//缩略图后缀名
        $obj->thumbPath = $obj->savePath . date('Y_m') . '/';	//缩略图保存图径
        $obj->thumbRemoveOrigin = true;	//删除原图
        $obj->autoSub = true;	//使用子目录保存文件
        $obj->subType = 'date';	//使用日期为子目录名称
        $obj->dateFormat = 'Y_m';	//使用 年_月 形式

        if (!$obj->upload()) {
            return array('status' => 0, 'msg' => $obj->getErrorMsg());
        } else {
            $info = $obj->getUploadFileInfo();
            $pic = explode('/', $info[0]['savename']);
            return array(
                'status' => 1,
                'path' => array(
                    'max' => $pic[0] . '/max_' . $pic[1],
                    'medium' => $pic[0] . '/medium_' . $pic[1],
                    'mini' => $pic[0] . '/mini_' . $pic[1]
                )
            );
        }



    }


    public function addGroup(){


        if(!IS_AJAX) $this->error('非法提交!');

        $data = array(
            'name'=>$this->_post('name'),
            'uid'=>session('uid')
        );

        if(M('group')->data($data)->add()){

            echo json_encode(array('status'=>1,'msg'=>'创建分组成功'));
        }else{
            echo json_encode(array('status'=>0,'msg'=>'创建分组失败...请重试...'));
        }


    }


    public function addFollow(){


        if(!IS_AJAX){
            $this->error('非法操作!');
        }
        $data = array(
            'follow'=>$this->_post('follow','intval'),
            'fans'=>(int)session('uid'),
            'gid'=>$this->_post('gid','intval'),
        );

        if(M('follow')->data($data)->add()){

           $db = M('userinfo');
            $db->where(array('uid'=>$data['follow']))->setInc('fans');
            $db->where(array('uid'=>session('uid')))->setInc('follow');
            echo json_encode(array('status'=>1,'msg'=>'关注成功'));

        }else{
            echo json_encode(array('status'=>0,'msg'=>'关注失败...'));
        }


    }


    public function delFollow(){

        if(!IS_AJAX){
            $this->error('非法提交');
        }

        $uid = $this->_post('uid', 'intval');
        $type = $this->_post('type', 'intval');

        $where = $type ? array('follow' => $uid, 'fans' => session('uid')) : array('fans' => $uid, 'follow' => session('uid'));

        if (M('follow')->where($where)->delete()) {
            $db = M('userinfo');

            if ($type) {
                $db->where(array('uid' => session('uid')))->setDec('follow');
                $db->where(array('uid' => $uid))->setDec('fans');
            } else {
                $db->where(array('uid' => session('uid')))->setDec('fans');
                $db->where(array('uid' => $uid))->setDec('follow');
            }

            echo 1;
        } else {
            echo 0;
        }


    }


    public function editStyle(){

        if(!IS_AJAX) $this->error('非法提交!');

        $style = $this->_post('style');
        $where = array('uid' => session('uid'));

        if (M('userinfo')->where($where)->save(array('style' => $style))) {
            echo 1;
        } else {
            echo 0;
        }

    }




    /**
     * 异步轮询推送消息
     */
    Public function getMsg () {
        if (!$this->isAjax()) {
            halt('页面不存在');
        }

        $uid = session('uid');
        $msg = S('usermsg' . $uid);

        if ($msg) {
            if ($msg['comment']['status']) {
                $msg['comment']['status'] = 0;
                S('usermsg' . $uid, $msg, 0);
                echo json_encode(array(
                    'status' => 1,
                    'total' => $msg['comment']['total'],
                    'type' => 1
                ));
                exit();
            }

            if ($msg['letter']['status']) {
                $msg['letter']['status'] = 0;
                S('usermsg' . $uid, $msg, 0);
                echo json_encode(array(
                    'status' => 1,
                    'total' => $msg['letter']['total'],
                    'type' => 2
                ));
                exit();
            }

            if ($msg['atme']['status']) {
                $msg['atme']['status'] = 0;
                S('usermsg' . $uid, $msg, 0);
                echo json_encode(array(
                    'status' => 1,
                    'total' => $msg['atme']['total'],
                    'type' => 3
                ));
                exit();
            }
        }
        echo json_encode(array('status' => 0));
    }




}