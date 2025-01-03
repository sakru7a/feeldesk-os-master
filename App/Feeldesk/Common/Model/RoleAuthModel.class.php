<?php
// +----------------------------------------------------------------------
// | FeelDesk-DEV开源工单管理系统
// +----------------------------------------------------------------------
// | 欢迎阅读学习系统程序代码，您的建议反馈是我们前进的动力
// | 开源版本仅供技术交流学习，请务必保留界面版权logo
// | 商业版本务必购买商业授权，以免引起法律纠纷
// | 禁止对系统程序代码以任何目的，任何形式的再发布
// | gitee下载：https://gitee.com/feelecs/feeldesk-os
// | 开源官网：https://www.feeldesk.com.cn
// | 成都菲莱克斯科技有限公司 版权所有 拥有最终解释权
// +----------------------------------------------------------------------
namespace Common\Model;

class RoleAuthModel extends BasicModel
{
	protected $pk   = 'role_id';

	protected $tableName = 'role_auth';

	/*
	* 分配给角色最高权限
	* @param int $company_id    公司ID
	* @param int $role_id       角色ID
	* @param int $systemAuth    系统权限
	*/
	public function grantHighestAuth($company_id = 0,$role_id = 0,$systemAuth = [])
	{
		$version_id = M('company_version')->where(['company_id'=>$company_id])->getField('version_id');

		$ticketVersionMenuIds = M('version_menu')->where(['version_id'=>$version_id])->getField('menu_id');

		$auth_id = $this->where(['company_id'=>$company_id,'role_id'=>$role_id])->getField('id');

		if(!$auth_id)
		{
			$this->add(['company_id'=>$company_id,'role_id'=>$role_id,'menu_id'=>$ticketVersionMenuIds]);
		}
		else
		{
			$this->save(['id'=>$auth_id,'menu_id'=>$ticketVersionMenuIds]);
		}
	}


	/*
	* 分配角色权限时获取角色权限菜单信息
	* @param int $role_id       角色ID
	* @param int $version_id    版本ID
	* @param int $menuName      菜单名称字段，用于多语言
	* @param int $system        系统
	*/
	public function getRoleAuthMenus($role_id = 0,$version_id = 0,$menuName = 'menu_name',$system = '')
	{
//    	  角色 —— 权限菜单ID
		$auth = D('FeelRoleAuth')->getMenuIdsByRoleId($role_id,$system);

//	     当前角色权限菜单信息
		$menus = M('menu')->field("menu_id,parent_id,{$menuName}")->select();

		$auth = $auth ?: ['-1'];//此代码主要用于分配角色权限时，该角色没有权限需要显示菜单树

		return getMenuTree($menus,'menu_id','parent_id','children',0,$auth);
	}


	/*
	 * 更新角色权限
	 * @param int       $company_id 公司ID
	 * @param int       $role_id    角色ID
	 * @param string    $system     系统
	 * @return mix
	 */
	public function updateRoleAuthMenus($company_id,$role_id,$system)
	{
		$menuIds = I('post.ticket',[]);

		$roleAuthModel = M('role_auth');

		sort($menuIds);

		$authMenuIds = json_encode($menuIds);

		$auth_id = $roleAuthModel->where(['company_id'=>$company_id,'role_id'=>$role_id])->getField('id');

		if($auth_id)
		{
			$result = $roleAuthModel->where(['id'=>$auth_id])->save(['menu_id'=>$authMenuIds]);
		}
		else
		{
			$result = $roleAuthModel->add(['company_id'=>$company_id,'role_id'=>$role_id,'menu_id'=>$authMenuIds]);
		}

		if($result !== false)
		{
//            更新角色权限缓存
			S('menuData/'.$system.'_role_auth_'.$company_id.'_'.$role_id,$menuIds,3600);
		}
	}


	/*
	* 通过Menu菜单校验角色操作权限
	* @param int $company_id 公司ID
	* @param string $action    菜单URL
	* @param int    $role_id   角色ID
	* @return int
	*/
	public function checkRoleAuthByMenu($company_id = 0,$action = '',$role_id = 0,$system = 'ticket')
	{
		$Model = M('menu');

		if(!$action)
		{
			$action = strtolower(CONTROLLER_NAME . '/' . ACTION_NAME);
		}
		error_log("权限：$action");
		// if(!in_array($action,['mobile/index','index/base','index/index','index/welcome','base/index','notice/index','notice/detail','ticket/service_report']))

		if(!in_array($action,['Mobile/ticket/allTicket','mobile/index','index/base','index/index','index/welcome','base/index','notice/index','Ticket/reply','notice/detail','ticket/service_report']))		{
			$auth = S('menuData/'.$system.'_role_auth_'.$company_id.'_'.$role_id);

			$menu_id = $Model->where(['menu_action'=>$action])->getField('menu_id');

			if(!in_array($menu_id,$auth))
			{
				return false;
			}

			return $menu_id;
		}

		return true;
	}
}
