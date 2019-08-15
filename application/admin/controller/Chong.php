<?php
/**
 * @Author Bob
 * Date: 2019/4/23
 * @Email  bob@bobcoder.cc
 * @Site https://www.bobcoder.cc/
 */

namespace app\admin\controller;


use app\admin\model\ChongZhiModel;
use app\admin\model\MemberModel;

class Chong extends Base
{
    /**
     * @return mixed|\think\response\Json
     * @author  Bob<bob@bobcoder.cc>
     */
    public function index()
    {
        if(request()->isPost()){
            $map = [];
            if ($type = input('type', '')) {
                $map['type'] = $type;
            }
            if ($account = input('account', '')){
                $map['member_id'] = MemberModel::where('account', $account)->value('id') ?? 0;
            }
            $start = input('start', '');
            $end = input('end', '');
            if ($start && $end) {
                $map['think_chongzhi.create_time'] = ['between time', [strtotime($start), strtotime($end)]];
            }
            $model = new ChongZhiModel();
            $Nowpage = input('page', 1);
            $limits = 12;
            $lists = $model->getAllList($map, $Nowpage, $limits);
            $count = $model->getAllCount($map);         //获取总条数
            $allpage = intval(ceil($count / $limits));  //计算
            return json(['code'=>1,'data'=>$lists,'pages'=>$allpage]);
        }

        return $this->fetch();
    }

    /**
     * @return mixed|\think\response\Json
     * @author  Bob<bob@bobcoder.cc>
     */
    public function member_index()
    {
        if(request()->isPost()){
            $map = ['member_id' => session('uid')];
            if ($type = input('type', '')) {
                $map['type'] = $type;
            }
            $start = input('start', '');
            $end = input('end', '');
            if ($start && $end) {
                $map['think_chongzhi.create_time'] = ['between time', [strtotime($start), strtotime($end)]];
            }
            $model = new ChongZhiModel();
            $Nowpage = input('page', 1);
            $limits = 12;
            $lists = $model->getAllList($map, $Nowpage, $limits);
            $count = $model->getAllCount($map);         //获取总条数
            $allpage = intval(ceil($count / $limits));  //计算
            return json(['code'=>1,'data'=>$lists,'pages'=>$allpage]);
        }

        return $this->fetch('chong/member_index');
    }

    /**
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author  Bob<bob@bobcoder.cc>
     */
    public function total()
    {
        $member = MemberModel::findOrFail(session('uid'));
        $member->status = $member['status'] == 1 ? '启用' : '禁用';

        $this->assign(compact('member'));

        return $this->fetch();
    }

    /**
     * 修改时间
     *
     * @return \think\response\Json
     * @author  Bob<bob@bobcoder.cc>
     */
    public function changeTime()
    {
        $id = (int)$this->request->post('id');
        $create_time = $this->request->post('create_time');
        if (date('Y-m-d H:i:s', strtotime($create_time)) != $create_time) {
            return json(['code' => 0, 'msg' => '时间格式不正确']);
        }

        $daoru = new ChongZhiModel();
        $daoru->where('id', $id)->update(['create_time' => strtotime($create_time)]);

        return json(['code' => 1, 'msg' => '修改成功']);
    }
}