<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/6
 * Time: 21:16
 */
class NewWidget extends Widget
{


    public function render($data){

        $limit = $data['limit'];

        $news = M('blog')->order('time DESC')->limit($limit)->select();
       $data['news']=$news;
        return $this->renderFile('',$data);
    }

}