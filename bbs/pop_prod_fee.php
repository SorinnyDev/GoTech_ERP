<?php
include_once('./_common.php');

if ($is_guest)
    alert_close('로그인 후 이용하세요.');

include_once(G5_PATH.'/head.sub.php');
add_stylesheet('<link rel="stylesheet" href="'.G5_ADMIN_URL.'/css/admin.css">', 0);
?>

<div class="new_win">
    <h1>제품 엑셀등록</h1>

    <div class="local_desc01 local_desc">
        <p>
            엑셀파일을 이용하여 제품의 수수료를 일괄등록할 수 있습니다.<br>
            형식은 <strong>수수료일괄등록용 엑셀파일</strong>을 다운로드하여 수수료 정보를 입력하시면 됩니다.<br>
            수정 완료 후 엑셀파일을 업로드하시면 수수료가 일괄등록됩니다.<br>
            엑셀파일을 저장하실 때는 <strong>Excel 97 - 2003 통합문서 (*.xls)</strong> 로 저장하셔야 합니다.
        </p>

        <p>
            <a href="javascript:;" onclick="fnDownSample();">수수료일괄등록용 엑셀파일 다운로드</a>
        </p>
    </div>

    <form name="fitemexcel" method="post" action="./pop_prod_fee_update.php" enctype="MULTIPART/FORM-DATA" autocomplete="off">

    <div id="excelfile_upload">
        <label for="excelfile">파일선택</label>
        <input type="file" name="excelfile" id="excelfile" required>
    </div>

    <div class="win_btn btn_confirm">
        <input type="submit" value="제품 엑셀파일 등록" class="btn_submit btn">
        <button type="button" onclick="window.close();" class="btn_close btn">닫기</button>
    </div>

    </form>

</div>
<script type="text/javascript">
function fnDownSample(){
	document.location.href = "./product_fee_sample.xls";
}
</script>
<?php
include_once(G5_PATH.'/tail.sub.php');