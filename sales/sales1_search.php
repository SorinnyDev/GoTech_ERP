<?php
include_once('./_common.php');
ini_set('memory_limit','-1');
if ($is_guest)
    alert_close('로그인 후 이용하세요.');

include_once(G5_PATH.'/head.sub.php');
add_stylesheet('<link rel="stylesheet" href="'.G5_ADMIN_URL.'/css/admin.css">', 0);

if(!$wr_19_s)
	$wr_19_s = G5_TIME_YMD;

if(!$wr_19_e)
	$wr_19_e = G5_TIME_YMD;

?>

<style>
.not_item td { background:red; color:#fff }
.no_ea1, .no_ea2, .no_ea3, .no_ea4, .no_ea5, .no_ea6, .no_ea7 { font-weight:600; color:red !important}
.bg1 td{ background:#eff3f9 }
</style>

<div class="new_win">
    <h1>매출자료 가져오기</h1>

    <form name="searchFrm" method="get" autocomplete="off">
        <div id="excelfile_upload">
            <label for="excelfile">도메인</label>
            <select name="wr_18">
                <option value="">선택하세요</option>
                <?php echo get_domain_option($_GET['wr_18'])?>
                
            </select>
			
            <label for="excelfile" style="margin-left:20px">재고 유무</label>
            <select name="stock_use">
                <option value="">선택하세요</option>
                <option value="1" <?php echo get_selected($_GET['stock_use'], 1)?>>유</option>
                <option value="2" <?php echo get_selected($_GET['stock_use'], 2)?>>무</option>
            </select>
			
            <label for="excelfile" style="margin-left:20px">창고선택</label>
            <select name="stock_warehouse">
                <option value="">창고전체</option>
                <option value="1000" <?php echo get_selected($_GET['stock_warehouse'], '1000')?>>한국창고</option>
                <option value="3000" <?php echo get_selected($_GET['stock_warehouse'], '3000')?>>미국창고</option>
            </select>
            <label for="excelfile" style="margin-left:20px">US여부</label>
            <select name="us_use">
                <option value="">선택하세요</option>
                <option value="1" <?php echo get_selected($_GET['us_use'], 1)?>>US 주문건만</option>
                <option value="2" <?php echo get_selected($_GET['us_use'], 2)?>>US 주문건 제외</option>
            </select>
            
            <label for="excelfile" style="margin-left:20px">매출기간</label>
            <input type="date" name="wr_19_s" value="<?php echo urldecode($wr_19_s)?>" class="frm_input"> ~
            <input type="date" name="wr_19_e" value="<?php echo urldecode($wr_19_e)?>" class="frm_input">
            
            <button class="btn btn_admin">검색</button>
        </div>
	</form>
	
	<form name="frm" action="./sales1_search_update.php" method="post" onsubmit="return chkfrm(this);">
        <div id="excelfile_upload" class="result_list" style="overflow-x:scroll;max-height:79vh;padding:0">
            <div class="tbl_head01 tbl_wrap" style="width:100%;margin-bottom:70px">
                <table>
                    <thead style="position:sticky;top:0;">
                        <tr>
                            <th style="width:20px"><input type="checkbox" onclick="selectAll(this)"></th>
                            <th style="width:50px">순번</th>
                            <th style="width:150px">매출일자</th>
                            <th style="width:100px">도메인명</th>
                            <th style="width:200px">주문번호</th>
                            <th style="width:150px">SKU</th>
                            <th style="width:300px">상품명</th>
                            <th style="width:90px">한국재고</th>
                            <th style="width:75px">미국재고</th>
                            <th style="width:70px">FBA재고</th>
                            <th style="width:70px">W-FBA재고</th>
                            <th style="width:70px">U-FBA재고</th>
                            <th style="width:93px">한국반품재고</th>
                            <th style="width:93px">미국반품재고</th>
                            <th style="width:70px">주문수량</th>
                            
                            <th style="width:150px">주문자명</th>
                            <th style="width:70px">단가</th>
                            <th style="width:70px">신고가격</th>
                            <th style="width:70px">개당무게</th>
                            <th style="width:70px">총 무게</th>
                            <th style="width:150px">HS CODE</th>
                            <th style="width:150px">나라명</th>
                            <!--<th style="width:150px">관리</th>-->
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if($wr_18)
                            $sql_search .= " and a.wr_domain = '{$wr_18}'";
                        
                        if($wr_19_s && $wr_19_e) {
                            $sql_search .= " and a.wr_date BETWEEN '{$wr_19_s}' AND '{$wr_19_e}' ";
                        }
                        
						if($stock_use == 1) {
							
							if(!$stock_warehouse) { //창고전체 
								$sql_search .= " and ( b.wr_32 >= a.wr_ea OR b.wr_36 >= a.wr_ea OR b.wr_42 >= a.wr_ea OR b.wr_43 >= a.wr_ea OR b.wr_44 >= a.wr_ea OR b.wr_40 >= a.wr_ea OR b.wr_41 >= a.wr_ea )";
							} else if($stock_warehouse == '1000') { //한국창고 
								$sql_search .= " and ( COALESCE(b.wr_32, 0) >= COALESCE(a.wr_ea, 0)) ";
							} else if($stock_warehouse == '3000') { //미국창고 
								$sql_search .= " and ( COALESCE(b.wr_36, 0) >= COALESCE(a.wr_ea, 0) ) ";
							}
							
						} else if($stock_use == 2){
							
							if(!$stock_warehouse) { //창고전체 
								$sql_search .= "and  b.wr_32 < a.wr_ea AND b.wr_36 < a.wr_ea AND b.wr_42 < a.wr_ea AND b.wr_43 < a.wr_ea AND b.wr_44 < a.wr_ea AND b.wr_40 < a.wr_ea AND b.wr_41 < a.wr_ea ";
							} else if($stock_warehouse == '1000') { //한국창고 
								$sql_search .= " and  COALESCE(b.wr_32, 0) < COALESCE(a.wr_ea, 0)  ";
							} else if($stock_warehouse == '3000') { //미국창고 
								$sql_search .= " and COALESCE(b.wr_36, 0) < COALESCE(a.wr_ea, 0)  ";
							}
							
							
							
							
							
						}
						if($us_use == 1) {
							$sql_search .= " and a.wr_country = 'US' ";
						} else if($us_use == 2) {
							$sql_search .= " and a.wr_country != 'US' ";
						}
						
                        if($sql_search) {
                            //$sql = "select * from g5_sales0_list where (1) {$sql_search} and wr_date != '' and wr_chk = 0 order by wr_order_num asc";
                            
							
							$sql = "select a.*, 
							b.wr_subject, b.wr_1, b.wr_32, b.wr_36, b.wr_42, b.wr_43, b.wr_44, b.wr_40, b.wr_41, b.wr_38
							from g5_sales0_list a 
							LEFT JOIN g5_write_product b ON b.wr_id = a.wr_product_id AND b.wr_delYn = 'N'
							where a.wr_code != '' and a.wr_date != '' and a.wr_chk = 0 {$sql_search} group by a.seq order by a.wr_order_num asc";
							
                            $rst = sql_query($sql);
                            for($i=0; $row=sql_fetch_array($rst); $i++) {
                                //$item = sql_fetch("select * from g5_write_product where (wr_1 = '".addslashes($row['wr_code'])."' or wr_27 = '".addslashes($row['wr_code'])."' or wr_28 = '".addslashes($row['wr_code'])."' or wr_29 = '".addslashes($row['wr_code'])."' or wr_30 = '".addslashes($row['wr_code'])."' or wr_31 = '".addslashes($row['wr_code'])."') ");
                                //24.06.28 세트상품제외 같은 주문번호가 있을 경우 패스
								$chk = sql_fetch("select * from g5_sales1_list where wr_order_num = '{$row['wr_order_num']}' and wr_set_sku = ''");
								
								if($chk){
									continue;
								}
								
                                $ea_chk = $row['wr_32'] < $row['wr_ea'] ? 'class="no_ea1"' : "";
                                $ea_chk2 = $row['wr_36'] < $row['wr_ea'] ? 'class="no_ea2"' : "";
                                $ea_chk3 = $row['wr_42'] < $row['wr_ea'] ? 'class="no_ea3"' : "";
                                $ea_chk4 = $row['wr_43'] < $row['wr_ea'] ? 'class="no_ea4"' : "";
                                $ea_chk5 = $row['wr_44'] < $row['wr_ea'] ? 'class="no_ea5"' : "";
                                $ea_chk6 = $row['wr_40'] < $row['wr_ea'] ? 'class="no_ea6"' : "";
                                $ea_chk7 = $row['wr_41'] < $row['wr_ea'] ? 'class="no_ea7"' : "";
                                $new_icon = $row['wr_38']=="Y" ? "<img src='".G5_IMG_URL."/new.png' alt='없음' width='25' height='25'/>" : "";

                                $set = ($row['wr_set_sku']) ? '<br><span style="color:blue">('.$row['wr_set_sku'].')</span>' : "";
                                
                                $bg = 'bg' . ($i % 2);
                            ?>
                                <tr class="<?php echo $bg?>">
                                    <td><input type="checkbox" name="chk_seq[]" value="<?php echo $row['seq']?>" <?php echo $disabled?> class="chkbox"></td>
                                    <td><?php echo ($i+1)?></td>
                                    <td><?php echo substr($row['wr_date'],0,10)?></td>
                                    <td><?php echo $row['wr_domain']?></td>
                                    <td><?php echo $row['wr_order_num']?></td>
                                    <td><?php echo $row['wr_code']?><?php echo $set?></td>
                                    <td style="text-align:left"><?=(!$row['wr_product_nm'])?$row['wr_subject']:$row['wr_product_nm']?></td>
                                    <td style="text-align:right;" <?php echo $ea_chk ?>><?php echo $row['wr_32']?></td>
                                    <td style="text-align:right;" <?php echo $ea_chk2 ?>><?php echo $new_icon.$row['wr_36']?></td>
                                    <td style="text-align:right;" <?php echo $ea_chk3 ?>><?php echo $row['wr_42']?></td>
                                    <td style="text-align:right;" <?php echo $ea_chk4 ?>><?php echo $row['wr_43']?></td>
                                    <td style="text-align:right;" <?php echo $ea_chk5 ?>><?php echo $row['wr_44']?></td>
                                    <td style="text-align:right;" <?php echo $ea_chk6 ?>><?php echo $row['wr_40']?></td>
                                    <td style="text-align:right;" <?php echo $ea_chk7 ?>><?php echo $row['wr_41']?></td>
                                    <td style="text-align:right"><?php echo $row['wr_ea']?></td>
                                    
                                    <td><?php echo $row['wr_mb_name']?></td>
                                    <td style="text-align:right"><?php echo $row['wr_danga']?></td>
                                    <td style="text-align:right"><?php echo $row['wr_singo']?></td>
                                    <td><?php echo $row['wr_weight1']?></td>
                                    <td><?php echo $row['wr_weight2']?></td>
                                    <td><?php echo $row['wr_hscode']?></td>
                                    <td><?php echo $row['wr_deli_country']?></td>
                                    <!--<td><button type="button" data="<?php echo $row['seq']?>" class="del_btn btn_b01" style="background:#9b2525;padding:0 8px; height:30px; line-height:30px">완전삭제</button></td>-->
                                </tr>
                        <?php 
                            }
                        
                        }
                        ?>
                    </tbody>
                    
                    
                </table>
            </div>
        </div>
        <input type="hidden" name="act" value="">
        <div class="win_btn btn_confirm" style="position:fixed;bottom:0;width:100%;background:#fff;border-top:1px solid #ddd;margin-bottom:0">
            발주적용 일자 <input type="date" name="wr_date2" value="<?php echo G5_TIME_YMD?>" placeholder="발주 적용일자" required>
            <input type="submit" value="발주생성" class="btn_submit btn" onclick="document.pressed=this.value">
            <input type="submit" value="한국창고 출고" class="btn_submit btn" style="background:#2a7eba" onclick="document.pressed=this.value">
            <input type="submit" value="미국창고 출고" class="btn_submit btn" style="background:#ba2a8e" onclick="document.pressed=this.value">
            <input type="submit" value="FBA창고 출고" class="btn_submit btn" style="background:#DC7100" onclick="document.pressed=this.value">
            <input type="submit" value="W-FBA창고 출고" class="btn_submit btn" style="background:#99C103" onclick="document.pressed=this.value">
            <input type="submit" value="U-FBA창고 출고" class="btn_submit btn" style="background:#03C1A4" onclick="document.pressed=this.value">
            <input type="submit" value="한국반품창고 출고" class="btn_submit btn" style="background:#F46262" onclick="document.pressed=this.value">
            <input type="submit" value="미국반품창고 출고" class="btn_submit btn" style="background:#ABBCFF" onclick="document.pressed=this.value">
            <button type="button" onclick="window.close();" class="btn_close btn">닫기</button>
        </div>
    </form>
</div>

<script>
$(function(){
	$('.del_btn').bind('click', function(){
		if(confirm('정말 해당 매출자료 완전 삭제하시겠습니까?\n삭제 후 데이터 복구는 불가능합니다.')) {
			let el = $(this).closest('tr');
			
			$.post('./sales1_delete.php', { seq : $(this).attr('data') }, function(data) {
				
				if(data == "y") {
					el.remove();
					alert('매출정보 완전 삭제되었습니다.');
				} else {
					alert('처리 중 오류가 발생했습니다.');
				}
				$('#result_form').html(data);
			})
			
		} else {
			return false;
		}
	})
	
	$('.chkbox').bind('click', function(){
		let stat = $(this).is(':checked');
		
		if(stat) {
			$(this).closest('tr').find('td').addClass('selected_line');
		} else {
			$(this).closest('tr').find('td').removeClass('selected_line');
		}
	})
})
function add_pop(sku,pname,wr_id) {
	
	window.open("/bbs/write.php?bo_table=product&sku="+sku+"&pname="+pname+"&swr_id="+wr_id, "add_item", "left=50, top=50, width=550, height=650, scrollbars=1");
	
}
function selectAll(selectAll)  {
  const checkboxes 
       = document.getElementsByName('chk_seq[]');
  
  checkboxes.forEach((checkbox) => {
	  if(checkbox.disabled == true) {
		  
	  } else {
    checkbox.checked = selectAll.checked;
	  }
  })
  
  $('.chkbox').each(function(){
		let stat = $(this).is(':checked');
	
		if(stat) {
			$(this).closest('tr').find('td').addClass('selected_line');
		} else {
			$(this).closest('tr').find('td').removeClass('selected_line');
		}
	})
}
	
function chkfrm(f){
	f.act.value = document.pressed;
	
	if (document.pressed == "발주생성") {
		
		if (!confirm("선택한 매출자료를 발주생성 하시겠습니까?")) {
			return false;
		}
	}
	
	if (document.pressed == "한국창고 출고") {
		let stat = false;
		$('input:checkbox[name="chk_seq[]"]').each(function(){
			
			if($(this).is(':checked')) {
				let chk = $(this).closest('tr').find('.no_ea1');
				
				if(chk.length) {
					stat = true;
				}
			}
			
		})
		
		if(stat == true) {
			alert('선택하신 매출자료 중 [한국재고]가 부족한 제품이 있어 [한국창고 출고]가 불가합니다.');
			return false;
		}
	
		
		if (!confirm("선택한 매출자료를 [한국창고]에서 출고등록 하시겠습니까?")) {
			return false;
		}
		
	}
	
	if (document.pressed == "미국창고 출고") {
		let stat = false;
		$('input:checkbox[name="chk_seq[]"]').each(function(){
			
			if($(this).is(':checked')) {
				let chk = $(this).closest('tr').find('.no_ea2');
				
				if(chk.length) {
					stat = true;
				}
			}
			
		})
		
		if(stat == true) {
			alert('선택하신 매출자료 중 [미국재고]가 부족한 제품이 있어 [미국창고 출고]가 불가합니다.');
			return false;
		}
		
		if (!confirm("선택한 매출자료를 미국창고에서 출고등록 하시겠습니까?")) {
			return false;
		}
	}

    if (document.pressed == "FBA창고 출고") {
        if("<?=$member['mb_id']?>"!="test"){
            // alert("사용X 테스트 진행중.");
            // return false;
        }

		let stat = false;
		$('input:checkbox[name="chk_seq[]"]').each(function(){
			
			if($(this).is(':checked')) {
				let chk = $(this).closest('tr').find('.no_ea3');
				
				if(chk.length) {
					stat = true;
				}
			}
			
		})
		
		if(stat == true) {
			alert('선택하신 매출자료 중 [FBA재고]가 부족한 제품이 있어 [FBA창고 출고]가 불가합니다.');
			return false;
		}
		
		if (!confirm("선택한 매출자료를 FBA창고에서 출고등록 하시겠습니까?")) {
			return false;
		}
	}

    if (document.pressed == "W-FBA창고 출고") {
        if("<?=$member['mb_id']?>"!="test"){
            // alert("사용X 테스트 진행중.");
            // return false;
        }

		let stat = false;
		$('input:checkbox[name="chk_seq[]"]').each(function(){
			
			if($(this).is(':checked')) {
				let chk = $(this).closest('tr').find('.no_ea4');
				
				if(chk.length) {
					stat = true;
				}
			}
			
		})
		
		if(stat == true) {
			alert('선택하신 매출자료 중 [W-FBA재고]가 부족한 제품이 있어 [W-FBA창고 출고]가 불가합니다.');
			return false;
		}
		
		if (!confirm("선택한 매출자료를 W-FBA창고에서 출고등록 하시겠습니까?")) {
			return false;
		}
	}


    if (document.pressed == "U-FBA창고 출고") {
        if("<?=$member['mb_id']?>"!="test"){
            // alert("사용X 테스트 진행중.");
            // return false;
        }

		let stat = false;
		$('input:checkbox[name="chk_seq[]"]').each(function(){
			
			if($(this).is(':checked')) {
				let chk = $(this).closest('tr').find('.no_ea5');
				
				if(chk.length) {
					stat = true;
				}
			}
			
		})
		
		if(stat == true) {
			alert('선택하신 매출자료 중 [U-FBA재고]가 부족한 제품이 있어 [U-FBA창고 출고]가 불가합니다.');
			return false;
		}
		
		if (!confirm("선택한 매출자료를 U-FBA창고에서 출고등록 하시겠습니까?")) {
			return false;
		}
	}

    
    if (document.pressed == "한국반품창고 출고") {
        if("<?=$member['mb_id']?>"!="test"){
            alert("사용X 테스트 진행중.");
            return false;
        }

		let stat = false;
		$('input:checkbox[name="chk_seq[]"]').each(function(){
			
			if($(this).is(':checked')) {
				let chk = $(this).closest('tr').find('.no_ea5');
				
				if(chk.length) {
					stat = true;
				}
			}
			
		})
		
		if(stat == true) {
			alert('선택하신 매출자료 중 [한국반품재고]가 부족한 제품이 있어 [한국반품창고 출고]가 불가합니다.');
			return false;
		}
		
		if (!confirm("선택한 매출자료를 한국반품창고에서 출고등록 하시겠습니까?")) {
			return false;
		}
	}

    
    if (document.pressed == "미국반품창고 출고") {
        if("<?=$member['mb_id']?>"!="test"){
            alert("사용X 테스트 진행중.");
            return false;
        }

		let stat = false;
		$('input:checkbox[name="chk_seq[]"]').each(function(){
			
			if($(this).is(':checked')) {
				let chk = $(this).closest('tr').find('.no_ea5');
				
				if(chk.length) {
					stat = true;
				}
			}
			
		})
		
		if(stat == true) {
			alert('선택하신 매출자료 중 [미국반품재고]가 부족한 제품이 있어 [미국반품창고 출고]가 불가합니다.');
			return false;
		}
		
		if (!confirm("선택한 매출자료를 미국반품창고에서 출고등록 하시겠습니까?")) {
			return false;
		}
	}
	
}
</script>
<?php
include_once(G5_PATH.'/tail.sub.php');