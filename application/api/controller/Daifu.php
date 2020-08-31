<?php
/**
 * @Author Bob
 * Date: 2019/7/28
 * @Email  bob@bobcoder.cc
 * @Site https://www.bobcoder.cc/
 */

namespace app\api\controller;


use app\admin\model\ChongZhiModel;
use app\admin\model\DaifuModel;
use app\admin\model\MemberModel;
use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use think\Exception;
use think\exception\DbException;
use think\Request;
use think\response\Json;
use think\response\Jsonp;

class Daifu extends Base
{
    /**
     * 代付记录列表
     *
     * @param DaifuModel $daifu
     * @return Json|Jsonp
     * @throws DataNotFoundException
     * @throws ModelNotFoundException
     * @throws DbException
     * @author  Bob<bob@bobcoder.cc>
     */
    public function lists(DaifuModel $daifu)
    {
        $get = $this->request->get();
        $limit = (int)$this->request->get('limit', 5);
        $offset = (int)$this->request->get('offset', 0);

        $map = [];
        isset($get['account']) && $get['account'] ? $map['account'] = $get['account'] : false;
        isset($get['money']) && $get['money'] ? $map['money'] = $get['money'] : false;
        isset($get['member_id']) && $get['member_id'] ? $map['member_id'] = $get['member_id'] : false;
        isset($get['bank_card']) && $get['bank_card'] ? $map['bank_card'] = $get['bank_card'] : false;
        isset($get['shenfenzheng']) && $get['shenfenzheng'] ? $map['shenfenzheng'] = $get['shenfenzheng'] : false;
        isset($get['bank_name']) && $get['bank_name'] ? $map['bank_name'] = $get['bank_name'] : false;

        $list = $daifu
            ->with('user')
            ->where($map)
            ->order('create_time', 'desc')
            ->limit($offset, $limit)
            ->select();

        return $this->sendJson($list);
    }

    /**
     * 代付审核列表
     *
     * @param DaifuModel $daifu
     * @return Json|Jsonp
     * @throws DataNotFoundException
     * @throws ModelNotFoundException
     * @throws DbException
     * @author  Bob<bob@bobcoder.cc>
     */
    public function examine(DaifuModel $daifu)
    {
        $get = $this->request->get();
        $limit = (int)$this->request->get('limit', 5);
        $offset = (int)$this->request->get('offset', 0);

        $map = [];
        isset($get['account']) && $get['account'] ? $map['account'] = $get['account'] : false;
        isset($get['money']) && $get['money'] ? $map['money'] = $get['money'] : false;
        isset($get['member_id']) && $get['member_id'] ? $map['member_id'] = $get['member_id'] : false;
        isset($get['bank_card']) && $get['bank_card'] ? $map['bank_card'] = $get['bank_card'] : false;
        isset($get['shenfenzheng']) && $get['shenfenzheng'] ? $map['shenfenzheng'] = $get['shenfenzheng'] : false;
        isset($get['bank_name']) && $get['bank_name'] ? $map['bank_name'] = $get['bank_name'] : false;

        $list = $daifu
            ->with('user')
            ->where($map)
            ->whereIn('status', [1, 3])
            ->order('create_time', 'desc')
            ->limit($offset, $limit)
            ->select();

        return $this->sendJson($list);
    }

    /**
     * 代付审核
     *
     * @param Request $request
     * @return Json
     * @throws Exception
     * @throws DbException
     * @author  Bob<bob@bobcoder.cc>
     */
    public function changeStatus(Request $request)
    {
        $id = $request->post('id');
        $status = $request->post('status');

        $daifu = DaifuModel::get($id);
        $member_id = MemberModel::where('bank_card', $daifu->bank_card)->value('id') ?? 0;

        //如果卡号存在就是转账
        if ($status == 5 && $member_id) {
            $status = 6;
        }

        $daifu->status = $status;
        $daifu->save();

        $this->writelog($daifu, $member_id);

        return $this->sendSuccess('更新成功');
    }

    /**
     * 写入日志
     * @param $daifu
     * @param $member_id
     * @throws Exception
     * @author  Bob<bob@bobcoder.cc>
     */
    public function writelog($daifu, $member_id)
    {
        $status = '';

        switch ($daifu->status) {
            case 1:
                $status = '待审核';
                writelog(session('uid'), session('username'), '代付：开户名【' . $daifu->bank_owner . '】' . $status, 1);
                break;
            case 2:
                //代付不成功返还
                ChongZhiModel::chongzhi($daifu);
                $status = '初审未通过';
                writelog(session('uid'), session('username'), '代付：开户名【' . $daifu->bank_owner . '】' . $status, 1);
                break;
            case 3:
                $status = '初审通过';
                writelog(session('uid'), session('username'), '代付：开户名【' . $daifu->bank_owner . '】' . $status, 1);
                break;
            case 4:
                //代付不成功返还
                ChongZhiModel::chongzhi($daifu);
                $status = '终审未通过';
                writelog(session('uid'), session('username'), '代付：开户名【' . $daifu->bank_owner . '】' . $status, 1);
                break;
            case 5:
                $status = '代付成功';
                $money = MemberModel::where('id', $daifu->member_id)->value('money');
                writelog(session('uid'), session('username'), '代付：开户名【' . $daifu->bank_owner . '】' . $status . ',金额：' . $daifu->money . ',余额：' . $money, 1);
                break;
            case 6:
                //转账
                ChongZhiModel::zhuanzhang($member_id, $daifu->money);
                $status = '转账成功';
                $money = MemberModel::where('id', $daifu->member_id)->value('money');
                writelog(session('uid'), session('username'), '代付：开户名【' . $daifu->bank_owner . '】' . $status . ',金额：' . $daifu->money . ',余额：' . $money, 1);
                break;
        }

        $user = $this->request->user;
        writelog(session('uid'), session('username'), '代付：开户名【' . $daifu->bank_owner . '】' . $status .',金额：'.$daifu->money, 1);
    }

    /**
     * 用户代付批次查询
     *
     * @param DaifuModel $daifu
     * @return mixed|Json
     * @throws DataNotFoundException
     * @throws ModelNotFoundException
     * @throws DbException
     * @author  Bob<bob@bobcoder.cc>
     */
    public function member_index(DaifuModel $daifu)
    {
        $get = $this->request->get();
        $limit = (int)$this->request->get('limit', 5);
        $offset = (int)$this->request->get('offset', 0);

        $map = [];
        isset($get['money']) && $get['money'] ? $map['money'] = $get['money'] : false;
        isset($get['bank_card']) && $get['bank_card'] ? $map['bank_card'] = $get['bank_card'] : false;
        isset($get['shenfenzheng']) && $get['shenfenzheng'] ? $map['shenfenzheng'] = $get['shenfenzheng'] : false;
        isset($get['bank_name']) && $get['bank_name'] ? $map['bank_name'] = $get['bank_name'] : false;

        $list = $daifu
            ->with('user')
            ->where($map)
            ->where('member_id', $this->request->user->id)
            ->order('create_time', 'desc')
            ->limit($offset, $limit)
            ->select();

        if ($this->request->user->id == cache('db_config_data')['member_id']) {
            foreach ($list as &$item) {
                $item['bank_owner'] = mb_substr($item['bank_owner'], 0, 1) . '*';
                $item['shenfenzheng'] = substr($item['shenfenzheng'], 0, 4) . '**********' . substr($item['shenfenzheng'], -4);
                $item['bank_card'] = '**********' . substr($item['bank_card'], -4);
            }
        }

        return $this->sendJson($list);
    }
}