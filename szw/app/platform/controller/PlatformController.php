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
namespace app\platform\controller;

use cmf\controller\HomeBaseController;
use think\Db;

class PlatformController extends HomeBaseController
{
    public function add_user()      //获客平台其他城市无效客户
    {
        $data = $this->request->param();
            unset($data['system_time']);
            unset($data['kefu_status']);
            unset($data['become_price']);
            unset($data['become_time']);
            // unset($data['kefu_remark']);
            unset($data['remark_status']);
        // print_r($data);die;


        $this_user = db('house')->where(['mobile' => $data['mobile']])->find();
        if($this_user){
            $user_id=$this_user['user_id'];
        }else{
            $user_id =Db::name('house')->insertGetId($data);//用户表

        }
        unset($data['status']);
        unset($data['keyword']);
        $data['user_id']=$user_id;
        $user_id =Db::name('house_copy')->insertGetId($data);//用户表
            unset($data['kefu_remark']);
        $info_id =Db::name('house_info')->insertGetId($data);//用户表
        // print_r($data);die;
        return 1;die;
    }
    /**
     * 前台首页
     */
    public function index()
    {
        $where['delete']=0;
        $where['type']=1;
        $where['status']=1;
        $data['company'] = db('company')->where($where)->paginate(30);

        $data['company_desc'] = db('company')->where($where)->order('id desc')->paginate(7);

        $data['fslist'] = db('xuexi') ->where(['status'=>1])->order('id desc')->paginate(6);
        $data['comment'] = db('company_comment') ->where(['status'=>1])->group('company_id')->order('id desc')->paginate(30);

        $data['anli'] = db('company_anli')->where(['status'=>1,'class'=>array('<>',4)])->order('rank desc')->paginate(6);
        $data['gongdi'] = db('company_anli')->where(['status'=>1,'class'=>4])->order('rank desc')->paginate(20);

        $this->assign('data', $data);
        return $this->fetch();
    }
    public function gongsi()
    {
        $city=450200;
        $data['area'] = db('area')->where(['citycode'=>$city])->select();
        // print_r(get_area('450200'));die;
        $param = $this->request->param();
        if(@$param['area']){
            // print_r($param['area']);die;
            $where['area'] = ['like', "%".$param['area']."%"];
        }
        if(@$param['fwlx']){
            // $where['fwlx']=array('like','%$param['fwlx']%');
            $where['fwlx'] = ['like', "%".$param['fwlx']."%"];
        }
        if(@$param['rz']){
            $where['rz']=$param['rz'];
        }
        $where['delete']=0;
        $where['type']=1;
        $where['status']=1;
        $data['companys'] = db('company')->where($where)->order('rank desc')->paginate(20);

        $data['new_companys'] = db('company')->where($where)->order('rank desc')->paginate(5);

        $data['companys']->appends($where);
        $page = $data['companys']->render();
        $this->assign('area', isset($param['area']) ? $param['area'] : '');
        $this->assign('fwlx', isset($param['fwlx']) ? $param['fwlx'] : '');
        $this->assign('rz', isset($param['rz']) ? $param['rz'] : '');
        $this->assign('data', $data);
        $this->assign('page', $page);
        return $this->fetch();
    }
    public function anli()
    {
        $param = $this->request->param();
        $where['class']=array('<>',4);
        if(@$param['hx']){
            $where['hx']=$param['hx'];
        }
        if(@$param['fg']){
            $where['fg']=$param['fg'];
        }
        if(@$param['ht_price']){
            $where['ht_price']=$param['ht_price'];
        }
        if(@$param['class']){
            $where['class']=$param['class'];
        }

        // print_r($param);die;
        $where['delete']=0;
        $where['type']=1;
        $where['status']=1;
        $data['anli'] = db('company_anli')->where($where)->order('rank desc')->paginate(20);

        $data['anli']->appends($param);
        // // 获取分页显示
        $page = $data['anli']->render();
        // print_r($page);die;
        $this->assign('data', $data);
        $this->assign('hx', isset($param['hx']) ? $param['hx'] : '');
        $this->assign('fg', isset($param['fg']) ? $param['fg'] : '');
        $this->assign('ht_price', isset($param['ht_price']) ? $param['ht_price'] : '');
        $this->assign('class', isset($param['class']) ? $param['class'] : '');
        $this->assign('page', $page);
        return $this->fetch();
    }
    public function alxq()  //案例详情
    {
        $id = $this->request->param('id');
        $where['delete']=0;
        $where['type']=1;
        $where['status']=1;
        $where['id']=$id;
        $data['anli'] = db('company_anli')->where($where)->find();
        $data['images']=explode(',',$data['anli']['images']);

        $data['up_anli'] = db('company_anli')->where(['delete'=>0,'type'=>1,'status'=>1,'class'=>array('<>',4),'id'=>array('<',$id)])->field('id,images')->find();
        $data['down_anli'] = db('company_anli')->where(['delete'=>0,'type'=>1,'status'=>1,'class'=>array('<>',4),'id'=>array('>',$id)])->field('id,images')->find();

        $data['company'] = db('company')->where(['id'=>$data['anli']['company_id']])->find();

        // print_r($data['company']);die;
        $this->assign('data', $data);
        return $this->fetch();
    }

    public function gonggao()
    {
        return $this->fetch();
    }
    public function pingjia()
    {
        $city=450200;
        $data['area'] = db('area')->where(['citycode'=>$city])->select();
        // print_r(get_area('450200'));die;
        $param = $this->request->param();
        if(@$param['area']){
            // print_r($param['area']);die;
            $where['area'] = ['like', "%".$param['area']."%"];
        }
        $where['delete']=0;
        $where['type']=1;
        $where['status']=1;
        $data['companys'] = db('company')->where($where)->order('rank desc')->paginate(20);

        $data['tj_companys'] = db('company')->where(['rz'=>2])->order('rank desc')->paginate(5);
        $data['companys']->appends($where);
        $page = $data['companys']->render();
        $this->assign('area', isset($param['area']) ? $param['area'] : '');
        $this->assign('data', $data);
        $this->assign('page', $page);
        return $this->fetch();
    }
    public function pingjiaxq()
    {
        $param = $this->request->param();
        $id = $this->request->param('company_id');
        $where['delete']=0;
        $where['type']=1;
        $where['status']=1;
        $where['id']=$id;
        $data['company'] = db('company')->where($where)->find();

        $data['rp_companys'] = db('company')->where(['id'=>array('<>',$id)])->order('rank desc')->paginate(5);
        $data['rz_companys'] = db('company')->where(['id'=>array('<>',$id),'rz'=>2])->order('rank desc')->paginate(5);

        $data['comment'] = db('company_comment')->where(['company_id'=>$id,'status'=>1])->order('id desc')->paginate(20);
        $data['hp_comment'] = db('company_comment')->where(['company_id'=>$id,'gy_pf'=>3,'fw_pf'=>3,'sj_pf'=>3,'sh_pf'=>3])->count();
        $data['zp_comment'] = db('company_comment')->where(['company_id'=>$id,'gy_pf'=>2,'fw_pf'=>2,'sj_pf'=>2,'sh_pf'=>2])->count();
        $data['cp_comment'] = db('company_comment')->where(['company_id'=>$id,'gy_pf'=>1,'fw_pf'=>1,'sj_pf'=>1,'sh_pf'=>1])->count();

        unset($where['id']);
        $where['class']=array('<>',4);
        $data['anli'] = db('company_anli')->where($where)->order('rank desc')->paginate(2);
        $data['hd'] = db('company_hd')->where(['type'=>1,'delete'=>0])->order('rank desc')->paginate(5);
        // print_r($data['anli']);die;
        $this->assign('data', $data);
        return $this->fetch();
    }
    public function add_common(){
        $param = $this->request->param();

            $authcode = db("authcodes")->where(['cellphone'=>$param['mobile']])->order('id desc')->find();
            if($param['code']!=$authcode['authcode']){
                $this->error("验证码错误！");
            }
            if($authcode['timeline']<time()){
                $this->error("验证码失效！");
            }
            unset($param['score']);
            unset($param['code']);
            $param['time']=time();
            // print_r($param);die;
            $user_id =Db::name('company_comment')->insertGetId($param);//用户表
        $this->success("评论成功！");
        // print_r($user_id);die;
    }

    public function liuc()
    {
        return $this->fetch();
    }
    public function baozhang()
    {
        return $this->fetch();
    }
    public function baojia()
    {
        return $this->fetch();
    }
    public function wyzx()
    {
        return $this->fetch();
    }
    public function znbaojia()
    {
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


    public function gongdi()
    {
        $param = $this->request->param();
        if(@$param['hx']){
            $where['hx']=$param['hx'];
        }
        if(@$param['fg']){
            $where['fg']=$param['fg'];
        }
        if(@$param['ht_price']){
            $where['ht_price']=$param['ht_price'];
        }

        // print_r($param);die;
        $where['delete']=0;
        $where['type']=1;
        $where['status']=1;
        $where['class']=4;
        $data['gongdi'] = db('company_anli')->where($where)->order('rank desc')->paginate(20);
        $data['new_gongdi'] = db('company_anli')->where($where)->order('id desc')->paginate(20);
        unset($where['class']);
        $data['companys'] = db('company')->where($where)->order('id desc')->paginate(4);



        $data['gongdi']->appends($param);
        // // 获取分页显示
        $page = $data['gongdi']->render();
        // print_r($page);die;
        $this->assign('data', $data);
        $this->assign('page', $page);
        return $this->fetch();
    }

    public function gongdixq()
    {
        $id = $this->request->param('id');

        // print_r($param);die;
        $where['delete']=0;
        $where['type']=1;
        $where['status']=1;
        $where['id']=$id;
        $data['gongdi'] = db('company_anli')->where($where)->find();

        // print_r($page);die;
        $this->assign('data', $data);
        return $this->fetch();
    }


    /*工地页面*/
    public function gongdi_new()
    {
        $data=db('gongdi')->select();
        // print_r($data);die;

        $count=db('gongdi')->count();

        $this->assign('data',$data);
        $this->assign('count',$count);
        // print_r($data);die;

        return $this->fetch();
    }

    /*工地详细页面*/
    public function gongdilist()
    {

        $id = $this->request->param();
        $list=db('gongdi')->where('id',$id['id'])->find();
        // $str=$list['photo_urls'];
        $photo=explode(",",$list['photo_urls']);
        $photo1=explode(",",$list['photos']);
        $photo2=explode(",",$list['photos2']);
        $photo3=explode(",",$list['photos3']);
        $photo4=explode(",",$list['photos4']);
        $photo5=explode(",",$list['photos5']);

        $this->assign('list',$list);
        $this->assign('photo',$photo);
        $this->assign('photo1',$photo1);
        $this->assign('photo2',$photo2);
        $this->assign('photo3',$photo3);
        $this->assign('photo4',$photo4);
        $this->assign('photo5',$photo5);
        return $this->fetch();

    }

    /*学装修页面*/
    public function xuexi()
    {

        $data_array=db('answer')->select();
        $data=[];
         foreach ($data_array as $key => $value) {
            $parentid=$value['id'];
            $xuexi = db('xuexi') ->where(['parent_id'=>$parentid,'status'=>1])->select();
            // $data[$value['name']]['xuexi']=$xuexi;
            $data[$value['id']]=$xuexi;
        }
        $key=array_keys($data);
        // $data = db('xuexi')
        //         ->alias('a')
        //         ->join('answer b ','a.parent_id = b.id')
                // ->where()
                // ->field('a.id,a.title,a.keywords,a.excerpt,a.content,a.thumbnail,a.photo_urls,a.parent_id,b.id as bid,b.alias,b.thumbnail,b.name')
                // ->select();
                // ->column('a.id');
                // print_r(db('xuexi')->getLastSql());die;
        $this->assign('data',$data);
        $this->assign('data_array',$data_array);
        $this->assign('key',$key);
        return $this->fetch();
    }
    /*学装修文章页面*/
     public function fs()
    {
        $id = $this->request->param();
        $fslist = db('xuexi') ->where(['parent_id'=>$id['id'],'status'=>1])->order('id desc')->paginate(20);

        $fenlei = db('answer') ->where('id',$id['id'])->find();

        /*精品推荐*/
        $recommend = db('xuexi') ->where('recommended',1)->order('id desc')->limit(8)->select();

        /*点击排行*/
        $rankings = db('xuexi') ->where(['parent_id'=>$id['id'],'status'=>1])->limit(8)->order('click desc')->select();


        $fslist->appends($id);
        // // 获取分页显示
        $page = $fslist->render();

        $this->assign('page',$page);
        $this->assign('fslist',$fslist);
        $this->assign('fenlei',$fenlei);
        $this->assign('recommend',$recommend);
        $this->assign('rankings',$rankings);
        return $this->fetch();
    }

    /*学装修文章详细页面*/
    public function fslist()
    {
        $id = $this->request->param();

        $fslist = db('xuexi') ->where(['id'=>$id['id'],'status'=>1])->find();
        // print_r($fslist);die;
        $fenlei = db('answer') ->where('id',$fslist['parent_id'])->find();

        //上一篇
        $prev_article=db('xuexi')->where(['id'=>array('<',$id['id']),'status'=>1,'parent_id'=>$fslist['parent_id']])->find();
        $this->assign('prev_article',$prev_article);
        //下一篇
        $next_article=db('xuexi')->where(['id'=>array('>',$id['id']),'status'=>1,'parent_id'=>$fslist['parent_id']])->find();
        // print_r($next_article);die;
        $this->assign('next_article',$next_article);

        $fslisttj = db('xuexi') ->where('recommended','1')->select();

        $this->assign('fslisttj',$fslisttj);
        $this->assign('fslist',$fslist);
        $this->assign('fenlei',$fenlei);
        return $this->fetch();
    }

}
