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
<link href="https://cdn.jsdelivr.net/npm/gridjs/dist/theme/mermaid.min.css" rel="stylesheet" />
<style>
	.cnt_left {
		padding: 5px 10px;
		border-right: 1px solid #ddd;
		word-break: normal;
		text-overflow: ellipsis;
		overflow: hidden;
		white-space: nowrap;
	}

	.set .cnt_left {
		height: 55px
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

	.tbl_frm01 td {
		border-bottom: 1px solid #ddd;
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

	.tbl_frm01 td input {
		border: 1px solid #ddd;
		padding: 3px;
		width: 100%
	}

	.tbl_frm01 input.readonly {
		background: #f2f2f2
	}
</style>
<div id="bo_list">
	<div class="bo_list_innr">
		<h2 class="board_tit">매출등록</h2>
		<form name="fboardlist" id="fboardlist" action="./sales0_list_update.php" onsubmit="return fboardlist_submit(this);" method="post">
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

					<span style="padding:7px;"></span>
				</div>
				<ul class="<?php echo isset($view) ? 'view_is_list btn_top' : 'btn_top2'; ?>">
					<li>
						<select class="frm_input" name="nm_type" id="nm_type">
							<option value="type_1" <?= get_selected('type_1', $nm_type) ?>>제품명칭</option>
							<option value="type_2" <?= get_selected('type_2', $nm_type) ?>>몰타이틀</option>
						</select>
					</li>
					<?php if ($rss_href) { ?><li><a href="<?php echo $rss_href ?>" class="btn_b02">RSS</a></li><?php } ?>
					<li class="wli_cnt">
						<label for="wmb_id" class="sound_only">중량무게1<strong>필수</strong></label>

						<select name="wmb_id" id="wmb_id" class="frm_input search_sel" onchange="location.href='?mb_id='+this.value" ;>
							<option value="">담당자 전체</option>
							<?php
							$mbSql = " select mb_id, mb_name from g5_member where del_yn = 'N' order by mb_name asc";
							$mbRst = sql_query($mbSql);
							for ($i = 0; $mb = sql_fetch_array($mbRst); $i++) {
							?>
								<option value="<?php echo $mb['mb_id'] ?>" <?php echo get_selected($mb['mb_id'], $mb_id) ?>><?php echo $mb['mb_name'] ?>(<?php echo $mb['mb_id'] ?>)</option>
							<?php } ?>
						</select>
					</li>
					<li><button type="button" class="btn_b01" onclick="pop_excel();"><i class="fa fa-file-excel-o" aria-hidden="true"></i> 엑셀자료 가져오기</button></li>
					<li><button type="button" class="btn_b02 btn_bo_sch"><i class="fa fa-search" aria-hidden="true"></i> 검색</button></li>
					<!-- <li><button type="button" class="btn_b02 all_delete" style="color:#fff;background:red"> 전체초기화(임시)</button></li>-->
				</ul>
			</div>
			<div id="wrapper">

			</div>

			<div id="bo_li_01" style="overflow-x:scroll;height:400px">
				<ul class="list_head" style="width:3500px;position:sticky;top:0;background:#fff;z-index:99999">
					<li style="width:50px"><input type="checkbox" id="chkall" onclick="if (this.checked) all_checked(true); else all_checked(false);"></li>
					<li style="width:70px"><?php echo subject_sort_link('seq', $qstr2, 1) ?>순번</a></li>
					<li style="width:100px">도메인명</li>
					<li style="width:200px"><?php echo subject_sort_link('wr_order_num', $qstr2, 1) ?>주문번호</a></li>
					<li style="width:100px"><?php echo subject_sort_link('wr_date', $qstr2, 1) ?>매출일자</a></li>
					<li style="width:150px"><?php echo subject_sort_link('wr_code', $qstr2, 1) ?>SKU</a></li>
					<li style="width:150px"><?php echo subject_sort_link('wr_2', $qstr2, 1) ?>약칭명</a></li>
					<li style="width:250px"><?php echo subject_sort_link('wr_2', $qstr2, 1) ?>상품명칭</a></li>
					<li style="width:200px">대표코드</li>
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
				</ul>

				<div class="list_03">
					<ul style="width:3500px;">
						<?php

						if ($date1 && $date2)
							$sql_search .= " and a.wr_date BETWEEN '{$date1}' AND '{$date2}'";


						if (!$sql_search) {
							$sql_search .= "  ";
						}

						if ($dType == "1") {
							$sql_search .= " AND wr_domain NOT IN('" . implode("','", $circulation) . "')";
						} else if ($dType == "2") {
							$sql_search .= " AND wr_domain IN('" . implode("','", $circulation) . "')";
						}

						if ($mb_id) {
							$sql_search .= " and a.mb_id = '$mb_id' ";
						}

						if ($wr_18)
							$sql_search .= " and a.wr_domain = '{$wr_18}' ";

						if ($stx)
							$sql_search .= " and a.wr_order_num LIKE '%$stx%' ";

						if (!$sst && !$sod) {
							$sst = "a.seq";
							$sod = "desc";
						}
						$sql_order = "order by $sst $sod";

						$sql = "select a.*, b.wr_subject, b.wr_2 from g5_sales0_list a 
					LEFT JOIN g5_write_product b ON a.wr_product_id=b.wr_id
					
					where wr_code != '' {$sql_search} {$sql_order} ";

						$rst = sql_query($sql);
						for ($i = 0; $row = sql_fetch_array($rst); $i++) {

							//$item = sql_fetch("select * from g5_write_product where (wr_1 = '".addslashes($row['wr_code'])."' or wr_27 = '".addslashes($row['wr_code'])."' or wr_28 = '".addslashes($row['wr_code'])."' or wr_29 = '".addslashes($row['wr_code'])."' or wr_30 = '".addslashes($row['wr_code'])."' or wr_31 = '".addslashes($row['wr_code'])."') ");

							$set = $set_class = "";
							if ($row['wr_set_sku']) {
								$set_class = "set";
								$set = '<br><span style="color:blue">(' . $row['wr_set_sku'] . ')</span>';
							}

							$wr_etc = "";
							if ($row['wr_etc']) {
								$wr_etc = '<br><strong style="color:red">비고</strong>';
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

						?>
							<li class="modify <?php echo $set_class ?>" data="<?php echo $row['seq'] ?>">


								<div class="num cnt_left" style="width:50px"><input type="checkbox" name="seq[]" value="<?php echo $row['seq'] ?>"></div>
								<div class="num cnt_left" style="width:70px"><?php echo ($i + 1) ?><?= $wr_etc ?></div>
								<div class="cnt_left" style="width:100px"><?php echo $row['wr_domain'] ?></div>
								<div class="cnt_left" style="width:200px;"><?php echo $row['wr_order_num'] ?></div>
								<div class="cnt_left" style="width:100px;text-align:center"><?php echo $row['wr_date'] ?></div>
								<div class="cnt_left" style="width:150px;text-align:center"><?php echo $row['wr_code'] ?><?php echo $set ?></div>
								<div class="cnt_left" style="width:150px;"><?php echo $row['wr_2'] ?></div>
								<div class="cnt_left" style="width:250px;"><?= $nm_type === 'type_1' ? $row['wr_subject'] : $row['wr_product_nm'] ?></div>
								<div class="cnt_left" style="width:200px;"><?php echo $row['wr_code'] ?></div>
								<div class="cnt_left" style="width:70px;text-align:right"><?php echo $row['wr_ea'] ?></div>
								<div class="cnt_left" style="width:70px;text-align:right"><?php echo $row['wr_box'] ?></div>
								<div class="cnt_left" style="width:100px;text-align:right"><?php echo $row['wr_danga'] ?></div>
								<div class="cnt_left" style="width:100px;text-align:right"><?php echo $row['wr_singo'] ?></div>
								<!--<div class="cnt_left" style="width:100px;text-align:right"><?= $wr_fee ?></div>-->
								<div class="cnt_left" style="width:70px;text-align:center"><?php echo $row['wr_currency'] ?></div>
								<div class="cnt_left" style="width:70px;text-align:right"><?php echo $row['wr_weight1'] ?></div>
								<div class="cnt_left" style="width:70px;text-align:right"><?php echo $row['wr_weight2'] ?></div>
								<div class="cnt_left" style="width:100px;text-align:center"><?php echo $row['wr_delivery'] ?></div>
								<div class="cnt_left" style="width:100px;text-align:right"><?php echo $row['wr_delivery_fee'] ?></div>
								<div class="cnt_left" style="width:100px;"><?php echo $row['wr_mb_id'] ?></div>
								<div class="cnt_left" style="width:100px;"><?php echo $row['wr_mb_name'] ?></div>



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
			<h2 style="margin-top:20px; margin-bottom:10px;font-size:14px">매출정보</h2>
			<form id="result_addform">
				<div style="border:1px solid #ddd; width:100%; height:600px;" id="result_form">
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

			<label for="stx" style="font-weight:bold">매출일자 조회<strong class="sound_only"> 필수</strong></label>
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
	// function pop_excel(){

	// 	let id = $(this).attr('data');
	// 	var _width = '1500';
	//     var _height = '850';
	//  
	//     var _left = Math.ceil(( window.screen.width - _width )/2);
	//     var _top = Math.ceil(( window.screen.height - _height )/2); 

	// 	window.open("./sales0_search.php?wr_id="+id, "sales0_search"+id, " width="+screen.width+", height="+screen.height+", scrollbars=1, fullscreen=yes");

	// 	return false;

	// }

	function pop_excel() {
		let id = $(this).attr('data');

		var screen_width = window.screen.availWidth;
		var screen_height = window.screen.availHeight;

		var features = "width=" + screen_width + ",height=" + screen_height;
		features += ",left=0,top=0";
		features += ",scrollbars=yes,fullscreen=yes";

		window.open("./sales0_search.php?wr_id=" + id, "sales0_search" + id, features);
		return false;
	}

	let lastScroll = 0;
	let page = 0;
	let nowPageLimit = 0;
	let nextPageLimit = 0;
	let loading_flag = false;

	function getData(limit) {
		//다음페이지

		nextPageLimit = (page + 1) * limit;

		$.ajax({
			type: "POST",
			enctype: 'multipart/form-data',
			url: "./sales0_list_data.php",
			async: false,
			data: {
				"next_num": nextPageLimit,
				"date1": "<?php echo $date1 ?>",
				"date2": "<?php echo $date2 ?>",
				"mb_id": "<?php echo $mb_id ?>",
				"wr_18": "<?php echo $wr_18 ?>",
				"stx": "<?php echo $stx ?>",
			},
			success: function(data) {
				$(".list_03").append(data);

			},
			error: function(data, status, err) {
				page = page;
			},
			complete: function() {
				loading_flag = false
				page = page + 1;
			}

		});

	}

	$(function() {

		let scrollContainer = $("#bo_li_01");
		let contentHeight = scrollContainer.height();
		let scrollHeight = scrollContainer[0].scrollHeight;
		let scrollThreshold = 50;

		scrollContainer.scroll(function() {
			if (!loading_flag && scrollContainer.scrollTop() + contentHeight + scrollThreshold >= scrollHeight) {
				loading_flag = true;
				console.log('끝');
				// getData(50);
			}
		});



		$('.all_delete').bind('click', function() {

			if (confirm('정말 데이터를 초기화 하시겠습니까?')) {
				location.href = './all_delete.php?table_name=0';
			}

			return false;

		});
		$('.modify').bind('click', function() {

			$('#result_form').html('');
			let id = $(this).attr('data');
			$('#result_form').html('<center><img style="padding-top:150px" src="/mobile/shop/img/loading.gif"></center>');
			$.post('./sales0_addbox.php', {
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
				url: "./sales0_addbox_update.php", // 요기에
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
		});




	});

	// 아이템 바로가기(1. sku, 2. wr_product_name1(제품명1), 3.wr_product_name1(제품명2) 순으로 검색 )
	/*
	function item_check_link(){
	    let wr_code = $("input[name=wr_code]").val(),
	        wr_product_name1 = $("input[name=wr_product_name1]").val(),
	        wr_product_name2 = $("input[name=wr_product_name2]").val();


	}
	*/

	function item_link() {
		if (confirm("검색된 제품이 없습니다\n\n직접 검색하시겠습니까?")) {
			location.href = g5_bbs_url + "/board.php?bo_table=product";
		}
	}

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
			f.action = "./sales0_list_update.php";
		}

		if (document.pressed == "완전삭제") {
			if (!confirm("선택한 데이터를 정말 삭제하시겠습니까?\n\n[경고] 주문번호에 관련된 자료 전부 삭제하는 기능입니다.\n\n한번 삭제한 자료는 복구할 수 없습니다.\n\n이전 과정 전부 삭제됩니다."))
				return false;

			f.removeAttribute("target");
			f.action = "./sales0_list_update.php";
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
