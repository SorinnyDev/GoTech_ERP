<?php 
include_once('./_common.php');

if(!$date1) $date1 = G5_TIME_YMD;
if(!$date2) $date2 = G5_TIME_YMD;

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
.bg1 { background:#eff3f9 }
</style>
<div id="bo_list">
	<div class="bo_list_innr">
		<h2 class="board_tit">반품등록</h2>
		<form name="fboardlist" id="fboardlist" action="" onsubmit="return fboardlist_submit(this);" method="post">
		
    
	    <div id="bo_li_top_op">
			<div class="bo_list_total">
			<div class="local_ov01 local_ov">
				<span class="btn_ov01">
					<span class="ov_txt"></span>
					<span class="ov_num">[판매관리] > [출고등록]에서 [출고] 처리 된 데이터가 출력됩니다.</span>
				</span>
				
			</div>
			   
			  
			</div>		
		   
		   <ul class="<?php echo isset($view) ? 'view_is_list btn_top' : 'btn_top2';?>">
				<?php if ($rss_href) { ?><li><a href="<?php echo $rss_href ?>" class="btn_b02">RSS</a></li><?php } ?>
			   
			   
			    <li><button type="button" class="btn_b02 btn_bo_sch"><i class="fa fa-search" aria-hidden="true"></i> 검색</button></li>
			
			</ul>
		</div>
	   
	    <div id="bo_li_01" style="clear:both">
	    	
	    	<ul class="list_head" style="width:100%;min-width:max-content;position:sticky;top:0;background:#fff;z-index:2;" >
	            <li style="width:120px">도메인</li>
	            <li style="width:120px">주문번호</li>
	            <li style="width:120px">출고일자</li>
	            <li style="width:300px">SKU</li>
	            <li style="width:550px">상품명</li>
	            <li style="width:100px">주문수량</li>
	            <li style="width:150px">반품수량</li>
	            <li style="width:120px">관리</li>
	        </ul>
	        <div id="bo_li_01" class="list_03" >
		        <ul style="width:100%;min-width:max-content;">
		            <?php 
					
					if($date1 && $date2)
						$sql_search .= " and wr_date4 BETWEEN '{$date1}' AND '{$date2}'";
					
					
					if(!$sql_search) {
						$sql_search .= "  ";
					}
					
					if($wr_18)
						$sql_search .= " and wr_domain = '$wr_18' ";
					

					if($stx)
						$sql_search .= " and wr_order_num LIKE '%$stx%' ";
					
					if(!$sst && !$sod) {
						$sst = "seq";
						$sod = "desc";
					}
					$sql_order = "order by $sst $sod";
					
					$sql = "select * from g5_sales3_list where wr_release_use = 1  {$sql_search} {$sql_order}";
					//echo $sql;
					$rst = sql_query($sql);
					for ($i=0; $row=sql_fetch_array($rst); $i++) {
						
						$item = sql_fetch("select * from g5_write_product where wr_id = '{$row['wr_product_id']}' ");
						
						//24.01.02 매출등록 > 출고등록 데이터로 교체 됨.
						$return = sql_fetch("select * from g5_return_list where sales3_id = '{$row['seq']}'");
						
						$bg = 'bg' . ($i % 2);
					?>
					<li class="<?php echo $bg?>">
		            	<div class="cnt_left" style="width:120px;text-align:center">
						<?php echo $row['wr_domain']?></div>
		            	<div class="cnt_left" style="width:120px;text-align:center">
						<?php echo $row['wr_order_num']?></div>
						<div class="cnt_left" style="width:120px;text-align:center">
						<?php echo $row['wr_date4']?></div>
		            	<div class="cnt_left" style="width:300px"><?php echo $item['wr_1']?></div>
		            	<div class="cnt_left" style="width:550px"><?php echo $item['wr_subject']?></div>
		            	<div class="cnt_left" style="text-align:right;width:100px"><?php echo $row['wr_ea']?></div>
		            	<div class="cnt_left" style="text-align:center;width:150px">
						<?php if($return){
							echo $return['wr_stock'];
						} else {?>
						
						<input type="text" name="wr_stock" class="frm_input wr_stock" data="<?php echo $row['wr_ea']?>" value="<?php echo $row['wr_ea']?>" max="2" style="width:70px;text-align:right" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
						<?php }?>
						
						</div>
		            
		            	<div class="cnt_left" style="width:120px;text-align:center">
							<?php if($return){?>
							<strong style="color:gray;line-height:1.0em" title="<?php echo $row['wr_state_date']?>">등록완료</strong>
							<?php } else {?>
							<button type="button" class="btn btn_b01 save_btn" data="<?php echo $row['seq']?>" data2="<?php echo $item['wr_id']?>">반품등록</button>
							<?php }?>
						</div>
		            	
						
				    </li>
				    <?php 
					$cur_no = $cur_no - 1;
					} ?>
		            <?php if ($i == 0) { echo '<li class="empty_table">내역이 없습니다. </li>'; } ?>
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
		<input type="hidden" name="mode" value="<?php echo $mode?>">
		<select name="wr_18" style="margin-bottom:15px">
			<option value="">도메인 선택</option>
			<?php echo get_domain_option($_GET['wr_18'])?>
		</select>
		<div class="sch_bar" style="margin-top:3px">
			<input type="date" name="date1" value="<?php echo $date1 ?>" required id="stx" class="sch_input" size="25" maxlength="20" placeholder="" style="width:45%;text-align:center">
			<span style="float:left;display:inline-block;height:38px;line-height:38px; margin:0 5px">~</span>
			<input type="date" name="date2" value="<?php echo stripslashes($date2) ?>" required id="stx" class="sch_input" size="25" maxlength="20" placeholder=" " style="width:45%;text-align:center">
			<input type="text" name="stx" value="<?php echo urldecode($_GET['stx'])?>" class="frm_input" style="width:100%;" placeholder="주문번호로 검색">
		
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
	$('.wr_stock').bind('keyup', function(){
		let max = parseInt($(this).attr('data'));
		let ea = parseInt($(this).val());
		
		if(!ea) {
			alert('반품 된 수량을 입력하세요.');
			$(this).val(max);
			$(this).focus();
			return false;
		}
		
		if(max < ea) {
			alert('주문된 수량 보다 많이 입력하실 수 없습니다.');
			$(this).val(max);
			return false;
		}
		
	})
	
	
	$('.save_btn').bind('click', function() {
		
		let id = $(this).attr('data');
		let wr_id = $(this).attr('data2');
		let stock = $(this).closest('li').find('.wr_stock').val();
	
		if(!stock) {
			alert('반품수량을 입력하세요.');
			return false;
		}
		
		$.post('./return_list_update.php', { seq : id, stock : stock, wr_id : wr_id }, function(data) {
			// console.log(data);
            if(data == "y") {
				alert('반품등록이 완료되었습니다.\n반품등록을 취소하시려면 [반품상품 수령등록]에서 선택삭제 하세요.');
				location.reload();
			} else {
				alert('처리 중 오류가 발생했습니다.');
			}
		})
		
	});
	
	$('.sel_type').bind('change', function(){
		if($(this).val() == 1) {
			location.href = '?mode=1';
		} else if($(this).val() == 2) {
			location.href = '?mode=2';
		}
	})


})

</script>


<?php 
include_once(G5_THEME_PATH.'/tail.php');