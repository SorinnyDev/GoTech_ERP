<?php 
include_once('./_common.php');
include_once(G5_THEME_PATH.'/head.php');

if($stx) {
	$sql_search .= " and b.wr_subject LIKE '%$stx%' or (b.wr_1 = '{$stx}' or b.wr_27 = '{$stx}' or b.wr_28 = '{$stx}' or b.wr_29 = '{$stx}' or b.wr_30 = '{$stx}' or b.wr_31 = '{$stx}')  ";
}

$cnt = sql_fetch("select count(*) as total from g5_sales2_list");
$total_count = $cnt['total'];
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

.select2-container--default .select2-selection--single { height:40px; border:1px solid #d9dee9; background:#f1f3f6 }
.select2-container--default .select2-selection--single .select2-selection__rendered { line-height:38px }
.select2-container--default .select2-selection--single .select2-selection__arrow b { margin-top:4px }
</style>
<div id="bo_list">
	<div class="bo_list_innr">
		<h2 class="board_tit">임시창고 재고 관리</h2>
		<form name="fboardlist" id="fboardlist" action="" onsubmit="return fboardlist_submit(this);" method="post">
		
		
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
			    <span>&nbsp;</span>
			  
			</div>		
			<ul class="<?php echo isset($view) ? 'view_is_list btn_top' : 'btn_top2';?>">
				<?php if ($rss_href) { ?><li><a href="<?php echo $rss_href ?>" class="btn_b02">RSS</a></li><?php } ?>
			   
			    <li><button type="button" class="btn_b01">일괄 재고이동</button></li>
			    <li><button type="button" class="btn_b02 btn_bo_sch"><i class="fa fa-search" aria-hidden="true"></i> 검색</button></li>
				
			</ul>
		</div>
	    <div id="bo_li_01" style="overflow-x:scroll;height:700px">
	    	<ul class="list_head" style="width:100%;min-width:1576px;position:sticky;top:0;background:#fff;z-index:99999" >
	            <li style="width:50px"><input type="checkbox" name="seq[]" value="<?php echo $row['seq']?>"></li>
	            <li style="width:100px">주문번호</li>
	            <li style="width:300px">SKU</li>
            	<li style="width:450px">상품명</li>
            	<li style="width:80px">재고</li>
            	<li style="width:80px">주문수량</li>
            	<li style="width:150px">이동창고</li>
            	<li style="width:100px">이동수량</li>
            	<li style="width:100px">랙번호</li>
            	<li style="width:120px">관리</li>
	        </ul>
	        
	        <div id="bo_li_01" class="list_03" >
		        <ul style="width:1576px">
		            <?php 
					$sql = "SELECT * FROM g5_temp_warehouse a LEFT JOIN g5_sales2_list b ON(a.sales2_id = b.seq) WHERE b.wr_chk = 0 {$sql_search} order by a.wr_state asc";
					$rst = sql_query($sql);
					for ($i=0; $row=sql_fetch_array($rst); $i++) {
						$item = sql_fetch("select * from g5_write_product where (wr_1 = '".addslashes($row['wr_code'])."' or wr_27 = '".addslashes($row['wr_code'])."' or wr_28 = '".addslashes($row['wr_code'])."' or wr_29 = '".addslashes($row['wr_code'])."' or wr_30 = '".addslashes($row['wr_code'])."' or wr_31 = '".addslashes($row['wr_code'])."') ");
						
						if($item['wr_37'] <= 0) continue;
						
						$order_ea = $row['wr_ea'];
						$order_ea_txt = $row['wr_ea'];
						$order_class = "";
						if($row['wr_state'] == 1) {
							$order_ea = '';
							$order_ea_txt = '<span style="color:green">처리됨</span>';
							$order_class = 'complete';
						}
					?>
		            <li class="modify" data="<?php echo $row['seq']?>">
		                <div class="cnt_left" style="width:50px;text-align:center">
						    <input type="checkbox" name="seq[]" value="<?php echo $row['seq']?>">
						</div>
		                <div class="cnt_left" style="width:100px"><?php echo $row['wr_order_num'] ?></div>
		                <div class="cnt_left" style="width:300px"><?php echo $item['wr_1'] ?></div>
		                <div class="cnt_left" style="width:450px;" title="<?php echo $item['wr_subject'] ?>"><?php echo $item['wr_subject'] ?></div>
		                <div class="cnt_left" style="width:80px;text-align:right"><?php echo $item['wr_37']?></div>
		                <div class="cnt_left" style="width:80px;text-align:right"><?php echo $order_ea_txt?></div>
						<div class="cnt_left" style="width:150px;text-align:center">
                            <select class="frm_input warehouse">
                                <option value="1000" selected>한국창고(1000)</option>
                                <option value="3000">미국창고(3000)</option>
                            </select>
						</div>
						<div class="cnt_left" style="width:100px;text-align:right">
                            <input type="text" name="wr_37" class="wr_37 frm_input" style="width:100%;text-align:right" value="<?php echo $order_ea ?>">
                        </div>
						<div class="cnt_left" style="width:100px;text-align:center">
                            <select name="wr_rack" class="wr_rack frm_input">
                                <option value="">선택</option>
                                <?php echo get_rack_option(1000)?>
                            </select>
						</div>
		                <div class="cnt_left" style="width:120px;text-align:center">
							<button type="button" class="btn btn_b01 save_btn" data="<?php echo $row['tw_seq']?>" data2="<?php echo $item['wr_id']?>" data3="<?php echo $row['sales2_id']?>" data4="<?php echo $order_class?>">재고이동</button>
						</div>
				    </li>
				    <?php } ?>
		            <?php if (count($i) == 0) { echo '<li class="empty_table">내역이 없습니다.</li>'; } ?>
		        </ul>
		    </div>
	    </div>
		
		</form>
	
	</div>
	
</div>

 <div class="bo_sch_wrap">
	<fieldset class="bo_sch">
		<h3>검색</h3>
		<form name="fsearch" method="get">

		<div class="sch_bar" style="margin-top:3px">
			<input type="text" name="stx" value="<?php echo stripslashes($stx) ?>" required id="stx" class="sch_input" size="25" maxlength="20" placeholder="SKU 또는 상품명으로 검색">
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

function pop_excel(){
	
	window.open("./sales0_search.php", "sales0_search", "left=50, top=50, width=1100, height=650, scrollbars=1");
}


$(function() {
	$(document).ready(function() {
		$('.wr_rack').select2();
	});
	
	$('.warehouse').bind('change', function() {
		
		if($(this).val() == "1000") {
			$(this).closest('li').find('.wr_rack').html('<option value="">선택</option><?php echo get_rack_option(1000)?>');
		} else if($(this).val() == "3000") {
			$(this).closest('li').find('.wr_rack').html('<option value="">선택</option><?php echo get_rack_option(3000)?>');
		}
		
	})
		
	$('.save_btn').bind('click', function() {
		
		let wr_id = $(this).attr('data2');
		let id = $(this).attr('data');
		let sid = $(this).attr('data3');
		let warehouse = $(this).closest('li').find('.warehouse').val();
		let stock = $(this).closest('li').find('.wr_37').val();
		let rack = $(this).closest('li').find('.wr_rack').val();
		let state = $(this).attr('data4');
		
        if(!confirm("정말 재고이동 하시겠습니까?")){
            return false;
        }
		
		if(state == "complete") {
			
			if(!rack) {
				alert('처리가 완료된 항목은 랙번호를 필수로 입력하셔야 합니다.');
				return false;
			}
		}
		
		if(!stock || stock <= 0) {
			alert('재고수량을 정확하게 입력하세요.');
			return false;
		}
		
		
		$.post('./temp_stock_update.php', { seq : id, sid : sid, wr_id: wr_id, warehouse : warehouse, stock : stock, rack : rack }, function(data) {
			if(data == "y") {
				alert('재고가 이동되었습니다.');
				location.reload();
			} else {
				alert('처리 중 오류가 발생했습니다.');
			}
		});
		
	});
	

	
})

</script>


<?php 
include_once(G5_THEME_PATH.'/tail.php');