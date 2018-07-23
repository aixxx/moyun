<?php

namespace app\admin\model;

use think\Model;

class Oauth extends Model
{
    // 表名
    protected $name = 'oauth';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

    // 追加属性
    protected $append = [
        'platform_text'
    ];
    

    protected static function init()
    {
        self::afterInsert(function ($row) {
            $pk = $row->getPk();
            $row->getQuery()->where($pk, $row[$pk])->update(['weigh' => $row[$pk]]);
        });
    }

    
    public function getPlatformList()
    {
        return ['weibo' => __('Platform weibo'),'weixin' => __('Platform weixin'),'qq' => __('Platform qq')];
    }     


    public function getPlatformTextAttr($value, $data)
    {        
        $value = $value ? $value : $data['platform'];
        $list = $this->getPlatformList();
        return isset($list[$value]) ? $list[$value] : '';
    }




}
