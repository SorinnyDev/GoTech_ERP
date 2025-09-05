<?
include_once('./_common.php');
# 출력하지 않는 창고 배열
$not_warehouse = array("7000","8000","9000");

# 파라메터 convert
$wr_id_arr = explode("|",$chk_arr_str);

# 담당자
$mbSql = " select mb_id, mb_name from g5_member order by mb_name asc";
$mbRst = sql_query($mbSql);

# 브랜드 목록
$brandData = get_code_list('1'); // 카테고리 코드 조회
?>
<style>
.tbl_frm01 .title {
    background: #444444;
    color: #fff;
    border: 1px solid #60718b;
    font-weight: normal;
    text-align: center;
    padding: 8px 5px;
    font-size: 0.92em;
}
.tbl_frm01 tbody tr td{
	text-align: center;
}
ul{
	list-style:none;
	margin:0;
	padding:0;
}
li{
	margin:0;
	padding:0;
	border:0;
	float:left;
	display:inline;
}
</style>
<fieldset style="padding:10px">
	<h3><?=$row['wr_order_num']?></h3>
	<form name="rack_frm" id="rack_frm" method="POST">
		<?foreach((array)$wr_id_arr as $key => $val){?>
		<input type="hidden" name="wr_id_arr[]" value="<?=$val?>"/>
		<?}?>
		<div class="tbl_frm01 tbl_wrap" style="margin-top:40px;">
			<table id="tbl_frm01">
				<tr>
					<th>담당자</th>
					<td>
						<select name="mb_id" class="frm_input search_sel" style="width:100%">
							<option value="">==담당자 선택==</option>
							<?while($row = @sql_fetch_array($mbRst)){?>
								<option value="<?=$row['mb_id']?>"><?=$row['mb_name']?></option>
							<?}?>
						</select>
					</td>
				</tr>
				<tr>
					<th>브랜드</th>
					<td>
						<select name="wr_23" class="frm_input search_sel" style="width:100%">
							<option value="">==브랜드 선택==</option>
							<?foreach((array)$brandData as $key => $val){?>
								<option value="<?=$val['idx']?>"><?=$val['code_name']?></option>
							<?}?>
						</select>
					</td>
				</tr>
				<tr>
					<th>창고</th>
					<td>
						<select name="wr_warehouse" class="frm_input search_sel" style="width:100%">
							<option value="">==창고 선택==</option>
							<?foreach((array)$storage_arr as $key => $val){?>
								<?if(!in_array($key,$not_warehouse)){?>
									<option value="<?=$key?>"><?=$val['code_nm']?></option>
								<?}?>
							<?}?>
						</select>
					</td>
				</tr>
				<tr>
					<th>지정랙</th>
					<td>
						<select name="wr_rack" class="frm_input search_sel" style="width:100%;">
							<option value="">==지정랙 선택==</option>
						</select>
					</td>
				</tr>
			</table>
		</div>
	</form>
	<div class="win_btn btn_confirm" style="margin-top:20px;text-align:center">
		<button type="button" title="저장" class="btn_b01" onclick="fnBatchRack();" style="width:100px;margin-top:15px">일괄지정</button>
		<button type="button" class="modal_cls" title="닫기" onclick="close_modal();"><i class="fa fa-times" aria-hidden="true"></i><span class="sound_only">닫기</span></button>
	</div>
</fieldset>
<script type="text/javascript">
$(document).ready(function(){
	$('.search_sel').select2(); // 검색 JS

	// 창고 선택 시 해당 창고의 랙목록 불러오기
	$("select[name='wr_warehouse']").bind("change",function(){
		var wr_warehouse = $(this).val();
		var params = "wr_warehouse="+wr_warehouse;
		$.post("./ajax.batch_rack.php",params,function(data){
			$("select[name='wr_rack']").html(data);
		},'html');
	});
});

// 일괄 지정 수정
function fnBatchRack(){
	$("#rack_frm").ajaxSubmit({
		url:"./ajax.batch_rack_proc.php",
		dataType:"json",
		success:function(data){
			if(isDefined(data.message)){
				alert(data.message);
			}

			if(data.ret_code == true){
				document.location.reload();
			}
		}
	});
}
</script>