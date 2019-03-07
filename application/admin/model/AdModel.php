<?php

namespace app\admin\model;
use think\Model;
use think\Db;

class AdModel extends Model
{
    protected $name = 'ad';

    /**
     * 根据条件获取列表信息
     * @param $map
     * @param $Nowpage
     * @param $limits
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getAdAll($map, $Nowpage, $limits)
    {
        return $this->field('think_ad.*,name')->join('think_ad_position', 'think_ad.ad_position_id = think_ad_position.id')->where($map)->page($Nowpage,$limits)->order('orderby desc')->select();
    }

    /**
     * 插入信息
     * @param $param
     * @return array
     */
    public function insertAd($param)
    {
        try{
            $result = $this->validate('AdValidate')->allowField(true)->save($param);
            if(false === $result){
                return ['code' => -1, 'data' => '', 'msg' => $this->getError()];
            }else{
                return ['code' => 1, 'data' => '', 'msg' => '添加广告成功'];
            }
        }catch( PDOException $e){
            return ['code' => -2, 'data' => '', 'msg' => $e->getMessage()];
        }
    }

    /**
     * 编辑信息
     * @param $param
     * @return array
     */
    public function editAd($param)
    {
        try{

            $result = $this->validate('AdValidate')->allowField(true)->save($param, ['id' => $param['id']]);

            if(false === $result){
                return ['code' => 0, 'data' => '', 'msg' => $this->getError()];
            }else{
                return ['code' => 1, 'data' => '', 'msg' => '编辑广告成功'];
            }
        }catch( PDOException $e){
            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }
    }

    /**
     * 根据id获取一条信息
     * @param $id
     * @return array|false|\PDOStatement|string|Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getOneAd($id)
    {
        return $this->where('id', $id)->find();
    }


    /**
     * 删除信息
     * @param $id
     * @return array
     */
    public function delAd($id)
    {
        try{
            $map['closed']=1;
            $this->save($map, ['id' => $id]);
            return ['code' => 1, 'data' => '', 'msg' => '删除广告成功'];
        }catch( PDOException $e){
            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }
    }

}