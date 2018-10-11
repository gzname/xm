<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2018 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: Powerless < wzxaini9@gmail.com>
// +----------------------------------------------------------------------
namespace app\user\controller;

use cmf\lib\Storage;
use think\Validate;
use think\Image;
use cmf\controller\UserBaseController;
use app\user\model\UserModel;
use think\Db;

class ProfileController extends UserBaseController
{

    function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 会员中心首页
     */
    public function center()
    {
        $user = cmf_get_current_user();
        $this->assign($user);

        $userId = cmf_get_current_user_id();
        $data['company'] = db('company')->where(['uid'=>$userId])->find();
        $data['photo_urls']=explode(',',$data['company']['photo_urls']);


        $userModel = new UserModel();
        $user      = $userModel->where('id', $userId)->find();
        $this->assign('user', $user);
        $this->assign('data', $data);
        return $this->fetch();
    }

    /**
     * 编辑用户资料
     */
    public function edit()
    {
        $user = cmf_get_current_user();
        $data['company'] = db('company')->where(['uid'=>$user['id']])->find();

        $data['photo_urls']=explode(',',$data['company']['photo_urls']);

        $this->assign('data', $data);
        return $this->fetch();
    }

    /**
     * 编辑用户资料
     */
    public function detail()        //修改公司详情
    {
                            // print_r(fengge_list());
        $user = cmf_get_current_user();
        $data['company'] = db('company')->where(['uid'=>$user['id']])->find();

        $data['photo_urls']=explode(',',$data['company']['photo_urls']);

        $this->assign('data', $data);
        return $this->fetch();
    }
    /**
     * 编辑公司图片
     */
    public function img()        //修改公司图片
    {
        $user = cmf_get_current_user();
        $data['company'] = db('company')->where(['uid'=>$user['id']])->find();

        $data['photo_urls']=explode(',',$data['company']['photo_urls']);

        $this->assign('data', $data);
        return $this->fetch();
    }

    /**
     * 编辑用户资料提交
     */
    public function editPost()
    {
        if ($this->request->isPost()) {
            $data   = $this->request->param();
            $id=$data['id'];
            if (!empty($data['photo_urls'])) {
                $data['photo_urls']=implode(",",$data['photo_urls']);
            }

            if(!empty($data['area'])){$data['area']=@implode(",",$data['area']);}
            if(!empty($data['fwlx'])){$data['fwlx']=@implode(",",$data['fwlx']);}
            if(!empty($data['scfg'])){$data['scfg']=@implode(",",$data['scfg']);}
            if(!empty($data['fwbz'])){$data['fwbz']=@implode(",",$data['fwbz']);}
            // print_r($data);die;
            if (!empty($data['logo'])) {$data['logo']=$data['logo'];}
            if(!empty($data['chengli'])){$data['chengli']=strtotime($data['chengli']);}
            // print_r($data);die;
            unset($data['photo_names']);
            unset($data['post']);
            unset($data['id']);
            $data['edittime']=time();
            // $data['status'] = 0;
            // print_r($data);die;

            Db::name('company')->where(array("id" => $id))->update($data);
            $this->success('保存成功!');
        }
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
        // print_r($chicked);die;

        $area = db('area')->where(['citycode'=>$citycode])->order("id ASC")->select();
        echo json_encode(array('code' => 1, 'area' => $area,'chicked'=>$chicked));exit;

    }
    /**
     * 个人中心修改密码
     */
    public function password()
    {
        $user = cmf_get_current_user();
        $this->assign($user);
        return $this->fetch();
    }

    /**
     * 个人中心修改密码提交
     */
    public function passwordPost()
    {
        if ($this->request->isPost()) {
            $validate = new Validate([
                'old_password' => 'require|min:6|max:32',
                'password'     => 'require|min:6|max:32',
                'repassword'   => 'require|min:6|max:32',
            ]);
            $validate->message([
                'old_password.require' => lang('old_password_is_required'),
                'old_password.max'     => lang('old_password_is_too_long'),
                'old_password.min'     => lang('old_password_is_too_short'),
                'password.require'     => lang('password_is_required'),
                'password.max'         => lang('password_is_too_long'),
                'password.min'         => lang('password_is_too_short'),
                'repassword.require'   => lang('repeat_password_is_required'),
                'repassword.max'       => lang('repeat_password_is_too_long'),
                'repassword.min'       => lang('repeat_password_is_too_short'),
            ]);

            $data = $this->request->post();
            if (!$validate->check($data)) {
                $this->error($validate->getError());
            }

            $login = new UserModel();
            $log   = $login->editPassword($data);
            switch ($log) {
                case 0:
                    $this->success(lang('change_success'));
                    break;
                case 1:
                    $this->error(lang('password_repeat_wrong'));
                    break;
                case 2:
                    $this->error(lang('old_password_is_wrong'));
                    break;
                default :
                    $this->error(lang('ERROR'));
            }
        } else {
            $this->error(lang('ERROR'));
        }

    }

    // 用户头像编辑
    public function avatar()
    {
        $user = cmf_get_current_user();
        $this->assign($user);
        return $this->fetch();
    }

    // 用户头像上传
    public function avatarUpload()
    {
        $file   = $this->request->file('file');
        $result = $file->validate([
            'ext'  => 'jpg,jpeg,png',
            'size' => 1024 * 1024
        ])->move('.' . DS . 'upload' . DS . 'avatar' . DS);

        if ($result) {
            $avatarSaveName = str_replace('//', '/', str_replace('\\', '/', $result->getSaveName()));
            $avatar         = 'avatar/' . $avatarSaveName;
            session('avatar', $avatar);

            return json_encode([
                'code' => 1,
                "msg"  => "上传成功",
                "data" => ['file' => $avatar],
                "url"  => ''
            ]);
        } else {
            return json_encode([
                'code' => 0,
                "msg"  => $file->getError(),
                "data" => "",
                "url"  => ''
            ]);
        }
    }

    // 用户头像裁剪
    public function avatarUpdate()
    {
        $avatar = session('avatar');
        if (!empty($avatar)) {
            $w = $this->request->param('w', 0, 'intval');
            $h = $this->request->param('h', 0, 'intval');
            $x = $this->request->param('x', 0, 'intval');
            $y = $this->request->param('y', 0, 'intval');

            $avatarPath = "./upload/" . $avatar;

            $avatarImg = Image::open($avatarPath);
            $avatarImg->crop($w, $h, $x, $y)->save($avatarPath);

            $result = true;
            if ($result === true) {
                $storage = new Storage();
                $result  = $storage->upload($avatar, $avatarPath, 'image');

                $userId = cmf_get_current_user_id();
                Db::name("user")->where(["id" => $userId])->update(["avatar" => $avatar]);
                session('user.avatar', $avatar);
                $this->success("头像更新成功！");
            } else {
                $this->error("头像保存失败！");
            }

        }
    }

    /**
     * 绑定手机号或邮箱
     */
    public function binding()
    {
        $user = cmf_get_current_user();
        $this->assign($user);
        return $this->fetch();
    }

    /**
     * 绑定手机号
     */
    public function bindingMobile()
    {
        if ($this->request->isPost()) {
            $validate = new Validate([
                'username'          => 'require|number|unique:user,mobile',
                'verification_code' => 'require',
            ]);
            $validate->message([
                'username.require'          => '手机号不能为空',
                'username.number'           => '手机号只能为数字',
                'username.unique'           => '手机号已存在',
                'verification_code.require' => '验证码不能为空',
            ]);

            $data = $this->request->post();
            if (!$validate->check($data)) {
                $this->error($validate->getError());
            }
            $errMsg = cmf_check_verification_code($data['username'], $data['verification_code']);
            if (!empty($errMsg)) {
                $this->error($errMsg);
            }
            $userModel = new UserModel();
            $log       = $userModel->bindingMobile($data);
            switch ($log) {
                case 0:
                    $this->success('手机号绑定成功');
                    break;
                default :
                    $this->error('未受理的请求');
            }
        } else {
            $this->error("请求错误");
        }
    }

    /**
     * 绑定邮箱
     */
    public function bindingEmail()
    {
        if ($this->request->isPost()) {
            $validate = new Validate([
                'username'          => 'require|email|unique:user,user_email',
                'verification_code' => 'require',
            ]);
            $validate->message([
                'username.require'          => '邮箱地址不能为空',
                'username.email'            => '邮箱地址不正确',
                'username.unique'           => '邮箱地址已存在',
                'verification_code.require' => '验证码不能为空',
            ]);

            $data = $this->request->post();
            if (!$validate->check($data)) {
                $this->error($validate->getError());
            }
            $errMsg = cmf_check_verification_code($data['username'], $data['verification_code']);
            if (!empty($errMsg)) {
                $this->error($errMsg);
            }
            $userModel = new UserModel();
            $log       = $userModel->bindingEmail($data);
            switch ($log) {
                case 0:
                    $this->success('邮箱绑定成功');
                    break;
                default :
                    $this->error('未受理的请求');
            }
        } else {
            $this->error("请求错误");
        }
    }

    public function anli(){
        $user = cmf_get_current_user();
        $data['company'] = db('company')->where(['uid'=>$user['id']])->find();


        $class = $this->request->param('class');
        if($class){
            $where['class']=$class;
        }
        $where['company_id']=$data['company']['id'];
        $where['delete']=0;
        $data['anli'] = db('company_anli')->where($where)->order("id DESC")->paginate(20);
        // print_r($data['anli']);die;

        $data['anli']->appends($where);
        // 获取分页显示
        $data['page'] = $data['anli']->render();
        $data['user']=$user;
        $this->assign('data', $data);
        return $this->fetch();
    }

    public function sjs(){
        $user = cmf_get_current_user();
        $data['company'] = db('company')->where(['uid'=>$user['id']])->find();
        // print_r($data['company']['id']);die;


        $where['company_id']=$data['company']['id'];
        $where['delete']=0;
        $data['sjs'] = db('company_sjs')->where($where)->order("id DESC")->paginate(20);

        $data['sjs']->appends($where);
        // 获取分页显示
        $data['page'] = $data['sjs']->render();
        // print_r($data['sjs']);die;

        $this->assign('data', $data);
        return $this->fetch();
    }

    public function add(){
        $user = cmf_get_current_user();
        $data['company'] = db('company')->where(['uid'=>$user['id']])->find();

        $location = $this->request->param('location');
        // print_r($location);die;
        if($location=='sjs'){
            $data['sjs'] = db('company_sjs')->where(['delete'=>0,'status'=>1,'company_id'=>$data['company']['id']])->select();
            $this->assign('data', $data);
            return $this->fetch('/profile/sjs_add');
        }elseif($location=='anli'){

            $data['area'] = db('area')->where(['citycode'=>get_company($data['company']['id'],'city')])->select();
            $data['sjs'] = db('company_sjs')->where(['delete'=>0,'status'=>1,'company_id'=>$data['company']['id']])->select();
        // $data['images']=explode(',',$data['anli']['images']);

            $this->assign('data', $data);
            return $this->fetch('/profile/anli_add');
        }elseif($location=='hd'){
            return $this->fetch('/profile/hd_add');

        }
    }

    public function add_post()
    {
        $user = cmf_get_current_user();
        $company = db('company')->where(['uid'=>$user['id']])->find();

        $data   = $this->request->param();
        if($data['location']=='sjs'){
            unset($data['location']);
            if (!empty($data['post'])) {
                $data['image']=$data['post']['more']['thumbnail'];
            }
            unset($data['post']);
            $data['time']=time();
            if(@$data['fengge']){
                $data['fengge']=implode(',',$data['fengge']);
            }
            if(@$data['lingyu']){
                $data['lingyu']=implode(',',$data['lingyu']);
            }
            $data['company_id']=$company['id'];
            $create_result =Db::name('company_sjs')->insert($data);

        }elseif($data['location']=='anli'){
            unset($data['location']);
            if (!empty($data['images'])) {
                $data['images']=implode(",",$data['images']);
            }
            $data['starttime']=strtotime($data['starttime']);
            $data['endtime']=strtotime($data['endtime']);
            unset($data['photo_names']);
            $data['time']=time();
            $data['company_id']=$company['id'];
            $create_result =Db::name('company_anli')->insert($data);
        }
        if($create_result){
            $this->success("添加成功");
        }else{
            $this->error('添加失败');
        }
    }

    public function hd_edit(){
        $id = $this->request->param('id', 0, 'intval');
        $data['hd'] = db('company_hd')->where(['id'=>$id])->find();
        // print_r($data['hd']);die;
        $this->assign('data', $data);
        return $this->fetch();
    }
    public function hd_edit_post(){
        if ($this->request->isPost()) {
            $data   = $this->request->param();
            $id=$data['id'];
            unset($data['id']);
            $data['edittime']=time();

            $data['starttime']=strtotime($data['starttime']);
            $data['endtime']=strtotime($data['endtime']);
            // print_r($data);die;
            Db::name('company_hd')->where(array("id" => $id))->update($data);
            $this->success('保存成功!');
        }
    }
    public function hd_add_post()
    {
        $user = cmf_get_current_user();
        $company = db('company')->where(['uid'=>$user['id']])->find();
            $data   = $this->request->param();
            $data['time']=time();

            $data['starttime']=strtotime($data['starttime']);
            $data['endtime']=strtotime($data['endtime']);
        $data['company_id']=$company['id'];
            // print_r($data);die;
        $create_result =Db::name('company_hd')->insert($data);
        // print_r($create_result);die;
        if($create_result){
            $this->success("添加成功");
        }else{
            $this->error('添加失败');
        }
    }



    public function sjs_edit(){
        $id = $this->request->param('id', 0, 'intval');
        $data['sjs'] = db('company_sjs')->where(['id'=>$id])->find();
        // print_r($data['sjs']);die;
        $this->assign('data', $data);
        return $this->fetch();
    }
    public function sjs_edit_post(){
        if ($this->request->isPost()) {
            $data   = $this->request->param();
            $id=$data['id'];
            // print_r($data);die;
            unset($data['photo_names']);
            unset($data['post']);
            unset($data['id']);
            $data['edittime']=time();
            if(@$data['fengge']){
                $data['fengge']=implode(',',$data['fengge']);
            }
            if(@$data['lingyu']){
                $data['lingyu']=implode(',',$data['lingyu']);
            }
            // print_r($data);die;
            Db::name('company_sjs')->where(array("id" => $id))->update($data);
            $this->success('保存成功!');
        }
    }
    public function sjs_add_post()
    {
        $user = cmf_get_current_user();
        $company = db('company')->where(['uid'=>$user['id']])->find();
            $data   = $this->request->param();
            if (!empty($data['post'])) {
                $data['image']=$data['post']['more']['thumbnail'];
            }
            unset($data['post']);
            $data['time']=time();
            if(@$data['fengge']){
                $data['fengge']=implode(',',$data['fengge']);
            }
            if(@$data['lingyu']){
                $data['lingyu']=implode(',',$data['lingyu']);
            }
            // print_r($data);die;
        $data['company_id']=$company['id'];
        $create_result =Db::name('company_sjs')->insert($data);
        // print_r($create_result);die;
        if($create_result){
            $this->success("添加成功");
        }else{
            $this->error('添加失败');
        }
    }

    public function anli_add_post()
    {
        $user = cmf_get_current_user();
        $company = db('company')->where(['uid'=>$user['id']])->find();

            $data   = $this->request->param();
            if (!empty($data['images'])) {
                $data['images']=implode(",",$data['images']);
            }
            $data['starttime']=strtotime($data['starttime']);
            $data['endtime']=strtotime($data['endtime']);
            unset($data['photo_names']);
            $data['time']=time();
        $data['company_id']=$company['id'];
        $create_result =Db::name('company_anli')->insert($data);
        // print_r($create_result);die;
        if($create_result){
            $this->success("添加成功");

        }else{
            $this->error('添加失败');
        }
    }
    public function anli_edit(){
        $id = $this->request->param('id', 0, 'intval');
        $data['anli'] = db('company_anli')->where(['id'=>$id])->find();

        $data['area'] = db('area')->where(['citycode'=>get_company($data['anli']['company_id'],'city')])->select();
        $data['images']=explode(',',$data['anli']['images']);

        $data['sjs'] = db('company_sjs')->where(['delete'=>0,'status'=>1,'company_id'=>$data['anli']['company_id']])->select();
        // print_r($data['sjs']);die;
        $this->assign('data', $data);
        return $this->fetch();

    }
    public function anli_edit_post(){


        if ($this->request->isPost()) {
            $data   = $this->request->param();
            // print_r($data);die;
            $id=$data['id'];
            if (!empty($data['images'])) {
                $data['images']=implode(",",$data['images']);
            }else{
                $data['images'] = '';
            }
            unset($data['photo_names']);
            unset($data['post']);
            unset($data['id']);
            $data['edittime']=time();

            Db::name('company_anli')->where(array("id" => $id))->update($data);


            $this->success('保存成功!');

        }
        $this->assign('data', $data);
        return $this->fetch();
    }
    public function delete(){
        $data   = $this->request->param();
        $location = $this->request->param('location');
        if($location=='sjs'){
            Db::name('company_sjs')->where(['id'=>$data['id']])->update(['delete'=>1,'deletetime'=>time()]);
            $this->success("删除成功！", 'sjs');
        }elseif($location=='anli'){
            Db::name('company_anli')->where(['id'=>$data['id']])->update(['delete'=>1,'deletetime'=>time()]);
            $this->success("删除成功！", 'anli');
        }elseif($location=='hd'){
            Db::name('company_hd')->where(['id'=>$data['id']])->update(['delete'=>1,'deletetime'=>time()]);
            $this->success("删除成功！", 'hd');
        }

    }

    public function order()
    {
        $param   = $this->request->param();
        $user = cmf_get_current_user();
        $data['company'] = db('company')->where(['uid'=>$user['id']])->find();
        $data['order'] = db('order')->where(['company_id'=>$data['company']['id']])->order("id DESC")->paginate(20);
        // print_r($data['order']);die;


        $data['order']->appends($param);
        // 获取分页显示
        $data['page'] = $data['order']->render();

        $data['param']=$param;

        $this->assign('data', $data);
        return $this->fetch();
    }
    public function order_info(){
        $id = $this->request->param('id');

        $data['order'] = db('order')->where(['id'=>$id])->find();
        if($data['order']['type']==0){
            Db::name('order')->where(['id' => $id])->update(['type' => 1, 'typetime' => time()]);
        }
        $data['house'] = db('house_copy')->where(['user_id'=>$data['order']['uid']])->find();


        $data['company_ids'] = db('order')->where(['uid'=>$data['order']['uid']])->field('company_id')->select();
        // print_r($data['comapny_ids']);die;

        $this->assign('data', $data);
        return $this->fetch();
    }
    public function order_state(){          //修改订单状态
        $param = $this->request->param();

        $company_id=cmf_get_current_user_id();
        $company = db('company')->where(['uid'=>$company_id])->find();

        $order = db('order')->where(['id'=>$param['id'],'company_id'=>$company['id']])->find();
        // print_r($order);die;
        if($order){
            db('order')->where(['id'=>$order['id']])->update(['status'=>$param['state']]);
        }
    }
    public function publish()           //上线
    {
        $param = $this->request->param();

            $id = $this->request->param('id');
        // print_r(123);die;
        if (isset($id) && isset($param["yes"])) {
        // print_r(123);die;
            Db::name($param['db_name'])->where(['id' => $id])->update(['type' => 1,'status'=>1, 'typetime' => time()]);
            $this->success("上线成功！", '');
        }
        if (isset($id) && isset($param["no"])) {
        // print_r(456);die;
            Db::name($param['db_name'])->where(['id' => $id])->update(['type' => 0, 'typetime' => time()]);
            $this->success("取消上线成功！", '');
        }
    }
    /**
     * 前台首页
     */
    public function hd()
    {
        $uid=cmf_get_current_user_id();

        $data['company'] = db('company')->where(['uid'=>$uid])->find();
            $data['type'] = $this->request->param('type');
        if($data['type']!=0&&$data['type']){
            $where['type']=$data['type'];
        }
        $where['company_id']=$data['company']['id'];
        $where['delete']=0;
        // $where['type']=1;
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