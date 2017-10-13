<?php
namespace ScAdmin\Controller;

use Bases\Controller\BasesController;
use Think\Exception;

class IndexController extends BasesController
{
    public function __construct()
    {
        parent::__construct();
        $this->assign('is_supper', 1);
        $this->assign('username',session('username'));
    }

    private function isLogin(){
        $usrname = session('username');
        if(!isset($usrname)){
            $this->error('请登录！', U("index/index"));
        }
    }

    /**
     * 首页
     */
    public function index()
    {
        $this->display('index1');
    }

    public function login()
    {
        $where['user_name'] = $_POST['user_name'];
        $where['pwd'] = md5($_POST['pwd']);

        try{
            $db = D('admin_tb');
            $dbData = $db->where($where)->find();
        }catch (Exception $e){
            $ret['code'] = 422;
            $ret['msg'] = $e->getMessage();
        }

        if ($dbData) {
            session('username', $dbData['user_name']);
            $ret['code'] = 200;
            $ret['msg'] = '请求成功';
        } else {
            $ret['code'] = 422;
            $ret['msg'] = '操作失败';
        }
        $this->ajaxReturn($ret);
    }

    public function adminRegister()
    {
        $this->display('adminRegister');
    }

    /**
     * 管理员的列表
     */
    public function adminList()
    {
        $this->isLogin();
        $db = D('admin_tb');
        $count = $db->count();
        $p = getpage($count, 15);
        $dbData = $db->limit($p->firstRow, $p->listRows)->select();
        $this->assign('list', $dbData);
        $this->assign('page', $p->show());
        $this->display('list');
    }

    /**
     * 获取单个管理员的信息
     */
    public function adminOne()
    {
        $data['id'] = $_POST['id'];
        $db = D('admin_tb');
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
     * 添加添加管理员页面
     */

    public function adminAdd()
    {
        $this->isLogin();
        $this->display();
    }

    public function adminModify()
    {
        $this->isLogin();
        $data['id'] = $_GET['id'];
        $db = D('admin_tb');
        $ret = $db->where($data)->find();
//        var_dump($ret);
        $this->assign('ret', $ret);
        $this->display();
    }

    public function adminModifyDo()
    {
        $db = D('admin_tb');
        $data['user_name'] = $_POST['user_name'];
        $data['login_name'] = $_POST['login_name'];
        if ($_POST['pwd'] != '***') {
            $data['pwd'] = MD5($_POST['pwd']);
        }
        $where['id'] = $_POST['id'];
        if ($db->where($where)->save($data)) {
            $this->success('操作成功！', U("index/adminList"));
        } else {
            $this->error('操作失败！', U("index/adminList"));
        }
    }

    /**
     * 添加管理员
     */
    public function adminAddDo()
    {

        $db = D('admin_tb');
        $data['user_name'] = $_POST['user_name'];
        $data['login_name'] = $_POST['login_name'];
        $ret = $db->where('user_name="%s" or login_name="%s"', $data['user_name'], $data['login_name'])->find();
        if (!empty($ret)) {
            $this->error('登录账号或者管理员姓名重复', U("index/adminList"));
        }
        $data['pwd'] = md5($_POST['pwd']);
        $data['role'] = 1;
        $data['status'] = 1;
        if ($db->add($data)) {
            $this->success('操作成功！', U("index/adminList"));
        } else {

            $this->error('操作失败！', U("index/adminList"));
        }

    }

    public function registerDo()
    {
        $db = D('admin_tb');
        $data['user_name'] = $_POST['user_name'];
        $isExist = $db->where($data)->find();
        if ($isExist) {
            $ret['code'] = 422;
            $ret['msg'] = '已经注册过该用户名';
        } else {
            $data['login_name'] = $_POST['user_name'];
            $data['pwd'] = md5($_POST['pwd']);
            $data['role'] = 1;
            $data['status'] = 1;

            if ($db->add($data)) {
                session('username',  $data['user_name']);
                $ret['code'] = 200;
                $ret['msg'] = '请求成功';
            } else {
                $ret['code'] = 422;
                $ret['msg'] = '操作失败';
            }
        }
        $this->ajaxReturn($ret);
    }

    public function loginOut(){
        session('username',null);
        $ret['code'] = 200;
        $ret['msg'] = '请求成功';
        $this->ajaxReturn($ret);
    }

    /**
     * 更新管理员
     */
}
