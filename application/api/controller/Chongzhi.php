<?php
/**
 * @classname ChongZhi
 * @author   Bob<bob@bobcoder.cc>
 * @version   $
 * @link https://www.bobcoder.cc/
 * @Date  2019/7/29
 */

namespace app\api\controller;


use app\admin\model\ChongZhiModel;
use app\admin\model\DaoruModel;
use app\admin\model\MemberModel;

class Chongzhi extends Base
{
    /**
     * 用户充值记录
     *
     * @param ChongZhiModel $chongzhi
     * @return \think\response\Json|\think\response\Jsonp
     * @throws \think\exception\DbException
     * @link https://www.bobcoder.cc/
     * @Date 2019/7/29
     * @author Bob<bob@bobcoder.cc>
     */
    public function lists(ChongZhiModel $chongzhi)
    {
        $get = $this->request->get();
        $limit = (int)$this->request->get('limit', 5);
        $offset = (int)$this->request->get('offset', 0);

        $map = [];
        isset($get['type']) && $get['type'] ? $map['type'] =  $get['type'] : false;
        $start = $get['start'] ?? '';
        $end = $get['end'] ?? '';
        if ($start && $end) {
            $map['create_time'] = ['between time', [strtotime($get['start']), $get['end']]];
        }

        $list = $chongzhi->with('user2')
            ->where($map)
            ->where('member_id', $this->request->user->id)
            ->limit($offset, $limit)
            ->order('create_time','desc')
            ->select();

        return $this->sendJson($list);
    }

    /**
     * 资金池资金
     *
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author  Bob<bob@bobcoder.cc>
     */
    public function total()
    {
        $member = MemberModel::findOrFail($this->request->user->id);
        $member->status = $member['status'] == 1 ? '启用' : '禁用';

        return $this->sendJson($member);
    }

    /**
     * 首页
     *
     * @author Bob<bob@bobcoder.cc>
     * @link https://www.bobcoder.cc/
     * @Date 2019/7/29
     */
    public function home()
    {
        $user_id = $this->request->user->id;
        //余额
        $money = number_format(MemberModel::where('id', $user_id)->value('money')  ?? 0,2);
        //最近一个月充值
        $chongzhi = number_format(ChongZhiModel::where('member_id', $user_id)
                ->where('type',1)
                ->whereTime('create_time', 'month')
                ->sum('money') ?? 0,2);
        //最近一个月代付
        $daifu = number_format(DaoruModel::where('member_id', $user_id)
                ->whereTime('create_time', 'month')
                ->sum('money') ?? 0,2);

        return $this->sendJson(compact('money', 'chongzhi', 'daifu'));
    }

    /**
     * 充值记录
     *
     * @param ChongZhiModel $chongzhi
     * @return \think\response\Json|\think\response\Jsonp
     * @throws \think\exception\DbException
     * @link https://www.bobcoder.cc/
     * @Date 2019/7/29
     * @author Bob<bob@bobcoder.cc>
     */
    public function admin_lists(ChongZhiModel $chongzhi)
    {
        $get = $this->request->get();
        $limit = (int)$this->request->get('limit', 5);
        $offset = (int)$this->request->get('offset', 0);

        $map = [];
        isset($get['type']) && $get['type'] ? $map['type'] =  $get['type'] : false;
        $start = $get['start'] ?? '';
        $end = $get['end'] ?? '';
        if ($start && $end) {
            $map['create_time'] = ['between time', [strtotime($get['start']), $get['end']]];
        }

        $list = $chongzhi->with('user2')
            ->where($map)
            ->limit($offset, $limit)
            ->order('create_time','desc')
            ->select();

        return $this->sendJson($list);
    }

}