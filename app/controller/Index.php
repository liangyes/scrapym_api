<?php
namespace app\controller;

use app\BaseController;
use think\facade\Db;
class Index extends BaseController
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
     * 首页
    */
    function index()
    {
        
         
         return View::fetch();
         
         
    }
    /*
     * 首页分类推荐
    */
    function indexType():string
    {
        
    	  //
          $meijutt=Db::name('meijutt')->group('platform')->field('count(id) as badge,platform as id')->select()->toArray();
         
          foreach ($meijutt as $key => $value) {
                  
                  $meijutt[$key]['cuIcon']='video';
                  $meijutt[$key]['color']=$this->color[array_rand($this->color)][0]; 
                  switch ($value['id']) {
                            case '1':
                                $meijutt[$key]['name']='美剧';
                                break;
                            case '2':
                                $meijutt[$key]['name']='韩剧';
                                break;
                            case '3':
                                $meijutt[$key]['name']='日剧';
                                break;
                            case '4':
                                $meijutt[$key]['name']='泰剧';
                                break;
                            default:
                                # code...
                                break;
                 } 

          }
           exit(json_encode($meijutt));
         
         
    }

    public function getHot():string
    {
        /*
            每组取三条数据   
        */
         $resData=[];
         foreach ($this->platform as $key => $value) {
                  $meijutt=Db::name('meijutt')->where(['platform'=>$value])->field('id,name,img,content,update_title,platform')->limit(3)->select()->toArray();
                  if($meijutt){
                        $data['id']=$value;
                        switch ($value) {
                            case '1':
                                $data['name']='美剧';
                                break;
                            case '2':
                                $data['name']='韩剧';
                                break;
                            case '3':
                                $data['name']='日剧';
                                break;
                            case '4':
                                $data['name']='泰剧';
                                break;
                            default:
                                # code...
                                break;
                        }  
                        $data['list']= $meijutt; 
                        $data['color']='line-'.$this->color[array_rand($this->color)][0];   
                        $resData[]=$data;
                  }else{
                    continue;
                  }

                  
         }

         exit(json_encode($resData));   
    }
    /*
      获取该平台分类
     */
    public function getType(int $platform):string
    {
            $data=Db::name('meijutt')->where(['platform'=>$platform])->field('type')->group('type')->order('create_time')->select()->toArray();;
            foreach ($data as $key => $value) {
                $data[$key]['color']=$this->color[array_rand($this->color)][1]; 
                $data[$key]['count']=Db::name('meijutt')->where(['type'=>$value,'platform'=>$platform])->count();
            }
            exit(json_encode($data));
    }
    /*
    *  获取该平台列表
    **/
    public function getList(int $platform, string $type='', int $page=1):string
    {
                if($type)$where['type']=$type;
                $where['platform']=$platform;

                $data=Db::name('meijutt')->where($where)->page($page)->limit(5)->select()->toArray();
                $count=Db::name('meijutt')->where($where)->count();
                exit(json_encode(['data'=>$data,'pages_count'=>$count]));
    }
    /*
    * 播放列表
    */
    public function getMove(int $id):string
    {				
					
					 
                    $move=Db::name('meijutt')->where(['id'=>$id])->find();

                    $type=Db::name('move_video')->where(['move_id'=>$id])->field('type')->group('type')->select()->toArray();
                    
                    $follow=Db::name('follow')->where(['move_id'=>$id])->find();
                    if ($follow) {
                      $follow='已关注';
                    }else{
                       $follow='关注';
                    }

                    foreach ($type as $key => $value) {
                            if($move['platform']==1 && $value['type']==3){
                                unset($type[$key]);
                                continue;
                            }
                            $type[$key]['list']=Db::name('move_video')->where(['move_id'=>$id,'type'=>$value])->select();

                    }
					$move='';
					$type;='';
					$follow='';
                    $data['move']=$move;
                    $data['data']=$type;
                    $data['follow']=$follow;
                    $data['testdes']='获取更多资源，请关注微信公众号：尼莫看看';

                    exit(json_encode($data));
    }
    /*
    搜索
    */
    public function search(string $name):string
    {


                $data=Db::name('meijutt')->where('name|type','like',$name.'%')->select()->toArray();
                exit(json_encode($data));
    }
    /*
    首页轮播
     */
    public  function banner(){
            $count=Db::name('meijutt')->count();
            $numbers = range (1,$count);

            //shuffle 将数组顺序随即打乱
            //

            shuffle ($numbers);

            //array_slice 取该数组中的某一段 

            $result = array_slice($numbers,0,5);
           
            $data=Db::name('meijutt')->where('id','in',$result)->field('id,img')->select()->toArray();
            
            exit(json_encode($data));
    }

}
