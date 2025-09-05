<?php
include_once('./_common.php');

if ($is_guest)
    alert_close('로그인 후 이용하세요.');

include_once(G5_PATH.'/head.sub.php');
add_stylesheet('<link rel="stylesheet" href="'.G5_ADMIN_URL.'/css/admin.css">', 0);
?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<style>
#bo_w { max-width:100%; width:700px}
.set_sku_box input { margin-bottom:5px }
.select2-container--default .select2-selection--single { height:40px; border:1px solid #d9dee9; background:#f1f3f6 }
.select2-container--default .select2-selection--single .select2-selection__rendered { line-height:38px }
.box li { float:left }
</style>
<div class="new_win">
    <h1>브랜드 담당자 일괄변경</h1>

    <div class="local_desc01 local_desc">
        <p>
            현재 제품에 설정된 브랜드를 찾아 담당자를 일괄 변경하는 기능입니다.<br>
			<strong>변경 후 복구는 불가능하니 신중하게 실행하시기 바랍니다.</strong>
        </p>

    </div>

    <form name="fitemexcel" method="post" action="./pop_brand_update.php" enctype="MULTIPART/FORM-DATA" autocomplete="off" onsubmit="return chkfrm(this)">
	<div id="excelfile_upload">
	<ul class="box">
		<li class="bo_w_tit half_box">
		<div class="wli_tit">대상 브랜드</div>
		<div class="wli_cnt">
			<label for="wr_23" class="sound_only">브랜드<strong>필수</strong></label>
		
			<select name="wr_23" id="wr_23" class="frm_input search_sel">
				<?php 
				$arr = get_code_list('1'); // 카테고리 코드 조회
				foreach($arr as $key => $value){
					$selected = $write['wr_23']==$value['idx'] ? "selected" : "";
					echo "<option value=\"{$value['idx']}\" {$selected} >{$value['code_name']}</option>"; 
				}
				?>
			</select>
		</div>
		</li>
		<li class="bo_w_tit half_box">
		<div class="wli_tit">변경 할 담당자</div>
		<div class="wli_cnt">
			<label for="wmb_id" class="sound_only">중량무게1<strong>필수</strong></label>
		
			<select name="wmb_id" id="wmb_id" class="frm_input search_sel">
				<option value="">선택하세요</option>
				<?php 
				$mbSql = " select mb_id, mb_name from g5_member order by mb_name asc";
				$mbRst = sql_query($mbSql);
				for($i=0; $mb=sql_fetch_array($mbRst); $i++) {
					$checked = "";
					
					
				?>
				<option value="<?php echo $mb['mb_id']?>" <?php echo get_selected($mb['mb_id'], $write['mb_id'])?> <?php echo $checked?>><?php echo $mb['mb_name']?>(<?php echo $mb['mb_id']?>)</option>
				<?php }?>
			</select>
		</div>
	</li>
	</ul>
	<div style="clear:both"></div>
	</div>

    <div class="win_btn btn_confirm">
        <input type="submit" value="일괄변경" class="btn_submit btn">
        <button type="button" onclick="window.close();" class="btn_close btn">닫기</button>
    </div>

    </form>

</div>
<script>
function chkfrm(f){
	if(confirm('정말 변경하시겠습니까?')){
		return true;
	} else {
		return false;
	}
}
$('.search_sel').select2();
</script>
<?php
include_once(G5_PATH.'/tail.sub.php');