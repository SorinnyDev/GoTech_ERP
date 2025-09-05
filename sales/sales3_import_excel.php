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
            엑셀파일을 이용하여  출고등록을 할 수 있습니다.<br>
            수정 완료 후 엑셀파일을 업로드하시면 출고완료가 일괄 처리됩니다.<br>
            엑셀파일을 저장하실 때는 <strong>Excel 97 - 2003 통합문서 (*.xls)</strong> 로 저장하셔야 합니다.
			<br><a href="./sample.xls">샘플양식 다운로드</a>
        </p>

    </div>

    <form name="fitemexcel" method="post" action="./sales3_import_update.php" enctype="MULTIPART/FORM-DATA" autocomplete="off" onsubmit="return chkfrm2(this)">

    <div id="excelfile_upload">
        <label for="excelfile" style="display:block;padding-bottom:10px">엑셀자료</label>
        <input type="file" name="excelfile" id="excelfile" required  style="width:100%">
    </div>
 

    <div class="win_btn btn_confirm">
        <input type="submit" value="엑셀파일 출고등록" class="btn_submit btn">
        <button type="button" onclick="window.close();" class="btn_close btn">닫기</button>
    </div>

    </form>

</div>
<script>

function chkfrm2(f){
	$('.btn_submit').attr('disabled', true).hide();

}

</script>

<?php
include_once(G5_PATH.'/tail.sub.php');