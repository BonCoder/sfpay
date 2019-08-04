<?php
/**
 * @classname Member
 * @author   Bob<bob@bobcoder.cc>
 * @version   1$
 * @link https://www.bobcoder.cc/
 * @Date  2019/7/23
 */

namespace app\api\controller;

use app\admin\model\ChongZhiModel;
use app\admin\model\MemberModel;
use app\api\model\User as UserModel;

class Member extends Base
{
    /**
     * 用户列表
     *
     * @link https://www.bobcoder.cc/
     * @Date 2019/7/23
     * @author Bob<bob@bobcoder.cc>
     * @throws \think\exception\DbException
     */
    public function lists()
    {
        $username = $this->request->get('username');
        $limit = (int)$this->request->get('limit', 5);
        $offset = (int)$this->request->get('offset', 0);
        $where = [];
        if ($username) $where['account|nickname'] = ['like', '%' . $username . '%'];
        $user = new UserModel();
        $list = $user
            ->where($where)
            ->limit($offset, $limit)
            ->select();

        foreach ($list as &$item){
            $item->last_login_time = date('Y-m-d H:i:s', $item->last_login_time);
        }

        return $this->sendJson($list);
    }

    /**
     * 新增、修改用户
     *
     * @param UserModel $user
     * @return \think\response\Json|\think\response\Jsonp
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @link https://www.bobcoder.cc/
     * @Date 2019/7/23
     * @author Bob<bob@bobcoder.cc>
     */
    public function save(UserModel $user)
    {
        $param = $this->request->post();
        if (isset($param['id']) && $param) {
            $user = $user->find($param['id']);
        }
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
        $param['group_id'] = 4;

        $user->allowField(true)->save($param);

        return $this->sendSuccess('保存成功！');
    }

    /**
     * 给用户充值
     * @return mixed|\think\response\Json
     * @throws \think\Exception
     * @author  Bob<bob@bobcoder.cc>
     */
    public function recharge()
    {
        $money = $this->request->post('money');
        $id = $this->request->post('id', '');
        $remark = $this->request->post('remark','');
        if (!$id){
            return $this->sendError(0,'ID不能为空');
        }
        if (!$money || !is_numeric($money)) {
            return json(['code' => 0, 'data' => '', 'msg' => '请输入正确的金额！']);
        }
        $model = new ChongZhiModel();
        $res = $model->recharge($money, $id, $remark);
        if ($res) {
            return $this->sendSuccess('充值成功！');
        }

        return $this->sendError('充值失败');
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
        $id = $this->request->post('id');
        $member = new MemberModel();
        $flag = $member->delMember($id);

        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }

    /**
     * 修改登录密码
     *
     * @param MemberModel $model
     * @return \think\response\Json|\think\response\Jsonp
     * @throws \think\exception\DbException
     * @link https://www.bobcoder.cc/
     * @Date 2019/7/29
     * @author Bob<bob@bobcoder.cc>
     */
    public function updatePassword(MemberModel $model)
    {
        if (!$this->request->post('password')) {
            return $this->sendError('请填写密码');
        }
        $password = md5(md5($this->request->post('password')) . config('auth_key'));
        $member = $model->findOrFail($this->request->user->id);
        $member->password = $password;
        $member->save();

        return $this->sendSuccess('修改成功');
    }

    /**
     * 修改支付登录密码
     *
     * @param MemberModel $model
     * @return \think\response\Json|\think\response\Jsonp
     * @throws \think\exception\DbException
     * @link https://www.bobcoder.cc/
     * @Date 2019/7/29
     * @author Bob<bob@bobcoder.cc>
     */
    public function updatePayPassword(MemberModel $model)
    {
        $password = $this->request->post('password', '');
        $old_password = $this->request->post('old_password', '');
        if (!$password || !$old_password)
            return $this->sendError('请填写密码');

        $member = $model->findOrFail($this->request->user->id);
        if ($member->pay_password) {
            $cryption = crypt($old_password, 'deal');
            if ($member->pay_password != $cryption)
                return $this->sendError('旧交易密码错误');
        }
        $pay_password = crypt($password, 'deal');
        $member->pay_password = $pay_password;
        $member->save();

        return $this->sendSuccess('修改成功');
    }

}