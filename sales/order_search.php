<?php 
include_once('./_common.php');

include_once(G5_THEME_PATH.'/head.php');

?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


<link rel="stylesheet" href="/theme/parsley/mobile/skin/board/sales/style.css?ver=2303229">
<style>
.cnt_left {padding:5px 10px; border-right:1px solid #ddd; word-break: text-overflow: ellipsis; overflow: hidden; white-space: nowrap;}
.list_03 li { padding:0 }
.modify { cursor:pointer}
.tbl_frm01 th {background:#6f809a;color:#fff;border:1px solid #60718b;font-weight:normal;text-align:center;padding:8px 5px;font-size:0.92em}
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

.bg1 { background:#eff3f9 }

.modal_view {
    display: none;
    width: 100%;
    height: 100%;
    position: fixed;
    top: 0;
    left: 0;
    z-index: 999;
}

.modal_detail {
    position: absolute;
    top: 30%;
    left: 23%;
    background: #fff;
    text-align: left;
    width: 1400px;
	height: 700px;
    margin-left: -165px;
    margin-top: -180px;
    overflow-y: auto;
    border-radius: 5px;
    -webkit-box-shadow: 1px 1px 18px rgba(0, 0, 0, 0.2);
    -moz-box-shadow: 1px 1px 18px rgba(0, 0, 0, 0.2);
    box-shadow: 1px 1px 18px rgba(0, 0, 0, 0.2);
    border: 1px solid #dde7e9;
    background: #fff;
    border-radius: 3px;
}

.modal_detail .modal_cls {
    position: absolute;
    right: 0;
    top: 0;
    color: #b5b8bb;
    border: 0;
    padding: 12px 15px;
    font-size: 16px;
    background: #fff;
}
</style>
<div id="bo_list">
	<div class="bo_list_innr">
		<h2 class="board_tit">주문건 조회</h2>
	
		
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
				<li class="wli_cnt">
		    		<label for="wmb_id" class="sound_only"><strong>필수</strong></label>
	    		
					<select name="wmb_id" id="wmb_id" class="frm_input search_sel" onchange="location.href='?mb_id='+this.value";>
						<option value="">담당자 전체</option>
						<?php 
						$mbSql = " select mb_id, mb_name from g5_member order by mb_name asc";
						$mbRst = sql_query($mbSql);
						for($i=0; $mb=sql_fetch_array($mbRst); $i++) {
						?>
						<option value="<?php echo $mb['mb_id']?>" <?php echo get_selected($mb['mb_id'], $mb_id)?>><?php echo $mb['mb_name']?>(<?php echo $mb['mb_id']?>)</option>
						<?php }?>
					</select>
		    	</li>
			    <li><button type="button" class="btn_b02 btn_bo_sch" ><i class="fa fa-search" aria-hidden="true"></i> 검색</button></li>
			   <!--<li><button type="button" class="btn_b02 all_delete" style="color:#fff;background:red"> 전체초기화(임시)</button></li>-->
			 
			
				
			</ul>
		</div>
	    <div id="bo_li_01" style="overflow-x:scroll;height:800px">
	    	<ul class="list_head" style="width:2900px;position:sticky;top:0;background:#fff;z-index:99" >
	    	
	           
	            <li style="width:70px"><?php echo subject_sort_link('wr_hit', $qstr2, 1) ?>순번</a></li>
	            <li style="width:100px">도메인명</li>
				<li style="width:150px">주문번호</li>
	            <li style="width:100px">매출일자</li>
	            <li style="width:100px">발주일자</li>
	            <li style="width:100px">입고일자</li>
	            
	            <li style="width:100px">출고일자</li>
				<li style="width:100px">창고이동</li>
	            <li style="width:100px">담당자</li>
	            <li style="width:100px">출고위치</li>
	            <li style="width:100px">매출수량</li>
            	<li style="width:150px">상품코드</li>
            	
            	<li style="width:150px">약칭명</li>
            	<li style="width:250px">상품명칭</li>
            	<li style="width:200px">대표코드</li>
            	
            	<li style="width:70px">수량</li>
            	<li style="width:70px">박스수</li>
            	<li style="width:100px">단가</li>
            	<li style="width:100px">신고가격</li>
            	<li style="width:70px">통화</li>
            	<li style="width:70px">개당무게</li>
            	<li style="width:70px">총무게</li>
            	<li style="width:100px">배송사</li>
            	<li style="width:100px">배송요금</li>
            	<li style="width:100px">주문자ID</li>
            	<li style="width:100px">주문자명</li>
	        </ul>
	        
	        <div id="bo_li_01" class="list_03">
		        <ul style="width:2900px;">
		            <?php 

					if(!$date1 && !$date2 && !$stx){
						$sql_search .= " and wr_date BETWEEN '".G5_TIME_YMD."' AND '".G5_TIME_YMD."'";
					}

					if($date1 && $date2){
						$sql_search .= " and wr_date BETWEEN '".$date1."' AND '".$date2."'";
					}
					
					if($mb_id){
						$sql_search .= " and mb_id = '$mb_id' ";
					}
					
					if($wr_18){
						$sql_search .= " and wr_domain = '{$wr_18}' ";
					}
					
					if($stx){
						$sql_search .= " and wr_order_num LIKE '%$stx%' ";
					}
					
					if($orderchk == 0) {
						
					} else if($orderchk == 1) {
						$sql_search .= " and wr_order_ea > 0 ";
					} else if($orderchk == 2) {
						$sql_search .= " and wr_order_ea = 0  ";
					}
					
					
					if(!$sst && !$sod) {
						$sst = "wr_order_num";
						$sod = "asc";
					}
					$sql_order = "order by $sst $sod";
					
					$sql = "select * from g5_sales0_list where (1) {$sql_search} {$sql_order}";
					$rst = sql_query($sql);
					for ($i=0; $row=sql_fetch_array($rst); $i++) {
						
						$item = sql_fetch("select * from g5_write_product where (wr_1 = '".addslashes($row['wr_code'])."' or wr_27 = '".addslashes($row['wr_code'])."' or wr_28 = '".addslashes($row['wr_code'])."' or wr_29 = '".addslashes($row['wr_code'])."' or wr_30 = '".addslashes($row['wr_code'])."' or wr_31 = '".addslashes($row['wr_code'])."') ");
						
						$sales1 = sql_fetch("select wr_date2 from g5_sales1_list where wr_order_num = '{$row['wr_order_num']}'");
						
						$sales2 = sql_fetch("select seq, wr_date3,wr_warehouse from g5_sales2_list where wr_order_num = '{$row['wr_order_num']}'");
						
						$sales3 = sql_fetch("select wr_date4, wr_warehouse from g5_sales3_list where wr_order_num = '{$row['wr_order_num']}'");
						
						$mb = get_member($item['mb_id']);
						$wr_warehouse = "";
						
						switch($sales2['wr_warehouse']){
							case 1000 :
							$in_warehouse = "한국창고";
							break;
							case 3000 :
							$in_warehouse = "미국창고";
							break;
							case 4000 :
							$in_warehouse = "FBA창고";
							break;
							case 6000 :
							$in_warehouse = "U-FBA창고";
							break;
							case 9000 :
							$in_warehouse = "임시창고";
							break;
						}
						
						switch($sales3['wr_warehouse']){
							case 1000 :
							$wr_warehouse = "한국창고";
							break;
							case 3000 :
							$wr_warehouse = "미국창고";
							break;
							case 4000 :
							$wr_warehouse = "FBA창고";
							break;
							case 6000 :
							$wr_warehouse = "U-FBA창고";
							break;
						}
						/*
						if($sales3['wr_warehouse'] == 1000)
							$wr_warehouse = "한국창고";
						else if($sales3['wr_warehouse'] == 3000)
							$wr_warehouse = "미국창고";
						else if($sales3['wr_warehouse'] == 9000)
							$wr_warehouse = "임시창고";
						*/
						$release_state = "&nbsp;";
						if($row['wr_order_ea'] > 0) {
							
							$release_state = '<i class="fa fa-check" aria-hidden="true" style="color:green"></i>';
						}
						
						$bg = 'bg' . ($i % 2);
					?>
		            <li class="modify <?php echo $bg?>" data="<?php echo $row['seq']?>">
		               
		                <div class="num cnt_left" style="width:70px"><?php echo ($i+1) ?></div>
		               
		                <div class="cnt_left" style="width:100px"><?php echo $row['wr_domain'] ?></div>
						<div class="cnt_left" style="width:150px;"><?php echo $row['wr_order_num'] ?></div>
						<div class="cnt_left" style="width:100px;text-align:center"><?php echo $row['wr_date'] ?></div>
						<div class="cnt_left" style="width:100px;text-align:center"><?php echo $sales1['wr_date2'] ?></div>
						<div class="cnt_left" style="width:100px;text-align:center"><?php echo $sales2['wr_date3'] ?>
						<?php if($sales2['wr_date3']) echo "<br><span style=\"color:#818181\">".$in_warehouse."</span>"; ?>
						</div>	
						
						<div class="cnt_left" style="width:100px;text-align:center"><?php echo $sales3['wr_date4'] ?></div>
						
						<div class="cnt_left" style="width:100px;text-align:center">
						<?php if(!$sales3['wr_date4'] && $in_warehouse == "임시창고") {?>
						<select class="move_warehouse" style="width:100%;background:#fff;height:30px;display:block" data="<?php echo $sales2['seq']?>">
							<option value="">창고선택</option>
							<option value="1000">한국창고</option>
							<option value="3000">미국창고</option>
						</select>
						<?php }?>
						</div>
						
		                <div class="cnt_left" style="width:100px;text-align:center"><?php echo $mb['mb_name'] ?></div>
		                
		                <div class="cnt_left" style="width:100px;text-align:center"><?php echo $wr_warehouse ?></div>
						
		                <div class="cnt_left" style="width:100px;text-align:right"><?php echo $row['wr_ea'] ?></div>
		                <div class="cnt_left" style="width:150px;text-align:center"><?php echo $row['wr_code'] ?></div>
		                
		                <div class="cnt_left" style="width:150px;"><?php echo $item['wr_2'] ?></div>
		                <div class="cnt_left" style="width:250px;"><?php echo $item['wr_subject'] ?></div>
		                <div class="cnt_left" style="width:200px;"><?php echo $row['wr_code'] ?></div>
		                <div class="cnt_left" style="width:70px;text-align:right"><?php echo $row['wr_ea'] ?></div>
		                <div class="cnt_left" style="width:70px;text-align:right"><?php echo $row['wr_box'] ?></div>
		                <div class="cnt_left" style="width:100px;text-align:right"><?php echo $row['wr_danga'] ?></div>
		                <div class="cnt_left" style="width:100px;text-align:right"><?php echo $row['wr_singo'] ?></div>
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
		
		<div style="display:none">
		<h2 style="margin-top:20px; margin-bottom:10px;font-size:14px">발주정보</h2>
		<form id="result_addform">
		<div style="border:1px solid #ddd; width:100%; height:500px" id="result_form">
		<p style="text-align:center; font-size:15px; color:red;padding-top:150px">상단 리스트에서 선택하세요.</p>
		</div>
	
		</div>
	</div>
</div>

<!-- 주문건 확인 팝업-->
<div class="modal_view">
	<div class="modal_detail" id="modal_view_order"></div>
	<div class="bo_sch_bg"></div>
</div>
<!--// 주문건 확인 팝업-->

<div class="bo_sch_wrap">
	<fieldset class="bo_sch" style="padding:10px">
		<h3>검색</h3>
		<form name="fsearch" method="get" >
		<select name="wr_18" style="margin-bottom:15px">
			<option value="">도메인 선택</option>
			<?php echo get_domain_option($_GET['wr_18'])?>
		
		</select>
		
	
		
		<div style="border:1px solid #ddd; margin-bottom:20px; padding:10px">
			<input type="text" name="stx" value="<?php echo urldecode($_GET['stx'])?>" class="frm_input" style="width:100%;" placeholder="주문번호 조회">
		</div>
		
		<label for="stx" style="font-weight:bold">매출일자 조회<strong class="sound_only"> 필수</strong></label>
		<div class="sch_bar" style="margin-top:3px">
			
			<input type="date" name="date1" value="<?php echo $date1 ?>"  id="stx" class="sch_input" size="25" maxlength="20" placeholder="" style="width:45%;text-align:center">
			<span style="float:left;display:inline-block;height:38px;line-height:38px; margin:0 5px">~</span>
			<input type="date" name="date2" value="<?php echo stripslashes($date2) ?>"  id="stx" class="sch_input" size="25" maxlength="20" placeholder=" " style="width:45%;text-align:center">
			
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
	let id = $(this).attr('data');
	var _width = '1500';
    var _height = '850';
 
    var _left = Math.ceil(( window.screen.width - _width )/2);
    var _top = Math.ceil(( window.screen.height - _height )/2); 

	window.open("./sales1_search.php", "sales1_search", "left="+_left+", top="+_top+", width="+_width+", height="+_height+", scrollbars=1");
	
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
	
	$('.move_warehouse').bind('change', function(){
		let seq = $(this).attr('data');
		let warehouse = $(this).val();
		let warehouse_name = "";
		
		if(warehouse == 1000) {
			warehouse_name = "한국창고(1000)";
		} else if(warehouse == 3000) {
			warehouse_name = "미국창고(3000)";
		}
		
		if(confirm('해당 주문건의 창고위치를 ['+warehouse_name+']으로 강제 이동하시겠습니까?\n이동 후 재변경은 안됩니다.')){
			$.post('./ajax.movewarehouse.php', { seq : seq, warehouse : warehouse }, function(data) {
				
				if(data == "y") {
					alert('지정하신 창고로 이동되었습니다.\n해당 페이지에서 새로고침하셔야 변경된 창고로 확인 가능합니다.');
				} else {
					alert('처리 중 오류가 발생하였습니다.');
				}
				
			})
		} else {
			return false;
		}
		
		
	})
	
	$('.all_delete').bind('click', function() {
		
		if(confirm('정말 데이터를 초기화 하시겠습니까?')) {
			location.href = './all_delete.php?table_name=1';
		}
		
		return false;
		
	});

	$('.modify').bind('click', function() {
		$(".bo_sch_bg").show();
		let id = $(this).attr('data');
		//var url = "./sales1_addbox.php";
		var url = "./ajax.order_view.php";
		$.post(url, { seq : id }, function(data) {
			//$('#result_form').html(data);
			$('#modal_view_order').html(data);
			$(".modal_view").toggle();
		},'html')
		
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
	
});

// 모달 닫기
function close_modal(){
	$(".bo_sch_bg").hide();
	$(".modal_view").hide();
	$("#modal_view_order").empty();
}

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

    return true;
}

function fnModalReset(seq){
	let id = seq;
	//var url = "./sales1_addbox.php";
	var url = "./ajax.order_view.php";
	$.post(url, { seq : id }, function(data) {
		$('#modal_view_order').html(data);
	},'html')
}

</script>


<?php 
include_once(G5_THEME_PATH.'/tail.php');