<?php
include_once('./_common.php');

if ($is_guest) {
    alert_close('로그인 후 이용하세요.');
}

include_once(G5_PATH.'/head.sub.php');
add_stylesheet('<link rel="stylesheet" href="'.G5_ADMIN_URL.'/css/admin.css">', 0);

$it = sql_fetch("select * from g5_write_product where wr_id = '{$wr_id}'");
?>

<style>
    .not_item td { background:red; color:#fff }
    .pg a { margin:0 5px }
    .tbl_head01 td { background:none }
    .move_stock th{ width:100px;background:#ddd }
    .move_stock select{ width:30% !important }
    .move_stock button { width:30% !important; height:35px; line-height:35px; border:1px solid #2aba8a; background:#2aba8a; color:#fff }
    .move_stock2 td {padding:15px }
    .down_arrow{position:relative}
    .down_arrow::after{content:"↓"; position:absolute; top:-14px;font-size:20px;font-weight:600;left:50%;background:#fff; margin-left:-20px;color:#2aba8a }
    .diabled_btn { background:#ddd; cursor:not-allowed }
    .loading { text-align: center; padding: 20px; }
</style>

<div class="new_win">
<h1>창고 별 랙관리</h1>

<form name="addFrm" action="./rack_form_update.php" method="post" autocomplete="off">
	<input type="hidden" name="mode" value="add">
	<div id="excelfile_upload">
	<h2 style="margin-left:0">랙 추가</h2>
	<select name="warehouse">
		<?php foreach (PLATFORM_TYPE as $key => $value) { ?>
		<option value="<?= $key ?>"><?= $value ?>(<?= $key ?>)</option>
		<?php } ?>
	</select>
	<input type="text" name="rack_name" value="" class="frm_input required" required placeholder="랙 이름 , 로 다중 입력">
	<button type="button" class="btn_b01" onclick="add_rack();">랙 추가</button>
	</div>
</form>

<div id="excelfile_upload" class="result_list" style="padding:12px;">
	<div style="clear:both"></div>

	<?php foreach (PLATFORM_TYPE as $key => $value) { ?>
	<div id="warehouse-rack-<?=$key?>" class="tbl_head01 tbl_wrap warehouse-rack" style="width:20%;height:550px;overflow-y:scroll;float:left">
		<div class="loading">로딩 중...</div>
	</div>
	<?php } ?>

	<div style="clear:both"></div>
</div>
</div>

<script>
function error_msg(){
	alert('해당 랙에 재고가 존재하여 삭제가 불가합니다.');
	return false;
}

// 각 창고별 랙 데이터 로드 함수
function loadWarehouseRack(warehouse, warehouseName) {
	return $.ajax({
	url: './ajax.rack_list.php',
	type: 'GET',
	data: {
		warehouse: warehouse
	},
	success: function(response) {
		$('#warehouse-rack-' + warehouse).html(response);
	},
	error: function() {
		$('#warehouse-rack-' + warehouse).html('<div class="error">데이터 로드 중 오류가 발생했습니다.</div>');
	}
	});
}

// 모든 이벤트 핸들러를 등록하는 함수
function attachEventHandlers() {
	$('.modify').off('click').on('click', function(){
	let rack_name = $(this).closest('tr').find('.rack_name').val();
	if(!rack_name) {
		alert('랙이름을 입력하세요');
		$(this).closest('tr').find('.rack_name').focus();
		return false;
	}
	$.post('./rack_form_update.php', { rack_name : rack_name, seq : $(this).attr('data'), mode : 'mod' }, function(data){
		if(data == "y") {
		alert('랙 이름이 수정되었습니다.');
		} else {
		alert('처리 중 오류가 발생했습니다.');
		}
	});
	});

	$('.delete').off('click').on('click', function(){
	if(confirm('정말 해당 랙을 삭제하시겠습니까?\n삭제 후 데이터 복구는 불가능합니다.')) {
		const $row = $(this).closest('tr');
		const warehouse = $(this).data('warehouse');

		$.post('./rack_form_update.php', { seq : $(this).attr('data'), mode : 'del' }, function(result){
		if(result.success) {
			$row.fadeOut(300, function() { $(this).remove(); });
			alert('랙이 삭제되었습니다.');
		} else {
			alert(result.message ?? '처리 중 오류가 발생했습니다.');
		}
		}, 'json');
	}
	});
}

$(function(){
	// 모든 창고의 랙 정보를 병렬로 로드
	const requests = [];

	<?php foreach (PLATFORM_TYPE as $key => $value) { ?>
	requests.push(loadWarehouseRack('<?=$key?>', '<?=$value?>'));
	<?php } ?>

	// 모든 요청이 완료되면 이벤트 핸들러 연결
	Promise.all(requests).then(() => {
	attachEventHandlers();
	})

	// 랙 추가 후 해당 창고 데이터만 리로드
	$('form[name="addFrm"]').on('submit', function(e) {
	e.preventDefault();

	$.ajax({
		url: $(this).attr('action'),
		type: 'POST',
		data: $(this).serialize(),
		success: function(response) {
		if(response == "y") {
			alert('랙이 추가되었습니다.');
			const warehouse = $('select[name="warehouse"]').val();
			loadWarehouseRack(warehouse).then(attachEventHandlers);
			$('input[name="rack_name"]').val('');
		} else {
			alert('처리 중 오류가 발생했습니다.');
		}
		}
	});
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

function selectAll(selectAll) {
	const checkboxes = document.getElementsByName('chk_seq[]');
	checkboxes.forEach((checkbox) => {
	if(checkbox.disabled == true) {
		// 비활성화된 체크박스는 건너뜀
	} else {
		checkbox.checked = selectAll.checked;
	}
	});
}

function chkfrm(f){
	f.act.value = document.pressed;

	if (document.pressed == "발주생성") {
	if (!confirm("선택한 매출자료를 발주생성 하시겠습니까?")) {
		return false;
	}
	}

	if (document.pressed == "한국창고 출고") {
	if (!confirm("선택한 매출자료를 한국창고에서 출고등록 하시겠습니까?")) {
		return false;
	}
	}

	if (document.pressed == "미국창고 출고") {
	if (!confirm("선택한 매출자료를 미국창고에서 출고등록 하시겠습니까?")) {
		return false;
	}
	}
}

//
$(document).on('keyup', '.rack_search', function () {
	const searchText = this.value.toLowerCase();
	const warehouseName = this.getAttribute('data-warehouse');
	const rows = document.querySelectorAll('#warehouse_'+ warehouseName +'_list tr');

	rows.forEach(function(row) {
	const rackName = row.getAttribute('data-rack-name');
	if (rackName && rackName.includes(searchText)) {
		row.style.display = '';
	} else {
		row.style.display = 'none';
	}
	});
});

function add_rack() {
	const rack_name = document.querySelector('[name=rack_name]').value;
	const warehouse = document.querySelector('[name=warehouse]').value;

	if (!rack_name) {
	alert('랙 이름을 입력해주세요.');
	return;
	}

	$.post('./rack_form_update.php', {mode: 'add', rack_name, warehouse}, function (response) {
	console.log(response);
	if (!response.success) {
		alert(response.message ?? '알 수 없는 오류입니다.');
	} else {
		alert(response.message ?? '등록되었습니다');
		location.reload();
	}
	}, 'json');
}

</script>
<?php
include_once(G5_PATH.'/tail.sub.php');
