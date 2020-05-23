<?php

namespace app\admin\controller;

use app\admin\model\ChongZhiModel;
use app\admin\model\MemberModel;
use app\admin\model\MemberGroupModel;
use app\admin\model\RechargeModel;
use think\Db;
use think\Request;

class Member extends Base
{
    //*********************************************会员组*********************************************//
    /**
     * [group 会员组]
     */
    public function group()
    {

        $key = input('key');
        $map = [];
        if ($key && $key !== "") {
            $map['group_name'] = ['like', "%" . $key . "%"];
        }
        $group = new MemberGroupModel();
        $Nowpage = input('get.page') ? input('get.page') : 1;
        $limits = config('list_rows');
        $count = $group->getAllCount($map);         //获取总条数
        $allpage = intval(ceil($count / $limits));  //计算总页面      
        $lists = $group->getAll($map, $Nowpage, $limits);
        $this->assign('Nowpage', $Nowpage); //当前页
        $this->assign('allpage', $allpage); //总页数 
        $this->assign('val', $key);
        if (input('get.page')) {
            return json($lists);
        }
        return $this->fetch();
    }

    /**
     * [add_group 添加会员组]
     */
    public function add_group()
    {
        if (request()->isAjax()) {
            $param = input('post.');
            $group = new MemberGroupModel();
            $flag = $group->insertGroup($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        return $this->fetch();
    }


    /**
     * [edit_group 编辑会员组]
     */
    public function edit_group()
    {
        $group = new MemberGroupModel();
        if (request()->isPost()) {
            $param = input('post.');
            $flag = $group->editGroup($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        $id = input('param.id');
        $this->assign('group', $group->getOne($id));
        return $this->fetch();
    }


    /**
     * [del_group 删除会员组]
     */
    public function del_group()
    {
        $id = input('param.id');
        $group = new MemberGroupModel();
        $flag = $group->delGroup($id);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }

    /**
     * [group_status 会员组状态]
     */
    public function group_status()
    {
        $id = input('param.id');
        $status = Db::name('member_group')->where(array('id' => $id))->value('status');//判断当前状态情况
        if ($status == 1) {
            $flag = Db::name('member_group')->where(array('id' => $id))->setField(['status' => 0]);
            return json(['code' => 1, 'data' => $flag['data'], 'msg' => '已禁止']);
        } else {
            $flag = Db::name('member_group')->where(array('id' => $id))->setField(['status' => 1]);
            return json(['code' => 0, 'data' => $flag['data'], 'msg' => '已开启']);
        }
    }


    //*********************************************会员列表*********************************************//

    /**
     * 会员列表
     */
    public function index()
    {

        $key = input('key');
        $map = [];
        if ($key && $key !== "") {
            $map['account|nickname'] = ['like', "%" . $key . "%"];
        }
        $member = new MemberModel();
        $Nowpage = input('get.page') ? input('get.page') : 1;
        $limits = 10;// 获取总条数
        $count = $member->getAllCount($map);//计算总页面
        $allpage = intval(ceil($count / $limits));
        $lists = $member->getMemberByWhere($map, $Nowpage, $limits);
        $this->assign('Nowpage', $Nowpage); //当前页
        $this->assign('allpage', $allpage); //总页数 
        $this->assign('val', $key);
        if (input('get.page')) {
            return json($lists);
        }
        return $this->fetch();
    }


    /**
     * 添加会员
     */
    public function add_member()
    {
        if (request()->isAjax()) {
            $param = input('post.');
            $param['password'] = md5(md5($param['password']) . config('auth_key'));
            $param['pay_password'] = crypt($param['pay_password'], 'deal');
            $param['group_id'] = 4;
            $member = new MemberModel();
            $flag = $member->insertMember($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }

        $group = new MemberGroupModel();
        $this->assign('group', $group->getGroup());
        return $this->fetch();
    }


    /**
     * 编辑会员
     */
    public function edit_member()
    {
        $member = new MemberModel();
        if (request()->isAjax()) {
            $param = input('post.');
            if (empty($param['password'])) {
                unset($param['password']);
            } else {
                $param['password'] = md5(md5($param['password']) . config('auth_key'));
            }
            if (empty($param['pay_password'])) {
                unset($param['pay_password']);
            } else {
                $param['pay_password'] = crypt($param['pay_password'], 'deal');
            }
            $flag = $member->editMember($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }

        $id = input('param.id');
        $group = new MemberGroupModel();
        $this->assign([
            'member' => $member->getOneMember($id),
            'group' => $group->getGroup()
        ]);
        return $this->fetch();
    }


    /**
     * 删除会员
     *
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     * @author  Bob<bob@bobcoder.cc>
     */
    public function del_member()
    {
        $id = input('param.id');
        $member = new MemberModel();
        $flag = $member->delMember($id);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }


    /**
     * 会员状态
     */
    public function member_status()
    {
        $id = input('param.id');
        $status = Db::name('member')->where('id', $id)->value('status');//判断当前状态情况
        if ($status == 1) {
            $flag = Db::name('member')->where('id', $id)->setField(['status' => 0]);
            return json(['code' => 1, 'data' => $flag['data'], 'msg' => '已禁止']);
        } else {
            $flag = Db::name('member')->where('id', $id)->setField(['status' => 1]);
            return json(['code' => 0, 'data' => $flag['data'], 'msg' => '已开启']);
        }

    }

    /**
     * 给用户充值
     * @param Request $request
     * @return mixed|\think\response\Json
     * @throws \think\Exception
     * @author  Bob<bob@bobcoder.cc>
     */
    public function recharge(Request $request)
    {
        $id = input('param.id');
        if ($request->isAjax()) {
            $key = 'recharge_'.$id;
            if (cache($key)){
                return json(['code' => 2, 'data' => '', 'msg' => '老板，请稍等3秒钟~']);
            }
            cache($key, $key, 5);
            $data = $request->post();
            if (!is_numeric($data['money'])) {
                return json(['code' => 0, 'data' => '', 'msg' => '请输入正确的金额！']);
            }
            $model = new ChongZhiModel();
            $res = $model->recharge($data['money'], $id, $data['remark']);
            if ($res) {
                $username = MemberModel::where('id',$id)->value('account');
                writelog(session('uid'), session('username'), '用户【' . $username . '】充值金额' . $data['money'], 1);

                return json(['code' => 1, 'data' => '', 'msg' => '充值成功']);
            }

            return json(['code' => 0, 'data' => '', 'msg' => '充值失败']);
        }


        $this->assign('id', $id);
        return $this->fetch();
    }


    /**
     * 修改登陆密码
     * @param Request $request
     * @return mixed|\think\response\Json
     * @author  Bob<bob@bobcoder.cc>
     */
    public function updatePassword(Request $request)
    {
        if ($request->isPost()) {
            $password = md5(md5($request->post('password')) . config('auth_key'));
            $member = MemberModel::findOrFail(session('uid'));
            $member->password = $password;
            $member->save();

            return json(['code' => 1, 'data' => '', 'msg' => '修改成功']);
        }

        return $this->fetch('member/update_password');
    }

    /**
     * 修改登陆密码
     * @param Request $request
     * @return mixed|\think\response\Json
     * @author  Bob<bob@bobcoder.cc>
     */
    public function updatePayPassword(Request $request)
    {
        if ($request->isPost()) {
            $member = MemberModel::findOrFail(session('uid'));

            if ($member->pay_password) {
                $cryption = crypt($request->post('old_password'), 'deal');
                if ($member->pay_password != $cryption)
                    return json(['code' => 0, 'data' => '', 'msg' => '旧交易密码错误']);
            }

            $pay_password = crypt($request->post('password'), 'deal');
            $member->pay_password = $pay_password;
            $member->save();

            return json(['code' => 1, 'data' => '', 'msg' => '修改成功']);
        }

        return $this->fetch('member/update_pay_password');
    }
}