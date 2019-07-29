<?php
/**
 * @classname Log
 * @author   Bob<bob@bobcoder.cc>
 * @version   1$
 * @link https://www.bobcoder.cc/
 * @Date  2019/7/29
 */

namespace app\api\controller;


use app\admin\model\LogModel;

class Log extends Base
{
    /**
     * 日志列表
     *
     * @param LogModel $log
     * @return \think\response\Json|\think\response\Jsonp
     * @throws \think\exception\DbException
     * @link https://www.bobcoder.cc/
     * @Date 2019/7/29
     * @author Bob<bob@bobcoder.cc>
     */
    public function lists(LogModel $log)
    {
        $get = $this->request->get();
        $limit = (int)$this->request->get('limit', 5);
        $offset = (int)$this->request->get('offset', 0);
        $map = [];
        if (isset($get['admin_name']) && $get['admin_name']) {
            $map['admin_name'] = ['like', "%" . $get['admin_name'] . "%"];
        }
        $list = $log
            ->where($map)
            ->order('add_time','desc')
            ->limit($offset, $limit)
            ->select();

        foreach ($list as &$item){
            $item->add_time = date('Y-m-d H:i:s', $item->add_time);
        }

        return $this->sendJson($list);
    }

    /**
     * 删除日志
     *
     * @param LogModel $log
     * @return \think\response\Json
     * @author Bob<bob@bobcoder.cc>
     * @link https://www.bobcoder.cc/
     * @Date 2019/7/29
     */
    public function del_log(LogModel $log)
    {
        $log_id = input('param.log_id');
        $flag = $log->delLog($log_id);

        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }
}