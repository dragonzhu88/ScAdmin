<?php
/**
 * 管理员的model
*/
namespace ScAdmin\Model;


/**
 */
class AdminModel extends CommModel {

    public function __construct()
    {
        parent::__construct();
    }
    protected $insertFields = array('login_name','user_name','pwd');
    protected $updateFields = array('status','role','pwd','user_name','id');
}
