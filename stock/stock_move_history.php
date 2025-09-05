<?php 
include_once('./_common.php');
$menu_num = 7;
include_once(G5_THEME_PATH.'/head.php');

?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


<link rel="stylesheet" href="/theme/parsley/mobile/skin/board/sales/style.css?ver=2303229">
<style>
.cnt_left {padding:5px 10px; border-right:1px solid #ddd; word-break: text-overflow: ellipsis; overflow: hidden; white-space: nowrap;}
.list_03 li { padding:0 }
.list_03 li .cnt_left { line-height:43px }
.modify { cursor:pointer}
.tbl_frm01 th {background:#6f809a;color:#fff;border:1px solid #60718b;font-weight:normal;text-align:center;padding:8px 5px;font-size:0.92em}
.tbl_frm01 td  { border-bottom:1px solid #ddd; padding:10px 10px }
.tbl_frm01 td input { border:1px solid #ddd; padding:3px; width:100%; height:30px}
.tbl_frm01 .btn_b02 { height:30px; line-height:30px}
.tbl_frm01 input.readonly { background:#f2f2f2}

.local_ov01 {position:relative;margin: 10px 0;}
.local_ov01 .ov_a{display:inline-block;line-height:30px;height:30px;font-size:0.92em;background:#ff4081;color:#fff;vertical-align:top;border-radius:5px;padding:0 7px}
.local_ov01 .ov_a:hover{background:#ff1464}
.btn_ov01{display:inline-block;line-height:30px;height:30px;font-size:0.92em;vertical-align:top}
.btn_ov01:after{display:block;visibility:hidden;clear :both;content:""}
.btn_ov01 .ov_txt{float:left;background:#9eacc6;color:#fff;border-radius:5px 0 0 5px;padding:0 5px}
.btn_ov01 .ov_num{float:left;background:#ededed;color:#666;border-radius:0 5px 5px 0;padding:0 5px}
a.btn_ov02,a.ov_listall{display:inline-block;line-height:30px;height:30px;font-size:0.92em;background:#565e8c;color:#fff;vertical-align:top;border-radius:5px;padding:0 7px }
a.btn_ov02:hover,a.ov_listall:hover{background:#3f51b5}

</style>
<div id="bo_list">
	<div class="bo_list_innr">
		<h2 class="board_tit">이관중 재고확인</h2>
		<form name="fboardlist" id="fboardlist" action="<?php echo G5_BBS_URL; ?>/board_list_update.php" onsubmit="return fboardlist_submit(this);" method="post">
			<input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
			<input type="hidden" name="sfl" value="<?php echo $sfl ?>">
			<input type="hidden" name="stx" value="<?php echo $stx2 ?>">
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
				<ul class="btn_top2">
					<li style="margin-bottom:10px;">
						<label for="wr_out_warehouse" class="sound_only">출발지</label>
						<select name="wr_out_warehouse" id="wr_out_warehouse" class="frm_input" onchange="fnSearch();">
							<option value="">출발지 선택</option>
							<?foreach($storage_arr as $key => $val){?>
								<?if($key != '7000' && $key != '8000' && $key != '9000'){?>
									<option value="<?=$key?>" <?=($key == $wr_out_warehouse)?"SELECTED":""?>><?=$val['code_nm']?></option>
								<?}?>
							<?}?>
						</select>
					</li>
					<li style="margin-bottom:10px;">
					<label for="wr_in_warehouse" class="sound_only">도착지</label>
						<select name="wr_in_warehouse" id="wr_in_warehouse" class="frm_input" onchange="fnSearch();">
							<option value="">도착지 선택</option>
							<?foreach($storage_arr as $key => $val){?>
								<?if($key != '7000' && $key != '8000' && $key != '9000'){?>
									<option value="<?=$key?>" <?=($key == $wr_in_warehouse)?"SELECTED":""?>><?=$val['code_nm']?></option>
								<?}?>
							<?}?>
						</select>
					</li>
				</ul>
			</div>
		   
			<div id="bo_li_01">
				
				<ul class="list_head" style="width:100%;min-width:max-content;position:sticky;top:0;background:#fff;z-index:2;" >
					<li style="width:200px">처리일시</li>
					<li style="width:300px">SKU</li>
					<li style="width:540px">상품명</li>
					<li style="width:200px">이관창고</li>
					<li style="width:150px">이관수량</li>
					<li style="width:200px">처리자</li>
				</ul>
				<div id="bo_li_01" class="list_03" >
					<ul style="width:max-content;">
						<?php 
						$sql_common = " from g5_stock_move ";
						$sql_search = " where wr_state = 0 ";
						
						if($wr_out_warehouse){
							$sql_search .= " AND wr_out_warehouse = '{$wr_out_warehouse}' ";
						}

						if($wr_in_warehouse){
							$sql_search .= " AND wr_in_warehouse = '{$wr_in_warehouse}' ";
						}
						
						$sql_order = " order by seq desc";
						$sql = " select count(*) as cnt {$sql_common} {$sql_search} {$sql_order}";
						$row = sql_fetch($sql);
						$total_count = $row['cnt'];

						$rows = 100;
						$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
						if ($page < 1) {
							$page = 1; // 페이지가 없으면 첫 페이지 (1 페이지)
						}
						$from_record = ($page - 1) * $rows; // 시작 열을 구함
						
						$cur_no = $total_count - $from_record;


						$sql = " select * {$sql_common} {$sql_search} {$sql_order} limit {$from_record}, {$rows} ";
						$rst = sql_query($sql);
						for ($i=0; $row=sql_fetch_array($rst); $i++) {
							
							$item = sql_fetch("select wr_1, wr_subject from g5_write_product where wr_id = '{$row['product_id']}'");
							$mb = get_member($row['mb_id'], 'mb_name');
							$bg = 'bg' . ($i % 2);
						?>
						<li class="<?php echo $bg?>">
							<div class="cnt_left" style="width:200px;text-align:center">
							<?php echo $row['wr_datetime']?></div>
							<div class="cnt_left" style="width:300px"><?php echo $item['wr_1']?></div>
							<div class="cnt_left" style="width:540px"><?php echo $item['wr_subject']?></div>
							<div class="cnt_left" style="width:200px"><?=$storage_arr[$row['wr_out_warehouse']]['code_nm']?> -> <?=$storage_arr[$row['wr_in_warehouse']]['code_nm']?></div>
							<div class="cnt_left" style="text-align:right;width:150px"><?php echo $row['wr_stock']?></div>
							<div class="cnt_left" style="text-align:right;width:200px"><?php echo $mb['mb_name']?>(<?php echo $row['mb_id']?>)</div>
							
							
						</li>
						<?php 
						$cur_no = $cur_no - 1;
						} ?>
						<?php if ($i == 0) { echo '<li class="empty_table">내역이 없습니다.</li>'; } ?>
					</ul>
				</div>
			</div>
		</form>
	</div>
	<div style="clear:both"></div>
<?php //echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, $_SERVER['SCRIPT_NAME'] . '?' . $qstr . '&amp;page='); ?>
</div>

 <div class="bo_sch_wrap">
	<fieldset class="bo_sch">
		<h3>검색</h3>
		<form name="fsearch" method="get">

		<div class="sch_bar" style="margin-top:3px">
			
			<input type="text" name="stx2" value="<?php echo stripslashes($stx2) ?>" required id="stx" class="sch_input" size="25" maxlength="255" placeholder="배송사 검색">
			<button type="submit" value="검색" class="sch_btn"><i class="fa fa-search" aria-hidden="true"></i><span class="sound_only">검색</span></button>
		</div>
		<button type="button" class="bo_sch_cls" title="닫기"><i class="fa fa-times" aria-hidden="true"></i><span class="sound_only">닫기</span></button>
		</form>
	</fieldset>
	<div class="bo_sch_bg"></div>
</div>
<script>
jQuery(function($){
	// 게시판 검색
	$(".btn_bo_sch").on("click", function() {
		$(".bo_sch_wrap").toggle();
	})
	$('.bo_sch_bg, .bo_sch_cls').click(function(){
		$('.bo_sch_wrap').hide();
	});
});
</script>
<script>
function fnSearch(){
	var wr_out_warehouse = $("#wr_out_warehouse").val();
	var wr_in_warehouse = $("#wr_in_warehouse").val();
	document.location.href="?wr_out_warehouse="+wr_out_warehouse+"&wr_in_warehouse="+wr_in_warehouse;
}
</script>


<?php 
include_once(G5_THEME_PATH.'/tail.php');