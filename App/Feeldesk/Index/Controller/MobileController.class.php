<?php

namespace Index\Controller;

use Index\Common\BasicController;
use Think\Page;

class MobileController extends BasicController
{
    protected static $ticket_status = [];

    protected static $first_status = [];

    protected static $end_status = [];
    public function index()
    {
        $member_id  = $this->member_id;
        $role_id = $this->_member['role_id'];
        // 这里可以从数据库中获取相关信息并赋值给模板
        // $data['my_ticket_count'] = $ticketModel->where(['dispose_id' => $member_id])->count();
        $data = D('Ticket')->getTicketAuthByMobile(1, $member_id,  $role_id);
        error_log("fffff:$role_id");
        $this->assign('data', $data);

        // 显示视图
        $this->display();
    }
    public function ticket($action = null)
    {
        switch ($action) {
            case 'allTicket':
                $this->allTicket();
                break;
            default:
                $this->error('Action not defined');
                break;
        }
    }

    // public function allTicket($source = '', $where = [], $field = '')
    // {
    // 	$where = $source == 'export' ? $where : $this->getField($source);

    // 	$list = $this->getTicketList($source, $where, $field);

    // 	if ($source == 'export') return $list;

    // 	$this->assign('ticket', $list);

    // 	$this->display();
    // }

    public function allTicket()
    {

        $member = $this->_member;
        //error_log( print_r($member) );
        // 向视图分配数据
        $this->assign('data', $member);
        $this->display();
    }

    
	public function loging()
	{
		$result = D('Login')->login('index', 1);

		$this->ajaxReturn($result);
	}

}
