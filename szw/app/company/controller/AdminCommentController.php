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

class AdminCommentController extends AdminBaseController
{

    /**
     * 后台首页
     */
    public function index()
    {
        $param   = $this->request->param();

        $startTime = empty($param['start_time']) ? 0 : strtotime($param['start_time']);
        $endTime   = empty($param['end_time']) ? 0 : strtotime($param['end_time']);
        $where=[];
            if (!empty($startTime)) {
                $where['time'] = ['>= time', $startTime];
            }
            if (!empty($endTime)) {
                $where['time'] = ['<= time', $endTime];
            }
            if (!empty($param['keyword'])) {
                $where['content'] = ['like', "%".$param['keyword']."%"];
            }
        $data['comment'] = db('company_comment')->where($where)->order("id DESC")->paginate(20);

        $data['comment']->appends($param);
        // 获取分页显示
        $data['page'] = $data['comment']->render();

        $data['param']=$param;

        $this->assign('data', $data);
        return $this->fetch();
    }
    public function add()
    {
        $data['companys'] = db('company')->where($where)->order("id DESC")->paginate(20);
        $this->assign('data', $data);
        return $this->fetch();
    }

    public function addPost()
    {

            $data   = $this->request->param();

            if (!empty($data['post'])) {
                $data['image']=$data['post']['more']['thumbnail'];
            }
            unset($data['post']);
            $data['time']=time();
            $data['fengge']=implode(',',$data['fengge']);
            $data['lingyu']=implode(',',$data['lingyu']);
            // print_r($data);die;
        $create_result =Db::name('company_sjs')->insert($data);
        // print_r($create_result);die;
        if($create_result){
            $this->success("添加成功");

        }else{
            $this->error('添加失败');
        }


    }

    public function edit()
    {


        $where['delete']=0;
        $where['type']=1;
        $data['companys'] = db('company')->where($where)->order("id DESC")->paginate(20);
        $id = $this->request->param('id', 0, 'intval');
        $data['sjs'] = db('company_sjs')->where(['id'=>$id])->find();
        // print_r($data['photo_urls']);die;
        $this->assign('data', $data);

        return $this->fetch();
    }
    public function editPost()
    {

        if ($this->request->isPost()) {
            $data   = $this->request->param();
            // print_r($data);die;
            $id=$data['id'];
            // print_r($data);die;
            unset($data['photo_names']);
            unset($data['post']);
            unset($data['id']);
            $data['edittime']=time();
            $data['fengge']=implode(',',$data['fengge']);
            $data['lingyu']=implode(',',$data['lingyu']);

            Db::name('company_sjs')->where(array("id" => $id))->update($data);


            $this->success('保存成功!');

        }
    }

    public function publish()           //商家上线
    {
        $param = $this->request->param();

            $ids = $this->request->param('ids/a');

            // print_r(123123);die;
        if (isset($param['ids']) && isset($param["yes"])) {
            Db::name('company_comment')->where(['id' => ['in', $ids]])->update(['status'=>1, 'status_time' => time()]);
            $this->success("修改成功！", '');
        }
        if (isset($param['ids']) && isset($param["no"])) {
            Db::name('company_comment')->where(['id' => ['in', $ids]])->update(['status' => 0, 'status_time' => time()]);
            $this->success("取消显示成功！", '');
        }
    }

    public function upstatus()      //审核商家
    {
        $Id     = $this->request->param("id");
        $data['sjs'] = db('company_sjs')->where(['id'=>$Id])->find();
        $this->assign('data', $data);
        return $this->fetch();
    }
    public function upStatusPost()      //审核商家
    {
        $param           = $this->request->param();
        $param['time']=time();
        $id     = $this->request->param("id");
        if($param['status']==2){
            $param['type']=0;
        }
        unset($param['id']);
        db('company_sjs')->where(['id'=>$id])->update($param);
        $this->success("审核完毕！", 'index');

    }

    public function delete()
    {
        $param           = $this->request->param();
        // print_r($param);die;
        if (isset($param['id'])) {
            $id           = $this->request->param('id', 0, 'intval');
            $result = db('company_comment')->where(['id'=>$id])->find();
            if ($result) {
                Db::name('company_comment')->where(['id'=>$id])->delete();
            }else{
                $this->success("删除失败！", '');
            }
        }
        if (isset($param['ids'])) {
            $ids     = $this->request->param('ids/a');
                Db::name('company_comment')->where(['id' => ['in', $ids]])->delete();
                // Db::name('company_comment')->where(['id' => ['in', $ids]])->update(['delete'=>1,'deletetime'=>time()]);
        }
        $this->success("删除成功！", '');
    }
}
