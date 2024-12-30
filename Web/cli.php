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

// 检测PHP环境
if(version_compare(PHP_VERSION,'7.1.0','<'))  die('require PHP > 7.1.0 !');

error_reporting(0);

header("Content-type: text/html; charset=utf-8");

define('TODAY', date('Y-m-d', $_SERVER['REQUEST_TIME']));

const APP_MODE = 'cli';

const BIND_MODULE = 'Cli';

const APP_PATH = __DIR__ . '/../App/Feeldesk/';

ini_set('date.timezone', 'Asia/Shanghai');

require __DIR__.'/../App/ThinkPHP/ThinkPHP.php';
