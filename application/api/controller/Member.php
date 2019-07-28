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
        if (isset($param['id']) && $param){
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
            $param['pay_password'] = crypt($param['pay_password'],'deal');
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
        $data = $this->request->post();
        if (! is_numeric($data['money'])) {
            return json(['code' => 0, 'data' => '', 'msg' => '请输入正确的金额！']);
        }
        $model = new ChongZhiModel();
        $res = $model->recharge($data['money'], $data['id'], $data['remark']);
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
}