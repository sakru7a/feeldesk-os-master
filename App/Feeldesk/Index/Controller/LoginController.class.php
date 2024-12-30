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

namespace Index\Controller;

use  Think\Controller;

class LoginController extends Controller
{
	public function index()
	{
		if(session('?index'))
		{
			redirect('/u-home');
		}
		else
		{
			$login = cookie('user_login_cache');

            $this->assign('lang',strtolower(cookie('think_language')));

            $this->assign('login',$login);
			$isMobile = $this->checkClientAgentIsMobile();
			if($isMobile){
				$this->display('indexMobile');
			}else{
				$this->display();
			}

	
		}
	}

	function checkClientAgentIsMobile()
	{
		$user_agent = $_SERVER['HTTP_USER_AGENT'];
	
		$mobile_agents = ["240x320","acer","acoon","acs-","abacho","ahong","airness","alcatel","amoi","android","anywhereyougo.com","applewebkit/525","applewebkit/532","asus",
			"audio","au-mic","avantogo","becker","benq","bilbo","bird","blackberry","blazer","bleu","cdm-","compal","coolpad","danger","dbtel","dopod","elaine","eric","etouch",
			"fly ","fly_","fly-","go.web","goodaccess","gradiente","grundig","haier","hedy","hitachi","htc","huawei","hutchison","inno","ipad","ipaq","ipod","jbrowser",
			"kddi","kgt","kwc","lenovo","lg ","lg2","lg3","lg4","lg5","lg7","lg8","lg9","lg-","lge-","lge9","longcos","maemo","mercator","meridian","micromax","midp","mini",
			"mitsu","mmm","mmp","mobi","mot-","moto","nec-","netfront","newgen","nexian","nf-browser","nintendo","nitro","nokia","nook","novarra","obigo","palm","panasonic",
			"pantech","philips","phone","pg-","playstation","pocket","pt-","qc-","qtek","rover","sagem","sama","samu","sanyo","samsung","sch-","scooter","sec-","sendo",
			"sgh-","sharp","siemens","sie-","softbank","sony","spice","sprint","spv","symbian","tablet","talkabout","tcl-","teleca","telit","tianyu","tim-","toshiba","tsm",
			"up.browser","utec","utstar","verykool","virgin","vk-","voda","voxtel","vx","wap","wellco","wig browser","wii","windows ce","wireless","xda","xde","zte"];
	
		$is_mobile = false;
	
		//这里把值遍历一遍，用于查找是否有上述字符串出现过
		foreach ($mobile_agents as $device)
		{
			// stristr 查找访客端信息是否在上述数组中，不存在即为PC端。
			if(stristr($user_agent, $device))
			{
				$is_mobile = true;
	
				break;
			}
		}
	
		return $is_mobile;
	}


//    登录
	public function loging()
	{
		$result = D('Login')->login('index', 1);

		$this->ajaxReturn($result);
	}


//	  退出登录
    public function logout()
	{
		D('Login')->removeDisposeInRedis(session('index'),'index',true);

        $this->success(L('LOGIN_OUT_SUCCESS'),'/u-login');
    }
}
