<?PHP

namespace app\common\model;

use think\Model;

/**
 * 标准model
 */
class MyModel extends Model
{
    public function __construct($data = [])
    {
        $lang = strip_tags(\think\Request::instance()->langset());
        if($lang == "zh-cn"){}
        else
            $this->table = $lang."_".$this->name;
        
        //echo $this->table;
        parent::__construct($data);
    }
}
