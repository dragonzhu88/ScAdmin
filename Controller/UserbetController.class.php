<?php
namespace ScAdmin\Controller;

use Bases\Controller\BasesController;

class UserbetController extends BasesController
{
    public function __construct()
    {
        parent::__construct();
        $this->assign('is_supper',1);
        $this->assign('username',session('username'));
    }

    private function isLogin(){
        $usrname = session('username');
        if(!isset($usrname)){
            $this->error('请登录！', U("index/index"));
        }
    }

    /**
     * 上传
    */
    public function upload()
    {
        $this->isLogin();
        $db = D('user_bet');
        if (empty($_FILES['file'])) {
            $this->display('upload');
            die;
        }
        vendor("PHPExcel.PHPExcel");
        $reader = \PHPExcel_IOFactory::createReaderForFile($_FILES['file']['tmp_name']);
        $PHPExcel = $reader->load($_FILES['file']['tmp_name']); // 载入excel文件
        $sheet = $PHPExcel->getSheet(0); // 读取第一個工作表
        $highestRow = $sheet->getHighestRow(); // 取得总行数
        $colsNum = $sheet->getHighestColumn(); // 取得总列数
        $highestColumm= \PHPExcel_Cell::columnIndexFromString($colsNum);//字母列转换为数字列 如:AA变为27
        $data['user_name'] = '';
        $data['bet'] = '';
        $data['added'] = 0;
        for ($row = 1; $row <= $highestRow; $row++){//行数是以第1行开始
            for ($column = 0; $column < $highestColumm; $column++) {//列数是以第0列开始
//                $data[$row-4][$column] = $sheet->getCellByColumnAndRow($column, $row)->getValue();
                $value = $sheet->getCellByColumnAndRow($column, $row)->getValue();
                switch ($column){
                    case 0:
                        $data['user_name'] = $value;
                        break;
                    case 1:
                        $data['bet'] = $value;
                        break;
                }
            }
            $data['added'] = time();
            $db->add($data);
            $data['user_name'] = '';
            $data['bet'] = '';
            $data['added'] = 0;
        }
        $this->success('操作成功！', U("Userbet/userList"));

    }

    /**
     * 管理员的列表
    */
    public function userList()
    {
        $this->isLogin();
        $db = D('user_bet');
        $count = $db->count();
        $p = getpage($count,15);
        $dbData = $db->limit($p->firstRow, $p->listRows)->select();

        foreach($dbData as $k => $v){
            $dbData[$k]['added'] = date('Y-m-d H:i:s',$v['added']);
        }
        $this->assign('list',$dbData);
        $this->assign('page', $p->show());
        $this->display('list');
    }

    /**
     * 管理员的列表
     */
    public function ajax_user_search()
    {
        $data['user_name'] = $_POST['username'];
        $db = D('user_bet');
        $result = $db->where($data)->select();
        if (empty($result)) {
            $ret['code'] = 422;
            $ret['msg'] = 'id错误';
        } else {
            foreach($result as $k => $v){
                $result[$k]['added'] = date('Y-m-d H:i:s',$v['added']);
            }
            $ret['code'] = 200;
            $ret['msg'] = '请求成功';
            $ret['data'] = $result;
        }
        $this->ajaxReturn($ret);
    }
}
