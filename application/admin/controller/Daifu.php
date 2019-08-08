<?php
/**
 * @Author Bob
 * Date: 2019/4/14
 * @Email  bob@bobcoder.cc
 * @Site https://www.bobcoder.cc/
 */

namespace app\admin\controller;

use app\admin\model\ChongZhiModel;
use app\admin\model\DaifuModel;
use app\admin\model\DaoruModel;
use app\admin\model\MemberModel;
use think\Request;


class Daifu extends Base
{
    /**
     * @param Request $request
     * @return mixed|\think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author  Bob<bob@bobcoder.cc>
     */
    public function index(Request $request)
    {
        $map = [];
        if ($money = input('money', '')) $map['think_daifu.money'] = $money;
        if ($account = input('account', '')) $map['think_member.account'] = $account;
        if ($bank_card = input('bank_card', '')) $map['think_daifu.bank_card'] = $bank_card;
        if ($shenfenzheng = input('shenfenzheng', '')) $map['think_daifu.shenfenzheng'] = $shenfenzheng;
        if ($bank_name = input('bank_name', '')) $map['think_daifu.bank_name'] = $bank_name;
        $this->assign(compact('money', 'bank_card', 'shenfenzheng', 'bank_name', 'account'));

        if ($request->isGet()) {
            return $this->fetch();
        }

        $daifu = new DaifuModel();
        $Nowpage = input('get.page') ? input('get.page') : 1;
        $limits = 10;// 获取总条数
        $lists = $daifu
            ->field('think_daifu.*,think_member.account,think_member.nickname')
            ->join('think_member', 'think_daifu.member_id = think_member.id', 'left')
            ->where($map)
            ->order('create_time','desc')
            ->paginate($limits);

        return json($lists);
    }

    /**
     * 代付审核
     * @param Request $request
     * @return mixed|\think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author  Bob<bob@bobcoder.cc>
     */
    public function examine(Request $request)
    {
        $map = [];
        if ($money = input('money', '')) $map['think_daifu.money'] = $money;
        if ($account = input('account', '')) $map['think_member.account'] = $account;
        if ($bank_card = input('bank_card', '')) $map['think_daifu.bank_card'] = $bank_card;
        if ($shenfenzheng = input('shenfenzheng', '')) $map['think_daifu.shenfenzheng'] = $shenfenzheng;
        if ($bank_name = input('bank_name', '')) $map['think_daifu.bank_name'] = $bank_name;
        $this->assign(compact('money', 'bank_card', 'shenfenzheng', 'bank_name', 'account'));

        if ($request->isGet()) {
            return $this->fetch();
        }
        $map['think_daifu.status'] = ['in', '1,3'];
        $daifu = new DaifuModel();
        $Nowpage = input('get.page') ? input('get.page') : 1;
        $limits = 10;// 获取总条数
        $lists = $daifu
            ->field('think_daifu.*,think_member.account,think_member.nickname')
            ->join('think_member', 'think_daifu.member_id = think_member.id', 'left')
            ->where($map)
            ->order('create_time','desc')
            ->paginate($limits);

        return json($lists);
    }

    /**
     * @param Request $request
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\Exception\DbException
     * @author  Bob<bob@bobcoder.cc>
     */
    public function changeStatus(Request $request)
    {
        $id = $request->param('id');
        $status = $request->param('status');

        $daifu = DaifuModel::get($id);
        $member_id = MemberModel::where('bank_card', $daifu->bank_card)->value('id') ?? 0;

        //如果卡号存在就是转账
        if ($status == 5 && $member_id) {
            $status = 6;
        }

        $daifu->status = $status;
        $daifu->save();

        $this->writelog($daifu, $member_id);

        return json(['code' => 1, 'msg' => '更新成功！']);
    }

    /**
     * 写入日志
     * @param $daifu
     * @param $member_id
     * @throws \think\Exception
     * @author  Bob<bob@bobcoder.cc>
     */
    public function writelog(&$daifu, $member_id)
    {
        $status = '';

        switch ($daifu->status) {
            case 1:
                $status = '待审核';
                break;
            case 2:
                //代付不成功返还
                ChongZhiModel::chongzhi($daifu);
                $status = '初审未通过';
                break;
            case 3:
                $status = '初审通过';
                break;
            case 4:
                //代付不成功返还
                ChongZhiModel::chongzhi($daifu);
                $status = '终审未通过';
                break;
            case 5:
                $status = '代付成功';
                break;
            case 6:
                //转账
                ChongZhiModel::zhuanzhang($member_id, $daifu->money);
                $status = '转账成功';
                break;
        }

        writelog(session('uid'), session('username'), '代付：开户名【' . $daifu->bank_owner . '】' . $status, 1);
    }


    /**
     * 用户代付批次查询
     * @return mixed|\think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author  Bob<bob@bobcoder.cc>
     */
    public function member_index()
    {
        if (request()->isPost()) {
            if ($money = input('money', '')) $map['think_daifu.money'] = $money;
            if ($bank_card = input('bank_card', '')) $map['think_daifu.bank_card'] = $bank_card;
            if ($shenfenzheng = input('shenfenzheng', '')) $map['think_daifu.shenfenzheng'] = $shenfenzheng;
            if ($bank_name = input('bank_name', '')) $map['think_daifu.bank_name'] = $bank_name;

            $map = ['think_daifu.member_id' => session('uid')];
            $daifu = new DaifuModel();
            $Nowpage = input('get.page') ? input('get.page') : 1;
            $limits = 10;// 获取总条数
            $lists = $daifu->getDaifuByWhere($map, $Nowpage, $limits);
            if (session('uid') == cache('db_config_data')['member_id']){
                $lists->each(function ($item){
                    $item['bank_owner'] = mb_substr($item['bank_owner'], 0,1).'*';
                    $item['shenfenzheng'] = substr($item['shenfenzheng'],0, 4).'**********'.substr($item['shenfenzheng'], -4);
                    $item['bank_card'] = '**********'.substr($item['bank_card'], -4);
                });
            }
            return json($lists);
        }

        return $this->fetch();
    }
}
