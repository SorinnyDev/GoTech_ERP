<?php 
include_once('./_common.php');

include_once(G5_THEME_PATH.'/head.php');

if(!$date1) $date1 = G5_TIME_YMD;
if(!$date2) $date2 = G5_TIME_YMD;
?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


<link rel="stylesheet" href="/theme/parsley/mobile/skin/board/sales/style.css?ver=2303229">
<style>
.cnt_left {padding:5px 10px; border-right:1px solid #ddd; word-break: text-overflow: ellipsis; overflow: hidden; white-space: nowrap;}
.list_03 li { padding:0 }
.modify { cursor:pointer}
.tbl_frm01 th {background:#6f809a;color:#fff;border:1px solid #60718b;font-weight:normal;text-align:center;padding:8px 5px;font-size:0.92em}
.tbl_frm01 .title{background:#444444;color:#fff;border:1px solid #60718b;font-weight:normal;text-align:center;padding:8px 5px;font-size:0.92em}
.tbl_frm01 td  { border-bottom:1px solid #ddd; }
.tbl_frm01 td input, .tbl_frm01 td select { border:1px solid #ddd; padding:3px; width:100%}
.tbl_frm01 input.readonly { background:#f2f2f2}
</style>
<style>
.select2-container--default .select2-selection--single { height:40px; border:1px solid #d9dee9; background:#f1f3f6 }
.select2-container--default .select2-selection--single .select2-selection__rendered { line-height:38px }
.select2-container--default .select2-selection--single .select2-selection__arrow b { margin-top:4px }

.tbl_frm01 .select2-container--default .select2-selection--single { height:25px; border:1px solid #d9dee9;  }
.tbl_frm01 .select2-container--default .select2-selection--single .select2-selection__rendered { line-height:25px }
.tbl_frm01 .select2-container--default .select2-selection--single .select2-selection__arrow b { margin-top:-6px }
</style>
<div id="bo_list">
	<div class="bo_list_innr">
		<h2 class="board_tit">발주등록</h2>
		<form name="fboardlist" id="fboardlist" action="./sales1_list_update.php" onsubmit="return fboardlist_submit(this);" method="post">
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
			<ul class="<?php echo isset($view) ? 'view_is_list btn_top' : 'btn_top2';?>">
				<?php if ($rss_href) { ?><li><a href="<?php echo $rss_href ?>" class="btn_b02">RSS</a></li><?php } ?>
				<li class="wli_cnt">
		    		<label for="wmb_id" class="sound_only">중량무게1<strong>필수</strong></label>
	    		
					<select name="wmb_id" id="wmb_id" class="frm_input search_sel" onchange="location.href='?mb_id='+this.value";>
						<option value="">담당자 전체</option>
						<?php 
						$mbSql = " select mb_id, mb_name from g5_member where del_yn = 'N' order by mb_name asc";
						$mbRst = sql_query($mbSql);
						for($i=0; $mb=sql_fetch_array($mbRst); $i++) {
						?>
						<option value="<?php echo $mb['mb_id']?>" <?php echo get_selected($mb['mb_id'], $mb_id)?>><?php echo $mb['mb_name']?>(<?php echo $mb['mb_id']?>)</option>
						<?php }?>
					</select>
		    	</li>
				<li><button type="button" class="btn02" onclick="export_excel();" style="height:37px"><i class="fa fa-file-excel-o" aria-hidden="true"></i> 엑셀출력</button></li>
			  <li><button type="button" class="btn_b01" onclick="pop_excel();"><i class="fa fa-database" aria-hidden="true"></i> 매출자료 가져오기</button></li>
			    <li><button type="button" class="btn_b02" onclick="pop_add()"><i class="fa fa-plus" aria-hidden="true"></i> 기타발주 등록</button></li>
			    <li><button type="button" class="btn_b02 btn_bo_sch" ><i class="fa fa-search" aria-hidden="true"></i> 검색</button></li>
			   <!--<li><button type="button" class="btn_b02 all_delete" style="color:#fff;background:red"> 전체초기화(임시)</button></li>-->
			</ul>
		</div>
	    <div id="bo_li_01" style="overflow-x:scroll;height:400px">
	    	<ul class="list_head" style="width:3500px;position:sticky;top:0;background:#fff;z-index:99" >
	    	
	            <li style="width:50px"><input type="checkbox" id="chkall" onclick="if (this.checked) all_checked(true); else all_checked(false);"></li>
	            <li style="width:70px"><?php echo subject_sort_link('wr_hit', $qstr2, 1) ?>순번</a></li>
	            <li style="width:30px"><?php echo subject_sort_link('wr_order_ea', $qstr2, 1) ?>발주</a></li>
	            <li style="width:100px">도메인명</li>
				<li style="width:200px">주문번호</li>
	            <li style="width:100px">매출일자</li>
	            <li style="width:100px">발주일자</li>
	            <li style="width:100px">담당자</li>
	            <li style="width:100px">한국재고</li>
	            <li style="width:100px">미국재고</li>
	            <li style="width:100px">매출수량</li>
            	<li style="width:150px"><?php echo subject_sort_link('wr_code', $qstr2, 1) ?>상품코드</a></li>
            	
            	<li style="width:150px">약칭명</li>
            	<li style="width:250px"><?php echo subject_sort_link('wr_order_num', $qstr2, 1) ?>상품명칭</a></li>
            	<li style="width:200px">대표코드</li>
            	
            	<li style="width:70px">발주수량</li>
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
	        
	        <div id="bo_li_01" class="list_03">
		        <ul style="width:3500px;">
		            <?php 
					if($date1 && $date2) {
						$sql_search .= " and a.wr_date2 BETWEEN '{$date1}' AND '{$date2}'";
					}
					
					if($mb_id) {
						$mb_id = trim($mb_id); 
						$sql_search .= " and b.mb_id = '$mb_id' ";
					}
					
					if($wr_18) {
						$wr_18 = trim($wr_18); 
						$sql_search .= " and a.wr_domain = '{$wr_18}' ";
					}
					
					if($stx) {
						$stx = trim($stx);
						$sql_search .= " and a.wr_order_num LIKE '%$stx%' ";
					}

					if ($search_wrcode) {
						$search_wrcode = trim($search_wrcode);
						$sql_search .= " and a.wr_code = '$search_wrcode'";
					}

					if($dType == "1"){
						$sql_search .= " AND wr_domain NOT IN('".implode("','",$circulation)."')";
					}else if($dType == "2"){
						$sql_search .= " AND wr_domain IN('".implode("','",$circulation)."')";
					}
					
					if($orderchk == 0) {
						
					} else if($orderchk == 1) {
						$sql_search .= " and a.wr_order_ea > 0 ";
					} else if($orderchk == 2) {
						$sql_search .= " and a.wr_order_ea = 0  ";
					} else if($orderchk == 3) {
						$sql_search .= " and a.wr_order_etc != ''  ";
					}
					
					$sql_search .= " and a.wr_warehouse != '3000' ";
					
					if(!$sst && !$sod) {
						$sst = "a.seq";
						$sod = "desc";
					}
					$sql_order = "order by $sst $sod";
					
					$sql = "select * from g5_sales1_list where (1) {$sql_search} {$sql_order}";
					
					
					$sql = "select a.*, b.wr_subject, b.wr_2, b.wr_32, b.wr_36, b.mb_id from g5_sales1_list a 
					LEFT JOIN g5_write_product b ON b.wr_id=a.wr_product_id where (1) {$sql_search} group by a.seq {$sql_order} ";
					
					$rst = sql_query($sql);
					for ($i=0; $row=sql_fetch_array($rst); $i++) {
						
						/*$item = sql_fetch("select * from g5_write_product where (wr_1 = '".addslashes($row['wr_code'])."' or wr_27 = '".addslashes($row['wr_code'])."' or wr_28 = '".addslashes($row['wr_code'])."' or wr_29 = '".addslashes($row['wr_code'])."' or wr_30 = '".addslashes($row['wr_code'])."' or wr_31 = '".addslashes($row['wr_code'])."') ");*/
						$mb = get_member($row['mb_id']);
						$release_state = "&nbsp;";

						if($row['wr_order_ea'] > 0) {
							$release_state = '<i class="fa fa-check" aria-hidden="true" style="color:green"></i>';
						}
						$wr_order_etc = "";

						# 수수료 계산
						if($row['wr_sales_fee']){
							$wr_fee = round(($row['wr_singo'] * $row['wr_sales_fee'] / 100) , 2);
						}else{
							$item_fee = sql_fetch("SELECT * FROM g5_write_product_fee WHERE wr_id='".$row['wr_product_id']."' AND warehouse='".$row['wr_warehouse']."' AND domain='".$row['wr_domain']."'");
							if(!$item_fee['fidx']){
								$wr_fee = "0";
							}else{
								$wr_fee = round(($row['wr_singo'] * $item_fee['product_fee'] /100) , 2);
							}
						}
						
						if($row['wr_order_etc'])
							$wr_order_etc = '<br><strong style="color:red">비고</strong>';
						
						//$sales2 = sql_fetch("select seq, wr_date3,wr_warehouse from g5_sales2_list where wr_order_num = '{$row['wr_order_num']}'");
					?>
		            <li class="modify" data="<?php echo $row['seq']?>">
		                <div class="num cnt_left" style="width:50px"><input type="checkbox" name="seq[]" value="<?php echo $row['seq']?>"></div>
		                <div class="num cnt_left" style="width:70px"><?php echo ($i+1) ?>
						<?php echo $wr_order_etc?>
						</div>
		                <div class="cnt_left" style="width:30px;text-align:center"><?php echo $release_state?></div>
		                <div class="cnt_left" style="width:100px"><?php echo $row['wr_domain'] ?></div>
						<div class="cnt_left" style="width:200px;"><?php echo $row['wr_order_num'] ?></div>
						<div class="cnt_left" style="width:100px;text-align:center"><?php echo $row['wr_date'] ?></div>
						<div class="cnt_left" style="width:100px;text-align:center"><?php echo $row['wr_date2'] ?>
						<?php if($is_admin) echo '<strong style="color:red">'.$sales2['wr_date3'].'</strong>';?>
						</div>
		                <div class="cnt_left" style="width:100px;text-align:center"><?php echo $mb['mb_name'] ?></div>
		                
		                <div class="cnt_left" style="width:100px;text-align:right"><?php echo $row['wr_32'] ?></div>
		                <div class="cnt_left" style="width:100px;text-align:right"><?php echo $row['wr_36'] ?></div>
		                <div class="cnt_left" style="width:100px;text-align:right"><?=($row['wr_etc_chk'] == "0")?$row['wr_ea']:"0"?></div>
		                <div class="cnt_left" style="width:150px;text-align:center"><?php echo $row['wr_code'] ?></div>
		                
		                <div class="cnt_left" style="width:150px;"><?php echo $row['wr_2'] ?></div>
		                <div class="cnt_left" style="width:250px;"><?php echo $row['wr_product_nm'] ?></div>
		                <div class="cnt_left" style="width:200px;"><?php echo $row['wr_code'] ?></div>
		                <div class="cnt_left" style="width:70px;text-align:right"><?php echo $row['wr_order_ea'] ?></div>
		                <div class="cnt_left" style="width:70px;text-align:right"><?php echo $row['wr_box'] ?></div>
		                <div class="cnt_left" style="width:100px;text-align:right"><?php echo $row['wr_danga'] ?></div>
		                <div class="cnt_left" style="width:100px;text-align:right"><?php echo $row['wr_singo'] ?></div>
						<!--<div class="cnt_left" style="width:100px;text-align:right"><?php echo $wr_fee?></div>-->
		                <div class="cnt_left" style="width:70px;text-align:center"><?php echo $row['wr_currency'] ?></div>
		                <div class="cnt_left" style="width:70px;text-align:right"><?php echo $row['wr_weight1'] ?></div>
		                <div class="cnt_left" style="width:70px;text-align:right"><?php echo $row['wr_weight2'] ?></div>
		                <div class="cnt_left" style="width:100px;text-align:center"><?php echo $row['wr_delivery'] ?></div>
		                <div class="cnt_left" style="width:100px;text-align:right"><?php echo $row['wr_delivery_fee'] ?></div>
		                <div class="cnt_left" style="width:100px;"><?php echo $row['wr_mb_id'] ?></div>
		                <div class="cnt_left" style="width:100px;"><?php echo $row['wr_mb_name'] ?></div>
					
				     
				    </li>
				    <?php } ?>
		            <?php if ($i == 0) { echo '<li class="empty_table">내역이 없습니다.</li>'; } ?>
		        </ul>
		    </div>
	    </div>
		
		</form>
		
		<div >
		<h2 style="margin-top:20px; margin-bottom:10px;font-size:14px">발주정보</h2>
		<form id="result_addform">
		<div style="width:100%; height:100%" id="result_form">
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
		<input type="hidden" name="mb_id" value="<?php echo $mb_id?>">
		<select name="wr_18" style="margin-bottom:15px">
			<option value="">도메인 선택</option>
			<?php echo get_domain_option($_GET['wr_18'])?>
		</select>
		
		<div style="border:1px solid #ddd; margin-bottom:20px; padding:10px">
			<label><input type="radio" name="orderchk" value="0" <?php echo get_checked($orderchk, 0)?>> 전체</label>
			<label><input type="radio" name="orderchk" value="1" <?php echo get_checked($orderchk, 1)?>> 발주건</label>
			<label><input type="radio" name="orderchk" value="2" <?php echo get_checked($orderchk, 2)?>> 미발주건</label>
			<label><input type="radio" name="orderchk" value="3" <?php echo get_checked($orderchk, 3)?>> 비고</label>
		</div>

		<div style="border:1px solid #ddd; margin-bottom:20px; padding:10px">
			<label><input type="radio" name="dType" value="0" <?php echo get_checked($dType, 0)?>> 전체</label>
			<label><input type="radio" name="dType" value="1" <?php echo get_checked($dType, 1)?>> 화장품</label>
			<label><input type="radio" name="dType" value="2" <?php echo get_checked($dType, 2)?>> 유통</label>
		</div>
		
		<div style="border:1px solid #ddd; margin-bottom:20px; padding:10px">
			<input type="text" name="stx" value="<?php echo urldecode($_GET['stx'])?>" class="frm_input" style="width:100%;" placeholder="주문번호 조회">
		</div>

		<div style="border:1px solid #ddd; margin-bottom:20px; padding:10px">
			<input type="text" name="search_wrcode" value="<?php echo urldecode($_GET['search_wrcode'])?>" class="frm_input" style="width:100%;" placeholder="상품코드 조회">
		</div>
		
		<label for="stx" style="font-weight:bold">발주일자 조회<strong class="sound_only"> 필수</strong></label>
		<div class="sch_bar" style="margin-top:3px">
			
			<input type="date" name="date1" value="<?php echo $date1 ?>" required id="stx" class="sch_input" size="25" maxlength="20" placeholder="" style="width:45%;text-align:center">
			<span style="float:left;display:inline-block;height:38px;line-height:38px; margin:0 5px">~</span>
			<input type="date" name="date2" value="<?php echo stripslashes($date2) ?>" required id="stx" class="sch_input" size="25" maxlength="20" placeholder=" " style="width:45%;text-align:center">
			
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
$(function(){
	$(document).ready(function() {
		$('.search_sel').select2();
	});
})
function pop_excel(){
    if("<?=$member['mb_id']?>"!="test"){
        // alert("테스트 진행 중 입니다. 잠시 후 사용해주세요.");
        // return false;
    }

	let id = $(this).attr('data');
	var _width = '1620';
    var _height = '850';
 
    var _left = Math.ceil(( window.screen.width - _width )/2);
    var _top = Math.ceil(( window.screen.height - _height )/2); 
	
	window.open("./sales1_search.php", "sales1_search", " width="+screen.width+", height="+screen.height+", scrollbars=1, fullscreen=yes");
	

	
	return false;
	
}
function pop_add(){
	
	let id = $(this).attr('data');
	var _width = '1150';
    var _height = '550';
 
    var _left = Math.ceil(( window.screen.width - _width )/2);
    var _top = Math.ceil(( window.screen.height - _height )/2); 

	window.open("./sales1_etc_form.php", "sales1_add", "left="+_left+", top="+_top+", width="+_width+", height="+_height+", scrollbars=1");
	
	return false;
	

}


$(function() {
	$('.all_delete').bind('click', function() {
		
		if(confirm('정말 데이터를 초기화 하시겠습니까?')) {
			location.href = './all_delete.php?table_name=1';
		}
		
		return false;
		
	});
	$('.modify').bind('click', function() {
		
		$('#result_form').html('');
		let id = $(this).attr('data');
		$('#result_form').html('<center><img style="padding-top:150px" src="/mobile/shop/img/loading.gif"></center>');
		$.post('./sales1_addbox.php', { seq : id }, function(data) {
			$('#result_form').html(data);
		})
		
	});
	
	$(document).on('keyup', '.add_wr_order_ea, .add_wr_order_price, .add_wr_order_fee', function(){
		let ea = parseInt($('.add_wr_order_ea').val());
		let price = parseInt($('.add_wr_order_price').val());
		let fee = parseInt($('.add_wr_order_fee').val());
		let total = price * ea + fee;
		
		
		$('.add_wr_order_total').val(total);
		
	});
	
	$(document).on('click', '#frm_submit', function() {
		$(this).attr('disabled', true);
		 var formData = $("#result_addform").serialize();

        $.ajax({
            cache : false,
            url : "./sales1_addbox_update.php", 
            type : 'POST', 
            data : formData, 
            success : function(data) {
               if(data == "y") {
				   alert('데이터가 정상적으로 저장되었습니다.\n페이지가 새로고침 됩니다.');
				   location.reload();
			   } else {
				   alert('데이터 저장중 오류가 발생했습니다.');
				   return false;
			   }
			   
			   $('#frm_submit').attr('disabled', false);
            }, // success 
    
            error : function(xhr, status) {
                alert(xhr + " : " + status);
            }
        }); 
	})
	
})
function view_item(wr_id) {
	
	window.open("/bbs/write.php?bo_table=product&mode=sales1&w=u&wr_id="+wr_id, "view_item", "left=50, top=50, width=550, height=650, scrollbars=1");
	
}


function all_checked(sw) {
    var f = document.fboardlist;

    for (var i=0; i<f.length; i++) {
        if (f.elements[i].name == "seq[]")
            f.elements[i].checked = sw;
    }
}

function fboardlist_submit(f) {
    var chk_count = 0;

    for (var i=0; i<f.length; i++) {
        if (f.elements[i].name == "seq[]" && f.elements[i].checked)
            chk_count++;
    }

    if (!chk_count) {
        alert(document.pressed + "할 데이터를 하나 이상 선택하세요.");
        return false;
    }


    if(document.pressed == "선택삭제") {

        if (!confirm("선택한 데이터를 정말 삭제하시겠습니까?\n\n[경고] 한번 삭제한 자료는 복구할 수 없습니다."))
            return false;

        f.removeAttribute("target");
        f.action = "./sales1_list_update.php";
    }

    if(document.pressed == "완전삭제") {
        if (!confirm("선택한 데이터를 정말 삭제하시겠습니까?\n\n[경고] 주문번호에 관련된 자료 전부 삭제하는 기능입니다.\n\n한번 삭제한 자료는 복구할 수 없습니다.\n\n이전 과정 전부 삭제됩니다."))
            return false;

        f.removeAttribute("target");
        f.action = "./sales1_list_update.php";
    }

    return true;
}

// 발주정보 엑셀 다운로드
function export_excel(){
	let params = $("form[name='fsearch']").serialize();
    let params2 = $("form[name=fboardlist]").serialize();
	document.location.href="./sales1_excel.php?"+params+"&"+params2;
}

</script>


<?php 
include_once(G5_THEME_PATH.'/tail.php');