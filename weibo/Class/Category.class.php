<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/5
 * Time: 14:35
 */
class Category
{

    Static Public function unlimitForLevel($cate,$html='--',$pid=0,$level=0){

        $arr = array();
        foreach($cate as $v){

            if($v['pid']==$pid){
                $v['level']=$level+1;
                $v['html']=str_repeat($html,$level);
                $arr[]=$v;
                $arr = array_merge($arr,self::unlimitForLevel($cate,$html,$v['id'],$v['level']));
            }

        }

        return $arr;

    }


    Static public function unlimitedForLayer($cate,$name='child',$html='--',$pid=0,$level=0){

        $arr = array();
        foreach($cate as $v){
            if($v['pid']==$pid){
                $v['level']=$level+1;
                $v['html']=str_repeat($html,$level);
                $v[$name]=self::unlimitedForLayer($cate,$name='child',$html='--',$v['id'],$level+1);
                $arr[]=$v;
            }
        }

        return $arr;
    }

    Static public function getParents($cate,$id){

        $arr =array();
        foreach($cate as $v){

            if($v['id']==$id){
                $arr[]=$v;
                $arr=array_merge(self::getParents($cate,$v['pid']),$arr);
            }
        }
       return $arr;
    }

    static public function getChilds($cate,$id){
        $arr = array();
        foreach($cate as $v){
            if($v['pid']==$id){
                $arr[] = $v;
                $arr=array_merge(self::getChilds($cate,$v['id']),$arr);

            }

        }
       return $arr;
    }
    static public function getChildsId($cate,$id){
        $arr = array();
        foreach($cate as $v){
            if($v['pid']==$id){
                $arr[] = $v['id'];
                $arr=array_merge(self::getChildsId($cate,$v['id']),$arr);

            }

        }
        return $arr;
    }

}