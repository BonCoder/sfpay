<?php

namespace app\api\controller;

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
        return '<style type="text/css">*{ padding: 0; margin: 0; } div{ padding: 4px 48px;} a{color:#2E5CD5;cursor: pointer;text-decoration: none} a:hover{text-decoration:underline; } body{ background: #fff; font-family: "Century Gothic","Microsoft yahei"; color: #333;font-size:18px;} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.6em; font-size: 42px }</style><div style="padding: 24px 48px;"> <h1>:)</h1><p> ThinkPHP V5<br/><span style="font-size:30px">十年磨一剑 - 为API开发设计的高性能框架</span></p><span style="font-size:22px;">[ V5.0 版本由 <a href="http://www.qiniu.com" target="qiniu">七牛云</a> 独家赞助发布 ]</span></div><script type="text/javascript" src="http://tajs.qq.com/stats?sId=9347272" charset="UTF-8"></script><script type="text/javascript" src="http://ad.topthink.com/Public/static/client.js"></script><thinkad id="ad_bd568ce7058a1091"></thinkad>';
    }

    /**
     * 接口数据
     *
     * @param Request $request
     * @return mixed
     * @author  Bob<bob@bobcoder.cc>
     */
    public function host(Request $request)
    {
        $config = cache('db_config_data');
        $info = file_get_contents("php://input");
        $data = json_decode($info);
        if (!$data){
            return json(['code' => 0, 'msg' => '参数不能为空！']);
        }
        $model = new DaifuModel();
        $model->member_id = $config['member_id'];
        $model->daoru_id = 1;
        $model->money = -$data->amount / 100;
        $model->create_time = strtotime($data->transDate);
        $statue = $data->transState == '00' ? 5 : 2;
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
		
		$user = MemberModel::where('id', $config['member_id'])->find();
        $user->money = $user->money - $model->money;
        $user->save();
		
        return json(['code' => 1, 'msg' => '插入成功！']);
    }
}
