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
namespace app\customer\controller;

use cmf\controller\AdminBaseController;
use think\Db;
use app\customer\model\CustomerModel;

class AdminCustomerController extends AdminBaseController
{

    /**
     * 后台首页
     */
    public function index()
    {
        $adminId = cmf_get_current_admin_id();
        /**搜索条件**/
        $user_id = trim($this->request->param('user_id'));
        $mobile = trim($this->request->param('mobile'));
        $start_time = $this->request->param('start_time');
        $end_time = $this->request->param('end_time');
        $status = $this->request->param('status');
        $where=[];
        $h_where=[];
        if ($start_time&&$end_time) {
            $h_where['addtime'] = array(array('egt',strtotime($start_time)),array('elt',strtotime($end_time)+86399),'AND');
        }
        // print_r($h_where);die;
        if ($user_id) {
            $h_where['user_id'] = $user_id;
        }

        if ($mobile) {
            $h_where['mobile'] = $mobile;
        }


        $order_wehre=@$this->request->param('order')?$this->request->param('order'):'addtime';
        $rank=@$this->request->param('rank')?$this->request->param('rank'):'DESC';
        $house = db('house')->where($h_where)->order("".$order_wehre." ".$rank."")->paginate(100);
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
        // print_r($house);die;

        $this->assign('start_time', isset($start_time) ? $start_time : '');
        $this->assign('end_time', isset($end_time) ? $end_time : '');
        $this->assign('status', isset($status) ? $status : '');
        $this->assign('user_id', isset($user_id) ? $user_id : '');
        $this->assign('mobile', isset($mobile) ? $mobile : '');
        $this->assign('param', $this->request->param());


        $this->assign('house', $house);
        $this->assign('house_info', $house_info);
        $this->assign('page', $page);
        return $this->fetch();
    }
    public function get_one_userinfo(){
        $adminId = cmf_get_current_admin_id();
        $user_id = intval($this->request->param('user_id'));
        $datas['house_info'] = db('house_info')->where(array('user_id'=>$user_id))->order("id ASC")->select();
        // print_r($datas['house_info']);die;
        echo json_encode(array('code' => 1, 'datas' => $datas));exit;
    }
    /**
     * 跟进用户资料
     */
    public function edit()
    {
        $user_id = intval($this->request->param('user_id'));
        $house = db('house')->where(array('user_id'=>$user_id))->find();
        $house_copy = db('house_copy')->where(array('user_id'=>$user_id))->find();

        $order=db('order')->where(array('uid'=>$user_id))->field('company_id')->select();
        // print_r($order);die;


        $this->assign('order', $order);
        $this->assign('house', $house);
        $this->assign('house_copy', $house_copy);
        return $this->fetch();
    }
    public function editPost()
    {

        if ($this->request->isPost()) {
            $data   = $this->request->param();
            $user_id=$data['user_id'];
            // print_r($data);die;
            // print_r($data);die;
            unset($data['user_id']);
            $data['edittime']=time();

            $data['kg_time']=strtotime($data['kg_time']);
            $data['lf_time']=strtotime($data['lf_time']);

            Db::name('house_copy')->where(array("user_id" => $user_id))->update($data);


            $this->success('保存成功!');

        }
    }
}
