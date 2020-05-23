<?php
/**
 * @Author Bob
 * Date: 2019/4/22
 * @Email  bob@bobcoder.cc
 * @Site https://www.bobcoder.cc/
 */

namespace app\admin\model;

use think\Cache;
use think\Db;
use think\Model;

/**
 * @property mixed member_id
 * @property int money
 * @property string beizhu
 * @property int daoru_id
 * @property int create_time
 * @property int type
 */
class ChongZhiModel extends Model
{
    protected $name = 'chongzhi';

    /**
     * @param $map
     * @return int
     * @author  Bob<bob@bobcoder.cc>
     */
    public function getAllCount($map)
    {
        return $this->where($map)->count();
    }

    /**
     * @param $map
     * @param $Nowpage
     * @param $limits
     * @return mixed
     * @author  Bob<bob@bobcoder.cc>
     */
    public function getAllList($map, $Nowpage, $limits)
    {
        return $this->with('user')->where($map)->page($Nowpage, $limits)->order('create_time','desc')->select();
    }

    /**
     * @param $value
     * @return mixed
     * @author  Bob<bob@bobcoder.cc>
     */
    public function getTypeAttr($value)
    {
        $status = [1 => '充值', 2 => '代付不成功返还', 3 => '手续费', 4 => '代付支出', 5 => '转账', 6 => '接口记录',];
        return $status[$value];
    }

    /**
     * @param $value
     * @return false|string
     * @author  Bob<bob@bobcoder.cc>
     */
    public function getCreateTimeAttr($value)
    {
        return date('Y-m-d H:i:s', $value);
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
     * @throws \think\Exception
     * @author  Bob<bob@bobcoder.cc>
     */
    public function user2()
    {
        return $this->belongsTo('MemberModel', 'member_id')->bind(['account', 'nickname']);
    }

    /**
     * 代付不成功返还
     * @param $daifu
     * @return int|true
     * @throws \think\Exception
     * @author  Bob<bob@bobcoder.cc>
     */
    public static function chongzhi($daifu)
    {
        $data['money'] = $daifu->money;
        $data['type'] = 2;
        $data['member_id'] = $daifu->member_id;
        $data['beizhu'] = "代付不成功返还";
        $data['create_time'] = time();

        //插入充值记录
        Db::name('chongzhi')->insert($data);

        //修改用户余额
        return MemberModel::money($daifu->member_id, $data['money']);
    }

    /**
     * 转账
     * @param $member_id
     * @param $money
     * @return int|true
     * @throws \think\Exception
     * @author  Bob<bob@bobcoder.cc>
     */
    public static function zhuanzhang($member_id, $money)
    {
        $data['money'] = $money;
        $data['type'] = 5;
        $data['member_id'] = $member_id;
        $data['beizhu'] = "转账成功";
        $data['create_time'] = time();

        //插入充值记录
        Db::name('chongzhi')->insert($data);

        //修改用户余额
        return MemberModel::money($member_id, $data['money']);
    }

    /**
     * 代付
     * @param $money
     * @param $daoru_id
     * @param $member_id
     * @return int|true
     * @throws \think\Exception
     * @author  Bob<bob@bobcoder.cc>
     */
    public static function dodaifuyue($money, $daoru_id, $member_id)
    {
        $data['money'] = 0 - $money;
        $data['type'] = 4;
        $data['member_id'] = $member_id;
        $data['beizhu'] = "代付支出";
        $data['daoru_id'] = $daoru_id;
        $data['create_time'] = time();

        //插入充值记录
        Db::name('chongzhi')->insert($data);

        //修改用户余额
        return MemberModel::money($member_id, $data['money']);
    }

    /**
     * @param $money
     * @param $user_id
     * @param string $remark
     * @return bool
     * @author  Bob<bob@bobcoder.cc>
     * @throws \think\Exception
     */
    public function recharge($money, $user_id, $remark = '')
    {
        $data['money'] = $money;
        $data['type'] = 1;
        $data['member_id'] = $user_id;
        $data['beizhu'] = $remark ;
        $data['create_time'] = time();
        //插入充值记录
        Db::name('chongzhi')->insert($data);
        //修改用户余额
        if ($amount = Cache::get('money') > 0){
            $user = MemberModel::where('id', $user_id)->find();
            $user->money = $user->money + $money - $amount;
            return $user->save();
        } else {
            return MemberModel::money($user_id, $data['money']);
        }
    }

}