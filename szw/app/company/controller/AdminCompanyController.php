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

class AdminCompanyController extends AdminBaseController
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
                $where['name'] = ['like', "%".$param['keyword']."%"];
            }
        $where['delete']=0;
        $data['companys'] = db('company')->where($where)->order("id DESC")->paginate(20);
        $data['companys']->appends($param);


        // 获取分页显示
        $data['page'] = $data['companys']->render();

        // print_r($data['page']);die;
        $data['param']=$param;
        // print_r($param);die;

        $this->assign('data', $data);
        return $this->fetch();
    }
    public function add(){
        // $postService = new PostService();
        // $data        = $postService->adminArticleList($param);
        return $this->fetch();
    }

    public function addPost()
    {

            $data   = $this->request->param();

            if (!empty($data['photo_urls'])) {
                $data['photo_urls']=implode(",",$data['photo_urls']);
            }
            if (!empty($data['post']['more'])) {
                $data['logo']=$data['post']['more']['thumbnail'];
            }
            if (!empty($data['fwlx'])) {
                $data['fwlx']=implode(",",$data['fwlx']);
            }
            unset($data['photo_names']);
            unset($data['post']);
            $data['time']=time();
        $create_result =Db::name('company')->insert($data);
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
        $data['company'] = db('company')->where(['id'=>$id])->find();
        $data['photo_urls']=explode(',',$data['company']['photo_urls']);
        $this->assign('data', $data);

        return $this->fetch();
    }
    public function editPost()
    {

        if ($this->request->isPost()) {
            $data   = $this->request->param();
            $id=$data['id'];
            if (!empty($data['photo_urls'])) {
                $data['photo_urls']=implode(",",$data['photo_urls']);
            }else{
                $data['photo_urls'] = '';
            }

            $data['area']=@implode(",",$data['area']);
            $data['fwlx']=@implode(",",$data['fwlx']);
            // print_r($data);die;
            if (!empty($data['logo'])) {
                $data['logo']=$data['logo'];
            }else{
                $data['logo'] = '';
            }
            // print_r($data);die;
            unset($data['photo_names']);
            unset($data['post']);
            unset($data['id']);
            $data['edittime']=time();

            Db::name('company')->where(array("id" => $id))->update($data);


            $this->success('保存成功!');

        }
    }

    public function publish()           //商家上线
    {
        $param = $this->request->param();

            $ids = $this->request->param('ids/a');
        if (isset($param['ids']) && isset($param["yes"])) {
            Db::name('company')->where(['id' => ['in', $ids]])->update(['type' => 1,'status'=>1, 'typetime' => time()]);
            $this->success("上线成功！", '');
        }
        if (isset($param['ids']) && isset($param["no"])) {
            Db::name('company')->where(['id' => ['in', $ids]])->update(['type' => 0, 'typetime' => time()]);
            $this->success("取消上线成功！", '');
        }
    }

    public function upstatus()      //审核商家
    {
        $Id     = $this->request->param("id");
        $data['company'] = db('company')->where(['id'=>$Id])->find();
        $this->assign('data', $data);
        return $this->fetch();
    }
    public function upStatusPost()      //审核商家
    {
        $param           = $this->request->param();
        $param['time']=time();
        $id     = $this->request->param("id");
        unset($param['id']);
        db('company')->where(['id'=>$id])->update($param);
        $this->success("审核完毕！", 'index');

    }

    public function delete()
    {
        $param           = $this->request->param();
        // print_r($param);die;
        if (isset($param['id'])) {
            $id           = $this->request->param('id', 0, 'intval');
            $result = db('company')->where(['id'=>$id])->find();
            if ($result) {
                Db::name('company')->where(['id'=>$id])->update(['delete'=>1,'deletetime'=>time()]);
            }else{
                $this->success("删除失败！", '');
            }
        }
        if (isset($param['ids'])) {
            $ids     = $this->request->param('ids/a');
                Db::name('company')->where(['id' => ['in', $ids]])->update(['delete'=>1,'deletetime'=>time()]);
        }
        $this->success("删除成功！", '');
    }

    public function get_province(){
        $province = db('province')->order("id ASC")->select();
        echo json_encode(array('code' => 1, 'province' => $province));exit;
    }
    public function get_city(){
            $provincecode = $this->request->param('provincecode', 0, 'intval');
            $city = db('city')->where(['provincecode'=>$provincecode])->order("id ASC")->select();
        echo json_encode(array('code' => 1, 'city' => $city));exit;
    }
    public function get_area(){
        $citycode = $this->request->param('citycode', 0, 'intval');
        $company_id = $this->request->param('company_id', 0, 'intval');
        $company = db('company')->where(['id'=>$company_id])->find();
        $chicked=[];
        if($company['area']){
            $chicked=explode(',',$company['area']);
        }
        $area = db('area')->where(['citycode'=>$citycode])->order("id ASC")->select();
        echo json_encode(array('code' => 1, 'area' => $area,'chicked'=>$chicked));exit;

    }
}
