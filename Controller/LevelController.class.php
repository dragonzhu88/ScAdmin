<?php
namespace ScAdmin\Controller;

use Bases\Controller\BasesController;

class LevelController extends BasesController
{
    public function __construct()
    {
        parent::__construct();
        $this->assign('is_supper', 1);
        $this->assign('username',session('username'));
    }

    /**
     * 等级的列表
     */
    private function isLogin(){
        $usrname = session('username');
        if(!isset($usrname)){
            $this->error('请登录！', U("index/index"));
        }
    }

    public function levelList()
    {
        $this->isLogin();
        $db = D('bet_level');
        $count = $db->count();
        $p = getpage($count,15);
        $dbData = $db->limit($p->firstRow, $p->listRows)->select();
        foreach ($dbData as $k => $v){
            if(floatval($v['bet_total'] > 10000)){
            $dbData[$k]['bet_total'] = floatval($v['bet_total'])/10000 + '万';
            } else{
                $dbData[$k]['bet_total'] = floatval($v['bet_total']);
            }

            if(floatval($v['bet_total_max'] > 10000)){
                $dbData[$k]['bet_total_max'] = floatval($v['bet_total_max'])/10000 + '万';
            } else{
                $dbData[$k]['bet_total_max'] = floatval($v['bet_total_max']);
            }

            if(floatval($v['grade_gift'] > 10000)){
                $dbData[$k]['grade_gift'] = floatval($v['grade_gift'])/10000 + '万';
            } else{
                $dbData[$k]['grade_gift'] = floatval($v['grade_gift']);
            }

            if(floatval($v['week_gift'] > 10000)){
                $dbData[$k]['week_gift'] = floatval($v['week_gift'])/10000 + '万';
            } else{
                $dbData[$k]['week_gift'] = floatval($v['week_gift']);
            }

            if(floatval($v['month_gift'] > 10000)){
                $dbData[$k]['month_gift'] = floatval($v['month_gift'])/10000 + '万';
            } else{
                $dbData[$k]['month_gift'] = floatval($v['month_gift']);
            }
        }
        $this->assign('list', $dbData);
        $this->assign('page', $p->show());
        $this->display('list');
    }

    /**
     * 添加等级页面
     */

    public function leveladd()
    {
        $this->isLogin();
        $checked = function ($one, $two) {
            if ($one == $two) {
                echo "checked";
            }
        };
        $this->assign('checked', $checked);
        $this->display();
    }

    public function levelOne()
    {
        $data['id'] = $_POST['id'];
        $db = D('bet_level');
        $ret = $db->where($data)->find();
        if (empty($data['id']) || empty($ret)) {
            $ret['code'] = 422;
            $ret['msg'] = 'id错误';
        } else {
            $ret['code'] = 200;
            $ret['msg'] = '请求成功';
            $ret['data'] = $data;
        }
        $this->ajaxReturn($ret);
    }

    /**
     * 添加管理员
     */
    public function levelAddDo()
    {
        $db = D('bet_level');
        $data['gread'] = $_POST['gread'];
        $data['bet_total'] = $_POST['bet_total'];
        $data['grade_gift'] = $_POST['grade_gift'];
        $data['week_gift'] = $_POST['week_gift'];
        $data['month_gift'] = $_POST['month_gift'];
//        $data['is_vip'] = $_POST['is_vip'];
        $data['customer_service'] = $_POST['customer_service'];
        $data['accelerate'] = $_POST['accelerate'];
        $data['one_on_one'] = $_POST['one_on_one'];
        $data['bet_total_max'] = $_POST['bet_total_max'];

        if($data['bet_total'] >= $data['bet_total_max']){
            $this->error('设置打码量下限不能超过上限！', U("level/leveladd"));
            return;
        }

        if ($db->add($data)) {
            $this->success('操作成功！', U("level/levelList"));
        } else {
            $this->error('操作失败！', U("level/levelList"));
        }

    }

    public function levelModify()
    {
        $this->isLogin();
        $checked = function ($one, $two) {
            if ($one == $two) {
                echo "checked";
            }
        };
        $this->assign('checked', $checked);
        $data['id'] = $_GET['id'];
        $db = D('bet_level');
        $ret = $db->where($data)->find();
        $this->assign('ret', $ret);
        $this->display();
    }

    public function levelModifyDo()
    {
        $db = D('bet_level');
        $where['id'] = $_POST['id'];
        if($_POST['bet_total'] >= $_POST['bet_total_max']){
            $this->error('设置打码量下限不能超过上限！', U("level/leveladd"));
            return;
        }
        if ($db->where($where)->save($_POST)) {
            $this->success('操作成功！',U("level/levelList"));
        }else{
            $this->error('操作失败！',U("level/levelList"));
        }
    }

    public function levelSearchList(){
        $this->isLogin();
        $data['bet'] = ['between', [$_GET['bet_total'], $_GET['bet_total_max']]];
        $db = D('user_bet');
        $count = $db->where($data)->count();
        $p = getpage($count,15);
        $dbData = $db->where($data)->limit($p->firstRow, $p->listRows)->select();
        foreach($dbData as $k => $v){
            $dbData[$k]['added'] = date('Y-m-d H:i:s',$v['added']);
        }
        $this->assign('list',$dbData);
        $this->assign('page', $p->show());
        $this->display();
    }

    public function level_del_do(){
        $where['id'] = $_POST['id'];
        $db = D('bet_level');
        $db->where($where)->delete();
        $ret['code'] = 200;
        $ret['msg'] = '操作成功';
        $this->ajaxReturn($ret);
    }

}
