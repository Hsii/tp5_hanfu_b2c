<?php
/**
 * Created by PhpStorm.
 * User: Hsii
 * Date: 2019/01/14
 * Time: 20:24 下午
 */

namespace app\common\model;
use think\Model;

class Rule extends Model
{
    protected $hidden = ['create_time', 'update_time', 'delete_time'];

    public function nature()
    {
        return $this->hasMany('RuleNature', 'rule_id', 'id');
    }
    /**
     * 查询所有的rule信息
     * @access public static
     * @return $rule 返回查询结果数据集
     */
    public static function getRuleNature()
    {
//        $rule = collection(model('Rule')->where('status', '<>', -1)->with('nature')->select());
        $rule = collection(model('Rule')->where('status', '<>', -1)->order('id')->with('nature')->select());
        return $rule;
    }

    /**
     * 查询id值对应的rule信息
     * @access public
     * @param int $id 主键id
     * @return $rule 返回主键id对应的规格属性信息
     */
    public static function getRuleNatureById($id)
    {
        $list = [
            'id' => $id,
            'status' => 0,
        ];
        $rule = collection(model('Rule')->where($list)->with('nature')->select());
        return $rule;
    }

    /**
     * 新增和修改规格属性
     * @access public
     * @param array $data 数据
     * @return $this->id 返回自增主键id
     */
    public function SaveRuleNature($data)
    {
        if ($data['id']) {
            //更新操作
            $where = [
                'id' => $data['id']
            ];
            $list = [
                'rule_name' => $data['rule_name'],
                'rule_memo' => $data['rule_memo']
            ];
            $this->save($list, $where);
            return $data['id'];
        } else {
            //新增操作
            $list = [
                'rule_name' => $data['rule_name'],
                'rule_memo' => $data['rule_memo']
            ];
            $this->allowField(true)->save($list);
            return $this->id;
        }

    }

//    public function saveRuleOrder($data)
//    {
//        if ($data['rule_order'] && is_array($data['rule_order'])){
//            // 更新排序操作
//        $arr = array();
//            $i = 0;
//            foreach ($data['rule_order'] as $k => $v){
//                $arr[$i]['id'] = $k;
//                $arr[$i]['listorder'] = $v;
//                $i++;
//            }
//            return $this->saveAll($arr);
//        }
//    }
    /**
     * 软删除规格属性
     * @access public
     * @param array $id 数据
     * @return $this->id 返回自增主键id
     */
    public static function destoryRuleNature($id)
    {
        $list = [
            'status' => -1
        ];
        $success = self::where('id', $id)->update($list);
        return $success;
    }
}