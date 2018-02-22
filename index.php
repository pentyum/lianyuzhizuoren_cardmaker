<?php
if(!isset($_POST['submit'])){
header("Content-type: text/html; charset=utf-8");
?>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=0.5, maximum-scale=2.0, user-scalable=yes" />
<title>恋与制作人羁绊生成器</title>

<!--请前往 https://weui.io 下载css -->
<link rel="stylesheet" href="./css/weui.css"/>
<link rel="stylesheet" href="./css/example.css"/>

</head>
<body ontouchstart>
	<div class="page_input_js_show">
		<form method="post" action="./" enctype="multipart/form-data">
			<div class="page__hd">
				<h1 class="page__title">恋与制作人DIY羁绊生成器</h1>
				<p class="page__desc">图片建议9:16</p>
			</div>
			<div class="page__bd">
				<div class="weui-cells weui-cells_form">
					<div class="weui-cell">
						<div class="weui-cell__hd"><label class="weui-label">名字</label></div>
						<div class="weui-cell__bd">
							<input name="name" class="weui-input" placeholder="请输入名字">
						</div>
					</div>
					<div class="weui-cell">
						<div class="weui-cell__hd"><label class="weui-label">英文名</label></div>
						<div class="weui-cell__bd">
							<input name="engname" class="weui-input" placeholder="请输入英文名">
						</div>
					</div>
					<div class="weui-cell">
						<div class="weui-cell__hd"><label class="weui-label">级别</label></div>
						<div class="weui-cell__bd">
							<select class="weui-select" name="level"><option value="r">R</option><option value="sr">SR</option><option value="ssr">SSR</option></select>
						</div>
					</div>
					<div class="weui-cell">
						<div class="weui-cell__hd"><label class="weui-label">星星数</label></div>
						<div class="weui-cell__bd">
							<select class="weui-select" name="stars"><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4(仅SR,SSR)</option><option value="5">5(仅SSR)</option></select>
						</div>
					</div>
					<div class="weui-cell">
						<div class="weui-cell__hd"><label class="weui-label">图片</label></div>
						<div class="weui-cell__bd">
							<input type="file" name="pic" >
						</div>
					</div>
				</div>
			</div>
			<div class="weui-btn-area">
				<input type="submit" class="weui-btn weui-btn_primary" name="submit" value="生成">
			</div>
		</form>
		<div class="page__ft">
			<p class="weui-footer__links">
				<a href="http://piggest.com" class="weui-footer__link">Powered by Pentyum</a>
			</p>
			<p class="weui-footer__text">Copyright © 2015-2018 Piggest</p>
		</div>
	</div>
</body>
</html>
<?php
}else{
header("Content-Type:image/png");
$name=$_POST["name"];
$engname=$_POST["engname"];
$level=$_POST["level"];
$stars=$_POST["stars"];
$newfilename="./upload/" . time().'_'.$_FILES["pic"]["name"];
move_uploaded_file($_FILES["pic"]["tmp_name"],$newfilename);

$image = $newfilename; // 原图
$imgstream = file_get_contents($image);
$im = imagecreatefromstring($imgstream);
$x = imagesx($im);//获取图片的宽
$y = imagesy($im);//获取图片的高
 
// 缩略后的大小
$xx = 1200;
$yy = 675;
 
if($x>$y){
//图片宽大于高
    $sx = abs(($y-$x)/2);
    $sy = 0;
    $thumbw = $y;
    $thumbh = $y;
} else {
//图片高大于等于宽
    $sy = abs(($x-$y)/2.5);
    $sx = 0;
    $thumbw = $x;
    $thumbh = $x;
  }
  $dim = imagecreatetruecolor($yy, $xx); // 创建目标图gd2
imageCopyreSampled ($dim,$im,0,0,$sx,$sy,$yy,$xx,$thumbw,$thumbh);

$imback = $dim;

function merge_rgb_alpha($imrgb,$imalpha){

	$rgbwidth=imagesx($imrgb);
	$rgbheight=imagesy($imrgb);

	$newim = imagecreatetruecolor($rgbwidth, $rgbheight);
	$color=imagecolorallocate($newim,255,255,255);

	imagecolortransparent($newim,$color);

	imagefill($newim,0,0,$color);

	for($x=0;$x<$rgbwidth;$x++){
		for($y=0;$y<$rgbheight;$y++){
			$index=imagecolorat($imalpha,$x,$y);
			$alphacolor=imagecolorsforindex($imalpha,$index);
			$index=imagecolorat($imrgb,$x,$y);
			$rgbcolor=imagecolorsforindex($imrgb,$index);
			$newcolor=$rgbcolor;
			$newcolor['alpha']=$alphacolor['alpha'];
			$color=imagecolorallocatealpha($newim, $newcolor['red'], $newcolor['green'], $newcolor['blue'],$newcolor['alpha']);
			imagesetpixel($newim,$x,$y,$color);
		}
	}
	
	return $newim;
}
//$newim=merge_rgb_alpha($imrgb,$imalpha);
function merge_rgb_alpha_back($imback,$imrgb,$imalpha,$resx,$resy,$reswidth,$resheight,$dstx,$dsty){

	$rgbwidth=imagesx($imrgb);
	$rgbheight=imagesy($imrgb);

	$newim = $imback;

	for($x=$resx;$x<($resx+$reswidth);$x++){
		for($y=$resy;$y<($resy+$resheight);$y++){
			$index=imagecolorat($imalpha,$x,$y);
			$alphacolor=imagecolorsforindex($imalpha,$index);
			$index=imagecolorat($imrgb,$x,$y);
			$rgbcolor=imagecolorsforindex($imrgb,$index);
			$newcolor=$rgbcolor;
			$newcolor['alpha']=$alphacolor['alpha'];
			$color=imagecolorallocatealpha($newim, $newcolor['red'], $newcolor['green'], $newcolor['blue'],$newcolor['alpha']);
			imagesetpixel($newim,$dstx+$x-$resx,$dsty+$y-$resy,$color);
		}
	}
	
	return $newim;
}
function add_sr($imback,$x,$y){
	$imrgb = imagecreatefrompng("UI_bigcard_RGB.png");
	$imalpha = imagecreatefrompng("UI_bigcard_Alpha.png");
	$newim=merge_rgb_alpha_back($imback,$imrgb,$imalpha,700,325,165,165,$x,$y);
	return $newim;
}
function add_r($imback,$x,$y){
	$imrgb = imagecreatefrompng("UI_bigcard_RGB.png");
	$imalpha = imagecreatefrompng("UI_bigcard_Alpha.png");
	$newim=merge_rgb_alpha_back($imback,$imrgb,$imalpha,857,550,165,165,$x,$y);
	return $newim;
}
function add_ssr($imback,$x,$y){
	$imrgb = imagecreatefrompng("UI_bigcard_RGB.png");
	$imalpha = imagecreatefrompng("UI_bigcard_Alpha.png");
	$newim=merge_rgb_alpha_back($imback,$imrgb,$imalpha,410,730,330,150,$x,$y);
	return $newim;
}
function add_tag($imback,$x,$y){
	$imrgb = imagecreatefrompng("UI_bigcard_RGB.png");
	$imalpha = imagecreatefrompng("UI_bigcard_Alpha.png");
	$newim=merge_rgb_alpha_back($imback,$imrgb,$imalpha,0,550,400,300,$x,$y);
	return $newim;
}
function add_text($imback,$x,$y,$size,$text){
	$color=imagecolorallocate($imback, 255, 255, 255);
	imagettftext($imback,$size,40 , $x , $y , $color , "/home/web/service/wwwroot/fonts/fzqk.ttf" , $text );
	return imagettfbbox ($size,40 , "/home/web/service/wwwroot/fonts/fzqk.ttf" , $text );
}
function add_star1($imback,$x,$y){
	$color=imagecolorallocatealpha($imback, 255, 255, 255,127);
	$imrgb = imagecreatefrompng("UI_bigcard_RGB.png");
	$imalpha = imagecreatefrompng("UI_bigcard_Alpha.png");
	$imrgb=imagerotate ($imrgb , 40 , $color);
	$imalpha=imagerotate ($imalpha , 40 , $color);
	$newim=merge_rgb_alpha_back($imback,$imrgb,$imalpha,940,230,37,37,$x,$y);
	return $newim;
}
function add_star2($imback,$x,$y){
	$color=imagecolorallocatealpha($imback, 255, 255, 255,127);
	$imrgb = imagecreatefrompng("UI_bigcard_RGB.png");
	$imalpha = imagecreatefrompng("UI_bigcard_Alpha.png");
	$imrgb=imagerotate ($imrgb , 40 , $color);
	$imalpha=imagerotate ($imalpha , 40 , $color);
	$newim=merge_rgb_alpha_back($imback,$imrgb,$imalpha,967,260,37,37,$x,$y);
	return $newim;
}
function add_star($imback,$star1,$star2){
	$x=455;
	$y=1180;
	for($i=1;$i<=$star2;$i++){
		if($i<=$star1){
			$newim=add_star1($imback,$x+$i*30,$y-$i*25);
		}else{
			$newim=add_star2($imback,$x+$i*30,$y-$i*25);
		}
	}
	return $newim;
}
function add_edge($imback,$color){
	imagerectangle ($imback , 0 , 0 , 674 , 1199 , $color );
	imagerectangle ($imback , 1 , 1 , 673 , 1198 , $color );
	imagerectangle ($imback , 2 , 2 , 672 , 1197 , $color );
	imagerectangle ($imback , 3 , 3 , 671 , 1196 , $color );
}
$color=imagecolorallocate($imback, 251, 161, 189);
add_edge($imback,$color);
$newim=add_tag($imback,275,897);
if($_POST['level']=='r'){
	$newim=add_r($newim,20,15);
	$newim=add_star($newim,$stars,4);
}elseif($_POST['level']=='sr'){
	$newim=add_sr($newim,20,15);
	$newim=add_star($newim,$stars,5);
}elseif($_POST['level']=='ssr'){
	$newim=add_ssr($newim,20,15);
	$newim=add_star($newim,$stars,6);
}
$info=add_text($newim,435,1185,46,$name);
//print_r($info);
add_text($newim,435+$info[2],1185+$info[3]-10,18,$engname);
//add_text($newim,605,1040,22,"USTC");
imagepng($newim);
/*
$rgbwidth=imagesx($im);
$rgbheight=imagesy($im);
$backwidth=imagesx($imback);
$backheight=imagesy($imback);
$width=min($backwidth,$rgbwidth);
$height=min($backheight,$backheight);
imagecopymerge($imback,$im,500,180,0,368,60,60,40);
imagepng($imback);
*/
unlink($newfilename);
imagepng($newim,$newfilename);
}
?>