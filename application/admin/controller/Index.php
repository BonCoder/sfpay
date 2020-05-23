<?php

namespace app\admin\controller;

use app\admin\model\ChongZhiModel;
use app\admin\model\DaoruModel;
use app\admin\model\MemberModel;
use think\Cache;

class Index extends Base
{
    public function index()
    {
        return $this->fetch('/index');
    }


    /**
     * [indexPage 后台首页]
     * @return mixed [type] [description]
     */
    public function indexPage()
    {
        $info = array(
            'web_server' => $_SERVER['SERVER_SOFTWARE'],
            'onload'     => ini_get('upload_max_filesize'),
            'think_v'    => THINK_VERSION,
            'phpversion' => phpversion(),
        );

        if(session('auth') == 'user'){
            //余额
            $money = number_format(MemberModel::where('id',session('uid'))->value('money')  ?? 0,2);
            //欠款累积金额
            $amount = Cache::get('money', 0);
            //最近一个月充值
            $chongzhi = number_format(ChongZhiModel::where('member_id',session('uid'))
                    ->where('type',1)
                    ->whereTime('create_time', 'month')
                    ->sum('money') ?? 0,2);

            //最近一个月代付
            $daifu = number_format(DaoruModel::where('member_id',session('uid'))
                    ->whereTime('create_time', 'month')
                    ->sum('money') ?? 0,2);
        }

        $this->assign(compact('money', 'chongzhi','daifu', 'amount'));
        $this->assign('info',$info);
        return $this->fetch('index');
    }
}
