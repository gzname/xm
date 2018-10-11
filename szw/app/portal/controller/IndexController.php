<?php
namespace app\portal\controller;

use cmf\controller\HomeBaseController;
use think\Db;
class IndexController extends HomeBaseController
{
    public function index()
    {
        return $this->redirect('/public/platform/platform');
        // return $this->fetch(':index');
    }
    public function form_house(){

        $user_arr = $this->request->param();
        $user_arr['gsd']=gsd($user_arr['mobile']);//归属地
        if(!cmf_check_mobile($user_arr['mobile'])){
            $this->error("请输入正确的手机号码！");
        }
        // $request = request();
        // print_r($user_arr);die;
        $user_arr['acreage']=@$user_arr['acreage']?$user_arr['acreage']:'0';
        $user_arr['loupan_name']=@htmlspecialchars($user_arr['loupan_name']);
        $user_arr['detailed']=@htmlspecialchars(implode(',', $user_arr['detailed']));

        $user_arr['wangzhi']=urldecode($user_arr['wangzhi']);
        $user_arr['wangzhi_entry']=urldecode($user_arr['wangzhi_entry']);


        unset($user_arr['time']);
        $this_user = db('house')->where(['mobile' => $user_arr['mobile']])->find();
        if($this_user){
            $user_id=$this_user['user_id'];
        }else{
            $this_user = db('house')->where(['mobile' => $user_arr['mobile']])->find();
            if(empty($this_user)){
                $user_id =db('house')->insertGetId($user_arr);//用户表
            }
        }
        $repeat_user = db('house_info')->where(array('user_id' => $user_id,'addtime'=>array('egt',strtotime(date('Y-m-d',time())),array('lt',strtotime(date('Y-m-d',time())).'+1 day'))))->count();  //重复号码
        if($repeat_user>2){
            $this->error("请勿重复提交！");
        }

        unset($user_arr['mobile']);
        $user_arr['user_id']=$user_id;

            $info_id =db('house_info')->insertGetId($user_arr);//用户表
        if($user_id){
            $this->success("提交成功！");
        }else{
            $this->error("未知错误，请联系管理员！");
            return -1;
        }

    }

    public function get_user_api(){

        $data = $this->request->param();
        print_r($data);die;
    }


    function get_province_list(){
        $province = db('province')->order("id ASC")->select();
        echo json_encode(array('code' => 1, 'province' => $province));exit;
    }
    function get_city_list(){
            $provincecode = $this->request->param('provincecode', 0, 'intval');
            // print_r($_POST);die;
            $city = db('city')->where(['provincecode'=>$provincecode])->order("id ASC")->select();
        echo json_encode(array('code' => 1, 'city' => $city));exit;
    }
    function get_area_list(){
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
