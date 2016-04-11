<?php



import('TagLib');

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/6
 * Time: 19:24
 */
class TagLibHd extends TagLib
{


    protected $tags = array(
        'nav'=>array('attr'=>'limit,order','close'=>1),

        'test'=>array('attr'=>'id,name','close'=>0),
        //0是闭合标签，1是非闭合标签
        'userinfo' => array('attr' => 'id', 'close' => 1),
        'maybe'=> array('attr' => 'uid', 'close' => 1)

    );


    public function _nav($attr,$content){

        $attr = $this->parseXmlAttr($attr);
        $str = <<<str
<?php

   \$_nav_cate = M('cate')->order("{$attr['order']}")->limit({$attr['limit']})->select();
      import('Class.Category',APP_PATH);
      \$_nav_cate = Category::unlimitedForLayer(\$cate);
      foreach(\$_nav_cate as \$_nav_cate_v):

?>
str;
         $str .=$content;
        $str .='<?php endforeach;?>';
        return $str;

    }

    public function _test($attr,$contetn){

        $attr = $this->parseXmlAttr($attr);
        $id = $attr['id'];
        $name = $attr['name'];
        $str = '';
        $str .='<?php echo '.$id.';?>';
        return $str;
    }
//<userinfo id="$_SESSION['uid']">
//</userinfo>
    Public function _userinfo ($attr, $content) {
        $attr = $this->parseXmlAttr($attr);
        $id = $attr['id'];
        $str = '';
        $str .= '<?php ';
        $str .= '$where = array("uid" => ' . $id . ');';
        $str .= '$field = array("username", "face80" => "face", "follow", "fans", "weibo", "uid");';
        $str .= '$userinfo = M("userinfo")->where($where)->field($field)->find();';
      //  $str .= 'extract($userinfo);';
        $str .= '?>';
        $str .= $content;

        return $str;
    }


    Public function _maybe ($attr, $content) {
        $attr = $this->parseXmlAttr($attr);
        $uid = $attr['uid'];
        $str = '';
        $str .= '<?php ';
        $str .= '$uid = ' . $uid . ';';
        $str .= '$db = M("follow");';
        $str .= '$where = array("fans" => $uid);';
        $str .= '$follow = $db->where($where)->field("follow")->select();';
        $str .= 'foreach ($follow as $k => $v) :';
        $str .= '$follow[$k] = $v["follow"];';
        $str .= 'endforeach;';
        $str .= '$sql = "SELECT `uid`,`username`,`face50` AS `face`,COUNT(f.`follow`) AS `count` FROM `hd_follow` f LEFT JOIN `hd_userinfo` u ON f.`follow` = u.`uid` WHERE f.`fans` IN (" . implode(\',\', $follow) . ") AND f.`follow` NOT IN (" . implode(\',\',$follow) . ") AND f.`follow` <>" . $uid . " GROUP BY f.`follow` ORDER BY `count` DESC LIMIT 4";';
        $str .= '$friend = $db->query($sql);';
        $str .= 'foreach ($friend as $v) :';
        $str .= 'extract($v);?>';
        $str .= $content;
        $str .= '<?php endforeach;?>';

        return $str;
    }



}