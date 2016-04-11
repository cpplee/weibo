<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/13
 * Time: 17:01
 */
class CommentViewModel extends ViewModel
{


    protected $viewFields=array(
        'comment'=>array(
            'id','content','time',
            '_type'=>'LEFT',
        ),
        'userinfo'=>array(

            'username','face50' =>'face','uid',
            '_on'=>'comment.uid=userinfo.uid',

    ),

    );

}