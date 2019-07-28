<?php
/**
 * @Author Bob
 * Date: 2019/4/18
 * @Email  bob@bobcoder.cc
 * @Site https://www.bobcoder.cc/
 */

namespace app\admin\model;


use think\Db;
use think\Model;

/**
 * @property  string filename
 * @property string filepath
 * @property mixed member_id
 * @property int count
 * @property int|string money
 * @property string picihao
 * @property mixed id
 */
class DaoruModel extends Model
{
    protected $name = 'daoru';
    protected $autoWriteTimestamp = true;   // 开启自动写入时间戳

    /**
     * 根据搜索条件获取列表信息
     * @param $map
     * @param $Nowpage
     * @param $limits
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author   Bob<bob@bobcoder.cc>
     */
    public function getDaoruByWhere($map, $Nowpage, $limits)
    {
        return $this->with('user')->where($map)->page($Nowpage, $limits)->order('create_time', 'desc')->select();
    }

    /**
     * 根据搜索条件获取数量
     * @param $where
     * @return int|string
     */
    public function getAllCount($map)
    {
        return $this->with('user')->where($map)->count();
    }

    /**
     * @return mixed
     * @throws \think\Exception
     * @author  Bob<bob@bobcoder.cc>
     */
    public function user()
    {
        return $this->belongsTo('MemberModel', 'member_id', 'id');
    }

    /**
     * @return mixed
     * @throws \think\Exception
     * @author  Bob<bob@bobcoder.cc>
     */
    public function user2()
    {
        return $this->belongsTo('MemberModel', 'member_id', 'id')->bind(['account', 'nickname']);
    }

    public function daifu()
    {
        return $this->hasMany('DaifuModel', 'daoru_id');
    }

    /**
     * 格式化时间戳后返回
     * @param $val
     * @return false|string
     * @author  Bob<bob@bobcoder.cc>
     */
    public function getCreateTimeAttr($val)
    {
        if ($val) {
            return date('Y-m-d H:i:s', $val);
        } else {
            return $val;
        }
    }

}