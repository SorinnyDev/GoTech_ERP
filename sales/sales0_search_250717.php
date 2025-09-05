<?php
include_once('./_common.php');

if ($is_guest)
	alert_close('로그인 후 이용하세요.');

include_once(G5_PATH . '/head.sub.php');
add_stylesheet('<link rel="stylesheet" href="' . G5_ADMIN_URL . '/css/admin.css">', 0);

if (!$wr_19_s)
	$wr_19_s = G5_TIME_YMD;

if (!$wr_19_e)
	$wr_19_e = G5_TIME_YMD;

$goodQualityWarehouses = [];
$refurbishedQualityWarehouses = [];

foreach ($warehouseConfig as $wareCode => $details) {
	switch ($details['quality']) {
		case 'good':
			$goodQualityWarehouses[$wareCode] = $details;
			break;
		case 'refur':
			$refurbishedQualityWarehouses[$wareCode] = $details;
			break;
		default: break;
	}
}
?>
<style>
	.not_item td {
		background: red;
		color: #fff
	}

	th,
	td {
		position: relative;
	}

	.resize-handle {
		position: absolute;
		width: 5px;
		height: 100%;
		top: 0;
		right: -2px;
		cursor: col-resize;
	}

	.tooltip {
		position: relative;
		display: inline-block;
		border-bottom: 1px dotted black;
		/* If you want dots under the hoverable text */
	}

	/* Tooltip text */
	.tooltip .tooltiptext {
		visibility: hidden;
		width: 470px;
		background-color: black;
		color: #fff;
		text-align: center;
		padding: 5px 0;
		border-radius: 6px;

		/* Position the tooltip text - see examples below! */
		position: absolute;
		left: 0;
		top: -40px;
		z-index: 1;
	}

	/* Show the tooltip text when you mouse over the tooltip container */
	.tooltip:hover .tooltiptext {
		visibility: visible;
	}

	.no_ea1,
	.no_ea2,
	.no_ea3,
	.no_ea4,
	.no_ea5,
	.no_ea6,
	.no_ea7 {
		font-weight: 600;
		color: red !important
	}
</style>
<div class="new_win">
	<h1>엑셀자료 가져오기</h1>
	<form name="searchFrm" method="get" autocomplete="off">
		<div id="excelfile_upload">
			<label for="excelfile" style="padding-left:20px">도메인</label>
			<select name="wr_18">
				<option value="">선택하세요</option>
				<?= get_domain_option($_GET['wr_18']) ?>
			</select>
			<label for="excelfile" style="margin-left:20px">재고 유무</label>
			<select name="stock_use">
				<option value="">선택하세요</option>
				<option value="1" <?= get_selected($_GET['stock_use'], 1) ?>>유</option>
				<option value="2" <?= get_selected($_GET['stock_use'], 2) ?>>무</option>
			</select>
			<label for="excelfile" style="margin-left:20px">창고선택</label>
			<select name="stock_warehouse">
				<option value="">창고전체</option>
				<option value="1000" <?= get_selected($_GET['stock_warehouse'], '1000') ?>>한국창고</option>
				<option value="3000" <?= get_selected($_GET['stock_warehouse'], '3000') ?>>미국창고</option>
			</select>
			<label for="excelfile" style="margin-left:20px">US여부</label>
			<select name="us_use">
				<option value="">선택하세요</option>
				<option value="1" <?= get_selected($_GET['us_use'], 1) ?>>US 주문건만</option>
				<option value="2" <?= get_selected($_GET['us_use'], 2) ?>>US 주문건 제외</option>
			</select>
			<label for="excelfile" style="margin-left:50px">기간</label>
			<input type="date" name="wr_19_s" value="<?= urldecode($wr_19_s) ?>" class="frm_input"> ~
			<input type="date" name="wr_19_e" value="<?= urldecode($wr_19_e) ?>" class="frm_input">
			<input type="text" name="stx" value="<?= urldecode($stx) ?>" class="frm_input" placeholder="주문번호/대표코드 검색">
			<button class="btn btn_admin">검색</button>
		</div>
	</form>

	<form name="frm" action="./sales0_search_update.php" method="post" onsubmit="return chkfrm(this)">
		<div id="excelfile_upload" class="result_list" style="overflow-x:scroll;max-height:79vh;padding:0">
			<div class="tbl_head01 tbl_wrap" style="min-width:1700px;margin-bottom:70px">
				<table>
					<thead style="position:sticky;top:0;z-index:99">
						<tr>
							<th style="width:20px"><input type="checkbox" onclick="selectAll(this)"></th>
							<th style="width:100px">
								<div class="resize-handle"></div>순번
							</th>
							<th style="width:110px">
								<div class="resize-handle"></div>업로드일자
							</th>
							<th style="width:100px">
								<div class="resize-handle"></div>도메인명
							</th>
							<th style="width:200px">
								<div class="resize-handle"></div>주문번호
							</th>
							<th style="width:150px">
								<div class="resize-handle"></div>SKU
							</th>
							<th style="width:400px">
								<div class="resize-handle"></div>상품명
							</th>
							<th style="width:90px">한국재고</th>
							<th style="width:75px">미국재고</th>
							<th style="width:70px">FBA재고</th>
							<th style="width:70px">W-FBA재고</th>
							<th style="width:70px">U-FBA재고</th>
							<th style="width:93px">한국반품재고</th>
							<th style="width:93px">미국반품재고</th>
							<th style="width:70px">
								<div class="resize-handle"></div>수량
							</th>
							<th style="width:150px">
								<div class="resize-handle"></div>구매자 이름
							</th>
							<th style="width:250px">
								<div class="resize-handle"></div>주소1/주소2
							</th>
							<th style="width:100px">
								<div class="resize-handle"></div>도시명
							</th>
							<th style="width:100px">
								<div class="resize-handle"></div>주명
							</th>
							<th style="width:100px">
								<div class="resize-handle"></div>나라명
							</th>
							<th style="width:70px">
								<div class="resize-handle"></div>우편번호
							</th>
							<th style="width:150px">
								<div class="resize-handle"></div>전화번호
							</th>
							<th style="width:150px">
								<div class="resize-handle"></div>이메일
							</th>
							<th style="width:70px">
								<div class="resize-handle"></div>박스수
							</th>
							<th style="width:70px">
								<div class="resize-handle"></div>단가
							</th>
							<th style="width:70px">
								<div class="resize-handle"></div>신고가격
							</th>
							<th style="width:70px">
								<div class="resize-handle"></div>통화
							</th>

							<th style="width:100px">
								<div class="resize-handle"></div>배송코드
							</th>
							<th style="width:100px">
								<div class="resize-handle"></div>비고
							</th>
						</tr>
					</thead>
					<tbody>
						<?php
						if ($wr_18)
							$sql_search .= " and a.wr_18 = '{$wr_18}'";

						if ($wr_19_s && $wr_19_e) {
							$sql_search .= " and a.wr_datetime BETWEEN '{$wr_19_s} 00:00:00' AND '{$wr_19_e} 23:59:59' ";
						}

						if ($stock_use == 1) {

							if (!$stock_warehouse) { //창고전체 
								$sql_search .= " and ( b.wr_32 >= a.wr_11 OR b.wr_36 >= a.wr_11 OR b.wr_42 >= a.wr_11 OR b.wr_43 >= a.wr_11 OR b.wr_44 >= a.wr_11 OR b.wr_40 >= a.wr_11 OR b.wr_41 >= a.wr_11 )";
							} else if ($stock_warehouse == '1000') { //한국창고 
								$sql_search .= " and ( COALESCE(b.wr_32, 0) >= COALESCE(a.wr_11, 0)) ";
							} else if ($stock_warehouse == '3000') { //미국창고 
								$sql_search .= " and ( COALESCE(b.wr_36, 0) >= COALESCE(a.wr_11, 0) ) ";
							}
						} else if ($stock_use == 2) {
							if (!$stock_warehouse) { //창고전체 
								$sql_search .= "and  b.wr_32 < a.wr_11 AND b.wr_36 < a.wr_11 AND b.wr_42 < a.wr_11 AND b.wr_43 < a.wr_11 AND b.wr_44 < a.wr_11 AND b.wr_40 < a.wr_11 AND b.wr_41 < a.wr_11 ";
							} else if ($stock_warehouse == '1000') { //한국창고 
								$sql_search .= " and  COALESCE(b.wr_32, 0) < COALESCE(a.wr_11, 0)  ";
							} else if ($stock_warehouse == '3000') { //미국창고 
								$sql_search .= " and COALESCE(b.wr_36, 0) < COALESCE(a.wr_11, 0)  ";
							}
						}
						if ($us_use == 1) {
							$sql_search .= " and a.wr_32 = 'US' ";
						} else if ($us_use == 2) {
							$sql_search .= " and a.wr_32 != 'US' ";
						}

						if ($stx) {
							$sql_search .= " AND ( a.wr_subject LIKE '%$stx%' or b.wr_1  LIKE '%$stx%' or b.wr_5  LIKE '%$stx%' ) ";
						}

						if ($sql_search) {
							$sql = "select a.*, 
						b.wr_subject as p_name, 
						b.wr_1 as p_sku, 
						b.wr_32 as p_wr_32, 
						b.wr_36 as p_wr_36, 
						b.wr_42 as p_wr_42, 
						b.wr_43 as p_wr_43, 
						b.wr_44 as p_wr_44, 
						b.wr_40 as p_wr_40, 
						b.wr_41 as p_wr_41, 
						b.wr_38 as p_wr_38,
						b.wr_33 as p_set,
						b.wr_34 as p_wr_34,
						b.wr_35 as p_wr_35
						from g5_write_sales a 
						LEFT JOIN g5_write_product b ON (a.wr_product_id = b.wr_id) AND b.wr_delYn = 'N'
						where (1) {$sql_search} order by a.wr_subject asc";

							//$sql = "select * from g5_write_sales a where (1) {$sql_search} order by wr_id desc";
							//echo $sql;
							$rst = sql_query($sql);
							for ($i = 0; $row = sql_fetch_array($rst); $i++) {

								$chk = sql_fetch("select * from g5_sales0_list where wr_id = '{$row['wr_id']}'");

								if ($chk) continue; //중복체크

								//$item = sql_fetch("select * from g5_write_product where (wr_1 = '".addslashes($row['wr_16'])."' or wr_27 = '".addslashes($row['wr_16'])."' or wr_28 = '".addslashes($row['wr_16'])."' or wr_29 = '".addslashes($row['wr_16'])."' or wr_30 = '".addslashes($row['wr_16'])."' or wr_31 = '".addslashes($row['wr_16'])."')");

								if ($row['p_set'] == "세트") {

									$item_34 = explode('|@|', $row['p_wr_34']);
									$item_35 = explode('|@|', $row['p_wr_35']);

									for ($a = 0; $a < count($item_34); $a++) {
										$item2 = sql_fetch("select * from g5_write_product where (wr_1 = '" . addslashes($item_34[$a]) . "' or wr_27 = '" . addslashes($item_34[$a]) . "' or wr_28 = '" . addslashes($item_34[$a]) . "' or wr_29 = '" . addslashes($item_34[$a]) . "' or wr_30 = '" . addslashes($item_34[$a]) . "' or wr_31 = '" . addslashes($item_34[$a]) . "') AND wr_delYn='N'");

										$disabled = "";
										// var_dump($item);
										if (!$item2) {
											$bg = 'class="not_item"';
											$disabled = "disabled";
										}

										if ($row['wr_16'] == '') {
											$disabled = "disabled";
											$row['wr_16'] = '등록하기';
										}

										//$ea_chk = $item2['wr_32'] < ($item_35[$a]*$row['wr_11']) ? 'class="no_ea1"' : "";
										//$ea_chk2 = $item2['wr_36'] < ($item_35[$a]*$row['wr_11']) ? 'class="no_ea2"' : "";
										//$ea_chk3 = $item2['wr_42'] < ($item_35[$a]*$row['wr_11']) ? 'class="no_ea3"' : "";
										//$ea_chk4 = $item2['wr_43'] < ($item_35[$a]*$row['wr_11']) ? 'class="no_ea4"' : "";
										//$ea_chk5 = $item2['wr_44'] < ($item_35[$a]*$row['wr_11']) ? 'class="no_ea5"' : "";
										//$ea_chk6 = $item2['wr_40'] < ($item_35[$a]*$row['wr_11']) ? 'class="no_ea6"' : "";
										//$ea_chk7 = $item2['wr_41'] < ($item_35[$a]*$row['wr_11']) ? 'class="no_ea7"' : "";
										//$new_icon = $item2['wr_38']=="Y" ? "<img src='".G5_IMG_URL."/new.png' alt='없음' width='25' height='25'/>" : "";
						?>
										<tr <?= $empty_pdt ?>>
											<td><input type="checkbox" name="chk_wr_id[]" value="<?= $row['wr_id'] ?>|<?= $item2['wr_id'] ?>" <?= $disabled ?> class="chkbox set_<?= $row['wr_id'] ?>"></td>
											<td style="color:blue">SET<br>(<?= $row['wr_subject'] ?>)</td>
											<td><?= date('y.m.d', strtotime($row['wr_datetime'])) ?></td>
											<td><?= $row['wr_18'] ?></td>
											<td><?= $row['wr_subject'] ?></td>
											<td>
												<?= $item_34[$a] ?><br><span style="color:blue">(
													<?php if ($disabled) { ?>
														<i class="fa fa-times-circle" aria-hidden="true" style="color:red" title="제품연동 안됨"></i>
														<a href="#none" onclick="add_pop('<?= urlencode($item_34[$a]) ?>', '', '<?= $row['wr_id'] ?>')">
															<?= $row['wr_16']; ?>
														</a>
														<?php } else {
														echo $row['wr_16'];
													} ?>)</span></td>

											<td><?= $item2['wr_subject'] ?></td>
											<td style="text-align:right;" <?= $ea_chk ?>><?= $item2['wr_32'] ?></td>
											<td style="text-align:right;" <?= $ea_chk2 ?>><?= $new_icon . $item2['wr_36'] ?></td>
											<td style="text-align:right;" <?= $ea_chk3 ?>><?= $item2['wr_42'] ?></td>
											<td style="text-align:right;" <?= $ea_chk4 ?>><?= $item2['wr_43'] ?></td>
											<td style="text-align:right;" <?= $ea_chk5 ?>><?= $item2['wr_44'] ?></td>
											<td style="text-align:right;" <?= $ea_chk6 ?>><?= $item2['wr_40'] ?></td>
											<td style="text-align:right;" <?= $ea_chk7 ?>><?= $item2['wr_41'] ?></td>

											<td><?= ($item_35[$a] ? $item_35[$a] * $row['wr_11'] : $row['wr_11']) ?></td>
											<td><?= $row['wr_2'] ?></td>
											<td><?= $row['wr_3'] ?> <?= $row['wr_4'] ?></td>

											<td><?= $row['wr_5'] ?></td>
											<td><?= $row['wr_6'] ?></td>
											<td><?= $row['wr_7'] ?></td>
											<td><?= $row['wr_8'] ?></td>
											<td><?= $row['wr_9'] ?></td>
											<td><?= $row['wr_10'] ?></td>

											<td><?= ($item_35[$a] ? $item_35[$a] * $row['wr_11'] : $row['wr_11']) ?></td>
											<td><?= $row['wr_13'] ?></td>
											<td><?= $row['wr_14'] ?></td>
											<td><?= $row['wr_15'] ?></td>

											<td><?= $row['wr_20'] ?></td>
											<td><?= $row['wr_21'] ?></td>
										</tr>

									<?php
									}
								} else {
									$bg = "";
									$disabled = "";
									// var_dump($item);
									if (!$row['p_sku']) {
										$bg = 'class="not_item"';
										$disabled = "disabled";
									}

									if ($row['wr_16'] == '') {
										$disabled = "disabled";
										$row['wr_16'] = '등록하기';
									}

									$ea_chk = $row['p_wr_32'] < $row['wr_11'] ? 'class="no_ea1"' : "";
									$ea_chk2 = $row['p_wr_36'] < $row['wr_11'] ? 'class="no_ea2"' : "";
									$ea_chk3 = $row['p_wr_42'] < $row['wr_11'] ? 'class="no_ea3"' : "";
									$ea_chk4 = $row['p_wr_43'] < $row['wr_11'] ? 'class="no_ea4"' : "";
									$ea_chk5 = $row['p_wr_44'] < $row['wr_11'] ? 'class="no_ea5"' : "";
									$ea_chk6 = $row['p_wr_40'] < $row['wr_11'] ? 'class="no_ea6"' : "";
									$ea_chk7 = $row['p_wr_41'] < $row['wr_11'] ? 'class="no_ea7"' : "";
									$new_icon = $row['p_wr_38'] == "Y" ? "<img src='" . G5_IMG_URL . "/new.png' alt='없음' width='25' height='25'/>" : "";

									?>
									<tr>
										<td><input type="checkbox" name="chk_wr_id[]" class="chkbox" value="<?= $row['wr_id'] ?>" <?= $disabled ?>></td>
										<td><?= ($i + 1) ?></td>
										<td><?= date('y.m.d', strtotime($row['wr_datetime'])) ?></td>
										<td><?= $row['wr_18'] ?></td>
										<td><?= $row['wr_subject'] ?></td>
										<td>
											<?php if ($disabled) { ?>
												<i class="fa fa-times-circle" aria-hidden="true" style="color:red" title="제품연동 안됨"></i>
												<a href="#none" onclick="add_pop('<?= urlencode($row['wr_16']) ?>', '<?= urlencode($row['wr_17']) ?>', '<?= $row['wr_id'] ?>')">
													<?= $row['wr_16']; ?>
												</a>
											<?php } else {
												echo $row['wr_16'];
											} ?>
										</td>
										<td><?= $row['wr_17'] ?></td>
										<td style="text-align:right;" <?= $ea_chk ?>><?= $row['p_wr_32'] ?></td>
										<td style="text-align:right;" <?= $ea_chk2 ?>><?= $new_icon . $row['p_wr_36'] ?></td>
										<td style="text-align:right;" <?= $ea_chk3 ?>><?= $row['p_wr_42'] ?></td>
										<td style="text-align:right;" <?= $ea_chk4 ?>><?= $row['p_wr_43'] ?></td>
										<td style="text-align:right;" <?= $ea_chk5 ?>><?= $row['p_wr_44'] ?></td>
										<td style="text-align:right;" <?= $ea_chk6 ?>><?= $row['p_wr_40'] ?></td>
										<td style="text-align:right;" <?= $ea_chk7 ?>><?= $row['p_wr_41'] ?></td>
										<td><?= $row['wr_11'] ?></td>
										<td><?= $row['wr_2'] ?></td>
										<td><?= $row['wr_3'] ?> <?= $row['wr_4'] ?></td>
										<td><?= $row['wr_5'] ?></td>
										<td><?= $row['wr_6'] ?></td>
										<td><?= $row['wr_7'] ?></td>
										<td><?= $row['wr_8'] ?></td>
										<td><?= $row['wr_9'] ?></td>
										<td><?= $row['wr_10'] ?></td>

										<td><?= $row['wr_12'] ?></td>
										<td><?= $row['wr_13'] ?></td>
										<td><?= $row['wr_14'] ?></td>
										<td><?= $row['wr_15'] ?></td>
										<td><?= $row['wr_20'] ?></td>
										<td><?= $row['wr_21'] ?></td>

									</tr>
						<?php }
							}
						} else {
							echo '<tr><td colspan="30" style="font-size:15px;color:red">내역이 없습니다.</td></tr>';
						} ?>
					</tbody>
				</table>
			</div>
		</div>

		<div class="win_btn btn_confirm" style="position:fixed;bottom:0;width:100%;background:#fff;border-top:1px solid #ddd;margin-bottom:0">
			<label for="excelfile">출고창고</label>
			<select name="wr_warehouse" id="wr_warehouse" required>
				<option value="">선택하세요</option>
				<option value="1000" <?= get_selected($_GET['warehouse'], '1000') ?>>한국창고</option>
				<option value="3000" <?= get_selected($_GET['warehouse'], '3000') ?>>미국창고</option>
				<option value="4000" <?= get_selected($_GET['warehouse'], '4000') ?>>FBA창고</option>
				<option value="5000" <?= get_selected($_GET['warehouse'], '5000') ?>>W-FBA창고</option>
				<option value="6000" <?= get_selected($_GET['warehouse'], '6000') ?>>U-FBA창고</option>
			</select>
			매출적용 일자 <input type="date" name="wr_date" value="<?= G5_TIME_YMD ?>" required>
			발주적용 일자 <input type="date" name="wr_date2" value="<?= G5_TIME_YMD ?>" required>
			<button type="submit" class="btn_submit btn tooltip" style="display:none">출고 및 발주생성<span class="tooltiptext">재고가 있는 건은 [출고등록], 재고가없는 건[발주등록]으로 바로등록 됩니다.</span></button>
			<button type="button" onclick="window.close();" class="btn_close btn">닫기</button>
		</div>
	</form>


</div>
<script>
	function chkfrm(f) {
		$('.btn_submit').html('<img style="width:15px" src="/mobile/shop/img/loading.gif">');
		$('.btn_submit').attr('disabled', true);
	}

	function add_pop(sku, pname, wr_id) {
		window.open("/bbs/write.php?bo_table=product&sku=" + sku + "&pname=" + pname + "&swr_id=" + wr_id, "add_item", "left=50, top=50, width=550, height=650, scrollbars=1");
	}

	$(function() {
		$('#wr_warehouse').bind('change', function() {
			if ($(this).val() == "") {
				$('.btn_submit').hide();
			} else {
				$('.btn_submit').show();
			}
		})

		$('input[name="chk_wr_id[]"]').bind('click', function() {
			let stat = $(this).attr('checked');
			let v = $(this).val();
			let vc = v.split('|');

			if (stat == "checked") {
				$('.set_' + vc[0]).prop('checked', true);
			} else {
				$('.set_' + vc[0]).prop('checked', false);
			}
		})

		$('.chkbox').bind('click', function() {
			let stat = $(this).is(':checked');

			if (stat) {
				$(this).closest('tr').find('td').addClass('selected_line');
			} else {
				$(this).closest('tr').find('td').removeClass('selected_line');
			}
		})

	})

	function selectAll(selectAll) {
		const checkboxes = document.getElementsByName('chk_wr_id[]');

		checkboxes.forEach((checkbox) => {
			if (checkbox.disabled == true) {

			} else {
				checkbox.checked = selectAll.checked;
			}
		})

		$('.chkbox').each(function() {
			let stat = $(this).is(':checked');

			if (stat) {
				$(this).closest('tr').find('td').addClass('selected_line');
			} else {
				$(this).closest('tr').find('td').removeClass('selected_line');
			}
		})
	}
</script>
<?php
include_once(G5_PATH . '/tail.sub.php');
