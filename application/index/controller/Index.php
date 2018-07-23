<?php

namespace app\index\controller;

use app\common\controller\Frontend;
use app\common\library\Token;
use think\Env;
use think\Request;

class Index extends Frontend
{

    protected $noNeedLogin = '*';
    protected $noNeedRight = '*';
    protected $layout = '';

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
        //https://devauth.gomoboo.com/sso/auth?appkey=xxx&successurl=https%3A%2F%2Fwww.b aidu.com%2

        $oauthUrl = Env::get('oauth.oauthUrl') . "/sso/auth";
        $oauthUrl .= "?appkey=".Env::get('oauth.AppKey');
        $oauthUrl .= "&successurl=".urlencode(Env::get('oauth.successurl'));
        $this->redirect($oauthUrl,302);
    }

    /*
     * 授权成功返回记录数据
     * */
    public function callback(){
        $token = Request::instance()->param("token","");
        if(!$token) $this->error("token错误", "/");
        //https://devauth.gomoboo.com/sso/validate?token=498381557-83362202-af93-48d1-ab6eaa19b6c95c44&appkey=xxx&appsecret=xyz
        $url = Env::get('oauth.oauthUrl') . "/sso/validate";
        $url .= "?token=".$token;
        $url .= "&appkey=".Env::get('oauth.AppKey');
        $url .= "&appsecret=".Env::get('oauth.AppSecret');
        echo $url;
        $res = curl_get_https($url);
        //$res = json_decode($res, true);
        echo "<br/>";
        print_r($res);

        echo "<br/> aaa";
        if($res["code"] != 1) $this->error($res["desc"], "/");
        
        $resule = $res["resule"];
        if(!$resule) $this->error("数据错误", "/");

        $oauth = $this->getAdminModel("Oauth");
        $info = $oauth->where(['userNo'=> $resule["userNo"]])->find();

        $data = [
            'openid' => $info["userNo"],
            'header_img_url' => $info["userNo"],
            'name' => $info["nickname"],
            'gender' => $info["gender"],
            'profileDesc' => $info["profileDesc"],
        ];
        if($info){
            $is_save = $oauth->isUpdate(true)->save($data, ["id"=> $info["id"]]);
            session("MOBOO_OAUTH_ID", $info["id"]);
        }else{
            $is_save = $oauth->isUpdate(false)->save($data);
            session("MOBOO_OAUTH_ID", $oauth->getLastInsID());
        }
        //记录成功，跳转活动首页
        $this->redirect('/',302);
    }
}
