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

class SjsController extends HomeBaseController
{

    /**
     * 前台首页
     */
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
