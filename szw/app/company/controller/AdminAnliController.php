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


use app\portal\model\PortalPostModel;
use app\portal\service\PostService;
use app\portal\model\PortalCategoryModel;
use app\admin\model\ThemeModel;

class AdminAnliController extends AdminBaseController
{

    /**
     * 后台首页
     */
    public function index()
    {
        $param   = $this->request->param();

        $startTime = empty($param['start_time']) ? 0 : strtotime($param['start_time']);
        $endTime   = empty($param['end_time']) ? 0 : strtotime($param['end_time']);
            if (!empty($startTime)) {
                $where['time'] = ['>= time', $startTime];
            }
            if (!empty($endTime)) {
                $where['time'] = ['<= time', $endTime];
            }
            if (!empty($param['keyword'])) {
                $where['xq_name'] = ['like', "%".$param['keyword']."%"];
            }
        $where['delete']=0;
        $data['anli'] = db('company_anli')->where($where)->order("id DESC")->paginate(20);

        $data['anli']->appends($param);
        // 获取分页显示
        $data['page'] = $data['anli']->render();

        $data['param']=$param;
        // print_r($param);die;

        $this->assign('data', $data);
        return $this->fetch();
    }
    public function add()
    {
        $where['delete']=0;
        $data['companys'] = db('company')->where($where)->order("id DESC")->paginate(20);

        $data['sjs'] = db('company_sjs')->where(['delete'=>0,'status'=>1])->select();
        $this->assign('data', $data);
        return $this->fetch();
    }

    public function addPost()
    {

            $data   = $this->request->param();
            // print_r($data);die;

            if (!empty($data['images'])) {
                $data['images']=implode(",",$data['images']);
            }
            if (!empty($data['starttime'])) {
                $data['starttime']=implode(",",$data['starttime']);
            }
            if (!empty($data['endtime'])) {
                $data['endtime']=implode(",",$data['endtime']);
            }
            unset($data['photo_names']);
            $data['time']=time();
        $create_result =Db::name('company_anli')->insert($data);
        // print_r($create_result);die;
        if($create_result){
            $this->success("添加成功");

        }else{
            $this->error('添加失败');
        }


    }

    public function edit()
    {
        $id = $this->request->param('id', 0, 'intval');
        $data['company_anli'] = db('company_anli')->where(['id'=>$id])->find();
        $data['images']=explode(',',$data['company_anli']['images']);

            // print_r($data['company_anli']);die;
        $data['sjs'] = db('company_sjs')->where(['delete'=>0,'status'=>1])->select();
        $data['companys'] = db('company')->where(['delete'=>0,'status'=>1])->select();
        $this->assign('data', $data);

        return $this->fetch();
    }
    public function editPost()
    {

        if ($this->request->isPost()) {
            $data   = $this->request->param();
            // print_r($data);die;
            $id=$data['id'];
            if (!empty($data['images'])) {
                $data['images']=implode(",",$data['images']);
            }else{
                $data['images'] = '';
            }
            // print_r($data);die;
            unset($data['photo_names']);
            unset($data['post']);
            unset($data['id']);
            $data['starttime']=strtotime($data['starttime']);
            $data['endtime']=strtotime($data['endtime']);
            $data['edittime']=time();

            Db::name('company_anli')->where(array("id" => $id))->update($data);


            $this->success('保存成功!');

        }
    }

    public function publish()           //商家上线
    {
        $param = $this->request->param();

            $ids = $this->request->param('ids/a');
        if (isset($param['ids']) && isset($param["yes"])) {
            Db::name('company_anli')->where(['id' => ['in', $ids]])->update(['type' => 1,'status'=>1, 'typetime' => time()]);
            $this->success("上线成功！", '');
        }
        if (isset($param['ids']) && isset($param["no"])) {
            Db::name('company_anli')->where(['id' => ['in', $ids]])->update(['type' => 0, 'typetime' => time()]);
            $this->success("取消上线成功！", '');
        }
    }

    public function upstatus()      //审核商家
    {
        $Id     = $this->request->param("id");
        $data['company_anli'] = db('company_anli')->where(['id'=>$Id])->find();
        $this->assign('data', $data);
        return $this->fetch();
    }
    public function upStatusPost()      //审核商家
    {
        $param           = $this->request->param();
        $param['time']=time();
        $id     = $this->request->param("id");
        unset($param['id']);
        db('company_anli')->where(['id'=>$id])->update($param);
        $this->success("审核完毕！", 'index');

    }

    public function delete()
    {
        $param           = $this->request->param();
        // print_r($param);die;
        if (isset($param['id'])) {
            $id           = $this->request->param('id', 0, 'intval');
            $result = db('company_anli')->where(['id'=>$id])->find();
            if ($result) {
                Db::name('company_anli')->where(['id'=>$id])->update(['delete'=>1,'deletetime'=>time()]);
            }else{
                $this->success("删除失败！", '');
            }
        }
        if (isset($param['ids'])) {
            $ids     = $this->request->param('ids/a');
                Db::name('company_anli')->where(['id' => ['in', $ids]])->update(['delete'=>1,'deletetime'=>time()]);
        }
        $this->success("删除成功！", '');
    }
}
