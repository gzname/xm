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

use cmf\controller\HomeBaseController;
use think\Db;

class CompanyController extends HomeBaseController
{

    /**
     * 前台首页
     */
    public function index()
    {
        // print_r(cmf_get_current_user());die;
        $companyId  = $this->request->param('id', 0, 'intval');
        $data['company'] = db('company')->where(['id'=>$companyId])->find();
        $data['anli'] = db('company_anli')->where(['delete'=>0,'type'=>1,'company_id'=>$companyId,'class'=>array('<>',4)])->order('rank desc')->select();
        // print_r($data['anli']);die;

        $data['hd'] = db('company_hd')->where(['company_id'=>$data['company']['id'],'delete'=>0,'type'=>1])->order('rank desc')->paginate(5);

        $data['comment'] = db('company_comment')->where(['company_id'=>$companyId,'status'=>1])->order('id desc')->paginate(5);
        $this->assign('data', $data);
        return $this->fetch();
    }
    public function company()
    {
        $companyId  = $this->request->param('id', 0, 'intval');
        $data['companys'] = db('company')->where(['id'=>$companyId])->find();
        // print_r($data);die;


        $this->assign('data', $data);
        return $this->fetch();
    }
    public function about(){
        $id  = $this->request->param('id', 0, 'intval');
        $data['type']  = $this->request->param('type', 'about');
        $data['company'] = db('company')->where(['id'=>$id])->find();
        $data['photo_urls']=explode(',',$data['company']['photo_urls']);
        // $data['company'] = db('company')->where(['id'=>$data['anli']['company_id']])->find();
        // $data['images']=explode(',',$data['anli']['images']);
        // print_r($data['type']);die;


        $this->assign('data', $data);
        return $this->fetch();
    }


    public function anli()
    {
        $id  = $this->request->param('id', 0, 'intval');
        $class  = $this->request->param('class', '1');
        if($class!=0){
            $where['class']=$class;
        }
        $data['company'] = db('company')->where(['id'=>$id])->find();

        $where['company_id']=$id;
        $where['delete']=0;
        $where['type']=1;
        // $data['anli'] = db('company_anli')->where($where)->select();
        $data['anli'] = db('company_anli')->where($where)->order('rank desc')->paginate(20);

        $data['anli_count'] = db('company_anli')->where($where)->count();
        $data['anli']->appends($where);
        // 获取分页显示
        $data['page'] = $data['anli']->render();
        // $data['images']=explode(',',$data['anli']['images']);
        // print_r($class);die;
            $data['class']=$class;


        $this->assign('data', $data);
        return $this->fetch();
    }
    public function anlixq()
    {
        $id  = $this->request->param('id', 0, 'intval');
        $data['anli'] = db('company_anli')->where(['id'=>$id,'delete'=>0,'type'=>1])->find();
        $data['company'] = db('company')->where(['id'=>$data['anli']['company_id']])->find();
        $data['images']=explode(',',$data['anli']['images']);
        if(!$data['anli']){
            $this->success('未找到或案例已下线!', url('Company/index',array('id'=>1)));
        }
        $data['qita_anli'] = db('company_anli')->where(['id'=>array('<>',$id),'delete'=>0,'type'=>1])->order('rank desc')->paginate(5);
        // print_r($data['qita_anli']);die;
        $this->assign('data', $data);
        return $this->fetch();
    }

    public function gdxq()          //工地详情
    {
        $id  = $this->request->param('id', 0, 'intval');
        $data['anli'] = db('company_anli')->where(['id'=>$id,'delete'=>0,'type'=>1,'class'=>4])->find();
        $data['company'] = db('company')->where(['id'=>$data['anli']['company_id']])->find();
        $data['images']=explode(',',$data['anli']['images']);
        if(!$data['anli']){
            $this->success('未找到或工地已下线!', url('Company/index',array('id'=>1)));
        }

        $data['photo_urls']=explode(",",$data['anli']['images']);
        // print_r($data['qita_anli']);die;
        $this->assign('data', $data);
        return $this->fetch();
    }


    public function hd()
    {
        $id  = $this->request->param('id', 0, 'intval');

        $data['company'] = db('company')->where(['id'=>$id])->find();
        $where['company_id']=$id;
        $where['delete']=0;
        $where['type']=1;
        // $data['anli'] = db('company_anli')->where($where)->select();
        $data['hd'] = db('company_hd')->where($where)->order('rank desc')->paginate(20);
        // print_r($data['hd']);die;

        $data['hd_count'] = db('company_hd')->where($where)->count();
        $data['hd']->appends($where);
        // 获取分页显示
        $data['page'] = $data['hd']->render();


        $this->assign('data', $data);
        return $this->fetch();
    }
    public function hdxq()
    {
        $id  = $this->request->param('id', 0, 'intval');
        $data['hd'] = db('company_hd')->where(['id'=>$id])->find();
        $data['company'] = db('company')->where(['id'=>$data['hd']['company_id']])->find();
        // print_r($data);die;
        if(!$data['hd']){
            $this->success('未找到活动!', url('Company/index',array('id'=>1)));
        }


        $this->assign('data', $data);
        return $this->fetch();
    }

    public function sjs()
    {
        $id  = $this->request->param('id', 0, 'intval');
        $data['company'] = db('company')->where(['id'=>$id])->find();

        $where['company_id']=$id;
        $where['delete']=0;
        $where['type']=1;
        $where['status']=1;

        $sjs = db('company_sjs')->where($where)->order('rank desc')->paginate(20);
        $data['sjs'] = convert_arr_key($sjs,'id');
        // print_r($data['sjs']);die;
        foreach ($data['sjs'] as $key => $value) {
            $data['sjs'][$key]['anli_count'] = db('company_anli')->where(['sjs_id'=>$value['id']])->count();
        }

        $data['sjs_count'] = db('company_sjs')->where($where)->count();
        // print_r($data);die;
        $sjs->appends($where);
        // 获取分页显示
        $data['page'] = $sjs->render();
        // print_r($data['company']['city']);die;
        $this->assign('data', $data);
        return $this->fetch();
    }
    public function sjsxq()
    {
        $id  = $this->request->param('id', 0, 'intval');
        $data['sjs'] = db('company_sjs')->where(['id'=>$id,'delete'=>0,'type'=>1,'status'=>1])->find();
        $data['company'] = db('company')->where(['id'=>$data['sjs']['company_id']])->find();


        $where['sjs_id']=$id;
        $where['delete']=0;
        $where['type']=1;
        $data['anli'] = db('company_anli')->where($where)->order("id DESC")->paginate(20);

        if(!$data['sjs']){
            $this->success('未找到或设计师已下线!', url('Company/index',array('id'=>1)));
        }

        $this->assign('data', $data);
        return $this->fetch();
    }
}
