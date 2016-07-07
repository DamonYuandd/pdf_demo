<?php
/**
 *
 * 首页控制器
 * @author uclnn
 *
 */
class IndexAction extends HomeAction
{
	function _initialize() {
		
		parent::_initialize();
	}
	public function index() {	
		 
		$this->display($this->web_theme.':Index:index');
	}
	//验证码生成
	public function verify() {
		$type = isset ( $_GET ['type'] ) ? $_GET ['type'] : 'gif';
		import ( "ORG.Util.Image" );
		Image::buildImageVerify ( 4, 1, $type );
	}
	
	public function upload(){
	
		if (!empty($_FILES)) {
			$name=time();
			import("ORG.Net.UploadFile");
	
			$upload = new UploadFile();// 实例化上传类
	
			$upload->maxSize  = 3145728 ;// 设置附件上传大小
	
			$upload->allowExts  = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
	
			$upload->savePath =  './Public/Uploads/'.date('Ymd',time()).'/';// 设置附件上传目录
	
			$upload->saveRule =  $name;
	
			if(!$upload->upload()) {// 上传错误提示错误信息
	
	
				$info=$upload->getErrorMsg();
				
				echo '<script>parent.error("'.$info.'")</script>';
	
				
	
			}else{// 上传成功 获取上传文件信息
	
				$info =  $upload->getUploadFileInfo();
	
				//$url=$upload->savePath.$info[0]['savename'];
	
				$url= date('Ymd',time()).'/'.$info[0]['savename'];
				echo '<script>parent.add("'.$url.'","'.$_GET['position'].'")</script>';
	
			}
		}
		else{
			echo '<script>parent.error("网络问题上传失败，请重新上传！！")</script>';
		}
	}
	
	
	public function pdfshow(){
		$this->assign('test','asdasdasd');
		//获取HTML 标签
		$htmlInfo = $this->fetch($this->web_theme.':Index:ch');
		//import('@.ORG.tcpdf.Tcpdf');
		import('ORG.tcpdf.Tcpdf');
		$pdf = New Tcpdf('P', 'mm', 'A4', true, 'UTF-8', false);
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('Damon Yuan');
		 
		
		// remove default header/footer
		$pdf->setPrintHeader(false);
		$pdf->setPrintFooter(false);
		
		// set font
		$pdf->SetFont('stsongstdlight', '', 10);
		
		


		//第一页
		// add a page
		$pdf->AddPage();
		// create some HTML content
		
		// 设置背景图
		$bMargin = $pdf->getBreakMargin();
		// get current auto-page-break mode
		$auto_page_break = $pdf->getAutoPageBreak();
		// disable auto-page-break
		$pdf->SetAutoPageBreak(false, 0);
		// set bacground image
		$img_file = dirname(__FILE__).'../../../../../Public/images/bg1.jpg';
		
		$pdf->Image($img_file, 0, 0, 210, 297, '', '', '', false, 300, '', false, false, 0);
		// restore auto-page-break status
		$pdf->SetAutoPageBreak($auto_page_break, $bMargin);
		// set the starting point for the page content
		$pdf->setPageMark();
		
		
		$html = $htmlInfo;
		
		// output the HTML content
		$pdf->writeHTML($html, true, false, true, false, '');
		
		$pdf->lastPage();
		
		// ---------------------------------------------------------
		
		//第二页
		$pdf->AddPage();
		
		$html = $htmlInfo;
		
		// output the HTML content
		$pdf->writeHTML($html, true, false, true, false, '');
		
		$pdf->lastPage();
		
		//Close and output PDF document
		$pdf->Output('example_006.pdf', 'I');
	}
}
?>