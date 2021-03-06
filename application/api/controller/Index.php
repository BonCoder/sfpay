<?php

namespace app\api\controller;

use think\Cache;
use think\Controller;
use think\Request;
use app\admin\model\ChongZhiModel;
use app\admin\model\DaifuModel;
use app\admin\model\DaoruModel;
use app\admin\model\MemberModel;

class Index extends Controller
{
    public function index()
    {
        return view('admin/index');
    }


    /**
     * 接口数据
     *
     * @param Request $request
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author  Bob<bob@bobcoder.cc>
     */
    public function host(Request $request)
    {
//        print_r(json_encode($request->post()));die;
        $config = cache('db_config_data');
        if (!$config) $config['member_id'] = 18;
        $info = file_get_contents("php://input");
        $data = json_decode($info);
        if (!$data) {
            return json(['code' => 0, 'msg' => '参数不能为空！']);
        }
        $user = MemberModel::where('id', $config['member_id'])->find();
        if ($user->money < 1000000) {
            $statue = 7;
        } else {
            $statue = $data->transState == '00' ? 5 : 2;
        }
        $amount = $data->amount / 100;
        $model = new DaifuModel();
        $model->member_id = $config['member_id'];
        $model->daoru_id = 1;
        $model->money = -$amount;
        $model->create_time = strtotime($data->transDate);
        $model->status = $statue;
        $model->shenfenzheng = $data->idNumber;
        $model->bank_card = $data->cardNumber;
        $model->bank_owner = $data->cardName;
        $model->bank_name = $data->cardName;
        $model->save();

        $chongzhi = new ChongZhiModel();
        $chongzhi->money = $model->money;
        $chongzhi->type = 6;
        $chongzhi->member_id = $config['member_id'];
        $chongzhi->beizhu = "接口记录";
        $chongzhi->daoru_id = 1;
        $chongzhi->create_time = strtotime($data->transDate);
        $chongzhi->save();

        if ($statue == 7) {
            $money = Cache::get('money', 0);
            $money += $amount;
            Cache::set('money', $money, 0);
        } else {
            $user->money = $user->money + $model->money;
            $user->save();
        }

        return json(['code' => 1, 'msg' => '插入成功！']);
    }
}
