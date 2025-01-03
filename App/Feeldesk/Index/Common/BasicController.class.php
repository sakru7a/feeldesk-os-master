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

namespace Index\Common;

use Think\Controller;

use Common\Controller\CommonController;

class BasicController extends Controller
{
	protected $common,$_lang,$_langAuth,$_member,$member_id,$_company_id,$login_token,$_company;

	protected static $systemAuth = [];

	public function _initialize()
	{
		$this->common = new CommonController();

        $this->common->feelDeskInit('index',$this->_member,$this->member_id,$this->_company_id,$this->login_token,$this->_lang,$this->_company);

        if(checkClientAgentIsMobile())
		{
			if ($this->getCurrentUrl() != '/m-home') {
				redirect('/m-home');
			}
		}
        else
        {
	        $this->_langAuth = ['en_auth'=>$this->_company['en_auth'],'jp_auth'=>$this->_company['jp_auth']];

	        $this->assign('langAuth',$this->_langAuth);

	        self::$systemAuth = ['ticket_auth'=>$this->_company['ticket_auth']];

	        $this->assign('systemAuth',self::$systemAuth);
        }
	}
	function getCurrentUrl() {
		// 获取当前页面的URL
		return $_SERVER['REQUEST_URI'];
	}


	protected function visitUcpaas()
	{
		$ucpass = new \Org\Ucpaas\Ucpaas(C('UCPAAS'));

		return $ucpass;
	}



	/*
	* 导出Excel
	* @param $letter		列 数组
	* @param $tableheader	表头数据 数组
	* @param $exceldata		表数据 数组
	* @param return			输出Excel文件至浏览器
	*/
	protected function exportExcel($letter,$tableHeader,$excelData)
	{
		import("Org.Util.PHPExcel");

		$excel = new \PHPExcel();

		$objActSheet = $excel->getActiveSheet();

		//填充表头数据
		for($i = 0;$i < count($tableHeader);$i++)
		{
			$objActSheet->setCellValue("$letter[$i]1",$tableHeader[$i]);

			$width = $i == 1 ? 50 : 25;

			//设置列宽
			$objActSheet->getColumnDimension("$letter[$i]")->setWidth($width);

			//文本居中
			$objActSheet->getStyle("$letter[$i]1")->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

			//加粗
			$objActSheet->getStyle("$letter[$i]1")->applyFromArray(['font'=> ['bold'=> true]]);

			//行高
			$objActSheet->getRowDimension(1)->setRowHeight('30');

			//垂直居中
			$objActSheet->getStyle("$letter[$i]1")->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
		}


		//填充表格信息
		for($i = 2;$i <= count($excelData)+1;$i++)
		{
			$l = 0;

			foreach($excelData[$i - 2] as $key=>$value)
			{
				//设置单元格数据
				$objActSheet->setCellValue("$letter[$l]$i","$value");

				//文本居中
				$objActSheet->getStyle("$letter[$l]$i")->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

				//设置行高
				$objActSheet->getRowDimension($i)->setRowHeight('20');

				//垂直居中
				$objActSheet->getStyle("$letter[$l]$i")->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

				$l++;
			}
		}

		$write = new \PHPExcel_Writer_Excel5($excel);//非2007格式

		header("Pragma: public");

		header("Expires: 0");

		header("Cache-Control:must-revalidate, post-check=0, pre-check=0");

		header("Content-Type:application/force-download");

		header("Content-Type:application/vnd.ms-execl");

		header("Content-Type:application/octet-stream");

		header("Content-Type:application/download");;

		header("Content-Transfer-Encoding:binary");

		$write->save('php://output');
	}


	/*
	* 返回错误信息
	* @param string $key 状态变量
	* @param string $message 错误信息
	* @param string 跳转链接
	*/
	public function returnError($message,$url,$key='status')
	{
		if(IS_AJAX)
		{
			$this->ajaxReturn([$key=>1,'msg'=>$message,'url'=>$url]);
		}
		else
		{
			$this->error($message,$url);
		}
	}
}
