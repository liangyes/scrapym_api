<?php
namespace app\controller;

use app\BaseController;
use think\facade\Db;
class Weixin extends BaseController
{
    
    public $platform=[1,2,3,4,5];#平台列表
    public $color=[#颜色 
                       ['red','#e54d42'],
                       ['orange','#f37b1d'],
                       ['yellow','#fbbd08'],
                       ['olive','#8dc63f'],
                       ['green','#39b54a'],
                       ['cyan','#1cbbb4'],
                       ['blue','#0081ff'],
                       ['purple','#6739b6'],
                       ['mauve','#9c26b0'],
                       ['pink','#e03997']
                    ];
    public  function __construct(){
         
         header('Access-Control-Allow-Origin:*'); // *代表允许任何网址请求
    }
    /*
     * 小程序登录
    */
    public function login(string $code)
    {
        

         $url="https://api.weixin.qq.com/sns/jscode2session";
         $param['appid']='wx757d60a51c7343';
         $param['secret']='35345345345345345345';
         $param['js_code']=$code;
         $param['grant_type']='authorization_code';

         $data=curlGet($url,60,$param,'https');
    	   $this->add(json_decode($data,1));
         
         
    }
    /*
      新增
     */
    public function add($data){
           $insertData['create_time']=date('Y-m-d H:i:s',time());
           $insertData['last_login_time']=date('Y-m-d H:i:s',time());
           $insertData['openid']=$data['openid'];
           $find=Db::name('user')->where(['openid'=>$data['openid']])->find();
           if(!$find){
                Db::name('user')->insert($insertData);
           }else{
                Db::name('user')->where(['openid'=>$data['openid']])->update(['last_login_time'=>$insertData['last_login_time']]);
           }
           exit($data['openid']);
    }
    /*
    修改
     */
    public function update(){
           $data=input('param.');
           //var_dump($data);exit;
           Db::name('user')->where(['openid'=>$data['openid']])->update($data);

    }
    /*
    获取我的关注
     */
    public function follow(){
           $data=input('param.');
           $sql=" select * from move_meijutt as m left join move_follow as f on m.id=f.move_id where f.openid='".$data['openid']."' order by f.create_time desc";
           $returnData=Db::query($sql);
           exit(json_encode($returnData));
    }
    /*
    关注操作
     */
    public function followDo(){
           $data=input('param.');

           $follow=Db::name('follow')->where(['openid'=>$data['openid'],'move_id'=>$data['move_id']])->find();
           if($follow){
                  Db::name('follow')->where(['openid'=>$data['openid'],'move_id'=>$data['move_id']])->delete();
                  exit('关注');
           }else{
                  $data['create_time']=date('Y-m-d H:i:s',time());
                  Db::name('follow')->insert($data);
                  exit('已关注');
           }
    }
    /*
    获取我的观看历史
     */
    public function wacth(){
           $data=input('param.');
           $sql=" select * from move_meijutt as m left join move_wacth as w on m.id=w.move_id where w.openid='".$data['openid']."'group by move_id order by w.create_time desc";
           //var_dump($sql);exit;
           $returnData=Db::query($sql);
           exit(json_encode($returnData));
    }
     /*
    观看操作
     */
    public function wacthDo(){
           $data=input('param.');
           $data['create_time']=date('Y-m-d H:i:s',time());
           echo exit(Db::name('wacth')->insert($data));
          
    }
    /*
    * 
     */
    
}
