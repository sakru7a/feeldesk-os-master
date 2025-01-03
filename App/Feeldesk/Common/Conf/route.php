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
return [
	'URL_ROUTER_ON'         => true,

	'URL_ROUTE_RULES'       => [
		# Index
		'u-login'                           => ['Login/index'],
		'u-log-in'                          => ['Login/loging'],
		'u-logout'                          => ['Login/logout'],
		'u-home'                            => ['Index/index'],
		'u-home-base'                       => ['Index/base'],
		'u-home-workbench'                  => ['Index/welcome'],
		'u-reset'                           => ['Forget/index'],
		'u-reset-pwd/:way'                  => ['Forget/resetPassword'],
		'u-reset-submit'                    => ['Forget/resetPassword'],
		'u-reset-code'                      => ['Forget/sendVerifyCode'],
		'u-reset-success'                   => ['Forget/reset_success'],
		'm-home'                            => ['Mobile/Index'],
		'm-login'                           => ['Mobile/Login']
	]
];
