<?php

// 公共助手函数

if (!function_exists('__')) {

    /**
     * 获取语言变量值
     * @param string $name 语言变量名
     * @param array $vars 动态变量值
     * @param string $lang 语言
     * @return mixed
     */
    function __($name, $vars = [], $lang = '')
    {
        if (is_numeric($name) || !$name)
            return $name;
        if (!is_array($vars)) {
            $vars = func_get_args();
            array_shift($vars);
            $lang = '';
        }
        return \think\Lang::get($name, $vars, $lang);
    }

}

if (!function_exists('format_bytes')) {

    /**
     * 将字节转换为可读文本
     * @param int $size 大小
     * @param string $delimiter 分隔符
     * @return string
     */
    function format_bytes($size, $delimiter = '')
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
        for ($i = 0; $size >= 1024 && $i < 6; $i++)
            $size /= 1024;
        return round($size, 2) . $delimiter . $units[$i];
    }

}

if (!function_exists('datetime')) {

    /**
     * 将时间戳转换为日期时间
     * @param int $time 时间戳
     * @param string $format 日期时间格式
     * @return string
     */
    function datetime($time, $format = 'Y-m-d H:i:s')
    {
        $time = is_numeric($time) ? $time : strtotime($time);
        return date($format, $time);
    }

}

if (!function_exists('human_date')) {

    /**
     * 获取语义化时间
     * @param int $time 时间
     * @param int $local 本地时间
     * @return string
     */
    function human_date($time, $local = null)
    {
        return \fast\Date::human($time, $local);
    }

}

if (!function_exists('cdnurl')) {

    /**
     * 获取上传资源的CDN的地址
     * @param string $url 资源相对地址
     * @param boolean $domain 是否显示域名 或者直接传入域名
     * @return string
     */
    function cdnurl($url, $domain = false)
    {
        $url = preg_match("/^https?:\/\/(.*)/i", $url) ? $url : \think\Config::get('upload.cdnurl') . $url;
        if ($domain && !preg_match("/^(http:\/\/|https:\/\/)/i", $url)) {
            if (is_bool($domain)) {
                $public = \think\Config::get('view_replace_str.__PUBLIC__');
                $url = rtrim($public, '/') . $url;
                if (!preg_match("/^(http:\/\/|https:\/\/)/i", $url)) {
                    $url = request()->domain() . $url;
                }
            } else {
                $url = $domain . $url;
            }
        }
        return $url;
    }

}


if (!function_exists('is_really_writable')) {

    /**
     * 判断文件或文件夹是否可写
     * @param    string $file 文件或目录
     * @return    bool
     */
    function is_really_writable($file)
    {
        if (DIRECTORY_SEPARATOR === '/') {
            return is_writable($file);
        }
        if (is_dir($file)) {
            $file = rtrim($file, '/') . '/' . md5(mt_rand());
            if (($fp = @fopen($file, 'ab')) === FALSE) {
                return FALSE;
            }
            fclose($fp);
            @chmod($file, 0777);
            @unlink($file);
            return TRUE;
        } elseif (!is_file($file) OR ($fp = @fopen($file, 'ab')) === FALSE) {
            return FALSE;
        }
        fclose($fp);
        return TRUE;
    }

}

if (!function_exists('rmdirs')) {

    /**
     * 删除文件夹
     * @param string $dirname 目录
     * @param bool $withself 是否删除自身
     * @return boolean
     */
    function rmdirs($dirname, $withself = true)
    {
        if (!is_dir($dirname))
            return false;
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dirname, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($files as $fileinfo) {
            $todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
            $todo($fileinfo->getRealPath());
        }
        if ($withself) {
            @rmdir($dirname);
        }
        return true;
    }

}

if (!function_exists('copydirs')) {

    /**
     * 复制文件夹
     * @param string $source 源文件夹
     * @param string $dest 目标文件夹
     */
    function copydirs($source, $dest)
    {
        if (!is_dir($dest)) {
            mkdir($dest, 0755, true);
        }
        foreach (
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::SELF_FIRST) as $item
        ) {
            if ($item->isDir()) {
                $sontDir = $dest . DS . $iterator->getSubPathName();
                if (!is_dir($sontDir)) {
                    mkdir($sontDir, 0755, true);
                }
            } else {
                copy($item, $dest . DS . $iterator->getSubPathName());
            }
        }
    }

}

if (!function_exists('mb_ucfirst')) {

    function mb_ucfirst($string)
    {
        return mb_strtoupper(mb_substr($string, 0, 1)) . mb_strtolower(mb_substr($string, 1));
    }

}

if (!function_exists('addtion')) {

    /**
     * 附加关联字段数据
     * @param array $items 数据列表
     * @param mixed $fields 渲染的来源字段
     * @return array
     */
    function addtion($items, $fields)
    {
        if (!$items || !$fields)
            return $items;
        $fieldsArr = [];
        if (!is_array($fields)) {
            $arr = explode(',', $fields);
            foreach ($arr as $k => $v) {
                $fieldsArr[$v] = ['field' => $v];
            }
        } else {
            foreach ($fields as $k => $v) {
                if (is_array($v)) {
                    $v['field'] = isset($v['field']) ? $v['field'] : $k;
                } else {
                    $v = ['field' => $v];
                }
                $fieldsArr[$v['field']] = $v;
            }
        }
        foreach ($fieldsArr as $k => &$v) {
            $v = is_array($v) ? $v : ['field' => $v];
            $v['display'] = isset($v['display']) ? $v['display'] : str_replace(['_ids', '_id'], ['_names', '_name'], $v['field']);
            $v['primary'] = isset($v['primary']) ? $v['primary'] : '';
            $v['column'] = isset($v['column']) ? $v['column'] : 'name';
            $v['model'] = isset($v['model']) ? $v['model'] : '';
            $v['table'] = isset($v['table']) ? $v['table'] : '';
            $v['name'] = isset($v['name']) ? $v['name'] : str_replace(['_ids', '_id'], '', $v['field']);
        }
        unset($v);
        $ids = [];
        $fields = array_keys($fieldsArr);
        foreach ($items as $k => $v) {
            foreach ($fields as $m => $n) {
                if (isset($v[$n])) {
                    $ids[$n] = array_merge(isset($ids[$n]) && is_array($ids[$n]) ? $ids[$n] : [], explode(',', $v[$n]));
                }
            }
        }
        $result = [];
        foreach ($fieldsArr as $k => $v) {
            if ($v['model']) {
                $model = new $v['model'];
            } else {
                $model = $v['name'] ? \think\Db::name($v['name']) : \think\Db::table($v['table']);
            }
            $primary = $v['primary'] ? $v['primary'] : $model->getPk();
            $result[$v['field']] = $model->where($primary, 'in', $ids[$v['field']])->column("{$primary},{$v['column']}");
        }

        foreach ($items as $k => &$v) {
            foreach ($fields as $m => $n) {
                if (isset($v[$n])) {
                    $curr = array_flip(explode(',', $v[$n]));

                    $v[$fieldsArr[$n]['display']] = implode(',', array_intersect_key($result[$n], $curr));
                }
            }
        }
        return $items;
    }

}

if (!function_exists('var_export_short')) {

    /**
     * 返回打印数组结构
     * @param string $var 数组
     * @param string $indent 缩进字符
     * @return string
     */
    function var_export_short($var, $indent = "")
    {
        switch (gettype($var)) {
            case "string":
                return '"' . addcslashes($var, "\\\$\"\r\n\t\v\f") . '"';
            case "array":
                $indexed = array_keys($var) === range(0, count($var) - 1);
                $r = [];
                foreach ($var as $key => $value) {
                    $r[] = "$indent    "
                        . ($indexed ? "" : var_export_short($key) . " => ")
                        . var_export_short($value, "$indent    ");
                }
                return "[\n" . implode(",\n", $r) . "\n" . $indent . "]";
            case "boolean":
                return $var ? "TRUE" : "FALSE";
            default:
                return var_export($var, TRUE);
        }
    }

}

if (!function_exists('getExcel')) {
    function getExcel($fileName, $headArrs, $datas, $sheet_title = ['SHEET1'])
    {
        //对数据进行检验
        //print_r($datas);die;
        if (empty($datas) || !is_array($datas)) {
            die("data must be a array");
        }
        //检查文件名
        if (empty($fileName)) {
            exit;
        }

        $date = date("Y_m_d", time());
        $fileName .= "_{$date}.xlsx";
        //设置缓存方式
        $cacheMethod = \PHPExcel_CachedObjectStorageFactory::cache_in_memory_gzip;
        $cacheSettings = array();
        \PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

        //创建PHPExcel对象，注意，不能少了\
        $objPHPExcel = new \PHPExcel();
        $objProps = $objPHPExcel->getProperties();
        foreach ($headArrs as $head_k => $headArr) {
            $head_k_title = $sheet_title[$head_k];
            if ($head_k == 0) {
                $objPHPExcel->getActiveSheet()->setTitle($head_k_title);
            } else {
                /* $msgWorkSheet = new \PHPExcel_Worksheet($objPHPExcel, $head_k_title);
                  $objPHPExcel->addSheet($msgWorkSheet); */
                $objPHPExcel->createSheet();
                $objPHPExcel->setActiveSheetIndex($head_k);
                $objPHPExcel->getActiveSheet()->setTitle($head_k_title);
            }
            //设置表头
            //设置表头
            foreach ($headArr as $k => $v) {
                $colum2 = \PHPExcel_Cell::stringFromColumnIndex($k);
                $objPHPExcel->setActiveSheetIndex($head_k)->setCellValue($colum2 . '1', $v);
            }
            //die();
            $column = 2;
            $objActSheet = $objPHPExcel->getActiveSheet();
            foreach ($datas[$head_k] as $key => $rows) { //行写入
                $span = 0;
                foreach ($rows as $keyName => $value) {// 列写入
                    $j = \PHPExcel_Cell::stringFromColumnIndex($span);
                    $objActSheet->setCellValue($j . $column, $value);
                    //print_r($j.$column.'-');
                    $span++;
                }
                $column++;
            }
        }
        //die;
        $fileName = iconv("utf-8", "gb2312", $fileName);
        //print_r($objActSheet);die;
        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;filename=\"{$fileName}\"");
        header('Cache-Control: max-age=0');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        //print_r($fileName);die;
        $objWriter->save('php://output'); //文件通过浏览器下载
        exit;
    }
}

if (!function_exists('getBrowseType')) {
    function getBrowseType()
    {
        $judge = $_SERVER['HTTP_USER_AGENT'];
        if (strpos($judge, "MicroMessenger") !== false) {
            return "weixin";
        } elseif (strpos($judge, "Weibo") !== false) {
            return "weibo";
        } elseif (strpos($judge, "QQ") !== false) {
            return "qq";
        } else {
            return false;
        }
    }
}

if (!function_exists('curl_get_https')) {
    function curl_get_https($url){
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);

            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            if(stristr($url, "https")){
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            }else{
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            }

            curl_setopt($ch, CURLOPT_HEADER, 0);

            $type = strtoupper($type);
            if($message){
                $message = http_build_query($message);
            }
            switch ($type){

                case "POST":
                    curl_setopt($ch, CURLOPT_POST,true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS,$message);
                    break;
                case "PUT" :
                    curl_setopt ($ch, CURLOPT_CUSTOMREQUEST, "PUT");
                    curl_setopt($ch, CURLOPT_POSTFIELDS,$message);
                    break;
                case "DELETE":
                    curl_setopt ($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
                    curl_setopt($ch, CURLOPT_POSTFIELDS,$message);
                    break;
                default :
                    curl_setopt($ch, CURLOPT_HTTPGET, true);
                    break;
            }


            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 23);
            curl_setopt($ch, CURLINFO_HEADER_OUT,1);//启用时追踪句柄的请求字符串。
//        $headers = [
//            "Content-Type: application/json;charset=UTF-8",
//            "Access-Control-Allow-Credentials: true",
//            "Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Connection, User-Agent, Cookie, Authorization ,token",
//            "Access-Control-Allow-Methods: POST,GET,PUT,DELETE,OPTIONS",
//            "Access-Control-Allow-Origin: *",
//        ];
//        $headers = [
//            "Content-Type: application/x-www-form-urlencoded",
//            "Accept: application/json",
//        ];
//        $arrHeaders = array_merge($headers,$arrHeaders);

            if(!empty($arrHeaders)){
                curl_setopt($ch, CURLOPT_HTTPHEADER, $arrHeaders);
            } //设置header
            $res =  curl_exec($ch);

//        $headers = curl_getinfo($ch, CURLINFO_HEADER_OUT);
//        echo "\n\n=====请求返回=====\n";
//        echo "out headers：\t".$headers ."\n";
//        $hearLen = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
//        echo "header len：\t".$hearLen ."\n";
//        $statuscode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
//        echo "httpcode:\t".$statuscode."\n";
//        echo "\n===================\n";


            $errorno = curl_errno($ch);
            $info  = curl_getinfo( $ch);
            //print_r($info);


            if ($errorno) {
                $re = array('code'=> 0, 'errorno' => $errorno, 'errmsg' => $info);
                return json_encode($re);
            }
            curl_close($ch);
            return $res;
    }
}