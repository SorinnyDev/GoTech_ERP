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
             엑셀파일을 이용하여 배송사별 배송비를 등록할 수 있습니다.<br>
            수정 완료 후 엑셀파일을 업로드하시면 배송비가 일괄등록됩니다.<br>
            엑셀파일을 저장하실 때는 <strong>Excel 97 - 2003 통합문서 (*.xls)</strong> 로 저장하셔야 합니다.
        </p>
		

    </div>

    <form name="fitemexcel" method="post" action="./excel_pop_update.php" enctype="MULTIPART/FORM-DATA" autocomplete="off">

    <div id="excelfile_upload">
        <label for="excelfile" style="display:block;padding-bottom:10px">배송사 선택 <strong style="color:#ff3061">(업로드시 선택하신 배송사의 기존 데이터는 초기화 됩니다.)</strong> </label>
        <select name="wr_code" style="width:100%" required>
			<option value="">선택하세요</option>
			<?php 
			$delivery_sql = "select * from g5_delivery_company where wr_use = 1";
			$delivery_rst = sql_query($delivery_sql);
			while($delivery_com=sql_fetch_array($delivery_rst)) {
				echo '<option value="'.$delivery_com['wr_code'].'">'.$delivery_com['wr_name'].'</option>';
			}
			?>
		</select>
    </div>
    <div id="excelfile_upload">
        <label for="excelfile" style="display:block;padding-bottom:10px">파일선택</label>
        <input type="file" name="excelfile" id="excelfile" required  style="width:100%">
    </div>

    <div class="win_btn btn_confirm">
        <input type="submit" value="배송비 파일 등록" class="btn_submit btn">
        <button type="button" onclick="window.close();" class="btn_close btn">닫기</button>
    </div>

    </form>

</div>

<?php
include_once(G5_PATH.'/tail.sub.php');