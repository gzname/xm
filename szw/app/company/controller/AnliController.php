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

class AnliController extends HomeBaseController
{

    /**
     * 前台首页
     */
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

}
