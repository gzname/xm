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

class HdController extends HomeBaseController
{

    /**
     * 前台首页
     */
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

}
