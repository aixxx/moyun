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
    获取列表数据
     */
    public function getList(){
        $sort = Request::instance()->param("sort","vote");
        $order = Request::instance()->param("order","desc");

        $product = $this->getAdminModel("Product");
        //->distinct('location3')->field('`location3` code,`cn_city` name')->where(['location2' => $location2, 'location3' => ['neq', '00']])->select();
        $list = $product->alias("a")
            ->distinct('a.oauth_id')
            ->field('a.image, a.id, a.oauth_id, b.vote')
            ->join("oauth b","a.oauth_id = b.id","left")
            ->where(["a.status"=> "1"])
            ->select();
        print_r($product->getLastSql());die;
    }
    
    /**
     * 上传文件
     */
    public function upload()
    {
        return action('api/common/upload');
    }


}
