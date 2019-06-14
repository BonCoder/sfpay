<?php
/**
 * @Author Bob
 * Date: 2019/4/18
 * @Email  bob@bobcoder.cc
 * @Site https://www.bobcoder.cc/
 */

namespace app\admin\controller;

use app\admin\model\ChongZhiModel;
use app\admin\model\DaifuModel;
use app\admin\model\DaoruModel;
use app\admin\model\MemberModel;
use think\Loader;
use think\Request;

class Daoru extends Base
{
    /**
     * @return mixed|\think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author  Bob<bob@bobcoder.cc>
     */
    public function index()
    {

        if (request()->isPost()) {
            $map = [];

            if ($filename = input('filename', '')) {
                $map['filename'] = ['like', "%" . $filename . "%"];
            }
            if ($money = input('money', '')) {
                $map['money'] = $money;
            }
            if ($count = input('count', '')) {
                $map['count'] = $count;
            }
            if ($picihao = input('picihao', '')) {
                $map['picihao'] = $picihao;
            }
            $start = input('start', '');
            $end = input('end', '');
            if ($start && $end) {
                $map['think_daoru.create_time'] = ['between time', [strtotime($start), strtotime($end)]];
            }
            $daoru = new DaoruModel();
            $Nowpage = input('page',1);
            $limits = 10;// 获取总条数
            $lists = $daoru->getDaoruByWhere($map, $Nowpage, $limits);
            $count = $daoru->getAllCount($map);//计算总页面
            $allpage = intval(ceil($count / $limits));

            return json(['code' => 1, 'data' => $lists, 'pages' => $allpage]);
        }

        return $this->fetch();
    }

    /**
     * @param Request $request
     * @author  Bob<bob@bobcoder.cc>
     */
    public function look(Request $request)
    {

    }

    /**
     * 代付批次管理
     * @return mixed|\think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author  Bob<bob@bobcoder.cc>
     */
    public function member_index()
    {
        if (request()->isPost()) {
            $map = ['member_id' => session('uid')];
            if ($filename = input('filename', '')) {
                $map['filename'] = ['like', '%' . $filename . '%'];
            }
            if ($money = input('money', '')) {
                $map['money'] = $money;
            }
            if ($count = input('count', '')) {
                $map['count'] = $count;
            }
            if ($picihao = input('picihao', '')) {
                $map['picihao'] = $picihao;
            }
            $model = new DaoruModel();
            $Nowpage = input('page', 1);
            $limits = 12;
            $count = $model->getAllCount($map);
            $lists = $model->getDaoruByWhere($map, $Nowpage, $limits);
            $allpage = intval(ceil($count / $limits));  //计算

            return json(['code' => 1, 'data' => $lists, 'pages' => $allpage]);
        }

        return $this->fetch();
    }

    /**
     * @param Request $request
     * @return \think\response\Json
     * @throws \PHPExcel_Exception
     * @throws \PHPExcel_Reader_Exception
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author  Bob<bob@bobcoder.cc>
     */
    public function daoru(Request $request)
    {
        $password = $request->post('password', '');
        $cryption = crypt($password, 'deal');
        $res = MemberModel::where('pay_password', $cryption)->where('id', session('uid'))->find();
        if (!$res) {
            return json(['code' => 0, 'data' => '', 'msg' => '交易密码错误']);
        }

        return $this->uploadExcel($request);
    }

    /**
     * @param Request $request
     * @return \think\response\Json
     * @throws \PHPExcel_Exception
     * @throws \PHPExcel_Reader_Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author  Bob<bob@bobcoder.cc>
     * @throws \think\Exception
     */
    protected function uploadExcel(Request $request)
    {
        Loader::import('PHPExcel.PHPExcel', EXTEND_PATH);
        Loader::import('PHPExcel.PHPExcel.IOFactory.PHPExcel_IOFactory', EXTEND_PATH);
        Loader::import('PHPExcel.PHPExcel.Reader.Excel5', EXTEND_PATH);

        $file = $request->file('excel');
        $file_path = ROOT_PATH . 'public' . DS . 'uploads' . DS . 'excel';
        $file_name = session('username') . '_daifu_' . time();
        $info = $file->move($file_path, $file_name);
        if ($info) {
            $exclePath = $info->getSaveName();  //获取文件名
            $accept = strrchr($exclePath, '.');
            $objReader = '';
            if ($accept == '.xlsx') {
                $objReader = \PHPExcel_IOFactory::createReader('Excel2007');
            } elseif ($accept == '.xls') {
                $objReader = \PHPExcel_IOFactory::createReader('Excel5');
            }
            $obj_PHPExcel = $objReader->load($file_path . DS . $exclePath, $encode = 'utf-8');  //加载文件内容,编码utf-8
            $worksheet = $obj_PHPExcel->getsheet(0);
            $excel_array = $worksheet->toArray();   //转换为数组格式
            array_shift($excel_array);  //删除第一个数组(标题);
            $allColumn = $worksheet->getHighestColumn();//取得最大的列号
            $allColumn = \PHPExcel_Cell::columnIndexFromString($allColumn);//将列数转换为数字 列数大于Z的必须转  A->1  AA->27
            if ($allColumn < 6) {
                return json(['code' => 0, 'msg' => 'Excel表单列表列数不对']);
            }

            //总金额
            $money = 0.00;
            foreach ($excel_array as $key => $value) {
                $need = array_values($value);
                $danp = $need[3];
                //如果excel表格的金额数据格式不对
                if (!is_numeric((float)$danp)) {
                    return json(['code' => 0, 'msg' => 'excel文件导入失败，导入数值格式不对！']);
                }
                $money += floatval($danp);  //总金额增加
            }

            $member = MemberModel::where('id', session('uid'))->find();
            if ($money > $member->money) {
                return json(['code' => 0, 'msg' => 'excel文件导入失败，账户余额不足！']);
            }

            //批次号
            $countdaoru = DaoruModel::find()->count();
            $picihao = "P" . date("Ymd") . sprintf("%05d", $countdaoru + 1);

            //保存导入动作记录，并生成id号
            $daoru = new DaoruModel();
            $daoru->filename = explode('.', $file->getInfo()['name'])[0];  //获取原文件名
            $daoru->filepath = $exclePath;  //文件存放路径
            $daoru->member_id = session('uid');  //导入用户
            $daoru->count = count($excel_array);  //总笔数
            $daoru->money = $money;
            $daoru->picihao = $picihao;
            $daoru->save();

            //生成每一步的代付记录详情
            foreach ($excel_array as $k => $v) {
                $item = array_values($v);
                $daifumodel = new DaifuModel();
                $daifumodel->member_id = session('uid');
                $daifumodel->daoru_id = $daoru->id;
                $daifumodel->shenfenzheng = $item[1];
                $daifumodel->bank_card = $item[2];
                $daifumodel->money = $item[3];
                $daifumodel->bank_owner = $item[4];
                $daifumodel->bank_name = $item[5];
                $daifumodel->status = 1;
                $daifumodel->save();
            }

            //修改用户余额和插入数据
            ChongZhiModel::dodaifuyue($money, $daoru->id);
        }

        return json(['code' => 1, 'msg' => 'excel文件导入成功！']);
    }
}