<?php
/**
 * @Author: Marte
 * @Date:   2018-08-17 10:31:47
 * @Last Modified by:   Marte
 * @Last Modified time: 2018-10-08 16:24:41
 */
function convert_arr_key($arr, $key_name)
{
    $result = array();
    foreach($arr as $key => $val){
        $result[$val[$key_name]] = $val;
    }
    return $result;
}
function cut_str($sourcestr,$cutlength)
{
   $returnstr='';
   $i=0;
   $n=0;
   $str_length=strlen($sourcestr);//字符串的字节数
   while (($n<$cutlength) and ($i<=$str_length))
   {
      $temp_str=substr($sourcestr,$i,1);
      $ascnum=Ord($temp_str);//得到字符串中第$i位字符的ascii码
      if ($ascnum>=224)    //如果ASCII位高与224，
      {
$returnstr=$returnstr.substr($sourcestr,$i,3); //根据UTF-8编码规范，将3个连续的字符计为单个字符
         $i=$i+3;            //实际Byte计为3
         $n++;            //字串长度计1
      }
      elseif ($ascnum>=192) //如果ASCII位高与192，
      {
         $returnstr=$returnstr.substr($sourcestr,$i,2); //根据UTF-8编码规范，将2个连续的字符计为单个字符
         $i=$i+2;            //实际Byte计为2
         $n++;            //字串长度计1
      }
      elseif ($ascnum>=65 && $ascnum<=90) //如果是大写字母，
      {
         $returnstr=$returnstr.substr($sourcestr,$i,1);
         $i=$i+1;            //实际的Byte数仍计1个
         $n++;            //但考虑整体美观，大写字母计成一个高位字符
      }
      else                //其他情况下，包括小写字母和半角标点符号，
      {
         $returnstr=$returnstr.substr($sourcestr,$i,1);
         $i=$i+1;            //实际的Byte数计1个
         $n=$n+0.5;        //小写字母和半角标点等与半个高位字符宽...
      }
   }
         if ($str_length>$i){
          $returnstr = $returnstr . "...";//超过长度时在尾处加上省略号
      }
    return $returnstr;
}

function get_gongsi($code,$name="all")
{
    $company = db('company')->where(['id'=>$code])->find();
    if($name!="all"){
        $company=$company[$name];
    }
    return $company;
}
function get_province($code,$name="all")
{
    $province = db('province')->where(['code'=>$code])->find();
    if($name!="all"){
        $province=$province[$name];
    }
    return $province;
}
function get_city($code,$name="all")
{
    $city = db('city')->where(['code'=>$code])->find();
    if($name!="all"){
        $city=$city[$name];
    }
    return $city;
}
function get_area($code,$name="all")
{
    $area = db('area')->where(['code'=>$code])->find();
    if($name!="all"){
        $area=$area[$name];
    }
    return $area;
}

function get_company($code,$name="all")
{
    $area = db('company')->where(['id'=>$code])->find();
    if($name!="all"){
        $area=$area[$name];
    }
    return $area;
}

function fengge_list($code="",$name="all")
{
    $result = array(1=>'现代简约',2=>'欧式风格',3=>'中式风格',4=>'古典风格',5=>'田园风格',6=>'地中海风格',7=>'美式风格',8=>'混搭风格',9=>'其他风格');
    if($name!="all"){
        $result=@$result[$code];
    }
    // print_r($result);die;
    return $result;
}
function dc_list($code="",$name="all")
{
    $result = array(1=>'高端',2=>'中端',3=>'低端');
    if($name!="all"){
        $result=@$result[$code];
    }
    // print_r($result);die;
    return $result;
}
function fwbz_list($code="",$name="all")    //服务保障
{
    $result = array(1=>'保修服务',2=>'施工保养',3=>'上门维修',4=>'优质保洁',5=>'隐蔽工程质保',6=>'整体质保');
    if($name!="all"){
        $result=@$result[$code];
    }
    return $result;
}
function lingyu_list($code="",$name="all")
{
    $result = array(1=>'住宅公寓',2=>'写字楼',3=>'别墅',4=>'专卖展示店',5=>'酒店宾馆',6=>'餐厅酒吧',7=>'歌舞厅',8=>'其他');
    if($name!="all"){
        $result=@$result[$code];
    }
    return $result;
}
function fuwu_list($code="",$name="all")
{
    $result = array(1=>'半包',2=>'全包',3=>'整装');
    if($name!="all"){
        $result=@$result[$code];
    }
    return $result;
}
function guimo($code="",$name="all")    //公司规模
{
    $result = array(1=>'全国连锁',2=>'大中型',3=>'中小型');
    if($name!="all"){
        $result=@$result[$code];
    }
    return $result;
}
function get_sjs($code="",$name="all")
{
    $result = db('company_sjs')->where(['id'=>$code])->find();
    if($name!="all"){
    // print_r($code);die;
        $result=$result[$name];
    }
    return $result;
}
function status($code="",$name="all")       //状态
{
    $result = array(0=>'待审核',1=>'审核通过',2=>'审核失败');
    if($name!="all"){
        $result=@$result[$code];
    }
    return $result;
}
function status_color($code="",$name="all")       //状态
{
    $result = array(0=>'#FF9800',1=>'green',2=>'red');
    if($name!="all"){
        $result=@$result[$code];
    }
    return $result;
}
function hd_type($code="",$name="all"){
    $result = array(0=>'下线中',1=>'进行中',2=>'暂停',3=>'结束');
    if($name!="all"){
        $result=@$result[$code];
    }
    return $result;
}
function order_status($code="",$name="all"){
    $result = array(0=>'未操作',1=>'已量房',2=>'已见面/已到店',3=>'未量房',4=>'已签单');
    if($name!="all"){
        $result=@$result[$code];
    }
    return $result;
}
function order_type($code="",$name="all"){
    $result = array(0=>'未读',1=>'已读');
    if($name!="all"){
        $result=@$result[$code];
    }
    return $result;
}
function type($code="",$name="all")       //状态
{
    $result = array(0=>'下线中',1=>'上线中');
    if($name!="all"){
        $result=@$result[$code];
    }
    return $result;
}
function type_color($code="",$name="all")       //状态
{
    $result = array(0=>'red',1=>'green');
    if($name!="all"){
        $result=@$result[$code];
    }
    return $result;
}
function user_role($code="",$name="all")       //状态
{
    $result = array(1=>'管理员',2=>'商家',3=>'普通用户');
    if($name!="all"){
        $result=@$result[$code];
    }
    return $result;
}

function anli_class($code="",$name="all")       //状态
{
    $result = array(1=>'家装案例',2=>'公装案例',3=>'商铺案例');
    if($name!="all"){
        $result=@$result[$code];
    }
    return $result;
}

function hx_list($code="",$name="all")       //装修类型
{
    $result = array(1=>'一室一厅一卫',2=>'两室一厅一卫',3=>'两室两厅一卫',4=>'两室两厅两卫',5=>'三室一厅一卫',6=>'三室两厅一卫',7=>'三室两厅两卫',8=>'四室两厅两卫',9=>'跃层住宅',10=>'别墅住宅');
    if($name!="all"){
        $result=@$result[$code];
    }
    return $result;
}
function zxlx_list($code="",$name="all")       //装修类型
{
    $result = array(1=>'家装',2=>'公装',3=>'商铺');
    if($name!="all"){
        $result=@$result[$code];
    }
    return $result;
}
function zw_list($code="",$name="all")       //职位列表
{
    $result = array(1=>'设计师',2=>'精英设计师',3=>'主任设计师',4=>'首席设计师',5=>'高级首席设计师',6=>'设计总监',7=>'艺术总监');
    if($name!="all"){
        $result=@$result[$code];
    }
    return $result;
}
function jobtime($code="",$name="all")       //状态
{
    $result = array(1=>'应届',2=>'一年',3=>'二年',4=>'三年~五年',5=>'五年~八年',6=>'八年~十年',7=>'十年以上');
    if($name!="all"){
        $result=@$result[$code];
    }
    return $result;
}
function zongjia_list($code="",$name="all")       //状态
{
    $result = array(1=>'4万以下',2=>'4-7万',3=>'7-10万',4=>'10-15万',5=>'15-20万',6=>'20-30万',7=>'30-50万',8=>'50-100万',9=>'100万以上',10=>'面议');
    if($name!="all"){
        $result=@$result[$code];
    }
    return $result;
}
function get_house_gongsi($user_id,$name="name")   //读取客户第一次填写的号码公司
{
                        $house_gongsi = db('house_info')
                        ->alias('o')
                        ->where(['o.user_id' => $user_id])
                        ->join('company u ','o.gongsi_id = u.id')
                        ->field('u.name,u.jc')
                        ->order("o.addtime asc")
                        ->find();
    return $house_gongsi[$name];
}

function msubstr($str, $start=0, $length, $charset="utf-8", $suffix=true){
    if(function_exists("mb_substr")){
        if($suffix){
            if(strlen($str)>$length)
                return mb_substr($str, $start, $length, $charset)."...";
            else
                return mb_substr($str, $start, $length, $charset);
        }else{
            return mb_substr($str, $start, $length, $charset);
        }
    }elseif(function_exists('iconv_substr')) {
        if($suffix){
            return iconv_substr($str,$start,$length,$charset);
        }else{
            return iconv_substr($str,$start,$length,$charset);
        }
    }
}
function yc_phone($str)
{
    $str = $str;
    $resstr = substr_replace($str, '****', 3, 4);
    return $resstr;
}

//归属地
function gsd($mobile)
{
    $url = 'http://cx.shouji.360.cn/phonearea.php?number=' . $mobile;
    $data = json_decode(file_get_contents($url), true);
    $data = $data['data']['province'] . $data['data']['city'] . $data['data']['sp'];
    return $data;
}

