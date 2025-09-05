<?php
include_once('./_common.php');

if ($is_guest)
    alert_close('로그인 후 이용하세요.');

include_once(G5_PATH.'/head.sub.php');
add_stylesheet('<link rel="stylesheet" href="'.G5_ADMIN_URL.'/css/admin.css">', 0);
?>

<div class="new_win">
    <h1>엑셀자료 매출등록</h1>

    <div class="local_desc01 local_desc">
        <p>
            엑셀파일을 이용하여 매출자료를 등록할 수 있습니다.<br>
            수정 완료 후 엑셀파일을 업로드하시면 매출자료가 일괄등록됩니다.<br>
            엑셀파일을 저장하실 때는 <strong>Excel 97 - 2003 통합문서 (*.xls)</strong> 로 저장하셔야 합니다.
        </p>

    </div>

    <form name="fitemexcel" method="post" action="./pop_sales_add_update.php" enctype="MULTIPART/FORM-DATA" autocomplete="off" onsubmit="return chkfrm(this)" >

    <div id="excelfile_upload">
        <label for="excelfile" style="display:block;padding-bottom:10px">플랫폼 선택</label>
        <select name="domain" id="domain" style="width:100%" required>
			<option value="">선택하세요</option>
            <?php //echo get_domain_option($wr_18);
            $code_list = get_code_list('4');
            foreach($code_list as $key =>$value){
                $selected = ($domain==$value['code_value']) ? "selected" : "";
                echo "<option value=\"{$value['code_value']}\" {$selected}>{$value['code_name']}</option>";
            }
            
            ?>
		</select> 
		
		<select name="currency" id="currency" style="width:100%;margin-top:5px;display:none" >
			<option value="GBP">통화 : GBP</option>
			<option value="EUR">통화 : EUR</option>
		</select>
		
    </div>
    <div id="excelfile_upload">
        <label for="excelfile" style="display:block;padding-bottom:10px">매출자료</label>
        <input type="file" name="excelfile" id="excelfile" required  style="width:100%">
    </div>
    <div id="excelfile_upload" class="qoo10_upload2" style="display:none">
        <label for="excelfile" style="display:block;padding-bottom:10px">정산금액</label>
        <input type="file" name="excelfile2" id="excelfile2"   style="width:100%">
    </div>
    <div id="excelfile_upload" class="qoo10_upload3" style="display:none">
        <label for="excelfile" style="display:block;padding-bottom:10px">정산배송비</label>
        <input type="file" name="excelfile3" id="excelfile3"   style="width:100%">
    </div>

    <div class="win_btn btn_confirm">
        <input type="submit" value="매출자료 엑셀파일 등록" class="btn_submit btn">
        <button type="button" onclick="window.close();" class="btn_close btn">닫기</button>
    </div>

    </form>

</div>
<script>
function chkfrm(f){
	
	$('.btn_submit').html('<img style="width:15px" src="/mobile/shop/img/loading.gif">');
	$('.btn_submit').attr('disabled', true);
	
}
$(function(){
	$('#domain').bind('change', function(){
		
		let domain = $(this).val();
		let f = document.fitemexcel;
		
		//큐텐은 총 3개파일 업로드 
		if(domain == "Qoo10" || domain == "qoo10-jp" || domain == "Qoo10-1" || domain == "qoo10jp-1") {
			$('.qoo10_upload2, .qoo10_upload3').show();
			f.action = './pop_sales_add_update2.php';
		} else {
			$('.qoo10_upload2, .qoo10_upload3').hide();
			f.action = './pop_sales_add_update.php';
		}
		
		//ACF-CAD는 통화선택
		if(domain == "ACF-CAD" || domain == "ACF-CAD_F" || domain == "ACF-CAD_I") {
			$('#currency').show();
		} else {
			$('#currency').hide();
		}
		
	})
})
</script>

<?php
include_once(G5_PATH.'/tail.sub.php');