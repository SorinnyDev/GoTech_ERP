<?php
include_once('./_common.php');

include_once(G5_THEME_PATH . '/head.php');

if (!$date1) $date1 = G5_TIME_YMD;
if (!$date2) $date2 = G5_TIME_YMD;

if (isset($nm_type)) {
	set_cookie('nm_type', $nm_type, 60 * 60 * 24 * 365);
}

if (!isset($nm_type) && get_cookie('nm_type')) {
	$nm_type = get_cookie('nm_type');
}

?>
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

	.tbl_frm01 .title {
		background: #444444;
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
</style>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<div id="bo_list">
	<div class="bo_list_innr">
		<h2 class="board_tit">입고등록</h2>
		<form name="fboardlist" id="fboardlist" action="./sales2_list_update.php" onsubmit="return fboardlist_submit(this);" method="post">
			<input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
			<input type="hidden" name="sfl" value="<?php echo $sfl ?>">
			<input type="hidden" name="stx" value="<?php echo $stx ?>">
			<input type="hidden" name="spt" value="<?php echo $spt ?>">
			<input type="hidden" name="sst" value="<?php echo $sst ?>">
			<input type="hidden" name="sod" value="<?php echo $sod ?>">
			<input type="hidden" name="page" value="<?php echo $page ?>">
			<input type="hidden" name="sw" value="">

			<?php if ($is_category) { ?>
				<nav id="bo_cate">
					<h2><?php echo ($board['bo_mobile_subject'] ? $board['bo_mobile_subject'] : $board['bo_subject']) ?> 카테고리</h2>
					<ul id="bo_cate_ul">
						<?php echo $category_option ?>
					</ul>
				</nav>
			<?php } ?>

			<div id="bo_li_top_op">
				<div class="bo_list_total">
					<span> <button type="submit" name="btn_submit" value="선택삭제" class="btn02" onclick="document.pressed=this.value">선택삭제</button></span>
					<!--<span> <button type="submit" name="btn_submit" value="완전삭제" class="btn02" style="background:#ff4081;border:1px solid #ff4081;" onclick="document.pressed=this.value">완전삭제</button></span>-->

				</div>
				<ul class="<?php echo isset($view) ? 'view_is_list btn_top' : 'btn_top2'; ?>">
					<li>
						<select class="frm_input" name="nm_type" id="nm_type">
							<option value="type_1" <?= get_selected('type_1', $nm_type) ?>>제품명칭</option>
							<option value="type_2" <?= get_selected('type_2', $nm_type) ?>>몰타이틀</option>
						</select>
					</li>
					<?php if ($rss_href) { ?><li><a href="<?php echo $rss_href ?>" class="btn_b02">RSS</a></li><?php } ?>
					<li><button type="button" class="btn_b01" onclick="pop_excel();"><i class="fa fa-database" aria-hidden="true"></i> 발주자료 가져오기</button></li>
					<li><button type="button" class="btn_b02 btn_bo_sch"><i class="fa fa-search" aria-hidden="true"></i> 검색</button></li>
					<!--<li><button type="button" class="btn_b02 all_delete" style="color:#fff;background:red"> 전체초기화(임시)</button></li>-->

				</ul>
			</div>
			<div id="bo_li_01" style="overflow-x:scroll;height:400px">
				<ul class="list_head" style="width:3500px">
					<li style="width:40px"><input type="checkbox" id="chkall" onclick="if (this.checked) all_checked(true); else all_checked(false);"></li>
					<li style="width:50px"><?php echo subject_sort_link('wr_hit', $qstr2, 1) ?>순번</a></li>
					<li style="width:100px">도메인명</li>
					<li style="width:120px">주문번호</li>
					<li style="width:100px">매출일자</li>
					<li style="width:100px">발주일자</li>
					<li style="width:100px">입고일자</li>
					<li style="width:120px">상품코드</li>

					<li style="width:90px">약칭명</li>
					<li style="width:250px">상품명칭</li>
					<li style="width:120px">대표코드</li>

					<li style="width:70px">수량</li>
					<li style="width:70px">박스수</li>
					<li style="width:100px">단가</li>
					<li style="width:100px">신고가격</li>
					<!--<li style="width:100px">수수료</li>-->
					<li style="width:70px">통화</li>
					<li style="width:70px">개당무게</li>
					<li style="width:70px">총무게</li>
					<li style="width:100px">배송사</li>
					<li style="width:100px">배송요금</li>
					<li style="width:100px">주문자ID</li>
					<li style="width:100px">주문자명</li>
                    <li style="width:100px">유통기한</li>
				</ul>

				<div id="bo_li_01" class="list_03">
					<ul style="width:3500px">
						<?php
						if ($date1 && $date2)
							$sql_search .= " and wr_date3 BETWEEN '{$date1}' AND '{$date2}'";

						if ($wr_18)
							$sql_search .= " and wr_domain = '{$wr_18}' ";

						if ($stx)
							$sql_search .= " and wr_order_num LIKE '%$stx%' ";

						if ($dType == "1") {
							$sql_search .= " AND wr_domain NOT IN('" . implode("','", $circulation) . "')";
						} else if ($dType == "2") {
							$sql_search .= " AND wr_domain IN('" . implode("','", $circulation) . "')";
						}

						$sql = "select a.*, b.wr_subject from g5_sales2_list a LEFT JOIN g5_write_product b ON b.wr_id=a.wr_product_id where (1) {$sql_search} and wr_direct_use = 0 order by seq desc";

						$rst = sql_query($sql);
						for ($i = 0; $row = sql_fetch_array($rst); $i++) {

							$item = sql_fetch("select * from g5_write_product where (wr_1 = '" . addslashes($row['wr_code']) . "' or wr_27 = '" . addslashes($row['wr_code']) . "' or wr_28 = '" . addslashes($row['wr_code']) . "' or wr_29 = '" . addslashes($row['wr_code']) . "' or wr_30 = '" . addslashes($row['wr_code']) . "' or wr_31 = '" . addslashes($row['wr_code']) . "') ");

							$imsi_item = "";
							if ($item['ca_name'] == "임시") {
								$imsi_item = "color:blue";
							} else if ($item['ca_name'] == "최종확정") {
								$imsi_item = "color:red";
							}

							# 수수료 계산
							if ($row['wr_sales_fee']) {
								$wr_fee = round(($row['wr_singo'] * $row['wr_sales_fee'] / 100), 2);
							} else {
								$item_fee = sql_fetch("SELECT * FROM g5_write_product_fee WHERE wr_id='" . $row['wr_product_id'] . "' AND warehouse='" . $row['wr_warehouse'] . "' AND domain='" . $row['wr_domain'] . "'");
								if (!$item_fee['fidx']) {
									$wr_fee = "0";
								} else {
									$wr_fee = round(($row['wr_singo'] * $item_fee['product_fee'] / 100), 2);
								}
							}

							$wr_warehouse_etc = "";
							if ($row['wr_warehouse_etc']) {
								$wr_warehouse_etc = '<br><strong style="color:red">비고</strong>';
							}
						?>
							<li class="modify" data="<?php echo $row['seq'] ?>">
								<div class="num cnt_left" style="width:40px"><input type="checkbox" name="seq[]" value="<?php echo $row['seq'] ?>"></div>
								<div class="num cnt_left" style="width:50px"><?php echo ($i + 1) ?><?= $wr_warehouse_etc ?></div>
								<div class="cnt_left" style="width:100px"><?php echo $row['wr_domain'] ?></div>
								<div class="cnt_left" style="width:120px;"><?php echo $row['wr_order_num'] ?></div>
								<div class="cnt_left" style="width:100px;text-align:center"><?php echo $row['wr_date'] ?></div>
								<div class="cnt_left" style="width:100px;text-align:center"><?php echo $row['wr_date2'] ?></div>
								<div class="cnt_left" style="width:100px;text-align:center"><?php echo $row['wr_date3'] ?></div>
								<div class="cnt_left" style="width:120px;text-align:center;<?php echo $imsi_item ?>"><?php echo $row['wr_code'] ?></div>

								<div class="cnt_left" style="width:90px;"><?php echo $item['wr_2'] ?></div>
								<div class="cnt_left" style="width:250px;<?php echo $imsi_item ?>"><?= $nm_type === 'type_1' ? $row['wr_subject'] : $row['wr_product_nm'] ?></div>
								<div class="cnt_left" style="width:120px;"><?php echo $row['wr_code'] ?></div>
								<div class="cnt_left" style="width:70px;text-align:right"><?php echo $row['wr_ea'] ?></div>
								<div class="cnt_left" style="width:70px;text-align:right"><?php echo $row['wr_box'] ?></div>
								<div class="cnt_left" style="width:100px;text-align:right"><?php echo $row['wr_danga'] ?></div>
								<div class="cnt_left" style="width:100px;text-align:right"><?php echo $row['wr_singo'] ?></div>
								<!--<div class="cnt_left" style="width:100px;text-align:right"><?php echo $wr_fee ?></div>-->
								<div class="cnt_left" style="width:70px;text-align:center"><?php echo $row['wr_currency'] ?></div>
								<div class="cnt_left" style="width:70px;text-align:right"><?php echo $row['wr_weight1'] ?></div>
								<div class="cnt_left" style="width:70px;text-align:right"><?php echo $row['wr_weight2'] ?></div>
								<div class="cnt_left" style="width:100px;text-align:center"><?php echo $row['wr_delivery'] ?></div>
								<div class="cnt_left" style="width:100px;text-align:right"><?php echo $row['wr_delivery_fee'] ?></div>
								<div class="cnt_left" style="width:100px;"><?php echo $row['wr_mb_id'] ?></div>
								<div class="cnt_left" style="width:100px;"><?php echo $row['wr_mb_name'] ?></div>
                                <div class="cnt_left" style="width:100px;"><?php echo $row['expired_date'] ?></div>

								<!-- 
				        // 추천, 비추천 
		                <?php if ($is_good) { ?><span class="sound_only">추천</span><i class="fa fa-thumbs-o-up" aria-hidden="true"></i> <?php echo $list[$i]['wr_good'] ?><?php } ?>
		                <?php if ($is_nogood) { ?><span class="sound_only">비추천</span><i class="fa fa-thumbs-o-down" aria-hidden="true"></i> <?php echo $list[$i]['wr_nogood'] ?><?php } ?>
		                -->

							</li>
						<?php } ?>
						<?php if ($i == 0) {
							echo '<li class="empty_table">내역이 없습니다.</li>';
						} ?>
					</ul>
				</div>
			</div>

		</form>

		<div>
			<h2 style="margin-top:20px; margin-bottom:10px;font-size:14px">입고정보</h2>
			<form id="result_addform">
				<div style="border:1px solid #ddd; width:100%; height:655px;" id="result_form">
					<p style="text-align:center; font-size:15px; color:red;padding-top:150px">상단 리스트에서 선택하세요.</p>
				</div>
			</form>
		</div>
	</div>
</div>

<div class="bo_sch_wrap">
	<fieldset class="bo_sch">
		<h3>검색</h3>
		<form name="fsearch" method="get">
			<select name="wr_18" style="margin-bottom:15px">
				<option value="">도메인 선택</option>
				<?php echo get_domain_option($_GET['wr_18']) ?>
			</select>

			<div style="border:1px solid #ddd; margin-bottom:20px; padding:10px">
				<label><input type="radio" name="dType" value="0" <?php echo get_checked($dType, 0) ?>> 전체</label>
				<label><input type="radio" name="dType" value="1" <?php echo get_checked($dType, 1) ?>> 화장품</label>
				<label><input type="radio" name="dType" value="2" <?php echo get_checked($dType, 2) ?>> 유통</label>
			</div>

			<div style="border:1px solid #ddd; margin-bottom:20px; padding:10px">
				<input type="text" name="stx" value="<?php echo urldecode($_GET['stx']) ?>" class="frm_input" style="width:100%;" placeholder="주문번호 조회">
			</div>

			<label for="stx" style="font-weight:bold">입고일자 조회<strong class="sound_only"> 필수</strong></label>
			<div class="sch_bar" style="margin-top:3px">

				<input type="date" name="date1" value="<?php echo $date1 ?>" required id="stx" class="sch_input" size="25" maxlength="20" placeholder="" style="width:45%;text-align:center">
				<span style="float:left;height:38px;line-height:38px; margin:0 5px">~</span>
				<input type="date" name="date2" value="<?php echo stripslashes($date2) ?>" required id="stx" class="sch_input" size="25" maxlength="20" placeholder=" " style="width:45%;text-align:center">

			</div>
			<button type="submit" value="검색" class="btn_b01" style="width:49%;margin-top:15px"><i class="fa fa-search" aria-hidden="true"></i> 검색하기</button>
			<button type="button" value="초기화" class="btn_b02" style="width:49%;margin-top:15px;" onclick="location.href='<?php echo $_SERVER['PHP_SELF'] ?>'"><i class="fa fa-repeat" aria-hidden="true"></i> 검색초기화</button>
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
	function pop_excel() {
		let id = $(this).attr('data');


		window.open("./sales2_search.php", "sales2_search", " width=" + screen.width + ", height=" + screen.height + ", scrollbars=1, fullscreen=yes");

		return false;

	}


	$(function() {
		$('.all_delete').bind('click', function() {

			if (confirm('정말 데이터를 초기화 하시겠습니까?')) {
				location.href = './all_delete.php?table_name=2';
			}

			return false;

		});
		$('.modify').bind('click', function() {

			$('#result_form').html('');
			let id = $(this).attr('data');
			$('#result_form').html('<center><img style="padding-top:150px" src="/mobile/shop/img/loading.gif"></center>');
			$.post('./sales2_addbox.php', {
				seq: id
			}, function(data) {
				$('#result_form').html(data);
			})

		});

		$(document).on('click', '#frm_submit', function() {
			$(this).attr('disabled', true);
			var formData = $("#result_addform").serialize();

			$.ajax({
				cache: false,
				url: "./sales2_addbox_update.php", // 요기에
				type: 'POST',
				data: formData,
				success: function(data) {
					if (data == "y") {
						alert('데이터가 정상적으로 저장되었습니다.');
					} else {
						alert('데이터 저장중 오류가 발생했습니다.');
						return false;
					}

					$('#frm_submit').attr('disabled', false);
				}, // success 

				error: function(xhr, status) {
					alert(xhr + " : " + status);
				}
			});
		})

	})
	$('input[name="seq[]"]').bind('click', function() {
		let stat = $(this).is(':checked');

		if (stat) {
			$(this).closest('li').css({
				'background': '#f2f2f2'
			});
		} else {
			$(this).closest('li').css({
				'background': '#fff'
			});
		}
	})

	function all_checked(sw) {
		var f = document.fboardlist;

		for (var i = 0; i < f.length; i++) {
			if (f.elements[i].name == "seq[]")
				f.elements[i].checked = sw;
		}

		$('input[name="seq[]"]').each(function() {
			let stat = $(this).is(':checked');

			if (stat) {
				$(this).closest('li').css({
					'background': '#f2f2f2'
				});
			} else {
				$(this).closest('li').css({
					'background': '#fff'
				});
			}
		})
	}

	function fboardlist_submit(f) {
		var chk_count = 0;

		for (var i = 0; i < f.length; i++) {
			if (f.elements[i].name == "seq[]" && f.elements[i].checked)
				chk_count++;
		}

		if (!chk_count) {
			alert(document.pressed + "할 데이터를 하나 이상 선택하세요.");
			return false;
		}


		if (document.pressed == "선택삭제") {

			if (!confirm("선택한 데이터를 정말 삭제하시겠습니까?\n\n[경고] 한번 삭제한 자료는 복구할 수 없습니다."))
				return false;

			f.removeAttribute("target");
			f.action = "./sales2_list_update.php";
		}

		if (document.pressed == "완전삭제") {
			if (!confirm("선택한 데이터를 정말 삭제하시겠습니까?\n\n[경고] 주문번호에 관련된 자료 전부 삭제하는 기능입니다.\n\n한번 삭제한 자료는 복구할 수 없습니다.\n\n이전 과정 전부 삭제됩니다."))
				return false;

			f.removeAttribute("target");
			f.action = "./sales2_list_update.php";
		}

		return true;
	}

	/* 제품명 몰타이틀/제품명칭 선택할 수 있게끔 */
	$(document).on('change', '#nm_type', function() {
		const currentUrl = new URL(window.location.href);
		currentUrl.searchParams.set('nm_type', this.value);

		window.location.href = currentUrl.toString();
	});
</script>


<?php
include_once(G5_THEME_PATH . '/tail.php');
