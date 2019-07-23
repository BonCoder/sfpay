<?php
/**
 * @classname Member
 * @author   Bob<bob@bobcoder.cc>
 * @version   1$
 * @link https://www.bobcoder.cc/
 * @Date  2019/7/23
 */

namespace app\api\controller;

use app\api\model\User as UserModel;

class Member extends Base
{
    /**
     * 用户列表
     *
     * @link https://www.bobcoder.cc/
     * @Date 2019/7/23
     * @author Bob<bob@bobcoder.cc>
     */
    public function lists()
    {
        $username = $this->request->get('username');
        $limit = (int)$this->request->get('limit', 10);
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
        $param['password'] = md5(md5($param['password']) . config('auth_key'));
        $param['pay_password'] = crypt($param['pay_password'],'deal');
        $param['group_id'] = 4;

        $user->allowField(true)->save($param);

        return $this->sendSuccess('保存成功！');
    }
}