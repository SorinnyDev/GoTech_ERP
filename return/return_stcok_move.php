<?php
include_once('./_common.php');
require_once('../warehouse/warehouse_list.php');

if ($is_guest) {
    alert_close('로그인 후 이용하세요.');
}

include_once(G5_PATH . '/head.sub.php');
add_stylesheet('<link rel="stylesheet" href="' . G5_ADMIN_URL . '/css/admin.css">', 0);

$row = sql_fetch("select * from g5_return_list where seq = '{$seq}'");
if (!$row) {
    alert_close('정보가 없습니다.');
}

$it = sql_fetch("select * from g5_write_product where wr_id = '{$row['product_id']}'");
if (!$it) {
    alert_close('상품마스터 정보가 없습니다.');
}

$stock_history = sql_fetch("select SUM(wr_stock) as qty from g5_return_stock where return_id = '{$row['seq']}'");

$wr_stock = (int)$row['wr_stock'] - (int)$stock_history['qty'];

$jsFairWarehouses = [];
$jsRefurWarehouses = [];
$jsDisposeWarehouses = [];

foreach ($warehouseConfig as $value => $config) {
    // $value는 창고 코드 (예: 1000, 3000 등)
    // $config는 해당 창고의 상세 정보 배열 (filed, filed_real, ware_name, quality)

    $item = [
        'value' => (int)$value, // JavaScript에서 숫자로 인식되도록 형변환
        'text' => $config['ware_name']
    ];

    switch ($config['quality']) {
        case 'good':
            $jsFairWarehouses[] = $item;
            break;
        case 'refur':
            $jsRefurWarehouses[] = $item;
            break;
        case 'dispose':
            $jsDisposeWarehouses[] = $item;
            break;
    }

    // if ($config['quality'] === 'good') {
    //     $jsFairWarehouses[] = $item;
    // } else { // quality가 'refur' 또는 'dispose'인 경우 모두 '리퍼' 타입으로 묶습니다.
    //     $jsRefurWarehouses[] = $item;
    // }

    // 초기 로딩 시 사용할 기본 창고 ID를 설정합니다.
    // 여기서는 'good' quality 창고 중 첫 번째 것을 기본값으로 설정할 수 있습니다.
    $initial_warehouse_id = null;
    foreach ($warehouseConfig as $value => $config) {
        if ($config['quality'] === 'good') {
            $initial_warehouse_id = $value;
            break; // 첫 번째 'good' quality 창고를 찾으면 루프 종료
        }
    }
    if (!$initial_warehouse_id) { // 'good' quality 창고가 없을 경우 대비
        $initial_warehouse_id = 1000; // 대체 기본값
    }


    // 초기 랙 목록을 가져옵니다.
    $initial_racks = [];
    $sql_search_initial = " where gc_warehouse = '{$initial_warehouse_id}' and gc_use = 1 order by gc_name asc";
    $sql_initial = " select seq, gc_name from g5_rack {$sql_search_initial} ";
    $result_initial = sql_query($sql_initial);

    while ($rack_initial = sql_fetch_array($result_initial)) {
        $initial_racks[] = $rack_initial;
    }
}
?>

<style>
	.memo-textarea {
	min-height: 30px;
    height: 30px; /* 원하는 높이로 직접 지정 */
    resize: none; /* 사용자가 크기를 조절하지 못하도록 함 */
    box-sizing: border-box; /* 패딩과 보더가 width/height에 포함되도록 함 */
	}
	.not_item td {
		background: red;
		color: #fff
	}

	.pg a {
		margin: 0 5px
	}

	.tbl_head01 td {
		background: none
	}

	.move_stock th {
		width: 100px;
		background: #ddd
	}

	.move_stock select {
		width: 30% !important
	}

	.move_stock button {
		width: 30% !important;
		height: 35px;
		line-height: 35px;
		border: 1px solid #2aba8a;
		background: #2aba8a;
		color: #fff
	}

	.move_stock2 td {
		padding: 15px
	}

	.down_arrow {
		position: relative
	}

	.down_arrow::after {
		content: "↓";
		position: absolute;
		top: -14px;
		font-size: 20px;
		font-weight: 600;
		left: 50%;
		background: #fff;
		margin-left: -20px;
		color: #2aba8a
	}

	.diabled_btn {
		background: #ddd;
		cursor: not-allowed
	}

	#excelfile_upload strong {
		display: inline-block;
		width: 70px;
		margin-bottom: 5px
	}
</style>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<div class="new_win">
	<h1>반품상품 재고이관</h1>

	<form name="addFrm" action="" method="post" autocomplete="off">
		<input type="hidden" name="mode" value="add">
		<div id="excelfile_upload">
			<h2 style="margin-left:0">상품정보</h2>
			<strong>주문번호</strong> <?= $row['wr_order_num'] ?><br>
			<strong>SKU</strong> <?= $it['wr_1'] ?><br>
			<strong>상품명</strong> <?= $it['wr_subject'] ?><br>
			<strong>총 반품수량</strong> <?= $row['wr_stock'] ?>개<br>
			<strong>남은수량</strong> <?= $wr_stock ?>개
		</div>
	</form>

	<form name="frm" action="./return_stock_update.php" method="post" onsubmit="return chkfrm(this);">
		<input type="hidden" name="wr_id" value="<?= $it['wr_id'] ?>">
		<input type="hidden" name="return_id" value="<?= $row['seq'] ?>">
		<div id="excelfile_upload" class="result_list" style="padding:12px;">

			<div style="clear:both"></div>
			<div class="tbl_head01 tbl_wrap">
				<table>
					<thead style="position:sticky;top:0;">
						<tr>
							<th>상품상태</th>
							<th>이동 재고수량</th>
							<th>창고선택</th>
							<th>랙선택</th>
							<!-- <th class="memo_area" style="display: none;">메모</th> -->
							<th class="memo_area">메모</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>
								<!-- <label><input type="radio" name="wr_type" value="양품" checked>양품</label>
								<label><input type="radio" name="wr_type" value="리퍼">리퍼</label> -->
								<label for="wr_type_good">
									<input type="radio" id="wr_type_good" name="wr_type" value="양품" checked> 양품
								</label>
								<label for="wr_type_refurb">
									<input type="radio" id="wr_type_refurb" name="wr_type" value="리퍼"> 리퍼
								</label>
								</label>
								<label for="wr_type_dispose">
									<input type="radio" id="wr_type_dispose" name="wr_type" value="폐기"> 폐기
								</label>
							</td>
							<td>
								<input type="text" name="wr_stock" class="rack_name frm_input" value="<?= $wr_stock ?>" style="width:70px;text-align:right">
							</td>
							<td>
								<select name="wr_warehouse" class="wr_warehouse" style="width:110px">
								<?php
								// $warehouseConfig 배열을 사용하여 <option> 태그 동적 생성
								foreach ($warehouseConfig as $value => $config) {
									// quality가 'good'인 경우에만 옵션을 출력합니다.
									if ($config['quality'] === 'good') {
										echo '<option value="' . htmlspecialchars($value) . '"';
										// 초기 선택 값을 설정하려면 아래 주석을 해제하세요.
										// if ($value == $initial_warehouse_id) {
										//     echo ' selected';
										// }
										echo '>' . htmlspecialchars($config['ware_name']) . '</option>';
									}
								}
								?>
									<!-- <option value="1000">한국창고</option>
									<option value="3000">미국창고</option>
									<option value="4000">FBA창고</option>
									<option value="5000">W-FBA창고</option>
									<option value="6000">U-FBA창고</option> -->
								</select>
							</td>
							<td>
								<select name="wr_rack" class="wr_rack" style="width:110px">
									<?php
									$sql_common = " from g5_rack ";
									$sql_search = " where gc_warehouse = '1000' and gc_use = 1 order by gc_name asc";
									$sql = " select * {$sql_common} {$sql_search}  ";
									$result = sql_query($sql);
									for ($a = 0; $rack = sql_fetch_array($result); $a++) {
										?>
										<option value="<?= $rack['seq'] ?>"><?= $rack['gc_name'] ?></option>
									<?php } ?>
								</select>
							</td>
							<!-- <td class="memo_area" style="display: none;"> -->
							<td class="memo_area" >
								<textarea name="memo" class="memo-textarea"></textarea>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			<div style="clear:both"></div>
			<div class="win_btn btn_confirm" style="position:fixed;bottom:0;width:100%;">
				<?php if ($wr_stock > 0) { ?>
					<input type="submit" value="재고이동" class="btn_submit btn">
				<?php } ?>
				<button type="button" onclick="window.close();" class="btn_close btn">닫기</button>
			</div>
		</div>
</div>

</div>
<script>
	function chkfrm(f) {
		if (<?= $wr_stock ?> == 0) {
			alert('남은수량이 없습니다.');
			return false;
		}

		if (f.wr_stock.value <= 0) {
			alert('이동수량은 1이상 입력하세요.');
			return false;
		}
	}

	function ajaxRack(warehouse) {
		$.post('./ajax.rack.php', {
			warehouse: warehouse
		}, function(data) {
			$('select[name="wr_rack"]').html(data);
		})
	}

	$(function() {
		const fair_warehouses_from_php = <?php echo json_encode($jsFairWarehouses); ?>;
		const refur_warehouses_from_php = <?php echo json_encode($jsRefurWarehouses); ?>;
		const dispose_warehouses_from_php = <?php echo json_encode($jsDisposeWarehouses); ?>;

		// const fair_warehouses = [
		//     { value: 1000, text: '한국창고' },
		//     { value: 3000, text: '미국창고' },
		//     { value: 4000, text: 'FBA창고' },
		// 	{ value: 5000, text: 'W-FBA창고' },
		// 	{ value: 4200, text: 'U-FBA창고' },
		// 	];

		// 	const refur_warehouses = [
		//     { value: 7000, text: '한국반품 창고' },
		//     { value: 8000, text: '미국반품 창고' },
		// 	{ value: 9000, text: 'FBA반품 창고' },
		// 	{ value: 9100, text: 'U-FBA반품 창고' },
		// 	{ value: 9200, text: 'W-FBA반품 창고' },

		//     { value: 11000, text: '한국폐기 창고' },
		//     { value: 12000, text: '미국폐기 창고' },
		// 	{ value: 13000, text: 'FBA폐기 창고' },
		// 	{ value: 13100, text: 'U-FBA폐기 창고' },
		// 	{ value: 13200, text: 'W-FBA폐기 창고' },
		// 	];

		$('.wr_rack').select2();

		$(document).on('change', '.wr_warehouse', function() {
			ajaxRack($(this).val());
		})

		$('input:radio[name="wr_type"]').bind('click', function() {
			let type = $(this).val();
			let optionsHtml = '';

			let initialAjaxRackValue = 0; // ajaxRack에 전달할 초기값

			if (type === "양품") {
				optionsHtml = fair_warehouses_from_php.map(warehouse =>
					`<option value="${warehouse.value}">${warehouse.text}</option>`
				).join('');
				// 양품 창고 중 첫 번째 항목의 value를 기본값으로 사용
				if (fair_warehouses_from_php.length > 0) {
					initialAjaxRackValue = fair_warehouses_from_php[0].value;
				}

			} else if (type === "리퍼") {
				optionsHtml = refur_warehouses_from_php.map(warehouse =>
					`<option value="${warehouse.value}">${warehouse.text}</option>`
				).join('');
				// 리퍼/폐기 창고 중 첫 번째 항목의 value를 기본값으로 사용
				if (refur_warehouses_from_php.length > 0) {
					initialAjaxRackValue = refur_warehouses_from_php[0].value;
				}
			} else if (type === "폐기") {
				optionsHtml = dispose_warehouses_from_php.map(warehouse =>
					`<option value="${warehouse.value}">${warehouse.text}</option>`
				).join('');
				// 폐기 창고 중 첫 번째 항목의 value를 기본값으로 사용
				if (dispose_warehouses_from_php.length > 0) {
					initialAjaxRackValue = dispose_warehouses_from_php[0].value;
				}
			}

			$('.wr_warehouse').html(optionsHtml);
			$('.memo_area').show();

			// 동적으로 설정된 첫 번째 창고의 value를 ajaxRack 함수에 전달
			if (initialAjaxRackValue !== 0) {
				ajaxRack(initialAjaxRackValue);
			}

			//   if (type == "양품") {
			//     const optionsHtml = fair_warehouses.map(warehouse =>
			//       `<option value="${warehouse.value}">${warehouse.text}</option>`
			//     ).join('');
			//     $('.wr_warehouse').html(optionsHtml);
			//     $('.memo_area').show();

			//     ajaxRack(1000);

			//   } else if (type == "리퍼") {
			//     const optionsHtml = refur_warehouses.map(warehouse =>
			//       `<option value="${warehouse.value}">${warehouse.text}</option>`
			//     ).join('');
			//     $('.wr_warehouse').html(optionsHtml);
			//     $('.memo_area').show();

			//     ajaxRack(7000);
			//   }
		})
	})
</script>

<?php
include_once(G5_PATH . '/tail.sub.php');
