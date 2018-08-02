<?php

namespace app\index\controller;

use app\common\controller\Frontend;
use think\Env;
use think\Request;

class Index extends Frontend
{
    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 首页
     *
     */
    public function index()
    {
        $getbyid = Request::instance()->param("getbyid",0,"intval");
        $act = Request::instance()->param("act","20180719dhy");
        session("MOBOO_ACT", $act);
        //session存在，跳转活动首页
        
        if(session("MOBOO_OAUTH_ID")) {
            $isUpload = action("Api/getIsUpload");
            $params = ['id'=> session("MOBOO_OAUTH_ID") - $this->sqlnum,'is_upload'=> $isUpload,'getbyid'=> $getbyid];
            $this->redirect("/act/".session("MOBOO_ACT")."/?". http_build_query($params),302);
        }
        
        $oauthUrl = Env::get('oauth.oauthUrl') . "/sso/auth";
        $oauthUrl .= "?appkey=".Env::get('oauth.AppKey');
        $oauthUrl .= "&successurl=".urlencode(Env::get('oauth.successurl')."?getbyid=".$getbyid);
        $this->redirect($oauthUrl,302);
    }

    /*
     * 授权成功返回记录数据
     * */
    public function callback(){
        $token = Request::instance()->param("token","");
        $getbyid = Request::instance()->param("getbyid",0,"intval");
        if(!$token) jsond(0, 'token not found');
        //https://devauth.gomoboo.com/sso/validate?token=498381557-83362202-af93-48d1-ab6eaa19b6c95c44&appkey=xxx&appsecret=xyz
        $url = Env::get('oauth.oauthUrl') . "/sso/validate";
        $url .= "?token=".$token;
        $url .= "&appkey=".Env::get('oauth.AppKey');
        $url .= "&appsecret=".Env::get('oauth.AppSecret');
        $res = curl_get_https($url);

        $res = json_decode($res, true);

        if($res["code"] != 1) jsond(0, $res["desc"]);

        $result = $res["result"];
        if(!$result) jsond(0, 'data not found');

        $oauth = $this->getAdminModel("Oauth");
        $info = $oauth->where(['openid'=> $result["userNo"]])->find();

        $data = [
            'openid' => $result["userNo"],
            'header_img_url' => $result["headIconUrl"],
            //'name' => base64_encode($result["nickname"]),
            'name' => $result["nickname"],
            'gender' => $result["gender"],
            'profile_desc' => $result["profileDesc"],
            'platform' => getBrowseType() ?: 'mobu',
        ];
        //print_r($data);
        if($info){
            $is_save = $oauth->isUpdate(true)->save($data, ["id"=> $info["id"]]);
            session("MOBOO_OAUTH_ID", $info["id"]);
            $isUpload = action("Api/getIsUpload");
        }else{
            $is_save = $oauth->isUpdate(false)->save($data);
            session("MOBOO_OAUTH_ID", $oauth->id);
            $isUpload = 1;
        }
        //记录成功，跳转活动首页
        $params = ['id'=> session("MOBOO_OAUTH_ID") - $this->sqlnum,'is_upload'=> $isUpload, 'getbyid'=> $getbyid];
        $this->redirect("/act/".session("MOBOO_ACT")."/?". http_build_query($params),302);
    }
}
