<?php
$mode = "restock";
include_once('./_common.php');

include_once(G5_THEME_PATH.'/head.php');



$cnt = sql_fetch("select SUM(wr_32) as kor, SUM(wr_36) as usa, SUM(wr_37) as tmp  from g5_write_product");
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
.tbl_frm01 td  { border-bottom:1px solid #ddd; }
.tbl_frm01 td input { border:1px solid #ddd; padding:3px; width:100%}
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
.select2-container--default .select2-selection--single { width:100%;height:40px; border:1px solid #d9dee9; background:#f1f3f6 }
.select2-container--default .select2-selection--single .select2-selection__rendered { line-height:38px }
.select2-container--default .select2-selection--single .select2-selection__arrow b { margin-top:4px }
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
				&nbsp;
			</div>
			   
			  
			</div>		
			<ul class="<?php echo isset($view) ? 'view_is_list btn_top' : 'btn_top2';?>">
				<?php if ($rss_href) { ?><li><a href="<?php echo $rss_href ?>" class="btn_b02">RSS</a></li><?php } ?>
			   
			    <li>
				
				</li>
			    <li><button type="button" class="btn_b02 btn_bo_sch"><i class="fa fa-search" aria-hidden="true"></i> 검색</button></li>
			  
			 
			
				
			</ul>
		</div>
	    <div id="bo_li_01" style="clear:both;overflow-x:scroll;overflow-y:hidden">
	    	<ul class="list_head" style="width:1150px;position:sticky;top:0;background:#fff;z-index:99999;" >
	            <li style="width:100px">순번</li>
	            <li style="width:250px"><?php echo subject_sort_link('wr_1', $qstr2, 1) ?>SKU</a></li>
            	<li style="width:300px"><?php echo subject_sort_link('wr_subject', $qstr2, 1) ?>상품명</a></li>
            	<li style="width:100px"><?php echo subject_sort_link('wr_32', $qstr2, 1) ?>보낸 수량</a></li>
            	<li style="width:100px"><?php echo subject_sort_link('wr_36', $qstr2, 1) ?>받은 수량</a></li>
            	<li style="width:150px"><?php echo subject_sort_link('wr_37', $qstr2, 1) ?>입고 랙</a></li>
            	<li style="width:150px">관리</li>
	        </ul>
	        <div id="bo_li_01" class="list_03" >
		        <ul style="width:1170px;">
		            <?php 
				
					
					
					$sql_common = " from g5_write_product ";
					$sql_search = " where (1) ";

						if($stx2) {
						$sql_search .= " and wr_subject LIKE '%$stx2%' or (wr_1 LIKE '%{$stx2}%' or wr_27 LIKE '%{$stx2}%' or wr_28 LIKE '%{$stx2}%' or wr_29 LIKE '%{$stx2}%' or wr_30 LIKE '%{$stx2}%' or wr_31 LIKE '%{$stx2}%')  ";
					}
					if (!$sst) {
						$sst  = "wr_id";
						$sod = "desc";
					}
					
					if($sst == "stock")
						$sst = "(wr_32+wr_36+wr_37)";
					
					$sql_order = " order by $sst $sod ";
					$sql = " select count(*) as cnt {$sql_common} {$sql_search} {$sql_order} limit 15";
					$row = sql_fetch($sql);
					$total_count = 15;

					$rows = 15;
					$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
					if ($page < 1) {
						$page = 1; // 페이지가 없으면 첫 페이지 (1 페이지)
					}
					$from_record = ($page - 1) * $rows; // 시작 열을 구함
					
					$cur_no = $total_count - $from_record;


					$sql = " select * {$sql_common} {$sql_search} {$sql_order} limit {$from_record}, {$rows} ";
					
					$rst = sql_query($sql);
					for ($i=0; $row=sql_fetch_array($rst); $i++) {
						
						$rand = rand(1,15);
					?>
		            <li class="modify" data="<?php echo $row['seq']?>">
		            	
		              
		                
		                <div class="num cnt_left" style="width:100px"><?php echo $cur_no  ?></div>
		                <div class="cnt_left" style="width:250px"><?php echo $row['wr_1'] ?></div>
		                <div class="cnt_left" style="width:300px;" title="<?php echo $row['wr_subject'] ?>"><?php echo $row['wr_subject'] ?></div>
		                <div class="cnt_left" style="width:100px;text-align:right">
						<?php echo $rand?></div>
						 <div class="cnt_left" style="width:100px;text-align:right"><input type="text" name="wr_36" class="wr_36 frm_input" style="width:100%;text-align:right" value="<?php echo $rand?>"></div>
						 <div class="cnt_left" style="width:150px;text-align:center">
							<select name="" class="wr_rack" required style="width:100px">
							<option value="">랙 선택</option>
							<?php 
							$sql_common = " from g5_rack ";
							$sql_search = " where gc_warehouse = '3000' and gc_use = 1 order by gc_name asc";
							$sql = " select * {$sql_common} {$sql_search}  ";
							$result = sql_query($sql);
							for($a=0; $rack=sql_fetch_array($result); $a++) {
							
								
							?>
							<option value="<?php echo $rack['seq']?>"><?php echo $rack['gc_name']?></option>
							<?php }?>
						</select>
						 </div>
		                <div class="cnt_left" style="width:150px;text-align:center">
							<button type="button" class="btn btn_b01" >입고하기</button>
							
						
						</div>
				    </li>
				    <?php 
					$cur_no = $cur_no - 1;
					} ?>
		            <?php if (count($i) == 0) { echo '<li class="empty_table">내역이 없습니다.</li>'; } ?>
		        </ul>
		    </div>
	    </div>
		
		</form>
	
	</div>
	
<?php //echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, $_SERVER['SCRIPT_NAME'] . '?' . $qstr . '&amp;page='); ?>
</div>

 <div class="bo_sch_wrap">
	<fieldset class="bo_sch">
		<h3>검색</h3>
		<form name="fsearch" method="get">

		<div class="sch_bar" style="margin-top:3px">
			
			<input type="text" name="stx2" value="<?php echo stripslashes($stx2) ?>" required id="stx" class="sch_input" size="25" maxlength="255" placeholder="SKU 또는 상품명으로 검색">
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



$(function() {
	$(document).ready(function() {
		$('.wr_rack').select2();
	});
	
	$('#sorting_box').bind('change', function(){
		
		let sort = $(this).val();
		
		if(sort == "default") {
			location.href = '?sst=wr_id&sod=desc&stx2=<?php echo $stx2?>';
		} else if(sort == "up") {
			location.href = '?sst=stock&sod=desc&stx2=<?php echo $stx2?>';
		} else if(sort == "down") {
			location.href = '?sst=stock&sod=asc&stx2=<?php echo $stx2?>';
		}
	})
	
	$('.save_btn').bind('click', function() {
		
		let id = $(this).attr('data');
		let stock1 = $(this).closest('li').find('.wr_32').val();
		let stock2 = $(this).closest('li').find('.wr_36').val();
		let stock3 = $(this).closest('li').find('.wr_37').val();
		
		$.post('./stock_update.php', { wr_id : id, stock1 : stock1, stock2 : stock2, stock3 : stock3 }, function(data) {
			if(data == "y") {
				alert('재고수량이 저장되었습니다.');
			} else {
				alert('처리 중 오류가 발생했습니다.');
			}
		})
		
	});
	
	$('.view_btn').bind('click', function() {
		
		let id = $(this).attr('data');
		var _width = '1150';
	    var _height = '800';
	 
	    var _left = Math.ceil(( window.screen.width - _width )/2);
	    var _top = Math.ceil(( window.screen.height - _height )/2); 
	
		window.open("./pop_rack.php?wr_id="+id, "pop_rack"+id, "left="+_left+", top="+_top+", width="+_width+", height="+_height+", scrollbars=1");
		
		return false;
	});
	
	$('.rack_form').bind('click', function() {
		
		var _width = '700';
	    var _height = '600';
	 
	    var _left = Math.ceil(( window.screen.width - _width )/2);
	    var _top = Math.ceil(( window.screen.height - _height )/2); 
	
		window.open("./pop_rack_form.php", "pop_rack_form", "left="+_left+", top="+_top+", width="+_width+", height="+_height+", scrollbars=1");
		
		return false;
	});
	

	
})

</script>


<?php 
include_once(G5_THEME_PATH.'/tail.php');