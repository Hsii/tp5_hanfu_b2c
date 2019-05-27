<?php
/**
 * Created by PhpStorm.
 * User: Hsii
 * Date: 2018/12/25
 * Time: 16:18 下午
 */

namespace app\admin\controller;

use app\admin\validate\FailMessage;
use app\admin\validate\SuccessMessage;
use app\common\model\Rule as RuleModel;
use think\Db;

class Rule extends Base
{
    public function index()
    {
        return $this->fetch('', [
            'count' => RuleModel::where('status', '<>', -1)->count(),
            'ruleData' => RuleModel::getRuleNature(),
        ]);
    }
    public function rule_edit()
    {
        if (request()->isGet()) {
            $id = input('get.id');
            $this->SaveRuleNature($id);
            return $this->fetch('', [
                'RuleNatureData' => RuleModel::getRuleNatureById($id)
            ]);
        }
    }
    public function rule_add()
    {
        return $this->fetch();
    }
    /**
     * 软删除规格属性
     * @access public
     * @return SuccessMessage() 成功结果 FailMessage()   失败结果
     */
    public function destoryRule()
    {
        $rule = new RuleModel();
        if (request()->isPost()) {
            $data = input('param.');
            Db::startTrans();
            try {
                $rule->destoryRuleNature($data['id']);
                Db::commit();   // 提交事务
                return new SuccessMessage();
            } catch (\Exception $e) {
                Db::rollback();  // 回滚事务
                return new FailMessage();
            }
        }
    }

    /**
     * @return FailMessage|SuccessMessage
     */
    public function SaveRuleNature()
    {
        $rule = new RuleModel();
        if (request()->isPost()) {
            $data = input('param.');
            if (empty($data['natureData'])) {
                unset($data['natureData']);
                try {
                    $rule->saveRuleNature($data);
                    return new SuccessMessage();
                } catch (\Exception $e) {
                    return new FailMessage();
                }
            } else {
                $ruleNatureData = $this->handleDataArr($data['natureData']);
                Db::startTrans();
                try {
                    $id = $rule->saveRuleNature($data);
                    $result = $rule->get($id);
                    if ($result) {
                        $resultList = $result->nature()->select();
                        for ($i = 0; $i < count($resultList); $i++) {
                            $where = [
                                'listorder' => $resultList[$i]['listorder'],
                                'rule_id' => $id
                            ];
                            $result->nature()->where($where)->delete();
                        }
                        $result->nature()->saveAll($ruleNatureData);
                    } else {
                        $result->nature()->saveAll($ruleNatureData);
                    }
                    Db::commit();   // 提交事务
                    return new SuccessMessage();
                } catch (\Exception $e) {
                    Db::rollback();  // 回滚事务
                    return new FailMessage();
                }
            }
        }
    }
    /**
     * 规格属性处理
     * @access protected static
     * @param array $data数据
     * @return $ruleNatureData 返回属性数组
     */
    protected function handleDataArr($data)
    {
        $ruleNatureData = array();
        $nature = array_filter($data);
        $natureSort = array_values($nature);
        for ($i = 0; $i < count($nature); $i++) {
            $ruleNatureData[$i] = [
                'listorder' => $i + 1,
                'name' => $natureSort[$i]
            ];
        }
        return $ruleNatureData;
    }
    /**
     * 规格属性listorder排序
     * @access protected static
     * @param array $data数据
     * @return $ruleNatureData 返回处理过的数组
     */
    protected function handleDataListorder($data)
    {
        $ruleNatureData = array();
        for ($i = 0; $i < count($data); $i++) {
            $ruleNatureData[$i] = [
                'listorder' => $i + 1,
                'name' => $data[$i]['name']
            ];
        }
        return $ruleNatureData;
    }
}
