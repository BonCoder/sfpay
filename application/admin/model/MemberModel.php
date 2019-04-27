<?php

namespace app\admin\model;
use think\Model;
use think\Db;

class MemberModel extends Model
{
    protected $name = 'member';  
    protected $autoWriteTimestamp = true;   // 开启自动写入时间戳


    public function getCreateTimeAttr($value)
    {
        return date('Y-m-d H:i:s',$value);
    }

    /**
     * 根据搜索条件获取用户列表信息
     * @param $map
     * @param $Nowpage
     * @param $limits
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author   Bob<bob@bobcoder.cc>
     */
    public function getMemberByWhere($map, $Nowpage, $limits)
    {
        return $this->where($map)->page($Nowpage, $limits)->select();
    }

    /**
     * 根据搜索条件获取所有的用户数量
     * @param $where
     * @return int|string
     */
    public function getAllCount($map)
    {
        return $this->where($map)->count();
    }

    /**
     * @param $value
     * @return false|string
     * @author  Bob<bob@bobcoder.cc>
     */
    public function getLastLoginTimeAttr($value)
    {
        return date('Y-m-d H:i:s', $value);
    }


    /**
     * 插入信息
     */
    public function insertMember($param)
    {
        try{
            $result = $this->validate('MemberValidate')->allowField(true)->save($param);
            if(false === $result){            
                return ['code' => -1, 'data' => '', 'msg' => $this->getError()];
            }else{
                return ['code' => 1, 'data' => '', 'msg' => '添加成功'];
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
    public function editMember($param)
    {
        try{
            $result =  $this->validate('MemberValidate')->allowField(true)->save($param, ['id' => $param['id']]);
            if(false === $result){            
                return ['code' => 0, 'data' => '', 'msg' => $this->getError()];
            }else{
                return ['code' => 1, 'data' => '', 'msg' => '编辑成功'];
            }
        }catch( PDOException $e){
            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }
    }


    /**
     * 根据管理员id获取角色信息
     * @param $id
     * @return array|false|\PDOStatement|string|Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getOneMember($id)
    {
        return $this->where('id', $id)->find();
    }


    /**
     * 删除管理员
     * @param $id
     * @return array
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function delUser($id)
    {
        try{

            $this->where('id', $id)->delete();
            Db::name('auth_group_access')->where('uid', $id)->delete();
            return ['code' => 1, 'data' => '', 'msg' => '删除成功'];

        }catch( PDOException $e){
            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }
    }


    /**
     * @param $id
     * @return array
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     * @author  Bob<bob@bobcoder.cc>
     */
    public function delMember($id)
    {
        try{
            $map['closed']=1;
            $this->where('id',$id)->delete();
            return ['code' => 1, 'data' => '', 'msg' => '删除成功'];
        }catch( PDOException $e){
            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }
    }

    /**
     * @return mixed
     * @author  Bob<bob@bobcoder.cc>
     */
    public function daifu()
    {
        return $this->hasMany('DaifuModel','member_id');
    }

    /**
     * @return mixed
     * @author  Bob<bob@bobcoder.cc>
     */
    public function daoru()
    {
        return $this->hasMany('DaoruModel','member_id');
    }

    /**
     * @return mixed
     * @author  Bob<bob@bobcoder.cc>
     */
    public function chongzhi()
    {
        return $this->hasMany('ChongZhiModel','member_id');
    }

    /**
     * @param $id
     * @param $money
     * @return int|true
     * @throws \think\Exception
     * @author  Bob<bob@bobcoder.cc>
     */
    public static function money($id, $money)
    {
        return self::where('id',$id)->setInc('money', $money);
    }

}