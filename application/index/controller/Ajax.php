<?php

namespace app\index\controller;

use app\common\controller\Frontend;
use think\Lang;
use think\Request;

/**
 * Ajax异步请求接口
 * @internal
 */
class Ajax extends Frontend
{

    protected $noNeedLogin = ['*'];
    protected $noNeedRight = ['*'];
    
    /**
     * 上传文件
     */
    public function upload()
    {
        return action('api/common/upload');
    }


}
