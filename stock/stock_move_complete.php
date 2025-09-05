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
		<h2 class="board_tit">재고이관 처리</h2>
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
			<div class="bo_list_total">
				<div class="local_ov01 local_ov">
					<span class="btn_ov01">
						<span class="ov_txt"></span>
						<span class="ov_num"><?=(!$wr_out_warehouse)?"전체":$storate_arr[$wr_out_warehouse]['code_nm']?> > <?=(!$wr_in_warehouse)?"전체":$storate_arr[$wr_in_warehouse]['code_nm']?></span>
					</span>
				</div>
			</div>
			<ul class="btn_top2">
				<?php if ($rss_href) { ?><li><a href="<?php echo $rss_href ?>" class="btn_b02">RSS</a></li><?php } ?>
				<li><button type="button" class="btn_b02 btn_bo_sch"><i class="fa fa-search" aria-hidden="true"></i> 검색</button></li>
			</ul>
		</div>
	   
	    <div id="bo_li_01" style="">
	    	
	    	<ul class="list_head" style="width:100%;min-width:max-content;position:sticky;top:0;background:#fff;z-index:2;" >
	            <li style="width:200px">접수일시/이관일시</li>
	            <li style="width:300px">SKU</li>
	            <li style="width:350px">상품명</li>
				<li style="width:150px">이관</li>
	            <li style="width:100px">이관수량</li>
	            <li style="width:100px">처리자</li>
	            <li style="width:150px">입고 랙 선택</li>
	            <li style="width:120px">관리</li>
	        </ul>
	        <div id="bo_li_01" class="list_03" >
		        <ul style="width:100%;min-width:max-content;">
		            <?php 
					$sql_common = " from g5_stock_move a LEFT JOIN g5_write_product b ON(a.product_id = b.wr_id) ";
					$sql_search = " where 1=1 ";

					if($wr_out_warehouse){
						$sql_search .= " AND a.wr_out_warehouse = '{$wr_out_warehouse}' ";
					}
					if($wr_in_warehouse){
						$sql_search .= " AND a.wr_in_warehouse = '{$wr_in_warehouse}' ";
					}
					
					if($complete == 1)
						$sql_search .= " and a.wr_state = 1 ";
					else if($complete == 2)
						$sql_search .= " and a.wr_state = 0 ";
					
					if($stx)
						$sql_search .= " and (b.wr_1 LIKE '%{$stx}%' or b.wr_subject LIKE '%{$stx}%')";
					
					$sql_order = " order by a.seq desc, a.wr_state desc";
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


					$sql = " select a.*, b.wr_id, b.wr_subject {$sql_common} {$sql_search} {$sql_order} limit {$from_record}, {$rows} ";
					
					$rst = sql_query($sql);
					for ($i=0; $row=sql_fetch_array($rst); $i++) {
						
						$item = sql_fetch("select wr_1, wr_subject from g5_write_product where wr_id = '{$row['product_id']}'");
						$mb = get_member($row['mb_id2'], 'mb_name');
						$bg = 'bg' . ($i % 2);
					?>
					<li class="<?php echo $bg?>">
		            	<div class="cnt_left" style="width:200px;text-align:center">
						<?php echo $row['wr_datetime']?></div>
		            	<div class="cnt_left" style="width:300px"><?php echo $item['wr_1']?></div>
		            	<div class="cnt_left" style="width:350px"><?php echo $item['wr_subject']?></div>
						<div class="cnt_left" style="width:150px"><?=$storage_arr[$row['wr_out_warehouse']]['code_nm']?> > <?=$storage_arr[$row['wr_in_warehouse']]['code_nm']?></div>
		            	<div class="cnt_left wr_stock" style="text-align:right;width:100px"><?php echo $row['wr_stock']?></div>
		            	<div class="cnt_left" style="text-align:right;width:100px"><?php echo $mb['mb_name']?>&nbsp;</div>
		            	<div class="cnt_left" style="text-align:center;width:150px">
							
							<?php if($row['wr_state'] == 1){
								
								$rack_name = sql_fetch("select gc_name from g5_rack where seq = '{$row['wr_rack']}'");
								
								echo $rack_name['gc_name'];
							?>
							
							<?php } else {?>
							<select name="" class="wr_rack frm_input" required style="width:100px">
							<option value="">랙 선택</option>
							<?php 
							$sql_common = " from g5_rack ";
							
							$sql_search = " where gc_warehouse = '{$row['wr_in_warehouse']}' and gc_use = 1 order by gc_name asc";
							
							$sql = " select * {$sql_common} {$sql_search}  ";
							$result = sql_query($sql);
							for($a=0; $rack=sql_fetch_array($result); $a++) {
							?>
							<option value="<?php echo $rack['seq']?>"><?php echo $rack['gc_name']?></option>
							<?php }
							
							}?>
						</select>
						
						</div>
		            	 <div class="cnt_left" style="width:120px;text-align:center">
							<?php if($row['wr_state'] == 1){?>
							<strong style="color:green;line-height:1.0em" title="<?php echo $row['wr_state_date']?>">이관완료</strong>
							<?php } else {?>
							<button type="button" class="btn btn_b01 save_btn" data="<?php echo $row['seq']?>" >이관완료</button>
							<?php }?>
						</div>
		            	
						
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
<?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, $_SERVER['SCRIPT_NAME'] . '?' . $qstr . '&amp;page='); ?>
</div>

 <div class="bo_sch_wrap">
	<fieldset class="bo_sch">
		<h3>검색</h3>
		<form name="fsearch" method="get">
		<input type="hidden" name="mode" value="<?php echo $mode?>">
		<div style="border:1px solid #ddd; margin-bottom:20px; padding:10px">
			<label><input type="radio" name="complete" value="0" <?php echo get_checked($complete, 0)?>> 전체</label>
			<label><input type="radio" name="complete" value="1" <?php echo get_checked($complete, 1)?>> 이관완료건</label>
			<label><input type="radio" name="complete" value="2" <?php echo get_checked($complete, 2)?>> 이관 미완료건</label>
		</div>

		<div style="border:1px solid #ddd; margin-bottom:20px; padding:10px">
			<select name="wr_out_warehouse" id="wr_out_warehouse" class="frm_input">
				<option value="">출발지 선택</option>
				<?foreach($storage_arr as $key => $val){?>
					<?if($key != '7000' && $key != '8000' && $key != '9000'){?>
						<option value="<?=$key?>" <?=get_selected($wr_out_warehouse,$key)?>><?=$val['code_nm']?></option>
					<?}?>
				<?}?>
			</select>
		</div>

		<div style="border:1px solid #ddd; margin-bottom:20px; padding:10px">
			<select name="wr_in_warehouse" id="wr_in_warehouse" class="frm_input">
				<option value="">도착지 선택</option>
				<?foreach($storage_arr as $key => $val){?>
					<?if($key != '7000' && $key != '8000' && $key != '9000'){?>
						<option value="<?=$key?>" <?=get_selected($wr_in_warehouse,$key)?>><?=$val['code_nm']?></option>
					<?}?>
				<?}?>
			</select>
		</div>
		
		<div class="sch_bar" style="margin-top:3px">
			
			<input type="text" name="stx" value="<?php echo urldecode($_GET['stx'])?>" class="frm_input" style="width:100%;" placeholder="상품명 또는 SKU로 검색">
		
		</div>
			<button type="submit" value="검색" class="btn_b01" style="width:49%;margin-top:15px"><i class="fa fa-search" aria-hidden="true"></i> 검색하기</button>
		<button type="button" value="초기화" class="btn_b02" style="width:49%;margin-top:15px;" onclick="location.href='<?php echo $_SERVER['PHP_SELF']?>'"><i class="fa fa-repeat" aria-hidden="true"></i> 검색초기화</button>
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
$(function() {
	$('.save_btn').bind('click', function() {
		
		let id = $(this).attr('data');
		let rack = $(this).closest('li').find('.wr_rack').val();
	
		if(!rack){
			alert('랙을 선택하세요.');
			return false;
		}
		
		$.post('./stock_move_complete_update.php', { seq : id, wr_rack : rack }, function(data) {
			alert(data.message);
			if(data.ret_code == true){
				document.location.reload();
			}
		},'json')
		
	});
})

</script>


<?php 
include_once(G5_THEME_PATH.'/tail.php');