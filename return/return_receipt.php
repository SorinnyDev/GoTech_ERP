<?php
include_once('./_common.php');
include_once(G5_THEME_PATH . '/head.php');

if (!$date1) {
	$date1 = date('Y-m-01');
}

if (!$date2) {
	$date2 = date('Y-m-d');
}
?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


<link rel="stylesheet" href="/theme/parsley/mobile/skin/board/sales/style.css?ver=2303229">
<style>
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
		padding: 10px 10px
	}

	.tbl_frm01 td input {
		border: 1px solid #ddd;
		padding: 3px;
		width: 100%;
		height: 30px
	}

	.tbl_frm01 .btn_b02 {
		height: 30px;
		line-height: 30px
	}

	.tbl_frm01 input.readonly {
		background: #f2f2f2
	}

	.local_ov01 {
		position: relative;
		margin: 10px 0;
	}

	.local_ov01 .ov_a {
		display: inline-block;
		line-height: 30px;
		height: 30px;
		font-size: 0.92em;
		background: #ff4081;
		color: #fff;
		vertical-align: top;
		border-radius: 5px;
		padding: 0 7px
	}

	.local_ov01 .ov_a:hover {
		background: #ff1464
	}

	.btn_ov01 {
		display: inline-block;
		line-height: 30px;
		height: 30px;
		font-size: 0.92em;
		vertical-align: top
	}

	.btn_ov01:after {
		display: block;
		visibility: hidden;
		clear: both;
		content: ""
	}

	.btn_ov01 .ov_txt {
		float: left;
		background: #9eacc6;
		color: #fff;
		border-radius: 5px 0 0 5px;
		padding: 0 5px
	}

	.btn_ov01 .ov_num {
		float: left;
		background: #ededed;
		color: #666;
		border-radius: 0 5px 5px 0;
		padding: 0 5px
	}

	a.btn_ov02,
	a.ov_listall {
		display: inline-block;
		line-height: 30px;
		height: 30px;
		font-size: 0.92em;
		background: #565e8c;
		color: #fff;
		vertical-align: top;
		border-radius: 5px;
		padding: 0 7px
	}

	a.btn_ov02:hover,
	a.ov_listall:hover {
		background: #3f51b5
	}

	.bg1 {
		background: #eff3f9
	}

	.list_03 ul {
		height: 580px;
		overflow: auto;
	}
</style>
<div id="bo_list">
	<div class="bo_list_innr">
		<h2 class="board_tit">반품상품 수령등록</h2>
		<form name="fboardlist" id="fboardlist" action="" onsubmit="return fboardlist_submit(this);" method="post">
			<input type="hidden" name="mode" value="delete">

			<div id="bo_li_top_op">
				<div class="bo_list_total">
					<div class="local_ov01 local_ov">
						<span class="btn_ov01">
							<span> <button type="submit" name="btn_submit" value="선택삭제" class="btn02" onclick="document.pressed=this.value">선택삭제</button></span>
						</span>
					</div>
				</div>

				<ul class="<?php echo isset($view) ? 'view_is_list btn_top' : 'btn_top2'; ?>">
					<?php if ($rss_href) { ?>
						<li><a href="<?php echo $rss_href ?>" class="btn_b02">RSS</a></li><?php } ?>
					<li>
						<button type="button" class="btn_b02 btn_bo_sch"><i class="fa fa-search" aria-hidden="true"></i> 검색</button>
					</li>
				</ul>
			</div>

			<div id="bo_li_01" style="clear:both; overflow-x: scroll;">

				<ul class="list_head" style="min-width: 1800px;position:sticky;top:0;background:#fff;z-index:2;">
					<li style="width:30px"><input type="checkbox" id="chkall" onclick="if (this.checked) all_checked(true); else all_checked(false);"></li>
					<li style="width:120px">주문번호</li>
					<li style="width:120px">도메인</li>
					<li style="width:130px">등록일시</li>
					<li style="width:300px">SKU</li>
					<li style="width:550px">상품명</li>
					<li style="width:100px">반품수량</li>
					<li style="width:120px">관리</li>
					<li style="width:120px">재고이관</li>
					<li style="width:120px">반품사진</li>
				</ul>
				<div id="bo_li_01" class="list_03">
					<ul style="width:100%;min-width:max-content;">
						<?php
						$sql_search = " ";

						if ($date1 && $date2) {
							$sql_search .= " and l.wr_datetime BETWEEN '{$date1} 00:00:00' AND '{$date2} 23:59:59' ";
						}
						if ($state == 1) {
							$sql_search .= " and l.wr_state = 0 ";
						}
						if ($state == 2) {
							$sql_search .= " and (l.wr_state = 1 or l.wr_state = 2) ";
						}
						if ($stx) {
							$sql_search .= " and l.wr_order_num LIKE '%$stx%' ";
						}
						if (isset($sc_domain) && isNotEmpty($sc_domain)) {
							$sql_search .= " and s3.wr_domain = '$sc_domain' ";
						}
                        if (isset($sc_warehouse) && isNotEmpty($sc_warehouse)) {
                            $sql_search .= " and EXISTS (
                                            SELECT 1 FROM g5_return_stock rs 
                                            WHERE rs.return_id = l.seq 
                                            AND rs.wr_warehouse = '$sc_warehouse'
                                        ) ";
                        }

						if (!$sst && !$sod) {
							$sst = "l.seq";
							$sod = "desc";
						}
						$sql_order = "order by $sst $sod";

						$sql = "select l.*, s3.wr_domain from g5_return_list as l
                    	left join g5_sales3_list as s3 on s3.seq = l.sales3_id
                    	where (1) {$sql_search} {$sql_order}";

						$rst = sql_query($sql);

						for ($i = 0; $row = sql_fetch_array($rst); $i++) {

							$item = sql_fetch("select * from g5_write_product where wr_id = '{$row['product_id']}' ");
							$bg = 'bg' . ($i % 2);

							//24.01.02 사진연동
							$img = sql_fetch("select * from g5_return_img where return_id = '{$row['seq']}'");

							$img_btn = "";
							if ($img['wr_img1'] || $img['wr_img2'] || $img['wr_img3'] || $img['wr_img4'] || $img['wr_img5']) {
								$img_btn = '<a href="javascript:pop_img_view(' . $img['seq'] . ')">보기</a>';
							}

							//재고이관
							$stock_log = sql_fetch("select * from g5_return_stock where return_id = '{$row['seq']}'");
						?>
							<li class="<?php echo $bg ?>">
								<div class="cnt_left" style="width:30px;text-align:center">
									<input type="checkbox" name="seq[]" value="<?php echo $row['seq'] ?>"
										<?php if ($row['wr_state'] == 2) {
											echo "disabled title=\"재고이관이 완료된 건은 삭제 불가\"";
										} ?>>
								</div>
								<div class="cnt_left" style="width:120px;text-align:center">
									<?php echo $row['wr_order_num'] ?>
								</div>
								<div class="cnt_left" style="width:120px;text-align:center">
									<?php echo $row['wr_domain'] ?>
								</div>
								<div class="cnt_left" style="width:130px;text-align:center">
									<?php echo $row['wr_datetime'] ?>
								</div>
								<div class="cnt_left" style="width:300px"><?php echo $item['wr_1'] ?></div>
								<div class="cnt_left" style="width:550px"><?php echo $item['wr_subject'] ?></div>
								<div class="cnt_left" style="text-align:center;width:100px;font-weight:bold">
									<?php echo $row['wr_stock'] ?>
								</div>

								<div class="cnt_left" style="width:120px;text-align:center">
									<?php if ($row['wr_state'] != 0) { ?>
										<strong style="color:gray;line-height:1.0em" title="<?php echo $row['wr_state_date'] ?>">
											수령완료</strong>
									<?php } else { ?>
										<button type="button" class="btn btn_b01 save_btn" data="<?php echo $row['seq'] ?>">
											수령완료</button>
									<?php } ?>
								</div>
								<div class="cnt_left" style="width:120px;text-align:center">
									<?php if ($row['wr_state'] == 1) { ?>
										<button type="button" class="btn btn_b02 move_btn" onclick="pop_move_stock(<?php echo $row['seq'] ?>)">재고이관</button>
									<?php } elseif ($row['wr_state'] == 2) { ?>
										<a href="javascript:open_stock(<?php echo $row['product_id'] ?>)">
											<?php echo $stock_log['wr_warehouse'] ?> <?php echo $stock_log['wr_rack'] ?>랙
										</a>
									<?php } else { ?>
										<span style="color:gray">미수령</span>
									<?php } ?>
								</div>

								<div class="cnt_left" style="width:120px;text-align:center">
									<?php echo $img_btn ?>
								</div>
							</li>
						<?php
							$cur_no = $cur_no - 1;
						} ?>
						<?php if ($i == 0) {
							echo '<li class="empty_table">내역이 없습니다.</li>';
						} ?>
					</ul>
				</div>
			</div>
		</form>
	</div>
	<div style="clear:both"></div>
	<?php //echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, $_SERVER['SCRIPT_NAME'] . '?' . $qstr . '&amp;page=');
	?>
</div>

<div class="bo_sch_wrap">
	<fieldset class="bo_sch">
		<h3>검색</h3>
		<form name="fsearch" method="get">
			<input type="hidden" name="mode" value="<?php echo $mode ?>">
			<div style="border:1px solid #ddd; margin-bottom:20px; padding:10px">
				<label><input type="radio" name="state" value="0" <?php echo get_checked($state, 0) ?>> 전체</label>
				<label><input type="radio" name="state" value="1" <?php echo get_checked($state, 1) ?>> 미수령만</label>
				<label><input type="radio" name="state" value="2" <?php echo get_checked($state, 2) ?>> 수령완료만</label>
			</div>
			<div style="margin-bottom: 15px; width: 100%; display: flex; align-items: center;">
				<select name="sc_domain" class="search_sel" style="width:100%;">
					<option value="">전체 도메인</option>
					<?php
					$arr = get_code_list('4');
					foreach ($arr as $key => $value) {
						$selected = ($value['code_name'] == $sc_domain) ? "selected" : "";
					?>
						<option value="<?= $value['code_name'] ?>" <?= $selected ?>><?= $value['code_name'] ?></option>
					<?php
					}
					?>
				</select>
			</div>
			<div style="margin-bottom: 15px; width: 100%; display: flex; align-items: center;">
				<input type="text" name="stx" value="<?php echo urldecode($_GET['stx']) ?>" class="frm_input" style="width:100%;" placeholder="주문번호로 검색">
			</div>
            <label for="stx" style="font-weight:bold">창고 조회</label>
            <select name="sc_warehouse" class="search_sel" style="width:100%;">
                    <option value="">이관 창고 선택</option>
                    <option value="한국반품창고">한국반품창고</option>
                    <option value="미국반품창고">미국반품창고</option>
                    <option value="한국폐기창고">한국폐기창고</option>
                    <option value="미국폐기창고">미국폐기창고</option>
            </select>

            
			<label for="stx" style="font-weight:bold">일자 조회<strong class="sound_only"> 필수</strong></label>
			<div class="sch_bar" style="margin-bottom: 15px; width: 100%; display: flex; align-items: center; margin-top: 0;">
				<input type="date" name="date1" value="<?php echo $date1 ?>" required id="stx" class="sch_input" size="25" maxlength="20" placeholder="" style="width:45%;text-align:center">
				<span style="display:inline-block;height:38px;line-height:38px; margin:0 5px">~</span>
				<input type="date" name="date2" value="<?php echo stripslashes($date2) ?>" required id="stx" class="sch_input" size="25" maxlength="20" placeholder=" " style="width:45%;text-align:center">
			</div>
			<button type="submit" value="검색" class="btn_b01" style="width:49%;margin-top:15px"><i class="fa fa-search" aria-hidden="true"></i> 검색하기</button>
			<button type="button" value="초기화" class="btn_b02" style="width:49%;margin-top:15px;" onclick="location.href='<?php echo $_SERVER['PHP_SELF'] ?>'"><i class="fa fa-repeat"
					aria-hidden="true"></i> 검색초기화
			</button>
			<button type="button" class="bo_sch_cls" title="닫기"><i class="fa fa-times" aria-hidden="true"></i><span class="sound_only">닫기</span></button>
		</form>
	</fieldset>
	<div class="bo_sch_bg"></div>
</div>
<script>
	jQuery(function($) {});

	$(document).ready(function() {
		$('.search_sel').select2();
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
	function open_stock(wr_id) {

		var _width = '1150';
		var _height = '850';

		var _left = Math.ceil((window.screen.width - _width) / 2);
		var _top = Math.ceil((window.screen.height - _height) / 2);

		window.open("/stock/pop_rack.php?wr_id=" + wr_id, "pop_img_view", "left=" + _left + ", top=" + _top + ", width=" + _width + ", height=" + _height + ", scrollbars=1");

		return false;
	}

	function pop_img_view(seq) {

		var _width = '1150';
		var _height = '850';

		var _left = Math.ceil((window.screen.width - _width) / 2);
		var _top = Math.ceil((window.screen.height - _height) / 2);

		window.open("./pop_return_img.php?seq=" + seq, "pop_img_view", "left=" + _left + ", top=" + _top + ", width=" + _width + ", height=" + _height + ", scrollbars=1");

		return false;
	}

	function pop_move_stock(seq) {
		let id = $(this).attr('data');
		var _width = '700';
		var _height = '450';

		var _left = Math.ceil((window.screen.width - _width) / 2);
		var _top = Math.ceil((window.screen.height - _height) / 2);

		window.open("./return_stcok_move.php?seq=" + seq, "pop_move_stock", "left=" + _left + ", top=" + _top + ", width=" + _width + ", height=" + _height + ", scrollbars=1");

		return false;
	}

	function all_checked(sw) {
		var f = document.fboardlist;

		for (var i = 0; i < f.length; i++) {
			if (f.elements[i].name == "seq[]")
				f.elements[i].checked = sw;
		}
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

			f.mode.value = "delete";
			f.removeAttribute("target");
			f.action = "./return_receipt_update.php";
		}
		return true;
	}

	$(function() {
		$('.save_btn').bind('click', function() {

			let id = $(this).attr('data');

			if (confirm('수령완료 처리 하시겠습니까?')) {
				$.post('./return_receipt_update.php', {
					seq: id,
					mode: 'update'
				}, function(data) {
					if (data == "y") {
						alert('반품수령등록이 완료되었습니다.\n재고이관을 진행해주세요.');
						location.reload();
					} else {
						alert('처리 중 오류가 발생했습니다.');
					}
				})
			}
		});

		$('.sel_type').bind('change', function() {
			if ($(this).val() == 1) {
				location.href = '?mode=1';
			} else if ($(this).val() == 2) {
				location.href = '?mode=2';
			}
		})
	})
</script>

<?php
include_once(G5_THEME_PATH . '/tail.php');
