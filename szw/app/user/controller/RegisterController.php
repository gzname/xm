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

use cmf\controller\HomeBaseController;
use think\Validate;
use app\user\model\UserModel;
use think\Db;

class RegisterController extends HomeBaseController
{

    /**
     * 前台用户注册
     */
    public function index()
    {
        $redirect = $this->request->post("redirect");
        if (empty($redirect)) {
            $redirect = $this->request->server('HTTP_REFERER');
        } else {
            $redirect = base64_decode($redirect);
        }
        session('login_http_referer', $redirect);

        if (cmf_is_user_login()) {
            return redirect($this->request->root() . '/');
        } else {
            return $this->fetch(":register");
        }
    }

    /**
     * 前台用户注册提交
     */
    public function doRegister()
    {
        $log=0;
        if ($this->request->isPost()) {

            $data = $this->request->post();


            $register          = new UserModel();
            $user['user_pass'] = $data['password'];
            $user['mobile'] = $data['tel'];
            $user['user_type'] = $data['user_type'];
            $user['name'] = $data['name'];


            $authcode = Db::name("authcodes")->where(['cellphone'=>$user['mobile']])->order('id desc')->find();
            // print_r($data['code']);die;
            if($data['code']!=$authcode['authcode']){
                $this->error("验证码错误！");
            }

        $userStatus = 1;
        if (cmf_is_open_registration()) {
            $userStatus = 2;
        }
            if ($user['mobile']) {

                $result = Db::name("user")->where(['mobile'=>$user['mobile']])->find();
                if (empty($result)) {
                    $user_data   = [
                        'mobile'          => empty($user['mobile']) ? '' : $user['mobile'],
                        'user_nickname'   => empty($user['name']) ? '' : $user['name'],
                        'user_pass'       => cmf_password($user['user_pass']),
                        'last_login_ip'   => get_client_ip(0, true),
                        'create_time'     => time(),
                        'last_login_time' => time(),
                        'user_status'     => $userStatus,
                        "user_type"       => empty($user['user_type']) ? '' : $user['user_type'],//
                    ];
                    $userId = Db::name("user")->insertGetId($user_data);
                    if($user['user_type']==3){
                        $company_data   = [
                            'name'=> empty($data['qc']) ? '' : $data['qc'],
                            'jc'=> empty($data['jc']) ? '' : $data['jc'],
                            'time'       => time(),
                            'uid'   => $userId,
                        ];
                        // print_r($company_data);die;
                        Db::name("company")->insertGetId($company_data);
                    }
                    $data   = Db::name("user")->where('id', $userId)->find();
                    cmf_update_current_user($data);
                    $token = cmf_generate_user_token($userId, 'web');
                }else{
                $log = 1;

                }
            } else {
                $log = 2;
            }
            $sessionLoginHttpReferer = session('login_http_referer');
            $redirect                = empty($sessionLoginHttpReferer) ? cmf_get_root() . '/' : $sessionLoginHttpReferer;
            // print_r($redirect);die;
            switch ($log) {
                case 0:
                    $this->success('注册成功', './user/profile/center');
                    break;
                case 1:
                    $this->error("您的账户已注册过");
                    break;
                case 2:
                    $this->error("您输入的账号格式错误");
                    break;
                default :
                    $this->error('未受理的请求');
            }

        } else {
            $this->error("请求错误");
        }

    }

    /**
     * 获取手机验证码
     */
    public function ajax_phone_verify()
    {
        $data   = $this->request->param();

        $cellphone = $data['cellphone'];
        $type = $data['type'];

        if (!isset($cellphone)) {
            echo json_encode(array('code' => -1, 'tips' => '请输入您的手机号码'));
            exit;
        }

    $month_start=date('Y-m-d',time());
    $month_end = date('Y-m-d',time())+86399;


                $iphone_count = db('authcodes')->where(array('cellphone'=>$cellphone,'time'=>array(array('>',$month_start),array('<',$month_end),'AND')))->count();

    if($iphone_count>=5){echo json_encode(array('code'=>-1,'tips'=>'当日短信发送数量达到上限，请明天再来操作把！'));exit;}



        $authcode = db('authcodes')->where(['cellphone'=>$cellphone,'type'=>$type,'timeline'=>array('>',time())])->find();

        if (!$authcode) {
            $authcode=array(
                'cellphone'=>$cellphone,
                'type'=>$type,
                'authcode'=>rand(100000,999999),
                'time'=>time(),
                'timeline'=>time()+10*60,
            );
            Db::name('authcodes')->insert($authcode);
        }
        $msg = array(
            'cellphone' => $cellphone,
            'msg' => '您的验证码是：'.$authcode['authcode'].'。请不要把验证码泄露给其他人。',
            'time' => time(),
        );
        $html = file_get_contents('http://106.ihuyi.cn/webservice/sms.php?method=Submit&account=C05013052&password=c4ab272ae48114c3a92f72af63c3673d&mobile=' . $msg['cellphone'] . '&content=' . $msg['msg']);
        //发送手机短信
        echo json_encode(array('code' => 1, 'tips' => '验证码已经发送到您的手机，请注意查收'));
        exit;
    }
}