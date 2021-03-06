<?php

namespace app\common\controller;

use think\Config;
use think\Controller;
use think\Loader;
use think\Env;

/**
 * 前台控制器基类
 */
class Frontend extends Controller
{
    public $browseType;

    public $activityHome;


    public $sqlnum = 37;
    public function _initialize()
    {
        $this->browseType = getBrowseType();
        if(!$this->browseType) $this->error("目前只支持微博，微信，QQ，魔部APP 里打开，敬请谅解!");
        $this->activityHome = Env::get('oauth.activityHome');
    }


    /**
     *   自定义加载model。 app/admin/model/ 下
     **/
    protected function getAdminModel($model){
        return Loader::model($model, "model", "", "admin");
    }


}
