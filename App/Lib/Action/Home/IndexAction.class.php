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
		//import('@.ORG.tcpdf.Tcpdf');
		import('ORG.tcpdf.Tcpdf');
		$pdf = New Tcpdf('P', 'mm', 'A4', true, 'UTF-8', false);
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('Nicola Asuni');
		$pdf->SetTitle('TCPDF Example 006');
		$pdf->SetSubject('TCPDF Tutorial');
		$pdf->SetKeywords('TCPDF, PDF, example, test, guide');
		
		// set default header data
		$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 006', PDF_HEADER_STRING);
		
		// set header and footer fonts
		$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
		
		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		
		// set margins
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
		
		// set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
		
		// set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
		
		
		
		// ---------------------------------------------------------
		
		// set font
		$pdf->SetFont('dejavusans', '', 10);
		
		// add a page
		$pdf->AddPage();
		
		// writeHTML($html, $ln=true, $fill=false, $reseth=false, $cell=false, $align='')
		// writeHTMLCell($w, $h, $x, $y, $html='', $border=0, $ln=0, $fill=0, $reseth=true, $align='', $autopadding=true)
		
		// create some HTML content
		$html = '<h1>HTML Example</h1>
Some special characters: &lt; € &euro; &#8364; &amp; è &egrave; &copy; &gt; \\slash \\\\double-slash \\\\\\triple-slash
<h2>List</h2>
List example:
<ol>
	';
		
		// output the HTML content
		$pdf->writeHTML($html, true, false, true, false, '');
		
		$pdf->lastPage();
		
		// ---------------------------------------------------------
		
		//Close and output PDF document
		$pdf->Output('example_006.pdf', 'I');
	}
}
?>