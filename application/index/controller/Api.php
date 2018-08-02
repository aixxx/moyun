<?php

namespace app\index\controller;

use app\common\controller\Frontend;
use fast\Random;
use think\Config;
use Think\Db;
use think\Env;
use think\Image;
use think\Request;

class Api extends Frontend
{

    protected $noNeedLogin = '*';
    protected $noNeedRight = '*';
    protected $layout = '';

    public $sqlnum = 37;

    public function _initialize()
    {
        parent::_initialize();
    }

    /**
    获取列表数据
     */
    public function getList(){
        $request = Request::instance();
        $order = $request->param("order",1,"intval");
        $paginateArr = [
            "page" => $request->param('page',1,'intval'),
            "list_rows" => $request->param('pageSize',10,'intval'),
        ];
        //默认排序：最新
        if($order == 1){
            $data = $this->getListOrderId($paginateArr);
        }elseif ($order == 2){
            $data = $this->getListOrderVote($paginateArr);
        }
        foreach($data['list'] as $k=>$v){
            $data['list'][$k]['image'] = Config::get("upload.imgurl").$v['image'];
            $data['list'][$k]['id'] =  $v['id'] - $this->sqlnum;
        }

        jsond(200,'',$data);

    }

    protected function getListOrderVote($paginateArr){
        $oauth = $this->getAdminModel("Oauth");

        $product = Db::table("fa_product")->alias("a")
            ->join("oauth b","a.oauth_id = b.id")
            ->distinct(true)
            ->field("vote,oauth_id")
            ->where(["status"=>"1"])
            ->order("b.vote desc, a.oauth_id desc")
            ->paginate($paginateArr['list_rows'], false, $paginateArr);

        $product_array = $product->toArray();
        $oauth_ids = [];
        foreach ($product_array['data'] as $k=>$v){
            $oauth_ids[] = $v["oauth_id"];
        }
        $total = Db::table("fa_product")
            ->field("oauth_id")
            ->where(["status"=>"1"])
            ->group("oauth_id")
            ->count();
        
        $data = [
            'currentPage' => $paginateArr['page'],
            'lastPage' => ceil($total / $paginateArr['list_rows']),
            'total' => $total,
        ];
        
        $data["list"] = $oauth
            ->with("product")
            ->field('id, vote, platform')
            ->where(['id'=>['in',implode(",",$oauth_ids)]])
            ->order("vote desc,id desc")
            ->select();

        return $data;
    }

    protected function getListOrderId($paginateArr){
        $oauth = $this->getAdminModel("Oauth");
        $product = Db::table("fa_product")
            ->distinct(true)
            ->field("oauth_id")
            ->where(["status"=>"1"])
            ->order("createtime desc")
            ->paginate($paginateArr['list_rows'], false, $paginateArr);

        $product_array = $product->toArray();
        $oauth_ids = [];
        foreach ($product_array['data'] as $k=>$v){
            $oauth_ids[] = $v["oauth_id"];
        }
		
        $total = Db::table("fa_product")
            ->field("oauth_id")
            ->where(["status"=>"1"])
            ->group("oauth_id")
            ->count();
        
        $data = [
            'currentPage' => $paginateArr['page'],
            'lastPage' => ceil($total / $paginateArr['list_rows']),
            'total' => $total,
        ];
        $list = $oauth
            ->with("product")
            ->field('id, vote, platform')
            ->where(['id'=>['in',implode(",",$oauth_ids)]])
            ->select();

        foreach ($oauth_ids as $k1 => $v1){
            foreach ($list as $k=>$v){
                if($v["id"] == $v1){
                    $data["list"][$k1] = $v;
                }
            }
        }
        return $data;
    }

    /**
    *   根据ID查询
     */
    public function getById(){
        $id = Request::instance()->param("id",0,"intval");
        if(!$id) jsond(0,'params error');

        $oauth = $this->getAdminModel("Oauth");

        $list = $oauth
            ->with("productMany")
            ->field("id,vote,platform,name")
            ->find($id + $this->sqlnum);
        
        if(!$list) jsond(0,'data error');
        //if(!$list->product_many) jsond(0,'data error for pruduct');
        
        $list['rank'] = $oauth->where(['vote'=>['>',$list['vote']]])->count() + 1;
        foreach($list->product_many as $k=>$v){
            $v->image = Config::get("upload.imgurl").$v->image;
            $v->id = $v->id - $this->sqlnum;
        }
        
        jsond(200,'',$list);
    }

    /**
    *   投票
     */
    public function vote(){
        if(date("Y-m-d") >= Env::get("oauth.over_time")) jsond(0, '活动结束');

        $uid = session("MOBOO_OAUTH_ID");
        if(!$uid) jsond(0, 'login time out');

        $id = Request::instance()->param("id",0,"intval");
        if(!$id) jsond(0,'params error');

        if($uid == $id) jsond(0, '不能给自己投票');

        $oauth = $this->getAdminModel("Oauth");
        $list = $oauth
            ->with("productMany")
            ->field("id,vote,platform")
            ->find($id);
        if(!$list) jsond(0,'data error');
        if(!$list->product_many) jsond(0,'data error for pruduct');

        $votelog = $this->getAdminModel("Votelog");
        //每天最多6票
        $max = $votelog->whereTime('createtime', 'today')
            ->where(['oauth_id'=> $uid])
            ->count();
        if($max >= 6) jsond(0, '您今天投票机会已经用完');
        //每天每人最多两票
        $p_vote = $votelog->whereTime('createtime', 'today')
            ->where(['oauth_id'=> $uid, 'oauth_pid'=> $id])
            ->count();
        if($p_vote >= 2) jsond(0, '每天最多给同一作品投两票');

        $data = [
            'oauth_id'=> $uid,
            'oauth_pid' => $id,
        ];
        $res = $votelog->isUpdate(false)->save($data);
        if($res){
            $oauth->where('id', $id)->setInc('vote');
            jsond(200, '投票记录成功');
        }else {
            jsond(0, '投票失败, 请重试');
        }
    }

    /**
    *   记录上传数据
     **/
    public function addUpload(){
        $imgs = Request::instance()->param("data/a");
        /*$imgs = [
            '/uploads/20180718/d408db57f0008965f96878790907d53e.png',
            '/uploads/20180718/d408db57f0008965f96878790907d53e.png',
            '/uploads/20180718/d408db57f0008965f96878790907d53e.png',
            '/uploads/20180718/d408db57f0008965f96878790907d53e.png',
            '/uploads/20180718/d408db57f0008965f96878790907d53e.png',
        ];*/
        if(!is_array($imgs)) jsond(0, 'params error');
        if(!$this->getIsUpload()) jsond(0, '您已经上传过作品');
        $data = [];
        foreach ($imgs as $k=>$v) {
            $data[$k] = [
                'oauth_id' => session("MOBOO_OAUTH_ID"),
                'image' => str_replace(Config::get("upload.imgurl"),"", $v),
                'status' => 0,
            ];
        }
        //print_r($data);die;
        $res = $this->getAdminModel("Product")->isUpdate(false)->saveAll($data);
        if($res){
            jsond(200, '上传成功');
        }else{
            jsond(0, '记录失败');
        }
    }

    /**
    *   图片上传
     **/
    public function upload(){
        if(date("Y-m-d") >= Env::get("oauth.over_time")) jsond(0, '活动结束');
        //$base64 = Config::get("base64.content");
        $base64 = Request::instance()->param("data","");
        if(!$base64) jsond(0, 'params empty');

        if(!$this->getIsUpload()) jsond(0, '您已经上传过作品');

        $upload = Config::get("upload");

        //print_r($upload);die;
        if(preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64, $result)){
            $suffix = $result[2];
            //验证文件后缀
            if(!in_array($suffix,array('jpeg','jpg','gif','bmp','png'))) jsond(0, '图片格式错误');
            //计算文件大小
            $img_len = strlen(str_replace($result[1], '', $base64));
            $file_size = $img_len - ($img_len/8)*2;
            $file_size = number_format($file_size/1024, 2);
            $file_size = number_format($file_size/1024, 2);
            if($file_size > (int)$upload['maxsize']) jsond(0, '图片不能超过5M，请重新选择图片');

            //设置上传信息
            $savekey = "/uploads/".date('Ymd')."/".time()."_".rand(1000,9999).".".$suffix;
            //文件路径
            $uploadDir = substr($savekey, 0, strripos($savekey, '/') + 1);
            if(!file_exists(ROOT_PATH . '/public' . $uploadDir))
                mkdir(ROOT_PATH . '/public' . $uploadDir);
            //echo ROOT_PATH . '/public' . $uploadDir;die;
            //文件名
            $fileName = substr($savekey, strripos($savekey, '/') + 1);
            //保存图片
            if(file_put_contents(ROOT_PATH . '/public' . $uploadDir. $fileName, base64_decode(str_replace($result[1], '', $base64)))){
                //记录上传信息
                $imagewidth = $imageheight = 0;
                if (in_array($suffix, ['gif', 'jpg', 'jpeg', 'bmp', 'png', 'swf'])) {
                    $imgInfo = getimagesize(ROOT_PATH . '/public' . $uploadDir. $fileName);
                    $imagewidth = isset($imgInfo[0]) ? $imgInfo[0] : $imagewidth;
                    $imageheight = isset($imgInfo[1]) ? $imgInfo[1] : $imageheight;
                }
                $params = array(
                    'admin_id'    => 0,
                    'user_id'     => session("MOBOO_OAUTH_ID"),
                    'filesize'    => $file_size,
                    'imagewidth'  => $imagewidth,
                    'imageheight' => $imageheight,
                    'imagetype'   => $suffix,
                    'imageframes' => 0,
                    'mimetype'    => "image/".$suffix,
                    'url'         => $savekey,
                    'uploadtime'  => time(),
                    'storage'     => 'local',
                    'sha1'        => time(),
                );
                $attachment = model("attachment");
                $attachment->data(array_filter($params));
                $attachment->save();

                //生成缩略图
                $image = Image::open(ROOT_PATH . '/public'.$savekey);
                $thumb = str_replace(".".$suffix,"_".Config::get("upload.width")."w.".$suffix, $savekey);
                $image->thumb(Config::get("upload.width"), 10000)->save(ROOT_PATH . '/public'.$thumb);
                jsond(200, '', ['img_url'=> Config::get("upload.imgurl").$thumb]);
            }else{
                jsond(0, '图片上传失败');
            }
        }else {
            jsond(0, 'params error');
        }
    }


    /**
    *   是否有上传资格
     */
    public function isUpload(){
        if(date("Y-m-d") >= Env::get("oauth.over_time")) jsond(0, '活动结束');
        $isUpload = $this->getIsUpload();
        if($isUpload) jsond(1,'可以上传');
        $uid = session("MOBOO_OAUTH_ID");
        $isUpload = $this->getAdminModel("Product")->where(['oauth_id'=>$uid, 'status' => "1"])->find();
        if($isUpload) jsond(2,'已经有审核通过的作品');
        $isUpload = $this->getAdminModel("Product")->where(['oauth_id'=>$uid, 'status' => "0"])->find();
        if($isUpload) jsond(3,'作品正在审核');
    }
    
    public function getIsUpload(){
        if(!session("MOBOO_OAUTH_ID")) jsond(0, 'login time out');

        $map = [
            'oauth_id' => session("MOBOO_OAUTH_ID"),
            'status' => ['<>', '2']
        ];
        $isUpload = $this->getAdminModel("Product")->where($map)->find();
        return $isUpload ? 0 : 1;
    }
    
    
    public function getShare(){
        $url = Env::get("oauth.oauthUrl")."/3rd/weixin/config";
        $url .= "?url=".  urlencode($_SERVER['HTTP_REFERER']);
        $res = curl_get_https($url);
        $res = json_decode($res, true);
        echo json_encode($res['result']);
        die;
    }
}