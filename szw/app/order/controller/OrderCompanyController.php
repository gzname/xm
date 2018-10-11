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
namespace app\order\controller;

use cmf\controller\AdminBaseController;
use think\Db;
use app\order\model\OrderModel;

class OrderCompanyController extends AdminBaseController
{

    /**
     * 后台首页
     */
    public function index()
    {
        $param   = $this->request->param();

        $where=[];
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
        $data['order'] = db('order')->where($where)->order("id DESC")->paginate(20);

        $data['order']->appends($param);
        // 获取分页显示
        $data['page'] = $data['order']->render();

        $data['param']=$param;

        $this->assign('data', $data);
        return $this->fetch();
    }
    public function add()
    {
        $where['delete']=0;
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

        $house = db('house')->where([])->order('user_id desc')->paginate(100);
        $house_ids=[];
        foreach ($house as $r_k => $r_v) {
            $house_ids[$r_v['user_id']]=$r_v['user_id'];
        }
            // print_r($house_ids);die;
        $_house_info = db('house_info')->where(array('user_id'=>array('in',$house_ids)))->field('count(user_id) num,user_id')->group('user_id')->paginate(9999);
        $house_info=convert_arr_key($_house_info,'user_id');
        // print_r($company);die;

        $house->appends($this->request->param());
        // // 获取分页显示
        $page = $house->render();

        $this->assign('house_info', $house_info);
        $this->assign('house', $house);

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
        if (isset($param['ids']) && isset($param["yes"])) {
            Db::name('company_sjs')->where(['id' => ['in', $ids]])->update(['type' => 1,'status'=>1, 'typetime' => time()]);
            $this->success("上线成功！", '');
        }
        if (isset($param['ids']) && isset($param["no"])) {
            Db::name('company_sjs')->where(['id' => ['in', $ids]])->update(['type' => 0, 'typetime' => time()]);
            $this->success("取消上线成功！", '');
        }
    }

    public function upstatus()      //审核商家
    {
        $uid     = $this->request->param("id");
        $data['order'] = db('order')->where(['uid'=>$uid])->select();

        $where['delete']=0;
        $data['companys'] = db('company')->where($where)->order("id DESC")->paginate(20);

        $this->assign('data', $data);
        $this->assign('uid', $uid);
        return $this->fetch();
    }
    public function upStatusPost()      //审核商家
    {
        $param           = $this->request->param();
        $param['allottime']=time();
        $param['order_id']=date("Ymd").rand(10000000,99999999);
        $uid     = $this->request->param("uid");

        $order = db('order')->where(['uid'=>$uid,'company_id'=>$param['company_id']])->find();
        if(!$order){
            // print_r($param);die;

            Db::name('order')->insert($param);
            $this->success("分配完毕！", 'edit');

        }else{
            $this->error('请勿分配同商家！');
        }

    }
    public function get_company_list(){

            $citycode = $this->request->param('citycode', 0, 'intval');
            $companys = db('company')->where(['city'=>$citycode])->order("id ASC")->select();
        echo json_encode(array('code' => 1, 'companys' => $companys));exit;

    }

    public function delete()
    {
        $param           = $this->request->param();
        // print_r($param);die;
        if (isset($param['id'])) {
            $id           = $this->request->param('id', 0, 'intval');
            $result = db('company_sjs')->where(['id'=>$id])->find();
            if ($result) {
                Db::name('company_sjs')->where(['id'=>$id])->update(['delete'=>1,'deletetime'=>time()]);
            }else{
                $this->success("删除失败！", '');
            }
        }
        if (isset($param['ids'])) {
            $ids     = $this->request->param('ids/a');
                Db::name('company_sjs')->where(['id' => ['in', $ids]])->update(['delete'=>1,'deletetime'=>time()]);
        }
        $this->success("删除成功！", '');
    }
}
