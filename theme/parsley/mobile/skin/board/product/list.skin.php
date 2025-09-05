<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// 선택옵션으로 인해 셀합치기가 가변적으로 변함
$colspan = 2;

if ($is_checkbox) $colspan++;

foreach($list as $i => $v) {
    $list[$i]['wr_reply_style'] = "padding-left:". ($list[$i]['reply'] ? (strlen($list[$i]['wr_reply'])*10) : '0'). "px";
}

// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
add_stylesheet('<link rel="stylesheet" href="'.$board_skin_url.'/style.css">', 0);


?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
<style>
.select2-container--default .select2-selection--single { height:40px; border:1px solid #d9dee9; background:#f1f3f6 }
.select2-container--default .select2-selection--single .select2-selection__rendered { line-height:38px }
.select2-container--default .select2-selection--single .select2-selection__arrow b { margin-top:4px }

/* .cnt_left {padding:5px 10px; border-right:1px solid #ddd; word-break: text-overflow: ellipsis; overflow: hidden; white-space: nowrap;} */
.cnt_left {padding:5px 10px; border-right:1px solid #ddd; word-break: text-overflow: ellipsis;}
.list_03 li { padding:0 }

.tbl_frm01 th {background:#6f809a;color:#fff;border:1px solid #60718b;font-weight:normal;text-align:center;padding:8px 5px;font-size:0.92em}
.tbl_frm01 td  { border-bottom:1px solid #ddd; }
.tbl_frm01 td input { border:1px solid #ddd; padding:3px; width:100%}
.tbl_frm01 input.readonly { background:#f2f2f2}
@media (max-width:767px){
	.btn_top2 { margin-bottom:5px }
	.stock_list_mtb { margin: 15px 0; font-size:13px }
	.stock_list_mtb th { width:60px}
	.stock_list_mtb td{ padding:10px }
	.stock_list_mtb .stock_table td { width:33.333%;text-align:center; border-right:1px solid #ddd }
}
</style>
<!-- 게시판 목록 시작 -->
<div id="bo_list">
	<div class="bo_list_innr">
		<h2 class="board_tit"><?php echo $board['bo_subject']?></h2>
	
		<form name="fboardlist" id="fboardlist" action="<?php echo G5_BBS_URL; ?>/board_list_update.php" onsubmit="return fboardlist_submit(this);" method="post">
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
			    <span>전체 <?php echo number_format($total_count) ?>건</span>
			    <?php echo $page ?> 페이지
			</div>		
			<?php if ($rss_href || $write_href) { ?>
			<ul class="<?php echo isset($view) ? 'view_is_list btn_top' : 'btn_top2';?>">
				<?php if ($rss_href) { ?><li><a href="<?php echo $rss_href ?>" class="btn_b02">RSS</a></li><?php } ?>
				
				<li class="wli_cnt">
		    		<label for="wmb_id" class="sound_only">중량무게1<strong>필수</strong></label>
	    		
					<select name="wmb_id" id="wmb_id" class="frm_input search_sel" onchange="location.href='?bo_table=<?php echo $bo_table?>&sfl=mb_id&stx='+this.value+'&sca=<?php echo $sca?>'";>
						<option value="">담당자 전체</option>
						<?php 
						$mbSql = " select mb_id, mb_name from g5_member order by mb_name asc";
						$mbRst = sql_query($mbSql);
						for($i=0; $mb=sql_fetch_array($mbRst); $i++) {
						?>
						<option value="<?php echo $mb['mb_id']?>" <?php echo get_selected($mb['mb_id'], $stx)?>><?php echo $mb['mb_name']?>(<?php echo $mb['mb_id']?>)</option>
						<?php }?>
					</select>
		    	</li>
			    <?php if ($write_href && $member['mb_1'] != "USA") { ?>
				<li><button type="button" class="btn_b02" onclick=" pop_excel(); "><i class="fa fa-file-excel-o" aria-hidden="true"></i> 엑셀등록</button></li>
				<li><button type="button" class="btn_b02" onclick=" pop_brand(); "><i class="fa fa-user" aria-hidden="true"></i> 브랜드 담당자 일괄변경</button></li>
				
				<?php } ?>
				
			    <?php if ($is_member) { ?>
					<li><a href="<?php echo $write_href ?>" class="btn_b01">제품등록</a></li>
					<!--<li><a href="javascript:;" onclick="fnFeeUpload();" class="btn_b01">수수료 엑셀 등록</a></li>-->
				<?php } ?>
			
				
			    <?php if ($is_admin == 'super' || $is_auth) {  ?>
				<li>
					<button type="button" class="btn_more btn_b04"><span class="sound_only">글쓰기 옵션 더보기</span><i class="fa fa-ellipsis-v" aria-hidden="true"></i></button>
					<?php if ($list_href || $is_checkbox || $write_href) { ?>
			        <ul class="btn_bo_adm">
			        	<?php if ($list_href) { ?>
				        <li><a href="<?php echo $list_href ?>" class="btn_b01 btn">목록</a></li>
				        <?php } ?>
			            <li><button type="submit" name="btn_submit" value="선택삭제" onclick="document.pressed=this.value">선택삭제</button></li>
			            <!--<li><button type="submit" name="btn_submit" value="선택복사" onclick="document.pressed=this.value">선택복사</button></li>
			            <li><button type="submit" name="btn_submit" value="선택이동" onclick="document.pressed=this.value">선택이동</button></li>-->
			        </ul>
			        <?php } ?>
				    <script>
						$(document).ready(function(){
							$(".btn_more").click(function(){
								$(".btn_bo_adm").toggle();
							});
						});
					</script>
				</li>
			    <?php } ?>
			</ul>
			<?php } ?>
		</div>
		
		
	    <div id="bo_li_01">
			<?php if(!is_mobile()){?>
	    	<ul class="list_head">
	    		<?php 
				
				if ($is_checkbox) { ?>
	            <li class="sel">
	                <?php if ($is_checkbox) { ?>
				    <div class="list_chk all_chk">
				        <input type="checkbox" id="chkall" onclick="if (this.checked) all_checked(true); else all_checked(false);">
				        <label for="chkall"><span class="sound_only">현재 페이지 게시물 </span>전체선택</label>
				    </div>
				    <?php } ?>
	            </li>
	            <?php } ?>
	            <li class="date"><?php echo subject_sort_link('wr_datetime', $qstr2, 1) ?>등록일시</a></li>
				<li style="width:10%"><?php echo subject_sort_link('wr_5', $qstr2, 1) ?>대표코드</a></li>
	            <li style="width:20%"><?php echo subject_sort_link('wr_1', $qstr2, 1) ?>SKU</a></li>
            	<li style="width:30%"><?php echo subject_sort_link('wr_subject', $qstr2, 1) ?>제품명</a></li>
            	<li style="width:10%">발주단가</li>
            	<li style="width:10%">수출신고명칭</li>
            	
            	<li style="width:10%">담당자</li>
	        </ul>
			<?php }?>
	        
			<div id="bo_li_01" class="list_03">
		        <ul>
				<?php if(is_mobile()){
				
				for ($i=0; $i<count($list); $i++) { 
                    $obj = sql_fetch("SELECT * FROM g5_write_product WHERE wr_id = '{$list[$i]['wr_id']}' ");
                    
					$mb = get_member($list[$i]['mb_id'], 'mb_name');
					
                    ?>
				<li onclick="location.href='/bbs/write.php?bo_table=product&w=u&wr_id=<?php echo $list[$i]['wr_id']?>'">
				<div class="tbl_frm01 tbl_wrap stock_list_mtb" >
					<table>
						<tr>
							<th>대표코드</th>
							<td><?php echo $list[$i]['wr_5'] ?></td>
						</tr>
						<tr>
							<th>SKU</th>
							<td><?php echo $list[$i]['wr_1'] ?></td>
						</tr>
						<tr>
							<th>제품명</th>
							<td><?php echo $list[$i]['wr_subject'] ?></td>
						</tr>
						<tr>
							<th>발주단가</th>
							<td><?php echo $list[$i]['wr_22'] ?></td>
						</tr>
						<tr>
							<th>신고명칭</th>
							<td><?php echo $list[$i]['wr_3'] ?></td>
						</tr>
						<tr>
							<th>담당자</th>
							<td><?php echo $mb['mb_name'] ?></td>
						</tr>
						
						
					</table>
					</div>
				</li>
				<?php 
				}
				if (count($list) == 0) { echo '<li class="empty_table">게시물이 없습니다.</li>'; }
				} else {?>
	       
		            <?php for ($i=0; $i<count($list); $i++) {

						$mb = get_member($list[$i]['mb_id'], 'mb_name');
					?>
		            <li class="<?php if ($list[$i]['is_notice']) echo "bo_notice"; ?>"  style="cursor:pointer">
		            	
		                <?php if ($is_checkbox) { // 게시글별 체크박스?>
		                <span class="sel bo_chk li_chk">
		                	<label for="chk_wr_id_<?php echo $i ?>"><span class="sound_only"><?php echo $list[$i]['subject'] ?></span></label>
		                    <input type="checkbox" name="chk_wr_id[]" value="<?php echo $list[$i]['wr_id'] ?>" id="chk_wr_id_<?php echo $i ?>"> 	
		                </span>
		                <?php } ?>
		                
		                <div class="num cnt_left date" onclick="location.href='/bbs/write.php?bo_table=product&w=u&wr_id=<?php echo $list[$i]['wr_id']?>'"><?php echo $list[$i]['datetime'] ?></div>
						<div class="num cnt_left" style="width:10%" onclick="location.href='/bbs/write.php?bo_table=product&w=u&wr_id=<?php echo $list[$i]['wr_id']?>'"><?php echo $list[$i]['wr_5'] ?></div>
		                <div class="num cnt_left" style="width:20%" onclick="location.href='/bbs/write.php?bo_table=product&w=u&wr_id=<?php echo $list[$i]['wr_id']?>'" title="<?php echo $list[$i]['wr_1'] ?>"><?php echo $list[$i]['wr_1'] ?></div>
		                <div class="num cnt_left" style="width:30%" onclick="location.href='/bbs/write.php?bo_table=product&w=u&wr_id=<?php echo $list[$i]['wr_id']?>'"><a href="/bbs/write.php?bo_table=product&w=u&wr_id=<?php echo $list[$i]['wr_id']?>" title="<?php echo $list[$i]['wr_subject']?>"><?php echo $list[$i]['wr_subject'] ?></a></div>
		                <div class="num cnt_left" style="width:10%" onclick="location.href='/bbs/write.php?bo_table=product&w=u&wr_id=<?php echo $list[$i]['wr_id']?>'"><?php echo $list[$i]['wr_22'] ?></div>
		                <div class="num cnt_left" style="width:10%" onclick="location.href='/bbs/write.php?bo_table=product&w=u&wr_id=<?php echo $list[$i]['wr_id']?>'"><?php echo $list[$i]['wr_3'] ?></div>
		                
		                <div class="num cnt_left" style="width:10%" onclick="location.href='/bbs/write.php?bo_table=product&w=u&wr_id=<?php echo $list[$i]['wr_id']?>'"><?php echo $mb['mb_name'] ?>&nbsp;</div>
					
				        <!-- 
				        // 추천, 비추천 
		                <?php if ($is_good) { ?><span class="sound_only">추천</span><i class="fa fa-thumbs-o-up" aria-hidden="true"></i> <?php echo $list[$i]['wr_good'] ?><?php } ?>
		                <?php if ($is_nogood) { ?><span class="sound_only">비추천</span><i class="fa fa-thumbs-o-down" aria-hidden="true"></i> <?php echo $list[$i]['wr_nogood'] ?><?php } ?>
		                -->
		                
				    </li>
				    <?php } ?>
		            <?php if (count($list) == 0) { echo '<li class="empty_table">게시물이 없습니다.</li>'; } ?>
		        
			<?php }?>
			</ul>
		    </div>
	    </div>
		</form>
	</div>
	<div id="bo_li_op">
		<!-- 게시판 검색 시작 { -->
		<fieldset id="bo_sch">
		    <legend>게시물 검색</legend>
		    <form name="fsearch" method="get">
		    <input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
		    <input type="hidden" name="sca" value="<?php echo $sca ?>">
		    <input type="hidden" name="sop" value="and">
		    <label for="sfl" class="sound_only">검색대상</label>
		   
			
		    <input name="stx" value="<?php echo stripslashes($stx) ?>" placeholder="검색어를 입력하세요" required id="stx" class="sch_input" size="15">
		    <button type="submit" value="검색" class="sch_btn"><i class="fa fa-search" aria-hidden="true"></i> <span class="sound_only">검색</span></button>
		    </form>
		</fieldset>
		
		<!-- } 게시판 검색 끝 -->
		<?php if ($rss_href || $write_href) { ?>		
		<ul class="<?php echo isset($view) ? 'view_is_list btn_top' : 'btn_top2';?>">
			<?php if ($rss_href) { ?><li><a href="<?php echo $rss_href ?>" class="btn_b02">RSS</a></li><?php } ?>
			 <?php if ($write_href) { ?><li><button type="button" class="btn_b02" onclick=" pop_excel(); "><i class="fa fa-file-excel-o" aria-hidden="true"></i> 엑셀등록</button></li><?php } ?>
		    <?php if ($write_href) { ?><li><a href="<?php echo $write_href ?>" class="btn_b01">제품등록</a></li><?php } ?>
			
		    <?php if ($is_admin == 'super' || $is_auth) {  ?>
			<li>
				
				<button type="button" class="btn_more2 btn_b04"><span class="sound_only">글쓰기 옵션 더보기</span><i class="fa fa-ellipsis-v" aria-hidden="true"></i></button>
				<?php if ($list_href || $is_checkbox || $write_href) { ?>
		        <ul class="btn_bo_adm2">
		        	<?php if ($list_href) { ?>
			        <li><a href="<?php echo $list_href ?>" class="btn_b01 btn">목록</a></li>
			        <?php } ?>
		            <li><button type="submit" name="btn_submit" value="선택삭제" onclick="document.pressed=this.value">선택삭제</button></li>
		           
		        </ul>
		        <?php } ?>
			    <script>
					$(document).ready(function(){
						$(".btn_more2").click(function(){
							$(".btn_bo_adm2").toggle();
						});
					});
				</script>
			</li>
		    <?php } ?>
		</ul>
		<?php } ?>		
	</div>
</div>

<?php if($is_checkbox) { ?>
<noscript>
<p>자바스크립트를 사용하지 않는 경우<br>별도의 확인 절차 없이 바로 선택삭제 처리하므로 주의하시기 바랍니다.</p>
</noscript>
<?php } ?>

<!-- 페이지 -->
<?php echo $write_pages; ?>

<?php if ($is_checkbox) { ?>
<script>
$(function(){
	$(document).ready(function() {
		$('.search_sel').select2();
		
		$('#bo_li_01').tooltip();
	});
	
})
function all_checked(sw) {
    var f = document.fboardlist;

    for (var i=0; i<f.length; i++) {
        if (f.elements[i].name == "chk_wr_id[]")
            f.elements[i].checked = sw;
    }
}

function fnFeeUpload(){
	window.open("/bbs/pop_prod_fee.php", "pop_prod_fee", "left=50, top=50, width=500, height=550, scrollbars=1");
}

function fboardlist_submit(f) {
    var chk_count = 0;

    for (var i=0; i<f.length; i++) {
        if (f.elements[i].name == "chk_wr_id[]" && f.elements[i].checked)
            chk_count++;
    }

    if (!chk_count) {
        alert(document.pressed + "할 게시물을 하나 이상 선택하세요.");
        return false;
    }

    if(document.pressed == "선택복사") {
        select_copy("copy");
        return;
    }

    if(document.pressed == "선택이동") {
        select_copy("move");
        return;
    }

    if(document.pressed == "선택삭제") {
        if (!confirm("선택한 게시물을 정말 삭제하시겠습니까?\n\n한번 삭제한 자료는 복구할 수 없습니다\n\n답변글이 있는 게시글을 선택하신 경우\n답변글도 선택하셔야 게시글이 삭제됩니다."))
            return false;

        f.removeAttribute("target");
        f.action = g5_bbs_url+"/board_list_update.php";
    }

    return true;
}

// 선택한 게시물 복사 및 이동
function select_copy(sw) {
    var f = document.fboardlist;

    if (sw == 'copy')
        str = "복사";
    else
        str = "이동";

    var sub_win = window.open("", "move", "left=50, top=50, width=500, height=550, scrollbars=1");

    f.sw.value = sw;
    f.target = "move";
    f.action = g5_bbs_url+"/move.php";
    f.submit();
}

function pop_excel(){
	
	window.open("/bbs/pop_product_add.php", "add", "left=50, top=50, width=500, height=550, scrollbars=1");
}
function pop_brand(){
	
	window.open("/bbs/pop_brand_change.php", "pop_brand_change", "left=50, top=50, width=500, height=550, scrollbars=1");
}
</script>
<?php } ?>
<!-- 게시판 목록 끝 -->
