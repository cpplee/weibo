<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/4
 * Time: 19:41
 */
class IndexAction extends CommonAction
{



        public function index()
        {


           import('ORG.Util.Page');

            $db=D('WeiboView');

            $uid=array(session('uid'));
            $where = array('fans'=>session('uid'));

            if(isset($_GET['gid'])){
                $gid = $this->_get('gid','intval');
                $where['gid']=$gid;
                $uid = array();
            }



            $result= M('follow')->field('follow')->where($where)->select();

            if($result){
                foreach($result as $v){
                    $uid[] = $v['follow'];
                }
            }
//            p($uid);
    //       p($result);

            $where = array('uid'=>array('IN',$uid));



            $count = $db->where($where)->count();
           $page = new Page($count,8);

            $limit = $page->firstRow.','.$page->listRows;
           //p($result);


            $result = $db->getAll($where,$limit);
            $this->assign('weibo',$result);

           $page->url=GROUP_NAME.'/Index/index/p';
            $this->assign('page',$page->show());
            $this->display();

        }



    public function turn(){

        if(!IS_POST){
            $this->error('非法操作');
        }

        //原微博ID
        $id = $this->_post('id', 'intval');
        $tid = $this->_post('tid', 'intval');
        //转发内容
        $content = $this->_post('content');

        //提取插入数据
        $data = array(
            'content' => $content,
            'isturn' => $tid ? $tid : $id,
            'time' => time(),
            'uid' => session('uid')
        );

        //插入数据至微博表
        $db = M('weibo');
        if ($wid=$db->data($data)->add()) {
            //原微博转发数+1
            $db->where(array('id' => $id))->setInc('turn');
            if ($tid) {
                $db->where(array('id' => $tid))->setInc('turn');
            }

            //用户发布微博数+1
            M('userinfo')->where(array('uid' => session('uid')))->setInc('weibo');

            //处理@用户
            $this->_atmeHandel($data['content'], $wid);
            //如果点击了同时评论插入内容到评论表
            if (isset($_POST['becomment'])) {
                $data = array(
                    'content' => $content,
                    'time' => time(),
                    'uid' => session('uid'),
                    'wid' => $id
                );
                //插入评论数据后给原微博评论次数+1
                if (M('comment')->data($data)->add()) {
                    $db->where(array('id' => $id))->setInc('comment');
                }
            }

            $this->success('转发成功', U(GROUP_NAME.'/Index/index'));
        } else {
            $this->error('转发失败请重试...');
        }

    }

    private function _atmeHandel ($content, $wid) {
        $preg = '/@(\S+?)\s/is';
        preg_match_all($preg, $content, $arr);

        if (!empty($arr[1])) {
            $db = M('userinfo');
            $atme = M('atme');
            foreach ($arr[1] as $v) {
                $uid = $db->where(array('username' => $v))->getField('uid');
                if ($uid) {
                    $data = array(
                        'wid' => $wid,
                        'uid' => $uid
                    );

                    //写入消息推送
                    set_msg($uid, 3);
                    $atme->data($data)->add();
                }
            }
        }
    }




    public function loginOut(){


        session_unset();
         session_destroy();
        @setcookie('auto', '', time() - 3600, '/');
        $this->success('退出成功',U(GROUP_NAME.'/Login/index'));

    }


    public function sendWeibo(){

        if(!IS_POST)$this->error('非法操作');
        $data =array(
            'content'=>I('content',''),
            'time'=>time(),
            'uid'=>session('uid'),
        );
        if($wid= M('weibo')->data($data)->add()){
            if(!empty($_POST['max'])){

                $img=array(
                    'mini'=>I('mini',''),
                    'medium'=>I('medium',''),
                    'max'=>I('max',''),
                    'wid'=>$wid
                );
                M('picture')->data($img)->add();

            }

            M('userinfo')->where(array('uid' => session('uid')))->setInc('weibo');

            //处理@用户
            $this->_atmeHandel($data['content'], $wid);
            $this->success('发布成功');

        }else{

            $this->error('发布失败');

        }
    }


    public function comment(){

        if(!IS_AJAX){
            $this->error('非法提交!');
        }
        //提取评论数据
        $data = array(
            'content' => $this->_post('content'),
            'time' => time(),
            'uid' => session('uid'),
            'wid' => $this->_post('wid', 'intval')
        );

        if (M('comment')->data($data)->add()) {
            //读取评论用户信息
            $field = array('username', 'face50' => 'face', 'uid');
            $where = array('uid' => $data['uid']);
            $user = M('userinfo')->where($where)->field($field)->find();

            //被评论微博的发布者用户名
            $uid = $this->_post('uid', 'intval');
            $username = M('userinfo')->where(array('uid' => $uid))->getField('username');

            $db = M('weibo');
            //评论数+1
            $db->where(array('id' => $data['wid']))->setInc('comment');

            //评论同时转发时处理
            if ($_POST['isturn']) {
                //读取转发微博ID与内容
                $field = array('id', 'content', 'isturn');
                $weibo = $db->field($field)->find($data['wid']);
                $content = $weibo['isturn'] ? $data['content'] . ' // @' . $username . ' : ' . $weibo['content'] : $data['content'];

                //同时转发到微博的数据
                $cons = array(
                    'content' => $content,
                    'isturn' => $weibo['isturn'] ? $weibo['isturn'] : $data['wid'],
                    'time' => $data['time'],
                    'uid' => $data['uid']
                );

                if ($db->data($cons)->add()) {
                    $db->where(array('id' => $weibo['id']))->setInc('turn');
                }

                echo 1;
                exit();
            }

            //组合评论样式字符串返回
            $str = '';
            $str .= '<dl class="comment_content">';
            $str .= '<dt><a href="' . U('/' . $data['uid']) . '">';
            $str .= '<img src="';
            $str .= __ROOT__;
            if ($user['face']) {
                $str .= '/Uploads/Face/' . $user['face'];
            } else {
                $str .= '/Public/Images/noface.gif';
            }
            $str .= '" alt="' . $user['username'] . '" width="30" height="30"/>';
            $str .= '</a></dt><dd>';
            $str .= '<a href="' . U('/' . $data['uid']) . '" class="comment_name">';
            $str .= $user['username'] . '</a> : ' . replace_weibo($data['content']);
            $str .= '&nbsp;&nbsp;( ' . time_format($data['time']) . ' )';
            $str .= '<div class="reply">';
            $str .= '<a href="">回复</a>';
            $str .= '</div></dd></dl>';

            set_msg(session('uid'), 1);
            echo $str;




        } else {
            echo 'false';
        }



    }


        public function getComment () {
            if (!$this->isAjax()) {
                halt('页面不存在');
            }
            $wid = $this->_post('wid', 'intval');
            $where = array('wid' => $wid);

            //数据的总条数
            $count = M('comment')->where($where)->count();
            //数据可分的总页数
            $total = ceil($count / 5);
            $page = isset($_POST['page']) ? $this->_post('page', 'intval') : 1;
            $limit = $page < 2 ? '0,5' : (5 * ($page - 1)) . ',5';

            sleep(1);
            $result = D('CommentView')->where($where)->order('time DESC')->limit($limit)->select();

            if ($result) {
                $str = '';
                foreach ($result as $v) {
                    $str .= '<dl class="comment_content">';
                    $str .= '<dt><a href="' . U('/' . $v['uid']) . '">';
                    $str .= '<img src="';
                    $str .= __ROOT__;
                    if ($v['face']) {
                        $str .= '/Uploads/Face/' . $v['face'];
                    } else {
                        $str .= '/Public/Images/noface.gif';
                    }
                    $str .= '" alt="' . $v['username'] . '" width="30" height="30"/>';
                    $str .= '</a></dt><dd>';
                    $str .= '<a href="' . U('/' . $v['uid']) . '" class="comment_name">';
                    $str .= $v['username'] . '</a> : ' . replace_weibo($v['content']);
                    $str .= '&nbsp;&nbsp;( ' . time_format($v['time']) . ' )';
                    $str .= '<div class="reply">';
                    $str .= '<a href="">回复</a>';
                    $str .= '</div></dd></dl>';
                }

                if ($total > 1) {
                    $str .= '<dl class="comment-page">';

                    switch ($page) {
                        case $page > 1 && $page < $total :
                            $str .= '<dd page="' . ($page - 1) . '" wid="' . $wid . '">上一页</dd>';
                            $str .= '<dd page="' . ($page + 1) . '" wid="' . $wid . '">下一页</dd>';
                            break;

                        case $page < $total :
                            $str .= '<dd page="' . ($page + 1) . '" wid="' . $wid . '">下一页</dd>';
                            break;

                        case $page == $total :
                            $str .= '<dd page="' . ($page - 1) . '" wid="' . $wid . '">上一页</dd>';
                            break;
                    }

                    $str .= '</dl>';
                }

                echo $str;

            } else {
                echo 'false';
            }
        }


    public function keep(){

        if(!IS_AJAX){

            $this->error('非法操作');
        }

       $wid = I('wid','','intval');
        $uid = session('uid');
        $db =M('keep');
        $where = array('wid'=>$wid,'uid'=>$uid);

        if($db->where($where)->getField('id')){
            echo -1;
            exit();
        }

        $data = array(
            'uid'=>$uid,
            'time'=>$_SERVER['REQUEST_TIME'],
            'wid'=>$wid
        );


        if($db->data($data)->add()){
            M('weibo')->where(array('id'=>$wid))->setInc('keep');
            echo 1;
        }else{
            echo 0;
        }


    }

     public function delWeibo () {
        if (!$this->isAjax()) {
            halt('页面不存在');
        }
        //获取删除微博ID
        $wid = $this->_post('wid', 'intval');
        if (M('weibo')->delete($wid)) {
            //如果删除的微博含有图片
            $db = M('picture');
            $img = $db->where(array('wid' => $wid))->find();

            //对图片表记录进行删除
            if ($img) {
                $db->delete($img['id']);

                //删除图片文件
                @unlink('./Uploads/Pic/' . $img['mini']);
                @unlink('./Uploads/Pic/' . $img['medium']);
                @unlink('./Uploads/Pic/' . $img['max']);
            }
            M('userinfo')->where(array('uid' => session('uid')))->setDec('weibo');
            M('comment')->where(array('wid' => $wid))->delete();

            echo 1;
        } else {
            echo 0;
        }
    }



}