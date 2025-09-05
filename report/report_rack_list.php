<?php
include_once('../common.php');

if ($is_guest)
    alert_close('로그인 후 이용하세요.');

include_once(G5_PATH.'/head.sub.php');
add_stylesheet('<link rel="stylesheet" href="'.G5_ADMIN_URL.'/css/admin.css">', 0);

if(!$wr_warehouse){
	$wr_warehouse = "1000";
}

$sql = "SELECT * FROM g5_rack WHERE gc_warehouse='".$wr_warehouse."' AND gc_use='1' AND seq NOT IN(1)";
$rackRs = sql_query($sql);
?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
	//문자열검색시 대소문자 구분없이 되도록
	$.expr[':'].icontains = function (a, i, m) {
		return $(a).text().toUpperCase().indexOf(m[3].toUpperCase()) >= 0;
	};
</script>
<style>
.select2-container--default .select2-selection--single { height:40px; border:1px solid #d9dee9; background:#f1f3f6 }
.select2-container--default .select2-selection--single .select2-selection__rendered { line-height:38px }
.select2-container--default .select2-selection--single .select2-selection__arrow b { margin-top:4px }

.not_item td { background:red; color:#fff }
.pg a { margin:0 5px }
.tbl_head01 td { background:none }
.move_stock th{ width:100px;background:#ddd }
.move_stock select{ width:30% !important }
.move_stock button { width:30% !important; height:35px; line-height:35px; border:1px solid #2aba8a; background:#2aba8a; color:#fff }
.move_stock2 td {padding:15px }
.down_arrow{position:relative}
.down_arrow::after{content:"↓"; position:absolute; top:-14px;font-size:20px;font-weight:600;left:50%;background:#fff; margin-left:-20px;color:#2aba8a }
.tbl_head01 tbody tr:nth-child(even) {}

.tbl_head01 tbody tr.bg_white{background: #ffffff;}
.tbl_head01 tbody tr.bg_gray{background:#eff3f9;}
@media (max-width:767px){
	.result_list { width:100% !important }
}
</style>
<div class="new_win">
	<h1>랙 보기</h1>

	<form name="searchFrm" id="searchFrm" method="POST" action="./report_rack_list.php">
		<div id="excelfile_upload">
			 <table>
				<tr>
					<td>
						<select name="wr_warehouse" id="wr_warehouse" onchange="fnLoadRack();">
							<option value="1000" <?=($wr_warehouse == "1000")?"SELECTED":""?>>한국창고</option>
							<option value="3000" <?=($wr_warehouse == "3000")?"SELECTED":""?>>미국창고</option>
							<option value="4000" <?=($wr_warehouse == "4000")?"SELECTED":""?>>FBA창고</option>
							<option value="5000" <?=($wr_warehouse == "5000")?"SELECTED":""?>>W-FBA창고</option>
							<option value="6000" <?=($wr_warehouse == "6000")?"SELECTED":""?>>U-FBA창고</option>
							<option value="7000" <?=($wr_warehouse == "7000")?"SELECTED":""?>>한국반품창고</option>
							<option value="8000" <?=($wr_warehouse == "8000")?"SELECTED":""?>>미국반품창고</option>
							<option value="9000" <?=($wr_warehouse == "9000")?"SELECTED":""?>>임시창고</option>
						</select>
						<span id="ajax_wr_rack">
							<select name="wr_rack" id="wr_rack" class="search_sel" onchange="fnLoadList();">
								<option value="">랙 선택</option>
								<?while($rackRow = sql_fetch_array($rackRs)){?>
									<option value="<?=$rackRow['seq']?>"><?=$rackRow['gc_name']?></option>
								<?}?>
							</select>
						</span>
						<button type="button" class="btn01" onclick="window.print();">출력하기</button>
						<!--<button type="button" class="btn01" onclick="fnUplaodStock();">재고 일괄 적용</button>-->
					</td>
					<td>
						
					</td>
				</tr>
			 </table>
		</div>
	</form>
	<?if($member['mb_id'] == "devAdmin"){?>
		<form name="excelFrm" id="excelFrm" method="POST" enctype="multipart/form-data" action="./ajax.stock_excel_upload.php">
				<input type="file" name="excelFile" id="excelFile" value=""/>
				<button type="button" class="btn01" onclick="fnExcelUpload();">엑셀 업로드</button>
		</form>
	<?}?>
	<div id="ajax_report"></div>
	<!--// 재고 업데이트 -->
	<div style="clear:both;margin-bottom:100px"></div>
	<div class="win_btn btn_confirm" style="position:fixed;bottom:0;width:100%;background:#fff;border-top:1px solid #ddd;margin-bottom:0;">
        <button type="button" onclick="window.close();" class="btn_close btn">닫기</button>
    </div>
</div>

<script>
$(document).ready(function(){
	fnLoadList();
	$(".search_sel").select2();
});

// 창고 선택시 랙목록 불러오기
function fnLoadRack(){
	var wr_warehouse = $("#wr_warehouse").val();
	if(wr_warehouse != "9000"){
		var params = "wr_warehouse="+wr_warehouse;
		$.post("./ajax.load_rack.php",params,function(data){
			$("#ajax_wr_rack").html(data);
			$(".search_sel").select2();
			fnLoadList();
		},'html');
	}else{
		var temp = $(`<input type="text" name="sc_subject" value="" class='frm_input' placeholder="상품명"/><button type="button" class="btn01" onclick="fnLoadList();">상품명 검색</button>`);
		$("#ajax_wr_rack").html(temp);
		fnLoadList();
	}
}

// 리스트 조회
function fnLoadList(){
	var params = $("#searchFrm").serialize();
	$.post("./ajax.rack_list.php",params,function(data){
		$("#ajax_report").html(data);
	},'html');
}

function rack_cnt_update(wr_warehouse,wr_rack,wr_product_id){
    let qty = $("#qty" + wr_rack).val(),
        data = {
            wr_warehouse :wr_warehouse,
            wr_rack :wr_rack,
            wr_stock :qty,
            wr_product_id :wr_product_id,
        };

    const obj = HttpJson(g5_url+"/report/ajax.rack_update.php","post",data);

    if(obj['result']){
        alert("실사재고수량을 저장하였습니다.");
        $("#report_title"+wr_warehouse).text(`(${obj['total']}개)`);
    }else{
        alert("저장에 실패하였습니다.");
    }

}

// 실사재고 수량 업데이트
function fnUplaodStock(){
	
	var wr_warehouse = $("#wr_warehouse").val();
	if(wr_warehouse == "9000"){
		var sc_subject = $("input[name='sc_subject']").val();
		if(!isDefined(sc_subject)){
			alert("특정 상품을 검색해서 적용해주세요.");
			return false;
		}
	}else{
		var wr_rack = $("#wr_rack").val();
		if(!isDefined(wr_rack)){
			alert("랙을 선택해주세요.");
			return false;
		}
	}

	$("#report_frm").ajaxSubmit({
		url:"ajax.report_stock.php",
		dataType:"json",
		success:function(data){
			if(isDefined(data.message)){
				alert(data.message);
			}
			if(data.ret_code == true){
				fnLoadList();
			}
		}
	});
}

function fnExcelUpload(){
	$("#excelFrm").ajaxSubmit({
		url:"ajax.stock_excel_upload.php",
		dataType:"json",
		success:function(data){
			console.log(data);
		}
	});
}

function error_msg(){
	alert('미분류 재고가 존재하여 랙별 이동이 불가합니다.\n미분류 재고 부터 이동 후 실행하세요.');
	return false;
}

$(function(){
	
	$('.ms_rack, .ms_rack2').select2();
		
	//모바일 편의성 대응
	$('.qty').bind('focus', function(){
		let ea = $(this).val();
		
		if(ea == 0) {
			$(this).val('');
		}
	});
	
	$('.qty').bind('blur', function(){
		let ea = $(this).val();
		
		if(ea == '') {
			$(this).val('0');
		}
	});
	
	$('.qty').bind('keyup', function(){
		
		let ea = $(this).val();
		let ori = $(this).prev().val();
        
		if(ea == ori) {
			alert('재고 변동이 없습니다.');
			return false;
		}
		
		if(ea < 0){
			alert('0이하는 입력하실 수 없습니다.');
			$(this).val(ori);
			return false;
		}
	});
	
	$('.ms_stock').bind('keyup', function(){
		
		let rack_ea = parseInt($('.ms_rack option:selected').attr('data'));
		let ea = parseInt($(this).val());
		
		if(!rack_ea) {
			alert('재고가 있는 랙을 먼저 선택하세요.');
			$(this).val('');
			$('.ms_rack').focus();
			return false;
		}
		
		if(ea <= 0){
			alert('0이하는 입력하실 수 없습니다.');
			$(this).val('');
			return false;
		}
		
		if(ea > rack_ea) {
			alert(rack_ea+'선택 한 랙의 재고보다 많이 입력하실 수 없습니다.');
			$(this).val(rack_ea);
			return false;
		}
		
	});
	
	$('.ms_warehouse').bind('change', function(){
		$.post('./ajax.rack.php', { wr_id : <?php echo $wr_id?>, warehouse : $(this).val(), mode : 'a' }, function(data){
			$('.ms_rack').html(data);
		})
	});
	
	$('.ms_warehouse2').bind('change', function(){
		$.post('./ajax.rack.php', { wr_id : <?php echo $wr_id?>, warehouse : $(this).val(), mode : '' }, function(data){
			$('.ms_rack2').html(data);
		})
	});
	

});

function add_pop(sku,pname,wr_id) {
	
	window.open("/bbs/write.php?bo_table=product&sku="+sku+"&pname="+pname+"&swr_id="+wr_id, "add_item", "left=50, top=50, width=550, height=650, scrollbars=1");
	
}

function add_item(wr_code, wr_2, wr_subject){
	
	opener.window.document.addform.wr_code.value = wr_code;
	opener.window.document.addform.wr_product_name1.value = wr_2;
	opener.window.document.addform.wr_product_name2.value = wr_subject;
	
	window.close();
}
function selectAll(selectAll)  {
  const checkboxes 
       = document.getElementsByName('chk_seq[]');
  
  checkboxes.forEach((checkbox) => {
	  if(checkbox.disabled == true) {
		  
	  } else {
    checkbox.checked = selectAll.checked;
	  }
  })
}

</script>
<?php
include_once(G5_PATH.'/tail.sub.php');