<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/9
 * Time: 20:32
 */
class SearchAction extends CommonAction
{


    public function sechUser(){

          $keyword = $this->_getkeyword();

        if($keyword){
            //检索出除自己外昵称含有关键字的用户

            import('ORG.Util.Page');
            $where = array(
                'username'=>array('LIKE','%'.$keyword.'%'),
                'uid'=>array('NEQ',$_SESSION['uid']),
            );

            $db = M('userinfo');
            $result = $db->where($where)->select();
           $this->result=$result;
            $count = $db->where($where)->count('id');

            $page = new Page($count,20);

            $limit  = $page->firstRow.','.$page->listRows;
            $result = $db->where($where)->limit($limit)->select();



            $result = $this->_getMutual($result);


            $this->result= $result?$result:false;

            $this->assign('page',$page->show());


        }
      $this->assign('keyword',$keyword);
        $this->display();

    }




    Public function sechWeibo () {
        $keyword = $this->_getKeyword();

        if ($keyword) {
            //检索含有关键字的微博
            $where = array('content' => array('LIKE', '%' . $keyword . '%'));

            $db = D('WeiboView');

            //导入分页类
            import('ORG.Util.Page');
            $count = M('weibo')->where($where)->count('id');
            $page = new Page($count, 20);
            $limit = $page->firstRow . ',' . $page->listRows;
            $weibo = $db->getAll($where, $limit);

            $this->weibo = $weibo ? $weibo : false;
            //页码
            $this->page = $page->show();
        }

        $this->keyword = $keyword;
        $this->display();
    }


    private function _getMutual($result){

       // p($result);
      if(!$result) return false;
        $db = M('follow');
        foreach($result as $k=>$v) {

            $sql = '(SELECT `follow` FROM `hd_follow` WHERE `follow`=' . $v['uid'] . ' AND `fans`=' . session('uid') . ')
        UNION (SELECT `follow` FROM `hd_follow` WHERE `follow`=' . session('uid') . ' AND `fans`=' . $v['uid'] . ')';
            $mutual = $db->query($sql);

            if (count($mutual) == 2) {

                $result[$k]['mutual'] = 1;
                $result[$k]['followed']=1;
            } else {
               // p(123);
                $result[$k]['mutual'] = 0;
                //未互相关注时检索是否已关注

                $where = array(
                    'follow'=>$v['uid'],
                    'fans'=>session('uid'),

                );

                $result[$k]['followed']= $db->where($where)->count();

            }
        }
      //  p($result);
        return $result;
    }

    private function _getKeyword(){

        return $_GET['keyword']=='搜索微博、找人'?NULL:$this->_get('keyword');
    }


}