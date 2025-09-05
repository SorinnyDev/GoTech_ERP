<?php
include_once('./_common.php');

if ($is_guest)
    alert_close('로그인 후 이용하세요.');

include_once(G5_PATH.'/head.sub.php');
add_stylesheet('<link rel="stylesheet" href="'.G5_ADMIN_URL.'/css/admin.css">', 0);

if(!$wr_19_s)
	$wr_19_s = G5_TIME_YMD;

if(!$wr_19_e)
	$wr_19_e = G5_TIME_YMD;    

if(!$warehouse)
    $warehouse = "1000";

?>
<style>
.not_item td { background:red; color:#fff }
.bg1 td{ background:#eff3f9 }
  th, td {
    position: relative;
  }
 .resize-handle {
    position: absolute;
    width: 5px;
    height: 100%;
	top:0;
    right: -2px;
    cursor: col-resize;
  }
</style>

<div class="new_win">
    <h1>입고자료 가져오기</h1>
    <form name="searchFrm" method="get" autocomplete="off">
        <div id="excelfile_upload">
            <label for="excelfile">도메인</label>
            <select name="wr_18">
                <option value="">선택하세요</option>
                <?php echo get_domain_option($_GET['wr_18'])?>
            </select>
            <?php if($is_admin){?>
			 <label for="excelfile" style="margin-left:20px">재고 유무</label>
            <select name="stock_use">
                <option value="">선택하세요</option>
                <option value="1" <?php echo get_selected($_GET['stock_use'], 1)?>>유</option>
                <option value="2" <?php echo get_selected($_GET['stock_use'], 2)?>>무</option>
            </select>
			
            <!-- <label for="excelfile" style="margin-left:20px">창고선택</label>
           <select name="stock_warehouse">
                <option value="">창고전체</option>
                <option value="1000" <?php echo get_selected($_GET['stock_warehouse'], '1000')?>>한국창고</option>
                <option value="3000" <?php echo get_selected($_GET['stock_warehouse'], '3000')?>>미국창고</option>
            </select>-->
            <label for="excelfile" style="margin-left:20px">US여부</label>
            <select name="us_use">
                <option value="">선택하세요</option>
                <option value="1" <?php echo get_selected($_GET['us_use'], 1)?>>US 주문건만</option>
                <option value="2" <?php echo get_selected($_GET['us_use'], 2)?>>US 주문건 제외</option>
            </select>
			<?php }?>
            <label for="excelfile" style="margin-left:50px">입고기간</label>
            <input type="date" name="wr_19_s" value="<?php echo urldecode($wr_19_s)?>" class="frm_input"> ~
            <input type="date" name="wr_19_e" value="<?php echo urldecode($wr_19_e)?>" class="frm_input">
            <input type="hidden" name="warehouse" value="<?php echo $warehouse?>" >
            <input type="text" name="stx" value="<?php echo urldecode($stx)?>" class="frm_input" placeholder="주문번호 검색">
            <button class="btn btn_admin">검색</button>
        </div>
	</form>
	
	<form name="frm" id="ListFrm" action="./sales3_search_update.php" method="post">
        <div id="excelfile_upload" class="result_list" style="overflow-x:scroll;max-height:90vh;padding:0">
            <div class="tbl_head01 tbl_wrap" style="min-width:1700px;width:100%;margin-bottom:70px">
                <table>
                    <thead style="position:sticky;top:0;z-index:99">
                    <tr>
                        <th style="width:20px"><input type="checkbox" id="select_all_checkbox"></th>
                        <th style="width:50px"><div class="resize-handle"></div>순번</th>
                        <th style="width:80px"><div class="resize-handle"></div>바로출고</th>
                        <th style="width:100px"><div class="resize-handle"></div>매출일자</th>
                        <th style="width:100px"><div class="resize-handle"></div>발주일자</th>
                        <th style="width:100px"><div class="resize-handle"></div>입고일자</th>
                        <th style="width:100px"><div class="resize-handle"></div>도메인명</th>
                        <th style="width:200px"><div class="resize-handle"></div>주문번호</th>
                        <th style="width:150px"><div class="resize-handle"></div>SKU</th>
                        <th style="width:400px"><div class="resize-handle"></div>상품명</th>
                        <th style="width:70px"><div class="resize-handle"></div>현 재고</th>
                        <th style="width:70px"><div class="resize-handle"></div>주문수량</th>
                        <th style="width:100px"><div class="resize-handle"></div>창고(랙번호)</th>
                        
                        <th style="width:150px"><div class="resize-handle"></div>주문자명</th>
                        <th style="width:70px"><div class="resize-handle"></div>단가</th>
                        <th style="width:70px"><div class="resize-handle"></div>신고가격</th>
                        <th style="width:70px"><div class="resize-handle"></div>개당무게</th>
                        <th style="width:70px"><div class="resize-handle"></div>총 무게</th>
                        <th style="width:150px"><div class="resize-handle"></div>HS CODE</th>
                        <th style="width:150px"><div class="resize-handle"></div>나라명</th>
                    </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if($wr_18)
                            $sql_search .= " AND a.wr_domain = '{$wr_18}'";
                        
                        if($wr_19_s && $wr_19_e) {
                            $sql_search .= " AND a.wr_date3 BETWEEN '{$wr_19_s}' AND '{$wr_19_e}' ";
                        }
                        
                        if($stx)
                            $sql_search .= " AND a.wr_order_num LIKE '%$stx%' ";
                        
						if($stock_use == 1) {
							
							if($warehouse == '1000') { //한국창고 
								$sql_search .= " and ( COALESCE(b.wr_32, 0) >= COALESCE(a.wr_ea, 0)) ";
							} else if($warehouse == '3000') { //미국창고 
								$sql_search .= " and ( COALESCE(b.wr_36, 0) >= COALESCE(a.wr_ea, 0) ) ";
							}
							
						} else if($stock_use == 2){
							
							if($warehouse == '1000') { //한국창고 
								$sql_search .= " and  COALESCE(b.wr_32, 0) < COALESCE(a.wr_ea, 0)  ";
							} else if($warehouse == '3000') { //미국창고 
								$sql_search .= " and COALESCE(b.wr_36, 0) < COALESCE(a.wr_ea, 0)  ";
							}
							
							
							
							
							
						}
						if($us_use == 1) {
							$sql_search .= " and a.wr_country = 'US' ";
						} else if($us_use == 2) {
							$sql_search .= " and a.wr_country != 'US' ";
						}
						
						
						
                        if($sql_search) {
                            if($warehouse=="FBA_ALL"){
                                $warehouse_sql=" (a.wr_warehouse = '4000' or a.wr_warehouse = '5000' or a.wr_warehouse = '6000') ";
                            }else{
                                $warehouse_sql=" a.wr_warehouse = '{$warehouse}' ";
                            }
						
                            //$sql = "SELECT * FROM g5_sales2_list a WHERE {$warehouse_sql} AND wr_chk = 0 AND wr_etc_use = 0 {$sql_search} ORDER BY wr_order_num ASC";
                            
							$sql = "select a.*, 
							b.ca_name, b.wr_subject, b.wr_1, b.wr_32, b.wr_36, b.wr_42, b.wr_43, b.wr_44, b.wr_40, b.wr_41, b.wr_38
							from g5_sales2_list a 
							LEFT JOIN g5_write_product b ON a.wr_code IN (b.wr_1, b.wr_27, b.wr_28, b.wr_29, b.wr_30, b.wr_31) 
							where {$warehouse_sql} and a.wr_code != '' and a.wr_date != '' and a.wr_chk = 0  AND a.wr_etc_use = 0 {$sql_search} group by a.seq order by a.wr_order_num asc";
							
							
							
							
                            //echo $sql;
                            $rst = sql_query($sql, true);

                            for($i=0; $row=sql_fetch_array($rst); $i++) {
                                //$item = sql_fetch("SELECT * FROM g5_write_product WHERE (wr_1 = '".addslashes($row['wr_code'])."' or wr_27 = '".addslashes($row['wr_code'])."' or wr_28 = '".addslashes($row['wr_code'])."' or wr_29 = '".addslashes($row['wr_code'])."' or wr_30 = '".addslashes($row['wr_code'])."' or wr_31 = '".addslashes($row['wr_code'])."') ");
                            
                                if($row['wr_warehouse'] == 1000)
                                    $stock = (int)$row['wr_32'];
                                else if($row['wr_warehouse'] == 3000)
                                    $stock = (int)$row['wr_36'];
                                else if($row['wr_warehouse'] == 4000)
                                    $stock = (int)$row['wr_42'];
                                else if($row['wr_warehouse'] == 5000)
                                    $stock = (int)$row['wr_43'];
                                else if($row['wr_warehouse'] == 6000)
                                    $stock = (int)$row['wr_44'];
                                else if($row['wr_warehouse'] == 7000)
                                    $stock = (int)$row['wr_40'];
                                else if($row['wr_warehouse'] == 8000)
                                    $stock = (int)$row['wr_41'];
                                else if($row['wr_warehouse'] == 9000)
                                    $stock = (int)$row['wr_37'];
                                
                                $ea_chk = $stock < $row['wr_ea'] ? ";color:red;font-weight:600" : "";
                                $disabled = $stock < $row['wr_ea'] ? "disabled" : "";
								
                                $direct = $row['wr_direct_use'] == 1 ? '<i class="fa fa-check" aria-hidden="true" style="color:green" "></i>' :"";
                                
                                if(!$row['wr_rack']) 
                                    $row['wr_rack'] = "X";
                                
                                $imsi_item = "";
                                if($row['ca_name'] == "임시"){
                                    $imsi_item = "color:blue";
                                } else if($row['ca_name'] == "최종확정"){
                                    $imsi_item = "color:red";
                                }
								
                                $set = ($row['wr_set_sku']) ? '<br><span style="color:blue">('.$row['wr_set_sku'].')</span>' : "";
                                $bg = 'bg' . ($i % 2);
                            ?>
                            <tr class="<?php echo $bg?> tr_<?php echo preg_replace('/[^0-9]*/s', '', $row['wr_order_num'])?>">
                                <td><input type="checkbox" name="chk_seq[]" value="<?php echo $row['seq']?>"  class="chkbox <?php echo $disabled?>" data-product="<?php echo $row['wr_code']?>" data-qty="<?php echo $row['wr_ea']?>" data-current-qty="<?php echo $stock?>"></td>
                                <td><?php echo ($i+1)?></td>
                                <td><?php echo $direct?></td>
                                <td><?php echo substr($row['wr_date'],0,10)?></td>
                                <td><?php echo substr($row['wr_date2'],0,10)?></td>
                                <td><?php echo substr($row['wr_date3'],0,10)?></td>
                                <td><?php echo $row['wr_domain']?></td>
                                <td><?php echo $row['wr_order_num'].$set?></td>
                                <td style="<?php echo $imsi_item ?>"><?php echo $row['wr_code']?></td>
                                <td style="text-align:left;<?php echo $imsi_item ?>"><?php echo $row['wr_subject']?></td>
                                <td style="text-align:right;<?php echo $ea_chk ?>"><?php echo $stock?></td>
                                <td style="text-align:right"><?php echo $row['wr_ea']?></td>
                                <td><?php echo $row['wr_warehouse']?> (<?php echo $row['wr_rack']?>)</td>
                                
                                <td><?php echo $row['wr_mb_name']?></td>
                                <td style="text-align:right"><?php echo $row['wr_danga']?></td>
                                <td style="text-align:right"><?php echo $row['wr_singo']?></td>
                                <td><?php echo $row['wr_weight1']?></td>
                                <td><?php echo $row['wr_weight2']?></td>
                                <td><?php echo $row['wr_hscode']?></td>
                                <td><?php echo $row['wr_country']?></td>
                            </tr>
                        <?php 
                            }
                        if(sql_num_rows($rst)==0) echo "<tr><td colspan='22'>검색된 목록이 없습니다.</td></tr>";
                        
                        }?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="win_btn btn_confirm" style="position:fixed;bottom:0;width:100%;background:#fff;border-top:1px solid #ddd;margin-bottom:0">
            출고 일자 <input type="date" name="wr_date4" placeholder="출고 적용일자" value="<?php echo G5_TIME_YMD?>" required>
            <input type="button" name="btn_name" value="출고등록" class="btn_submit btn" onclick="submit_confirm()">
            <input type="button" name="btn_name" value="선택삭제" onclick="delete_confirm2();" class="btn_submit btn" style="cursor: pointer;background:#ff4081;">
            <input type="button" name="btn_name" value="선택완전삭제" onclick="delete_confirm();" class="btn_submit btn" style="cursor: pointer;background:#ff4081;">
            <button type="button" onclick="window.close();" class="btn_close btn">닫기</button>
        </div>
    </form>
</div>

<script>

function add_pop(sku,pname,wr_id) {
	window.open("/bbs/write.php?bo_table=product&sku="+sku+"&pname="+pname+"&swr_id="+wr_id, "add_item", "left=50, top=50, width=550, height=650, scrollbars=1");	
}

$(function(){
	$('.chkbox').bind('click', function(){
		let stat = $(this).is(':checked');
		
		if(stat == true) {
			$(this).closest('tr').find('td').css({'background':'#f2f2f2'});
		} else {
			$(this).closest('tr').find('td').css({'background':'#fff'});
		}
	})
});

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
			$(this).closest('tr').find('td').css({'background':'#f2f2f2'});
		} else {
			$(this).closest('tr').find('td').css({'background':'#fff', 'color' :'#666'});
		}
  });
}
// 전체 재고와 선택된 수량을 추적하는 객체
var totalInventory = {};

// 초기화 함수
function initializeInventory() {
    var checkboxes = document.querySelectorAll('.chkbox');
    checkboxes.forEach(function(checkbox) {
        var product = checkbox.dataset.product;
        var currentQty = parseInt(checkbox.dataset.currentQty);
        if (!totalInventory[product]) {
            totalInventory[product] = {
                currentQty: currentQty,
                selectedQty: 0
            };
        } else {
            totalInventory[product].currentQty = currentQty;
        }
    });
}

// 체크박스 변경 시 처리하는 함수
function handleCheckboxChange(event) {
    var checkbox = event.target;
    var product = checkbox.dataset.product;
    var qty = parseInt(checkbox.dataset.qty);
    var currentQty = totalInventory[product].currentQty;

    // 선택된 수량 계산
    var selectedQty = 0;
    document.querySelectorAll('.chkbox[data-product="' + product + '"]').forEach(function(checkbox) {
        if (checkbox.checked) {
            selectedQty += parseInt(checkbox.dataset.qty);
        }
    });
	var tr = checkbox.closest('tr');
    // 현재고와 선택된 수량 비교
    if (currentQty < selectedQty) {
        // 재고가 부족한 경우 체크 해제
        //checkbox.checked = false;
		
        tr.style.backgroundColor = 'red';
        tr.querySelectorAll('td').forEach(function(td) {
            td.style.backgroundColor = 'lightcoral';
            td.style.color = '#fff';
        });
        //alert(product + " 제품의 재고가 부족합니다.");
    } else {
		 tr.style.backgroundColor = '';
        tr.querySelectorAll('td').forEach(function(td) {
            td.style.backgroundColor = '#fff';
            td.style.color = '#666';
        });
	}
}

// 페이지 로딩 시 초기화
window.onload = function() {
    initializeInventory();
};

// 체크박스 변경 시 이벤트 리스너 추가
document.querySelectorAll('.chkbox').forEach(function(checkbox) {
    checkbox.addEventListener('change', handleCheckboxChange);
});

// 전체 선택 체크박스 변경 시 처리하는 함수
function handleSelectAllChange(event) {
    var selectAllCheckbox = event.target;
    var isChecked = selectAllCheckbox.checked;

    // 모든 체크박스를 선택 또는 해제
    document.querySelectorAll('.chkbox').forEach(function(checkbox) {
        checkbox.checked = isChecked;
    });

    // 체크박스 변경 이벤트 수동으로 호출하여 재고 확인
    document.querySelectorAll('.chkbox').forEach(function(checkbox) {
        handleCheckboxChange({ target: checkbox });
    });
}

// 전체 선택 체크박스에 이벤트 리스너 추가
document.getElementById('select_all_checkbox').addEventListener('change', handleSelectAllChange);

function submit_confirm(){
    if(confirm("선택한 데이터를 출고등록하시겠습니까?")){
        //add custom value with hidden field
        const str = $("<input type='hidden' name='btn_name' value='출고등록' >");
		let flag = false;
		$('.chkbox').each(function(){
			let stat = $(this).is(':checked');
			
			if(stat) {
				
				let class_name = $(this).attr('class');
				
				if(class_name == "chkbox disabled") {
					flag = true;
					alert('재고가 부족하여 출고 불가 데이터가 선택되어 있습니다.');
					return false;
					
				}
				
			}
		});
		
		if(flag == false) {
        $("#ListFrm").append(str);
        $("#ListFrm").submit();
		}
    }
}

function delete_confirm(){
    if(confirm("선택한 데이터를 정말 삭제하시겠습니까?\n\n[경고] 해당 주문번호에 관련된 자료 전부 삭제하는 기능입니다.\n\n한번 삭제한 자료는 복구할 수 없습니다.\n\n이전 과정까지 삭제됩니다.")){
        //add custom value with hidden field
        const str = $("<input type='hidden' name='btn_name' value='선택완전삭제' >");
        $("#ListFrm").append(str);
        $("#ListFrm").submit();
    }
}

function delete_confirm2(){
    if(confirm("선택한 입고데이터를 삭제하시겠습니까?")){
        //add custom value with hidden field
        const str = $("<input type='hidden' name='btn_name' value='선택삭제' >");
        $("#ListFrm").append(str);
        $("#ListFrm").submit();
    }
}

</script>

<?php
include_once(G5_PATH.'/tail.sub.php');