<?php
include_once('./_common.php');
include_once(G5_THEME_PATH . '/head.php');

$kor_data = sql_fetch_all("SELECT * FROM g5_rack WHERE gc_use = 1 AND gc_warehouse=1000");
$kor_lack = [];
foreach ($kor_data as $item) {
	if (isset($item['seq'])) {
		$kor_lack[$item['seq']] = $item;
	}
}

$kor_gc_names_array = [];
$kor_seq_array = [];

foreach ($kor_lack as $item) {
	if (isset($item['gc_name'])) { // 'gc_name' 키가 존재하는지 확인
		$kor_gc_names_array[] = $item['gc_name'];
	}
	if (isset($item['seq'])) { // 'gc_name' 키가 존재하는지 확인
		$kor_seq_array[] = $item['seq'];
	}
}

// 추출된 배열을 JSON 형식으로 인코딩
$kor_json_valid_options = json_encode($kor_gc_names_array);
$kor_seq_options = json_encode($kor_seq_array);

$usa_data = sql_fetch_all("SELECT * FROM g5_rack WHERE gc_use = 1 AND gc_warehouse=3000");
$usa_lack = [];

foreach ($usa_data as $item) {
	if (isset($item['seq'])) {
		$usa_lack[$item['seq']] = $item;
	}
}

$usa_gc_names_array = [];
$usa_seq_array = [];

foreach ($usa_lack as $item) {
	if (isset($item['gc_name'])) { // 'gc_name' 키가 존재하는지 확인
		$usa_gc_names_array[] = $item['gc_name'];
	}
	if (isset($item['seq'])) { // 'gc_name' 키가 존재하는지 확인
		$usa_seq_array[] = $item['seq'];
	}
}

$usa_json_valid_options = json_encode($usa_gc_names_array);
$usa_seq_options = json_encode($usa_seq_array);
?>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<link rel="stylesheet" href="/theme/parsley/mobile/skin/board/sales/style.css?ver=2303229">
<style>
	.cnt_left {
		padding: 5px 10px;
		border-right: 1px solid #ddd;
		word-break: normal;
		text-overflow: ellipsis;
		overflow: hidden;
		white-space: nowrap;
	}

	.list_03 li {
		padding: 0
	}

	.list_03 li .cnt_left {
		line-height: 43px
	}

	.modify {
		cursor: pointer
	}

	.tbl_frm01 th {
		background: #6f809a;
		color: #fff;
		border: 1px solid #60718b;
		font-weight: normal;
		text-align: center;
		padding: 8px 5px;
		font-size: 0.92em
	}

	.tbl_frm01 td {
		border-bottom: 1px solid #ddd;
	}

	.tbl_frm01 td input {
		border: 1px solid #ddd;
		padding: 3px;
		width: 100%
	}

	.tbl_frm01 input.readonly {
		background: #f2f2f2
	}

	.select2-container--default .select2-selection--single {
		height: 40px;
		border: 1px solid #d9dee9;
		background: #f1f3f6
	}

	.select2-container--default .select2-selection--single .select2-selection__rendered {
		line-height: 38px
	}

	.select2-container--default .select2-selection--single .select2-selection__arrow b {
		margin-top: 4px
	}


	.cnt_left {
		overflow: unset;
		white-space: unset;
	}

	.select2-container {
		width: 80px !important;
	}

	.etc_label {
		display: block;
		border: 1px solid #5b9f28;
		background: #5b9f28;
		color: #fff;
		font-size: 11px;
		padding: 0px 3px;
		line-height: 15px;
		border-radius: 5px;
		width: 52px;
		margin-top: 3px
	}

	.tr_yellow td {
		background: yellow;
	}
</style>
<div id="bo_list">
	<div class="bo_list_innr">
		<h2 class="board_tit">임시창고 재고 관리</h2>
		<form name="fboardlist" id="fboardlist" action="./temp_stock_list_update.php" onsubmit="return chkfrm(this);" method="post">
			<input type="hidden" name="act" value="">

			<?php if ($is_category) { ?>
				<nav id="bo_cate">
					<h2><?= ($board['bo_mobile_subject'] ? $board['bo_mobile_subject'] : $board['bo_subject']) ?> 카테고리</h2>
					<ul id="bo_cate_ul">
						<?= $category_option ?>
					</ul>
				</nav>
			<?php } ?>

			<div id="bo_li_top_op">
				<div class="bo_list_total">
					<span>&nbsp;</span>

				</div>
				<ul class="<?= isset($view) ? 'view_is_list btn_top' : 'btn_top2'; ?>">
					<?php if ($rss_href) { ?><li><a href="<?= $rss_href ?>" class="btn_b02">RSS</a></li><?php } ?>

					<li> <select name="wmb_id" id="wmb_id" class="frm_input search_sel" onchange="location.href='?mb_id='+this.value" ;>
							<option value="">담당자</option>
							<?php
							$mbSql = " select mb_id, mb_name from g5_member order by mb_name asc";
							$mbRst = sql_query($mbSql);
							for ($i = 0; $mb = sql_fetch_array($mbRst); $i++) {
							?>
								<option value="<?= $mb['mb_id'] ?>" <?= get_selected($mb['mb_id'], $mb_id) ?>><?= $mb['mb_name'] ?>(<?= $mb['mb_id'] ?>)</option>
							<?php } ?>
						</select></li>
					<li><button type="submit" class="btn_b01" value="일괄재고이동" onclick="document.pressed=this.value">일괄재고이동</button></li>
					<li><button type="submit" class="btn_b01" value="일괄재고삭제" onclick="document.pressed=this.value">일괄재고삭제</button></li>
					<li><button type="button" class="btn_b02 btn_bo_sch"><i class="fa fa-search" aria-hidden="true"></i> 검색</button></li>

				</ul>
			</div>
			<div class="tbl_wrap tbl_head02" style="overflow-x:scroll;height:700px">
				<table>
					<thead>
						<tr style="position:sticky;z-index:99;border-top:2px solid #000">
							<th style="width:40px"><input type="checkbox" onclick="selectAll(this)"></th>
							<th style="width:100px">도메인</th>
							<th style="width:120px">주문번호</th>
							<th style="width:150px">SKU</th>
							<th style="width:250px">상품명</th>
							<th style="width:50px">재고</th>
							<th style="width:120px">이동창고</th>
							<th style="width:60px">이동수량</th>
							<th style="width:80px">랙번호</th>
							<th style="width:80px">유통기한</th>
							<th style="width:110px">지정랙</th>
							<th style="width:120px">관리</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$sql_search = "";

						if ($stx) {
							$sql_search .= " and (c.wr_subject LIKE '%{$stx}%' or c.wr_1 LIKE '%{$stx}%' or b.wr_order_num LIKE '%{$stx}%')  ";
						}

						if ($wr_18) {
							$sql_search .= " and b.wr_domain = '$wr_18' ";
						}
						if ($mb_id) $sql_search .= " and c.mb_id = '{$mb_id}' ";
						if ($release == 3) $sql_search .= " and b.wr_etc_use = '1' ";

						$sql = "SELECT a.*, b.wr_domain, b.seq, b.wr_order_num, b.wr_ea, b.wr_etc_use, b.expired_date,
											c.wr_subject, c.wr_1, c.wr_37, c.wr_id, c.mb_id, c.wr_rack, c.wr_warehouse
										FROM g5_temp_warehouse a
										LEFT JOIN g5_sales2_list b ON(a.sales2_id = b.seq)
										LEFT JOIN g5_write_product c ON(c.wr_id = a.wr_product_id)
										WHERE a.wr_stock > 0 AND a.sales2_id IS NOT NULL {$sql_search}
										ORDER BY a.tw_seq DESC";

						$rst = sql_query($sql);
						for ($i = 0; $row = sql_fetch_array($rst); $i++) {
							$mb = get_member($row['mb_id'], 'mb_name');
							$sel_wr_rack = $row['wr_rack'];

							$tr_bg = "";
							if (!$row['wr_domain'] && $row['wr_report_chk ']) {
								$tr_bg = "tr_yellow";
							}
						?>
							<tr class="modify <?= $tr_bg ?>" data="<?= $row['seq'] ?>">
								<td class="cnt_left" style="width:50px;text-align:center">
									<input type="hidden" name="chk_seq[<?= $i ?>]" value="<?= $row['tw_seq'] ?>">
									<input type="hidden" name="wr_id[<?= $i ?>]" value="<?= $row['wr_id'] ?>">
									<input type="hidden" name="sales2_id[<?= $i ?>]" value="<?= $row['sales2_id'] ?>">
									<input type="checkbox" name="chk[]" value="<?= $i ?>" <?= $chk_disabled ?> class="chkbox">
								</td>
								<td class="cnt_left" style="width:100px">
									<? if ($row['wr_domain']) echo $row['wr_domain'];
									else if ($row['wr_report_chk'] == "Y") { ?>
										<span style="color:blue;font-weight:bold;">실사재고조사</span>
									<? } else { ?>
										<span style="color:red;font-weight:bold;">입고 정보 삭제</span>
									<? } ?>
									<span style="display:block;font-size:12px;color:#757373" title="입고일시"><?= date('y.m.d H:i', strtotime($row['wr_datetime'])) ?></span>
								</td>
								<td class="cnt_left" style="width:120px"><?= $row['wr_order_num'] ?><?= $etc_order ?></td>
								<td class="cnt_left" style="width:150px"><?= $row['wr_1'] ?>
									<span style="display:block;font-size:12px;color:#757373"><?= $mb['mb_name'] ?></span>
								</td>
								<td class="cnt_left" style="width:250px;" title="<?= $row['wr_subject'] ?>"><?= $row['wr_subject'] ?>
									<a href="/bbs/write.php?bo_table=product&w=u&wr_id=<?= $row['wr_id'] ?>" target="_blank"><i class="fa fa-search" aria-hidden="true" style="font-size:10px"></i></a>
								</td>
								<td class="cnt_left" style="width:50px;text-align:right"><?= $row['wr_stock'] ?></td>

								<td class="cnt_left" style="width:120px;text-align:center">
									<select name="warehouse[<?= $i ?>]" class="frm_input warehouse">
										<option value="1000" selected>한국창고(1000)</option>
										<option value="3000">미국창고(3000)</option>
									</select>
								</td>
								<td class="cnt_left" style="width:60px;text-align:right">
									<input type="text" name="wr_37[<?= $i ?>]" class="wr_37 frm_input " style="width:100%;text-align:right" value="<?= $row['wr_stock'] ?>">
								</td>
								<td class="cnt_left" style="width:80px;text-align:center;">
									<input type="hidden" style="width:100%;text-align:center;" name="wr_rack[<?= $i ?>]"
										value="<?= $sel_wr_rack ? $sel_wr_rack : "" ?>" class="frm_input rack_num">
									<input type="text" style="width:100%;text-align:center;" name="rack_name[<?= $i ?>]"
										value="<?= $sel_wr_rack ? $kor_lack[$sel_wr_rack]['gc_name'] : "없음" ?>" class="wr_rack frm_input search_sel">
								</td>

								<td class="cnt_left" style="width:80px;text-align:center;">
									<input type="date" style="width:100%;text-align:center;" name="expired[<?= $i ?>]" class="expired" value="<?= $row['expired_date'] ?>" max="9999-12-31">
								</td>

								<td class="cnt_left" style="width:100px;text-align:center">
									<?php if ($row['wr_rack']) { ?>
										[<?= PLATFORM_TYPE[$row['wr_warehouse']] ?>] <?= get_rack_name($row['wr_rack']) ?>
									<?php } else {
										echo '<span style="color:#a1a1a1">없음</span>';
									} ?>
								</td>
								<td class="cnt_left" style="width:120px;text-align:center">
									<button type="button" class="btn btn_b01 save_btn" data="<?= $row['tw_seq'] ?>"
										data2="<?= $row['wr_id'] ?>" data3="<?= $row['sales2_id'] ?>" data4="<?= $order_class ?>">재고이동</button>
								</td>
							</tr>
						<?php } ?>
						<?php if ($i == 0) {
							echo '<tr class="empty_table"><td colspan="12">내역이 없습니다.</td></tr>';
						} ?>
					</tbody>
				</table>
			</div>
		</form>
	</div>
</div>

<div class="bo_sch_wrap">
	<fieldset class="bo_sch">
		<h3>검색</h3>
		<form name="fsearch" method="get">
			<select name="wr_18" style="margin-bottom:15px">
				<option value="">도메인 선택</option>
				<?= get_domain_option($_GET['wr_18']) ?>

			</select>
			<div style="border:1px solid #ddd; margin-bottom:20px; padding:10px">
				<label><input type="radio" name="release" value="0" <?= get_checked($release, 0) ?>> 전체</label>
				<!--<label><input type="radio" name="release" value="1" <?= get_checked($release, 1) ?>> 미출고건</label>
			<label><input type="radio" name="release" value="2" <?= get_checked($release, 2) ?>> 출고건</label>-->
				<label><input type="radio" name="release" value="3" <?= get_checked($release, 3) ?>> 기타발주건만</label>
			</div>

			<div style="border:1px solid #ddd; margin-bottom:20px; padding:10px">
				<input type="text" name="stx" value="<?= urldecode($_GET['stx']) ?>" class="frm_input" style="width:100%;" placeholder="SKU/상품명/주문번호로 검색">
			</div>

			<button type="submit" value="검색" class="btn_b01" style="width:49%;margin-top:15px"><i class="fa fa-search" aria-hidden="true"></i> 검색하기</button>
			<button type="button" value="초기화" class="btn_b02" style="width:49%;margin-top:15px;" onclick="location.href='<?= $_SERVER['PHP_SELF'] ?>'"><i class="fa fa-repeat" aria-hidden="true"></i> 검색초기화</button>
			<button type="button" class="bo_sch_cls" title="닫기"><i class="fa fa-times" aria-hidden="true"></i><span class="sound_only">닫기</span></button>
		</form>
	</fieldset>
	<div class="bo_sch_bg"></div>
</div>

<script>
	jQuery(function($) {
		// 게시판 검색
		$(".btn_bo_sch").on("click", function() {
			$(".bo_sch_wrap").toggle();
		})
		$('.bo_sch_bg, .bo_sch_cls').click(function() {
			$('.bo_sch_wrap').hide();
		});
	});
</script>
<script>
	function chkfrm(f) {
		let missingDateRows = [];

		$('input[name="chk[]"]:checked').each(function() {
			const $checkbox = $(this);
			const $row = $checkbox.closest('tr');

			const $expiredInput = $row.find('input.expired[type="date"]');

			if ($expiredInput.length && !$expiredInput.val()) {
				missingDateRows.push($row.index());
			}
		});

		const formattedRows = missingDateRows.map(rowIndex => {
			return `${rowIndex + 1}행`;
		});
		const resultString = formattedRows.join(', ');

		let finalMessage = "";

		if (resultString === "") {
			finalMessage = "선택한 재고를 일괄재고이동 하시겠습니까?";
		} else {
			finalMessage = resultString + "의 유통기한이 지정되지 않았습니다. 그래도 일괄재고이동하시겠습니까?";
		}

		f.act.value = document.pressed;

		if (document.pressed == "일괄재고이동") {
			if (!confirm(finalMessage)) {
				return false;
			}
		} else if (document.pressed == "일괄재고삭제") {
			if (!confirm("한번 삭제된 데이터는 복구가 되지 않습니다.\r\n선택한 재고를 일괄 삭제 하시겠습니까?")) {
				return false;
			}
		}
	}

	function selectAll(selectAll) {
		const checkboxes = document.getElementsByName('chk[]');

		checkboxes.forEach((checkbox) => {
			if (checkbox.disabled == true) {

			} else {
				checkbox.checked = selectAll.checked;
			}
		})

		$('.chkbox').each(function() {
			let stat = $(this).is(':checked');

			if (stat) {
				$(this).closest('tr').find('td').css({
					'background': '#f2f2f2'
				});
			} else {
				$(this).closest('tr').find('td').css({
					'background': '#fff'
				});
			}
		})
	}

	$(function() {
		$('.chkbox').bind('click', function() {
			let stat = $(this).is(':checked');

			if (stat == true) {
				$(this).closest('tr').find('td').css({
					'background': '#f2f2f2'
				});
			} else {
				$(this).closest('tr').find('td').css({
					'background': '#fff'
				});
			}
		})
	})

	function pop_excel() {
		window.open("./sales0_search.php", "sales0_search", "left=50, top=50, width=1100, height=650, scrollbars=1");
	}

	$(function() {
		let validOptions;
		let seqOptions;

		function updateOptionsBasedOnWarehouse(changedWarehouseValue) {
			if (changedWarehouseValue === "1000") {
				validOptions = <?= $kor_json_valid_options; ?>;
				seqOptions = <?= $kor_seq_options; ?>;
			} else {
				validOptions = <?= $usa_json_valid_options; ?>;
				seqOptions = <?= $usa_seq_options; ?>;
			}
		}

		updateOptionsBasedOnWarehouse($('.warehouse').val());

		$('.warehouse').on('change', function() {
			const initialValue = $(this).val();
			updateOptionsBasedOnWarehouse(initialValue);

			event.preventDefault();
			var $selectSelInSameRow = $(this).closest('tr').find('.search_sel');

			var inputValue = $selectSelInSameRow.val().trim();
			if (inputValue !== '') {
				$selectSelInSameRow.attr('value', '없음');
				$selectSelInSameRow.prev('input[type="hidden"]').attr('value', '');
				$selectSelInSameRow.css('background-color', '');
			}
			$(this).blur();
		});

		$('.search_sel').on('focus', function() {
			$(this).select();
		});

		$('.search_sel').on('click', function() {
			$(this).select();
		});

		$('.search_sel').on('keydown', function(event) {
			if (event.keyCode === 13) {
				event.preventDefault();
				var inputValue = $(this).val().trim();
				var index = validOptions.indexOf(inputValue);

				if (index !== -1 && inputValue !== '') {
					$(this).attr('value', inputValue);
					$(this).prev('input[type="hidden"]').attr('value', seqOptions[index]);
					$(this).css('background-color', '#93d6ecff');
				} else {
					$(this).attr('value', '없음');
					$(this).prev('input[type="hidden"]').attr('value', '');
					$(this).css('background-color', '');
				}
				$(this).blur();
			}
		});

		$('.save_btn').bind('click', function() {
			let wr_id = $(this).attr('data2');
			let id = $(this).attr('data');
			let sid = $(this).attr('data3');
			let warehouse = $(this).closest('tr').find('.warehouse').val();
			let stock = $(this).closest('tr').find('.wr_37').val();
			let rack = $(this).closest('tr').find('.wr_rack').val();
			let expired = $(this).closest('tr').find('.expired').val();
			let state = $(this).attr('data4');
			let rack_seq = $(this).closest('tr').find('.rack_num').attr('value');

			var index = validOptions.indexOf(rack.trim());
			if (index == -1) {
				const rackInput = $(this).closest('tr').find('.wr_rack');
				rackInput.val('없음');
				rackInput.css('background-color', '');
				return false;
			} else {
				rack_seq = seqOptions[index];
			}

			if (!confirm("정말 재고이동 하시겠습니까?")) {
				$(this).attr('disabled', true);
				return false;
			} else {
				$(this).attr('disabled', false);
			}

			if (state == "complete") {
				if (!rack) {
					alert('처리가 완료된 항목은 랙번호를 필수로 입력하셔야 합니다.');
					$(this).attr('disabled', false);
					return false;
				}
			}

			if (!stock || stock <= 0) {
				alert('재고수량을 정확하게 입력하세요.');
				$(this).attr('disabled', false);
				return false;
			}

            console.log("seq : " + id + ", sid : " + sid + ", wr_id : " + wr_id + ", warehouse : " + warehouse + ", stock : " + stock + ", rack : "+rack + ", expired : " + expired + ", rack_seq : " + rack_seq);

			$.post('./temp_stock_update.php', {
				seq: id,
				sid: sid,
				wr_id: wr_id,
				warehouse: warehouse,
				stock: stock,
				rack: rack,
                expired: expired,
				rack_seq: rack_seq
			}, function(data) {
				if (data == "y") {
					alert('재고가 이동되었습니다.');
					location.reload();
				} else if (data == "nn") {
					alert('창고지정이 잘못되었습니다. 개발자에게 문의하세요.');
				} else {
					//alert('처리 중 오류가 발생했습니다.\n일시적 오류이거나 다른 담당자가 재고를 이관시켰을 경우입니다.\n새로고침 하신 뒤 재고를 확인하신 후 다시 시도해주세요.');
                    alert('처리 중 오류가 발생했습니다.\n' + data);

					$(this).attr('disabled', false);
				}
			});
		});
	});
</script>

<?php
include_once(G5_THEME_PATH . '/tail.php');
