<?php
/**
 * @Author Bob
 * Date: 2019/7/28
 * @Email  bob@bobcoder.cc
 * @Site https://www.bobcoder.cc/
 */

namespace app\api\controller;


use app\admin\model\ChongZhiModel;
use app\admin\model\DaifuModel;
use app\admin\model\DaoruModel;
use app\admin\model\MemberModel;
use think\Loader;
use think\Request;

class Daoru extends Base
{
    /**
     * 代付批次管理
     *
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
        isset($get['money']) && $get['money'] ? $map['money'] = $get['money'] : false;
        isset($get['count']) && $get['count'] ? $map['count'] = $get['count'] : false;
        isset($get['picihao']) && $get['picihao'] ? $map['picihao'] = $get['picihao'] : false;

        $list = $daoru->with('user2')
            ->where($map)
            ->limit($offset, $limit)
            ->order('create_time', 'desc')
            ->select();

        return $this->sendJson($list);
    }

    /**
     * 用户代付批次管理
     * @param DaoruModel $daoru
     * @return mixed|\think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author  Bob<bob@bobcoder.cc>
     */
    public function member_index(DaoruModel $daoru)
    {
        $get = $this->request->get();
        $limit = (int)$this->request->get('limit', 5);
        $offset = (int)$this->request->get('offset', 0);

        $map = [];
        isset($get['filename']) && $get['filename'] ? $map['filename'] = ['like', "%" . $get['money'] . "%"] : false;
        isset($get['money']) && $get['money'] ? $map['money'] = $get['money'] : false;
        isset($get['count']) && $get['count'] ? $map['count'] = $get['count'] : false;
        isset($get['picihao']) && $get['picihao'] ? $map['picihao'] = $get['picihao'] : false;

        $list = $daoru->with('user2')
            ->where($map)
            ->where('member_id', $this->request->user->id)
            ->limit($offset, $limit)
            ->order('create_time', 'desc')
            ->select();

        return $this->sendJson($list);
    }


    /**
     * 导入Excel文件
     *
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
        $user = $this->request->user;
        $res = MemberModel::where('pay_password', $cryption)->where('id', $user->id)->find();
        if (!$res) {
            return json(['code' => 0, 'data' => '', 'msg' => '交易密码错误']);
        }

        return $this->uploadExcel($request, $user);
    }

    /**
     * @param Request $request
     * @param $user
     * @return \think\response\Json
     * @throws \PHPExcel_Exception
     * @throws \PHPExcel_Reader_Exception
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author  Bob<bob@bobcoder.cc>
     */
    protected function uploadExcel(Request $request, $user)
    {
        Loader::import('PHPExcel.PHPExcel', EXTEND_PATH);
        Loader::import('PHPExcel.PHPExcel.IOFactory.PHPExcel_IOFactory', EXTEND_PATH);
        Loader::import('PHPExcel.PHPExcel.Reader.Excel5', EXTEND_PATH);

        $file = $request->file('excel');
        $file_path = ROOT_PATH . 'public' . DS . 'uploads' . DS . 'excel';
        $file_name = $user->account . '_daifu_' . time();
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

            $member = MemberModel::where('id', $user->id)->find();
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
            $daoru->member_id = $user->id;  //导入用户
            $daoru->count = count($excel_array);  //总笔数
            $daoru->money = $money;
            $daoru->picihao = $picihao;
            $daoru->save();

            //生成每一步的代付记录详情
            foreach ($excel_array as $k => $v) {
                $item = array_values($v);
                $daifumodel = new DaifuModel();
                $daifumodel->member_id = $user->id;
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
            ChongZhiModel::dodaifuyue($money, $daoru->id, $user->id);
        }

        return json(['code' => 1, 'msg' => 'excel文件导入成功！']);
    }

    /**
     * @return \think\response\Json
     * @throws \think\exception\DbException
     * @author  Bob<bob@bobcoder.cc>
     */
    public function detail()
    {
        $id = $this->request->get('id');
        $daifu = new DaifuModel();
        $data = $daifu->with('user')->where('daoru_id',$id)->select();

        return json(['code' => 1, 'data' => $data]);
    }
}