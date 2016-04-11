<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/6
 * Time: 20:54
 */
class HotWidget extends Widget
{

    public function render($data){

       $blog = M('blog')->order('click DESC')->limit(5)->select();
        $data['blog']=$blog;
      return $this->renderFile('',$data);

    }



}