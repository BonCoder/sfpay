<?php
/**
 * @Author Bob
 * @Date: 2019/3/8
 * @Email  bob@bobcoder.cc
 * @Site https://www.bobcoder.cc/
 */

namespace app\admin\model;

use think\Db;
use think\Model;

/**
 * @property mixed member_id
 * @property mixed daoru_id
 * @property string shenfenzheng
 * @property string bank_card
 * @property string money
 * @property string bank_name
 * @property string bank_owner
 * @property int status
 */
class DaifuModel extends Model
{
    protected $name = 'daifu';
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
    public function getDaifuByWhere($map, $Nowpage, $limits)
    {
//        return $this->with('user')->where($map)->page($Nowpage, $limits)->order('create_time','desc')->select();
        return $this->where($map)->order('create_time','desc')->paginate($limits)->each(function($item, $key){
            $user = $item->user()->field('account,nickname')->find();
            $item->account = $user['account'];
            $item->nickname = $user['nickname'];
            return $item;
        });
    }

//    public function getStatusAttr($value, $data)
//    {
//        $status = [1 => '待审核', 2 => '初审未通过', 3 => '初审通过', 4 => '终审未通过', 5 => '代付成功', 6 => '转账成功',];
//        return $status[$data['status']];
//    }

    /**
     * 根据搜索条件获取数量
     * @param $where
     * @return int|string
     */
    public function getAllCount($map)
    {
        return $this->where($map)->count();
    }

    /**
     * @return mixed
     * @throws \think\Exception
     * @author  Bob<bob@bobcoder.cc>
     */
    public function user()
    {
        return $this->belongsTo('MemberModel', 'member_id');
    }

    /**
     * @return mixed
     * @author  Bob<bob@bobcoder.cc>
     */
    public function daoru()
    {
        return $this->belongsTo('DaoruModel', 'daoru_id')->bind(['filepath','filename','create_time']);
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
