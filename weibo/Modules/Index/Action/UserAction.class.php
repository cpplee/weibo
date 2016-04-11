<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/12
 * Time: 13:27
 */
class UserAction extends CommonAction
{



    public function index(){

        $id = I('uid','','intval');

//        p($_GET);

        //读取用户个人信息
        $where = array('uid' => $id);
        $userinfo = M('userinfo')->where($where)->find();

     //   p($userinfo);
        if (!$userinfo) {
            header('Content-Type:text/html;Charset=UTF-8');
            redirect(__ROOT__, 3, '用户不存在，正在为您跳转至首页...');
            exit();
        }

        $this->userinfo = $userinfo;
//导入分页处理页
        import('ORG.Util.Page');

        //统计分页
        $where = array('uid' => $id);
        $count = M('weibo')->where($where)->count();
        $page = new Page($count, 5);
        $limit = $page->firstRow . ',' . $page->listRows;

        //读取用户发布的微博
        $this->page = $page->show();
        $this->weibo = D('WeiboView')->getAll($where, $limit);




        if(S('follow_'.$id)){

            $follow = S('follow_'.$id);

        }else{

            $where = array('fans' => $id);
            $follow = M('follow')->where($where)->field('follow')->select();
            foreach ($follow as $k => $v) {
                $follow[$k] = $v['follow'];
            }
            $where = array('uid' => array('IN', $follow));
            $field = array('username', 'face50' => 'face', 'uid');
            $follow = M('userinfo')->field($field)->where($where)->limit(8)->select();
            S('follow_'.$id,$follow,10);

        }


        //我的粉丝
        if (S('fans_' . $id)) {
            //缓存已成功并且缓存未过期
            $fans = S('fans_' . $id);
        } else {
            //生成缓存
            $where = array('follow' => $id);
            $fans = M('follow')->where($where)->field('fans')->select();
            foreach ($fans as $k => $v) {
                $fans[$k] = $v['fans'];
            }
            $where = array('uid' => array('IN', $fans));
            $field = array('username', 'face50' => 'face', 'uid');
            $fans = M('userinfo')->field($field)->where($where)->limit(8)->select();

            S('fans_' . $id, $fans, 3600);
        }

        $this->follow = $follow;
        $this->fans = $fans;

        $this->display();

    }



  public function followList(){

    // p($_GET);
      $uid = I('uid');

      $uid = $this->_get('uid', 'intval');

      //区分关注 与 粉丝(1：关注，2：粉丝)
      $type = $this->_get('type', 'intval');

      //导入分页类
      import('ORG.Util.Page');
      $db = M('follow');

      //根据type参数不同，读取用户关注与粉丝ID
      $where = $type ? array('fans' => $uid) : array('follow' => $uid);
      $field = $type ? 'follow' : 'fans';

      $count = $db->where($where)->count();
      $page = new Page($count, 20);
      $limit = $page->firstRow . ',' . $page->listRows;

      $uids = $db->field($field)->where($where)->limit($limit)->select();

      if ($uids) {
          //把用户关注或者粉丝ID重组为一维数组
          foreach ($uids as $k => $v) {
              $uids[$k] = $type ? $v['follow'] : $v['fans'];
          }

          //提取用户个人信息
          $where = array('uid' => array('IN', $uids));
          $field = array('face50' => 'face', 'username', 'sex', 'location', 'follow', 'fans', 'weibo', 'uid');

          $users =M('userinfo')->where($where)->field($field)->select();


          //分配置用户信息到视图
          $this->users = $users;
      }

      $where = array('fans' => session('uid'));
      $follow = $db->field('follow')->where($where)->select();

      if ($follow) {
          foreach ($follow as $k => $v) {
              $follow[$k] = $v['follow'];
          }
      }

      $where = array('follow' => session('uid'));
      $fans = $db->field('fans')->where($where)->select();

      if ($fans) {
          foreach ($fans as $k => $v) {
              $fans[$k] = $v['fans'];
          }
      }

      $this->type = $type;
      $this->count = $count;
      $this->follow = $follow;
      $this->fans = $fans;
      $this->display();

  }


   public function cancelKeep(){

       if(!IS_AJAX){
           $this->error('非法提交');
       }

       $kid = $this->_post('kid', 'intval');
       $wid = $this->_post('wid', 'intval');

       if (M('keep')->delete($kid)) {
           M('weibo')->where(array('id' => $wid))->setDec('keep');

           echo 1;
       } else {
           echo 0;
       }
   }

    public function letter(){

        $uid = session('uid');

        set_msg($uid, 2, true);

        import('ORG.Util.Page');
        $count = M('letter')->where(array('uid' => $uid))->count();
        $page = new Page($count, 20);
        $limit = $page->firstRow . ',' . $page->listRows;

        $where = array('letter.uid' => $uid);
        $letter = D('LetterView')->where($where)->order('time DESC')->limit($limit)->select();

        $this->letter = $letter;
        $this->count = $count;
        $this->page = $page->show();



        $this->display('letter');


    }

    Public function delLetter () {
        if (!$this->isAjax()) {
            halt('页面不存在');
        }

        $lid = $this->_post('lid', 'intval');

        if (M('letter')->delete($lid)) {
            echo 1;
        } else {
            echo 0;
        }

    }

    public function letterSend(){

        if(!IS_POST){
           $this->error('非法提交');
        }
        $name = $this->_post('name');
        $where = array('username' => $name);
        $uid = M('userinfo')->where($where)->getField('uid');

        if (!$uid) {
            $this->error('用户不存在');
        }

        $data = array(
            'from' => session('uid'),
            'content' => $this->_post('content'),
            'time' => time(),
            'uid' => $uid
        );

        if (M('letter')->data($data)->add()) {

             set_msg($uid,2);

            $this->success('私信已发送', U('letter'));
        } else {
            $this->error('发送失败请重试...');
        }
    }




public function keepAll(){

    import('ORG.Util.Page');
    $uid  = session('uid');

    $count = M('keep')->where(array('uid' => $uid))->count();
    $page = new Page($count, 20);
    $limit = $page->firstRow . ',' . $page->listRows;

    $where = array('keep.uid' => $uid);
    $weibo = D('KeepView')->getAll($where, $limit);
    // p($weibo);die;
    $this->weibo = $weibo;
    $this->page = $page->show();

    $this->display('weiboList');
}



    public function comment(){


        import('ORG.Util.Page');
        set_msg(session('uid'), 1, true);
        $where = array('uid' => session('uid'));
        $count = M('comment')->where($where)->count();
        $page = new Page($count, 20);
        $limit = $page->firstRow . ',' . $page->listRows;

        $comment = D('CommentView')->where($where)->limit($limit)->order('time DESC')->select();
        $this->count = $count;
        $this->page = $page->show();
        $this->comment = $comment;



        $this->display('comment');
    }

    Public function delComment () {
        if (!$this->isAjax()) {
            halt('页面不存在');
        }
        $cid = $this->_post('cid', 'intval');
        $wid = $this->_post('wid', 'intval');

        if (M('comment')->delete($cid)) {
            M('weibo')->where(array('id' => $wid))->setDec('comment');
            echo 1;
        } else {
            echo 0;
        }
    }

    Public function reply () {
        if (!$this->isAjax()) {
            halt('页面不存在');
        }

        $data = array(
            'content' => $this->_post('content'),
            'time' => time(),
            'uid' => session('uid'),
            'wid' => $this->_post('wid', 'intval')
        );

        if ($wid=M('comment')->data($data)->add()) {
            M('weibo')->where(array('id' => $wid))->setInc('comment');
            echo 1;
        } else {
            echo 0;
        }
    }
    Public function atme () {
        set_msg(session('uid'), 3, true);

        $where = array('uid' => session('uid'));
        $wid = M('atme')->where($where)->field('wid')->select();

        if ($wid) {
            foreach ($wid as $k => $v) {
                $wid[$k] = $v['wid'];
            }
        }

        import('ORG.Util.Page');
        $count = count($wid);
        $page = new Page($count, 20);
        $limit = $page->firstRow . ',' . $page->listRows;

        $where = array('id' => array('IN', $wid));
        $weibo = D('WeiboView')->getAll($where, $limit);

        $this->weibo = $weibo;
        $this->page = $page->show();
        $this->atme = 1;
        $this->display('weiboList');
    }





    public function _empty($name){

         $uname=htmlspecialchars($name);
         if($uid=M('userinfo')->where(array('username'=>$uname))->getField('uid')){
             redirect(U(GROUP_NAME.'/User/index',array('uid'=>$uid)));
         }else{
             redirect(U(GROUP_NAME.'/Index/index'));
         }
    }

}