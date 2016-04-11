<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/8
 * Time: 0:12
 */
class UserRelationModel extends RelationModel
{


    protected  $tableName = 'user';

    protected $_link = array(

        'userinfo'=>array(
            'mapping_type'=>HAS_ONE,
            'foreign_key'=>'uid',
        )


    );



    public function insert($data=NULL){

        $data = is_null($data)?$_POST:$data;

        return $this->relation(true)->data($data)->add();

    }



}