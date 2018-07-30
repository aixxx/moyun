<?php

namespace app\admin\controller;

use app\common\controller\Backend;
use think\Request;

/**
 * 作品管理
 *
 * @icon fa fa-circle-o
 */
class Export extends Backend
{
    /**
     * 编辑
     */
    public function index()
    {
        return $this->view->fetch();
    }

    public function download(){
        $params = $this->request->param();
        //print_r($params);die;
        $lottoTime = $params['lottoTime'] ?: date("Y-m-d")." 00:00:00";
        $lottoTime2 = $params['lottoTime2'] ?: date("Y-m-d")." 23:59:59";

        //计算天数
        $i=strtotime(substr($lottoTime, 0, 10));
        while ($i <= strtotime(substr($lottoTime2, 0, 10))) {
            $datetime[] = date("Y-m-d",$i);
            $i += 86400;
        }

        $data[0] = $this->getOauthCount($datetime);
        $data[1] = $this->getProductCount($datetime);
        $data[2] = $this->getVoteCount($datetime);

        $headarr[0] = ["日期/平台","微博","微信","QQ","MOBU"];
        $headarr[1] = ["日期/平台","微博","微信","QQ","MOBU"];
        $headarr[2] = ["日期/平台","微博","微信","QQ","MOBU"];

        $sheet_title = ["授权","投稿","投票"];
        $filename = "moboo-";
        getExcel($filename, $headarr, $data, $sheet_title);
    }

    private function getOauthCount($datetime){
        foreach($datetime as $k=>$v){
            $where = "1";
            $where .= " and a.createtime >= '". strtotime($v . ' 00:00:00') ."'";
            $where .= " and a.createtime <= '". strtotime($v . ' 23:59:59') ."'";

            $oauth = model("Oauth");
            $weibo = $oauth->alias("a")->where($where. " and platform = 'weibo'")->count();
            $weixin = $oauth->alias("a")->where($where. " and platform = 'weixin'")->count();
            $qq = $oauth->alias("a")->where($where. " and platform = 'qq'")->count();
            $mobu = $oauth->alias("a")->where($where. " and platform = 'mobu'")->count();

            $data[$k] = [
                'day' => $v,
                'weibo' => $weibo ?: 0,
                'weixin' => $weixin ?: 0,
                'qq' => $qq ?: 0,
                'mobu' => $mobu ?: 0,
            ];
        }
        return $data;
    }


    private function getProductCount($datetime){
        foreach($datetime as $k=>$v){
            $where = "1";
            $where .= " and a.createtime >= '". strtotime($v . ' 00:00:00') ."'";
            $where .= " and a.createtime <= '". strtotime($v . ' 23:59:59') ."'";

            $product = model("Product");
            $weibo = $product->alias("a")
                ->join("oauth b","a.oauth_id = b.id","left")
                ->where($where. " and a.status = '1' and b.platform = 'weibo'")
                ->count();
            $weixin = $product->alias("a")
                ->join("oauth b","a.oauth_id = b.id","left")
                ->where($where. " and a.status = '1' and b.platform = 'weixin'")
                ->count();
            $qq = $product->alias("a")
                ->join("oauth b","a.oauth_id = b.id","left")
                ->where($where. " and a.status = '1' and b.platform = 'qq'")
                ->count();
            $mobu = $product->alias("a")
                ->join("oauth b","a.oauth_id = b.id","left")
                ->where($where. " and a.status = '1' and b.platform = 'mobu'")
                ->count();

            $data[$k] = [
                'day' => $v,
                'weibo' => $weibo ?: 0,
                'weixin' => $weixin ?: 0,
                'qq' => $qq ?: 0,
                'mobu' => $mobu ?: 0,
            ];
        }
        return $data;
    }

    private function getVoteCount($datetime){
        foreach($datetime as $k=>$v){
            $where = "1";
            $where .= " and a.createtime >= '". strtotime($v . ' 00:00:00') ."'";
            $where .= " and a.createtime <= '". strtotime($v . ' 23:59:59') ."'";

            $votelog = model("Votelog");
            $weibo = $votelog->alias("a")
                ->join("oauth b","a.oauth_id = b.id","left")
                ->where($where. " and platform = 'weibo'")
                ->count();
            $weixin = $votelog->alias("a")
                ->join("oauth b","a.oauth_id = b.id","left")
                ->where($where. " and platform = 'weixin'")
                ->count();
            $qq = $votelog->alias("a")
                ->join("oauth b","a.oauth_id = b.id","left")
                ->where($where. " and platform = 'qq'")
                ->count();
            $mobu = $votelog->alias("a")
                ->join("oauth b","a.oauth_id = b.id","left")
                ->where($where. " and platform = 'mobu'")
                ->count();

            $data[$k] = [
                'day' => $v,
                'weibo' => $weibo ?: 0,
                'weixin' => $weixin ?: 0,
                'qq' => $qq ?: 0,
                'mobu' => $mobu ?: 0,
            ];
        }
        return $data;
    }
}