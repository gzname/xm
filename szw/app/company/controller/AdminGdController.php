<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2018 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 小夏 < 449134904@qq.com>
// +----------------------------------------------------------------------
namespace app\company\controller;

use cmf\controller\AdminBaseController;
use think\Db;
use app\company\model\CompanyModel;

class AdminGdController extends AdminBaseController
{

    /**
     * 首页
     */
    public function index()
    {
        return $this->fetch();
    }

    /*工地添加加载*/
    public function gongdiadd()
    {

        return $this->fetch();
    }
    /*工地显示页面*/
    public function gongdi()
    {
        $where=[];
        $list = $this->request->param();
        if ($this->request->isPost()) {
            if(@$list['keyword']!=''){
                $keyword=@$list['keyword'];
                $where['keywords']=array('like','%'.$keyword.'%');
             }
             if ($list['start_time'] and $list['end_time'] !='') {
                $beginThismonth=$list['start_time'];
                $endThismonth=$list['end_time'];
                $where['time']  = array('BETWEEN',array($beginThismonth,$endThismonth));
             }


        }

        $fenlei=db('gongdi')->where($where)->select();
        // print_r(db('gongdi')->getLastSql());
        // print_r($fenlei);
        $this->assign('fenlei',$fenlei);

        return $this->fetch();
    }
    /*工地数据添加*/
    public function addpost()
    {
        if ($this->request->isPost()) {
            $posts = $this->request->param();
            //状态只能设置默认值。未发布、未置顶、未推荐
            $data['status'] = 0;
            $data['is_top']      = 0;
            $data['recommended'] = 0;
            $data['title'] =@$posts['title'];
            $data['keywords'] =@$posts['keywords'];
            $data['excerpt'] = @$posts['excerpt'];
            $data['yus'] = @$posts['yus'];
            $data['content'] = @$posts['content'];
            $data['time'] = @$posts['time'];

            // if (@$posts['photo_urls']) {
            //        foreach ($posts['photo_urls'] as $key => $value) {
            //             $photoUrl = cmf_asset_relative_url($value);
            //             array_push($data['photo_urls'], ["url" => $photoUrl, "name" => $data['photo_names'][$key]]);

            //             $data['photo_urls'] = $photoUrl;
            //             $data['photo_names'] = $data['photo_names'][$key];
            //             $data['url'] = $photoUrl;

            //     }
            // }

           if (@$posts['photo_urls']) {
                $photos=implode(",", $posts['photo_urls']);
                $data['photo_urls'] = $photos;
            }



            if (@$posts['photos']) {
                $photos1=implode(",", $posts['photos']);
                $data['photos'] = $photos1;
            }

            if (@$posts['photo_names']) {
                $names=implode(",", $posts['photo_names']);
                $data['photo_names'] = $names;
            }

            if (@$posts['names']) {
                $names1=implode(",", $posts['names']);
                $data['names'] = $names1;
            }


            if (@$posts['photos2']) {
                $photos2=implode(",", $posts['photos2']);
                $data['photos2'] = $photos2;
            }

            if (@$posts['names2']) {
                $names2=implode(",", $posts['names2']);
                $data['names2'] = $names2;
            }

            if (@$posts['photos3']) {
                $photos3=implode(",", $posts['photos3']);
                $data['photos3'] = $photos3;
            }

            if (@$posts['names3']) {
                $names3=implode(",", $posts['names3']);
                $data['names3'] = $names3;
            }

            if (@$posts['photos4']) {
                $photos4=implode(",", $posts['photos4']);
                $data['photos4'] = $photos4;
            }

            if (@$posts['names4']) {
                $names4=implode(",", $posts['names4']);
                $data['names4'] = $names4;
            }


            if (@$posts['photos5']) {
                $photos5=implode(",", $posts['photos5']);
                $data['photos5'] = $photos5;
            }

            if (@$posts['names5']) {
                $names5=implode(",", $posts['names5']);
                $data['names5'] = $names5;
            }




            // $data['photo_urls'] = @json_encode($photoUrl);
            $data['thumbnail'] = @$posts['thumbnail'];
            $res =db('gongdi')->insert($data);

            if ($res) {
                $this->success('添加成功!',url('AdminGd/gongdi'));die;
            }else{
                $this->error('添加失败!',url('AdminGd/gongdi'));die;
            }


        }

    }

    /*工地数据修改*/
    public function gongdiedit()
    {

            $id = $this->request->param();
            $data=db('gongdi')->where('id',$id['id'])->find();

            $str=$data['photo_urls'];
            $photo=explode(",",$data['photo_urls']);
            $this->assign('photo',$photo);

            $str=$data['photos'];
            $photo1=explode(",",$data['photos']);
            $this->assign('photo1',$photo1);

            $str=$data['photos2'];
            $photo2=explode(",",$data['photos2']);
            $this->assign('photo2',$photo2);

            $str=$data['photos3'];
            $photo3=explode(",",$data['photos3']);
            $this->assign('photo3',$photo3);

            $str=$data['photos4'];
            $photo4=explode(",",$data['photos4']);
            $this->assign('photo4',$photo4);

            $str=$data['photos5'];
            $photo5=explode(",",$data['photos5']);
            $this->assign('photo5',$photo5);

            $this->assign('data',$data);
        if ($this->request->isPost()) {

            $posts = $this->request->param();
            //需要抹除发布、置顶、推荐的修改。

            $data['status'] =@$posts['status'];
            $data['is_top'] =@$posts['is_top'];
            $data['recommended'] =@$posts['recommended'];

            $data['title'] =@$posts['title'];
            $data['keywords'] =@$posts['keywords'];
            $data['excerpt'] = @$posts['excerpt'];
            $data['content'] = @$posts['content'];
            $data['yus'] = @$posts['yus'];
            $data['photo_names'] = @$posts['photo_names'];
            $data['photo_urls'] = @$posts['photo_urls'];
            $data['time'] = @$posts['time'];

            if (@$posts['photo_urls']) {
                $photos=implode(",", $posts['photo_urls']);
                $data['photo_urls'] = $photos;
            }



            if (@$posts['photos']) {
                $photos1=implode(",", $posts['photos']);
                $data['photos'] = $photos1;
            }

            if (@$posts['photo_names']) {
                $names=implode(",", $posts['photo_names']);
                $data['photo_names'] = $names;
            }

            if (@$posts['names']) {
                $names1=implode(",", $posts['names']);
                $data['names'] = $names1;
            }


            if (@$posts['photos2']) {
                $photos2=implode(",", $posts['photos2']);
                $data['photos2'] = $photos2;
            }

            if (@$posts['names2']) {
                $names2=implode(",", $posts['names2']);
                $data['names2'] = $names2;
            }

            if (@$posts['photos3']) {
                $photos3=implode(",", $posts['photos3']);
                $data['photos3'] = $photos3;
            }

            if (@$posts['names3']) {
                $names3=implode(",", $posts['names3']);
                $data['names3'] = $names3;
            }

            if (@$posts['photos4']) {
                $photos4=implode(",", $posts['photos4']);
                $data['photos4'] = $photos4;
            }

            if (@$posts['names4']) {
                $names4=implode(",", $posts['names4']);
                $data['names4'] = $names4;
            }


            if (@$posts['photos5']) {
                $photos5=implode(",", $posts['photos5']);
                $data['photos5'] = $photos5;
            }

            if (@$posts['names5']) {
                $names5=implode(",", $posts['names5']);
                $data['names5'] = $names5;
            }

            $data['thumbnail'] = @$posts['thumbnail'];
            $res =db('gongdi')->where('id',$posts['id'])->update($data);

            if ($res) {
                $this->success('修改成功!',url('AdminGd/gongdi'));die;
            }else{
                $this->error('修改失败!',url('AdminGd/gongdi'));die;
            }


        }


        return $this->fetch();


    }

    /*工地数据删除*/
    public function gongdidelete()
    {

            $id = $this->request->param();
            $res=db('gongdi')->where('id',$id['id'])->delete();

            if ($res) {
                $this->success('删除成功!',url('AdminGd/gongdi'));die;
            }else{
                $this->error('删除失败!',url('AdminGd/gongdi'));die;
            }



    }


     /**
     * 学装修
     */
    public function xuexi()
    {
        $where=[];
        $where1=[];
        // $data=db('xuexi')->select();
        $list = $this->request->param();



        if ($this->request->isPost()) {

        if(@$list['category']!=''){
            $where1['name']=@$list['category'];
         }

         if(@$list['keyword']!=''){
            $keyword=@$list['keyword'];
            $where1['keywords']=array('like','%'.$keyword.'%');
         }

         if ($list['start_time'] and $list['end_time'] !='') {
                $beginThismonth=$list['start_time'];
                $endThismonth=$list['end_time'];
                $where1['time']  = array('BETWEEN',array($beginThismonth,$endThismonth));
             }
        }

        $data = db('xuexi')
                ->alias('a')
                ->join('answer b ','a.parent_id = b.id')
                ->where($where1)
                ->field('a.id,a.title,a.keywords,a.excerpt,a.content,a.thumbnail,a.photo_urls,a.time,a.parent_id,a.status,a.is_top,a.recommended,b.id as bid,b.alias,b.thumbnail,b.name,b.antime')
                ->select();

        $parentData = $this->getTree($where);
        $this->assign('parentData',$parentData);

        $this->assign('data',$data);
        return $this->fetch();
    }
    /*学装修添加页面*/
    public function xuexiadd()
    {
        $where=[];
        $parentData = $this->getTree($where);
        $this->assign('parentData',$parentData);


        return $this->fetch();
    }

    /*学装修数据添加*/
    public function xuexiaddpost()
    {
        if ($this->request->isPost()) {
            $posts = $this->request->param();
            //状态只能设置默认值。未发布、未置顶、未推荐
            $data['status'] = 0;
            $data['is_top']      = 0;
            $data['recommended'] = 0;
            $data['parent_id'] =@$posts['parent_id'];
            $data['title'] =@$posts['title'];
            $data['keywords'] =@$posts['keywords'];
            $data['excerpt'] = @$posts['excerpt'];
            $data['content'] = $posts['content'];
            $data['photo_names'] = @$posts['photo_names'];
            $data['photo_urls'] = @$posts['photo_urls'];
            $data['time'] = @$posts['time'];

            if (@$posts['photo_urls']) {
                $photos=implode(",", $posts['photo_urls']);
                $data['photo_urls'] = $photos;
            }



            if (@$posts['photo_names']) {
                $names=implode(",", $posts['photo_names']);
                $data['photo_names'] = $names;
            }



            // $data['photo_urls'] = @json_encode($photoUrl);
            $data['thumbnail'] = @$posts['thumbnail'];
            $res =db('xuexi')->insert($data);

            if ($res) {
                $this->success('添加成功!');die;
            }else{
                $this->error('添加失败!');die;
            }


        }

        return $this->fetch();

    }

    /*学装修数据修改*/
    public function xuexiedit()
    {
        $where=[];
        $id = $this->request->param();
        if ($this->request->isPost()) {
            $posts = $this->request->param();
            //状态只能设置默认值。未发布、未置顶、未推荐
            $data['parent_id'] =@$posts['parent_id'];
            $data['status'] =@$posts['status'];
            $data['is_top'] =@$posts['is_top'];
            $data['recommended'] =@$posts['recommended'];
            $data['title'] =@$posts['title'];
            $data['keywords'] =@$posts['keywords'];
            $data['excerpt'] = @$posts['excerpt'];
            $data['content'] = $posts['content'];
            $data['photo_names'] = @$posts['photo_names'];
            $data['photo_urls'] = @$posts['photo_urls'];
            $data['time'] = @$posts['time'];

            if (@$posts['photo_urls']) {
                $photos=implode(",", $posts['photo_urls']);
                $data['photo_urls'] = $photos;
            }



            if (@$posts['photo_names']) {
                $names=implode(",", $posts['photo_names']);
                $data['photo_names'] = $names;
            }


            $data['thumbnail'] = @$posts['thumbnail'];

            $res =db('xuexi')->where('id',$id['id'])->update($data);


            if ($res) {
                $this->success('修改成功!',url('AdminGd/xuexi'));die;
            }else{
                $this->error('修改失败!');die;
            }


        }

        $parentData = $this->getTree($where);
        $this->assign(array(
            'parentData' => $parentData,
        ));

        $data = db('xuexi')->where('id',$id['id'])->find();

        $this->assign('data', $data);


        return $this->fetch();

    }


   /*学装修数据删除*/
    public function xuexidelete()
    {
        $id = $this->request->param();

        $res =db('xuexi')->where('id',$id['id'])->delete();

        if($res){
            $this->success('删除成功');die;
        }else{
            $this->error('删除失败');die;
        }

    }




    /*分类*/
    public function category()
    {
        $where=[];
        $list = $this->request->param();
        if ($this->request->isPost()) {
            if(@$list['keyword']!=''){
                $keyword=@$list['keyword'];
                $where['name']=array('like','%'.$keyword.'%');
             }

        }

        // $lists=db('answer')->where($where)->select();

        $lists = $this->getTree($where);
        $this->assign('lists', $lists);
        return $this->fetch();
    }

    /*分类ADD*/
     public function categoryadd()
    {
        $where=[];
        $id= $this->request->param();
        $parentData = $this->getTree($where);
        $this->assign('parentData', $parentData);

        return $this->fetch();
    }
    /*分类子类ADD*/
    public function categoryadd1()
    {

        $id= $this->request->param();
        $this->assign('id', $id);
        return $this->fetch();
    }

    /*分类ADD数据添加*/
    public function categoryaddpost()
    {

        if ($this->request->isPost()) {
            $posts = $this->request->param();
            //状态只能设置默认值。未发布、未置顶、未推荐
            $data['name'] =@$posts['name'];
            $data['antime'] =time();
            $data['alias'] = @$posts['alias'];
            $data['thumbnail'] = @$posts['thumbnail'];

            $res =db('answer')->insert($data);


            if ($res) {
                $this->success('添加成功!');die;
            }else{
                $this->error('添加失败!');die;
            }


        }


        return $this->fetch();

    }
    /*分类修改*/
    public function categoryedit()
    {
        $where=[];
        $id = $this->request->param();
        if ($this->request->isPost()) {
            $posts = $this->request->param();
            //状态只能设置默认值。未发布、未置顶、未推荐
            $data['name'] =@$posts['name'];
            $data['antime'] =time();
            $data['alias'] = @$posts['alias'];
            $data['thumbnail'] = @$posts['thumbnail'];
            // print_r($id['id']);die;

            $res =db('answer')->where('id',$id['id'])->update($data);


            if ($res) {
                $this->success('修改成功!');die;
            }else{
                $this->error('修改失败!');die;
            }


        }

        $parentData = $this->getTree($where);
        $children = $this->getChildren($id['id']);
        $this->assign(array(
            'parentData' => $parentData,
            'children' => $children,
        ));

        $data = db('answer')->where('id',$id['id'])->find();
        $this->assign('data', $data);


        return $this->fetch();

    }


    /**
     * 分类删除
     */
    public function categorydelete()
    {
        $id = $this->request->param();

        $res =db('answer')->where('id',$id['id'])->delete();

        if($res){
            $this->success('删除成功');die;
        }else{
            $this->error('删除失败');die;
        }

    }




     /*
    父类
     */
    public function getTree($where='1')
    {
        // $data= db('answer') -> where('parent_id',$id)->select();
        $data = db('answer')->where($where)->select();
        return $this->_reSort($data);
    }


    private function _reSort($data, $parent_id=0, $level=0, $isClear=TRUE)
    {
        static $ret = array();

        if($isClear)
            $ret = array();

        foreach ($data as $k => $v)
        {

            if($v['parent_id'] == $parent_id)
            {

                $v['level'] = $level;
                $ret[] = $v;
                $this->_reSort($data, $v['id'], $level+1, FALSE);

            }
        }
        return $ret;

    }


    /*
    子类
     */
    public function getChildren($id)
    {
        $data = db('answer')->select();

        return $this->_children($data, $id);
    }
    private function _children($data, $parent_id=0, $isClear=TRUE)
    {
        static $ret = array();
        if($isClear)
            $ret = array();
        foreach ($data as $k => $v)
        {
            if($v['parent_id'] == $parent_id)
            {

                $ret[] = $v['id'];
                $this->_children($data, $v['id'], FALSE);
            }
        }
        return $ret;
    }

}
