<?php
/**
 * @Author Bob
 * Date: 2019/7/28
 * @Email  bob@bobcoder.cc
 * @Site https://www.bobcoder.cc/
 */

namespace app\api\controller;


use app\admin\model\DaoruModel;

class Daoru extends Base
{
    /**
     * @param DaoruModel $daoru
     * @return \think\response\Json|\think\response\Jsonp
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author  Bob<bob@bobcoder.cc>
     */
    public function lists(DaoruModel $daoru)
    {
        $get = $this->request->get();
        $limit = (int)$this->request->get('limit', 5);
        $offset = (int)$this->request->get('offset', 0);

        $map = [];
        isset($get['filename']) && $get['filename'] ? $map['filename'] = ['like', "%" . $get['money'] . "%"] : false;
        isset($get['bank_card']) && $get['bank_card'] ? $map['bank_card'] = $get['bank_card'] : false;
        isset($get['shenfenzheng']) && $get['shenfenzheng'] ? $map['shenfenzheng'] = $get['shenfenzheng'] : false;
        isset($get['bank_name']) && $get['bank_name'] ? $map['bank_name'] = $get['bank_name'] : false;

        $list = $daoru->with('user2')
            ->where($map)
            ->limit($offset, $limit)
            ->order('create_time', 'desc')
            ->select();

        return $this->sendJson($list);
    }
}