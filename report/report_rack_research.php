<?php
include_once('../common.php');

if ($is_guest)
    alert_close('로그인 후 이용하세요.');

include_once(G5_PATH.'/head.sub.php');
add_stylesheet('<link rel="stylesheet" href="'.G5_ADMIN_URL.'/css/admin.css">', 0);

$it = sql_fetch("select * from g5_write_product where wr_id = '{$wr_id}'");
?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
//문자열검색시 대소문자 구분없이 되도록
$.expr[':'].icontains = function (a, i, m) {
  return $(a).text().toUpperCase()
	.indexOf(m[3].toUpperCase()) >= 0;
};
</script>
<style>
.select2-container--default .select2-selection--single { height:40px; border:1px solid #d9dee9; background:#f1f3f6 }
.select2-container--default .select2-selection--single .select2-selection__rendered { line-height:38px }
.select2-container--default .select2-selection--single .select2-selection__arrow b { margin-top:4px }

.not_item td { background:red; color:#fff }
.pg a { margin:0 5px }
.tbl_head01 td { background:none }
.move_stock th{ width:100px;background:#ddd }
.move_stock select{ width:30% !important }
.move_stock button { width:30% !important; height:35px; line-height:35px; border:1px solid #2aba8a; background:#2aba8a; color:#fff }
.move_stock2 td {padding:15px }
.down_arrow{position:relative}
.down_arrow::after{content:"↓"; position:absolute; top:-14px;font-size:20px;font-weight:600;left:50%;background:#fff; margin-left:-20px;color:#2aba8a }

@media (max-width:767px){
	.result_list { width:100% !important }
}
</style>
<div class="new_win">
    <h1>재고관리</h1>

    <form name="searchFrm" method="get" autocomplete="off">
        <div id="excelfile_upload">
            <strong>SKU</strong> <?php echo $it['wr_1']?><br>
            <strong>제품명</strong> <?php echo $it['wr_subject']?><br>
        </div>
	</form>
	
	
	<div id="excelfile_upload" class="result_list" style="padding:12px;width:100%;float:left;">
		<div class="tbl_head02 tbl_wrap" style="width:500px;">
            <table>
                <tr>
                    <th style="width:100px">임시창고</th>
                    <td style="text-align:right"><?php echo number_format($it['wr_37'])?></td>
                    <th style="width:100px">전체재고</th>
                    <td style="text-align:right"><?php echo number_format($it['wr_32']+$it['wr_36']+$it['wr_37']+$it['wr_42']+$it['wr_43']+$it['wr_44'])?></td>
                </tr>
            </table>
		</div>
		<div style="clear:both"></div>

        <? foreach(PLATFORM_TYPE as $key => $value){
            if($key=="7000" || $key=="8000")
                continue;

            switch($key){
                case "1000" :
                    $store_stock = $it['wr_32'];
                    break;

                case "3000" :
                    $store_stock = $it['wr_36'];
                    break;
                    
                case "4000" :
                    $store_stock = $it['wr_42'];
                    break;
                    
                case "5000" :
                    $store_stock = $it['wr_43'];
                    break;
                     
                case "6000" :
                    $store_stock = $it['wr_44'];
                    break;

                default :    

            }

            ?>
		<div class="tbl_head01 tbl_wrap" style="width:20%; float:left;height:550px;overflow-y:scroll;">
            <table>
                <thead style="position:sticky;top:0;">
                    <tr>
                        <th style="width:100%" colspan="2"><strong><?=$value?>창고</strong> (<?php echo number_format($store_stock)?>개)</th>
                    </tr>
                    <tr>
                        <th style="width:100%" colspan="2">
                            <input type="text" id="<?=$key?>_ser" class="frm_input rack_search" placeholder="랙 이름 검색" style="width:100%">
                            <script>
                            $(document).ready(function() {
                                $("#<?=$key?>_ser").keyup(function() {
                                    var k = $(this).val();
                                    
                                    $("#<?=$key?>_list > tr").hide();
                                    var temp = $("#<?=$key?>_list > tr > td:icontains('" + k + "')");
                                    
                                    $(temp).parent().show();
                                });
                            });
                            </script>
                        </th>
                    </tr>
                    <tr >
                        <th style="width:30%">랙 번호</th>
                        <th>재고수량</th>
                    </tr>
                </thead>
                <tbody id="<?=$key?>_list">
                    <?php
                    $kor_stock = 0;
                    $sql_common = " from g5_rack ";
                    $sql_search = " where gc_warehouse = '{$key}' and gc_use = 1 order by SUBSTRING(gc_name, 1, 1) asc";
                    $sql = " select * {$sql_common} {$sql_search}  ";
                    $result = sql_query($sql);
                    for($i=0; $row=sql_fetch_array($result); $i++) {
                        
                        $stock = sql_fetch("select *, SUM(wr_stock) as total from g5_rack_stock where wr_warehouse = '{$row['gc_warehouse']}' and wr_rack = '{$row['seq']}' and wr_product_id = '{$wr_id}' ");
                        
                        $bg = 'bg' . ($i % 2);
                        
                    ?>
                    <tr class="<?php echo $bg; ?>">
                        <td><?php echo $row['gc_name']?></td>
                        <td>
                            <form name="frmIndi<?php echo $row['seq']?>" action="./rack_move_update.php" method="post">
                                <input type="hidden" name="mode" value="indi">
                                <input type="hidden" name="wr_id" value="<?php echo $wr_id?>">
                                <input type="hidden" name="warehouse" value="<?=$key?>">
                                <input type="hidden" name="seq" value="<?php echo $row['seq']?>">
                                <input type="hidden" name="ori_qty" value="<?php echo (int)$stock['total']?>">
                                <input type="number" name="qty" class="frm_input qty" value="<?php echo (int)$stock['total']?>" style="text-align:right;width:40%" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
                                <button class="btn01">수정</button>
                            </form>
                        </td>
                    </tr>
                    <?php 
                        $kor_stock += $stock['total'];
                    }?>
                </tbody>
            </table>
		</div>
        <? } ?>

	    <div style="clear:both"></div>
	</div>

	
	<div id="excelfile_upload" class="result_list" style="padding:12px;width:100%;float:left;">

		<div class="tbl_head02 tbl_wrap" style="width:300px;">
            <table>
                <tr>
                    <th style="width:100px">반품창고 재고</th>
                    <td style="text-align:right"><?php echo number_format($it['wr_40']+$it['wr_41'])?></td>
                </tr>
            </table>
		</div>

		<div style="clear:both"></div>
        <? foreach(PLATFORM_TYPE as $key => $value){ 
                if($key=="1000" ||$key=="3000" ||$key=="4000" ||$key=="5000" || $key=="6000")
                    continue;

                switch($key){
                    case "7000" :
                        $return_stock = $it['wr_40'];
                        break;

                    case "8000" :
                        $return_stock = $it['wr_41'];
                        break;
    
                    default :    
    
                }    
            ?>
		<div class="tbl_head01 tbl_wrap" style="width:19%; margin-right:1%;float:left;height:550px;overflow-y:scroll;">
            <table>
                <thead style="position:sticky;top:0;">
                    <tr>
                        <th style="width:100%" colspan="2"><strong><?=$value?>창고</strong> (<?php echo number_format($return_stock)?>개)</th>
                    </tr>
                    <tr>
                        <th style="width:100%" colspan="2">
                            <input type="text" id="<?=$key?>_ser" class="frm_input rack_search" placeholder="랙 이름 검색" style="width:100%">
                            <script>
                            $(document).ready(function() {
                                $("#<?=$key?>_ser").keyup(function() {
                                    var k = $(this).val();
                                    
                                    $("#<?=$key?>_list > tr").hide();
                                    var temp = $("#<?=$key?>_list > tr > td:icontains('" + k + "')");

                                    $(temp).parent().show();
                                });
                            });
                            </script>
                        </th>
                    </tr>
                    <tr >
                        <th style="width:30%">랙 번호</th>
                        <th>재고수량</th>
                    </tr>
                </thead>
                <tbody id="<?=$key?>_list">
                    <?php
                    $kor_stock = 0;
                    $sql_common = " from g5_rack ";
                    $sql_search = " where gc_warehouse = '{$key}' and gc_use = 1 order by gc_name asc";
                    $sql = " select * {$sql_common} {$sql_search}  ";
                    $result = sql_query($sql);
                    for($i=0; $row=sql_fetch_array($result); $i++) {
                        
                        $stock = sql_fetch("select *, SUM(wr_stock) as total from g5_rack_stock where wr_warehouse = '{$row['gc_warehouse']}' and wr_rack = '{$row['seq']}' and wr_product_id = '{$wr_id}' ");
                        
                        $bg = 'bg' . ($i % 2);
                        
                    ?>
                    <tr class="<?php echo $bg; ?>">
                        <td><?php echo $row['gc_name']?></td>
                        <td>
                            <form name="frmIndi<?php echo $row['seq']?>" action="./rack_move_update.php" method="post">
                                <input type="hidden" name="mode" value="indi">
                                <input type="hidden" name="wr_id" value="<?php echo $wr_id?>">
                                <input type="hidden" name="warehouse" value="<?=$key?>">
                                <input type="hidden" name="seq" value="<?php echo $row['seq']?>">
                                <input type="hidden" name="ori_qty" value="<?php echo (int)$stock['total']?>">
                                <input type="number" name="qty" class="frm_input qty" value="<?php echo (int)$stock['total']?>" style="text-align:right;width:40%" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
                                <button class="btn01">수정</button>
                            </form>
                        </td>
                    </tr>
                    <?php 
                        $kor_stock += $stock['total'];
                    }?>
                </tbody>
            </table>
		</div>
        <? } ?>
		

	</div>
    
	<div style="clear:both;margin-bottom:100px"></div>
	<div class="win_btn btn_confirm" style="position:fixed;bottom:0;width:100%;background:#fff;border-top:1px solid #ddd;margin-bottom:0;">
        <button type="button" onclick="window.close();" class="btn_close btn">닫기</button>
    </div>
</div>

<script>
function error_msg(){
	alert('미분류 재고가 존재하여 랙별 이동이 불가합니다.\n미분류 재고 부터 이동 후 실행하세요.');
	return false;
}
$(function(){
	
	$('.ms_rack, .ms_rack2').select2();
		
	//모바일 편의성 대응
	$('.qty').bind('focus', function(){
		let ea = $(this).val();
		
		if(ea == 0) {
			$(this).val('');
		}
	})
	
	$('.qty').bind('blur', function(){
		let ea = $(this).val();
		
		if(ea == '') {
			$(this).val('0');
		}
	})
	
	$('.qty').bind('keyup', function(){
		
		let ea = $(this).val();
		let ori = $(this).prev().val();
        
		if(ea == ori) {
			alert('재고 변동이 없습니다.');
			return false;
		}
		
		if(ea < 0){
			alert('0이하는 입력하실 수 없습니다.');
			$(this).val(ori);
			return false;
		}
	})
	
	$('.ms_stock').bind('keyup', function(){
		
		let rack_ea = parseInt($('.ms_rack option:selected').attr('data'));
		let ea = parseInt($(this).val());
		
		if(!rack_ea) {
			alert('재고가 있는 랙을 먼저 선택하세요.');
			$(this).val('');
			$('.ms_rack').focus();
			return false;
		}
		
		if(ea <= 0){
			alert('0이하는 입력하실 수 없습니다.');
			$(this).val('');
			return false;
		}
		
		if(ea > rack_ea) {
			alert(rack_ea+'선택 한 랙의 재고보다 많이 입력하실 수 없습니다.');
			$(this).val(rack_ea);
			return false;
		}
		
	})
	
	$('.ms_warehouse').bind('change', function(){
		$.post('./ajax.rack.php', { wr_id : <?php echo $wr_id?>, warehouse : $(this).val(), mode : 'a' }, function(data){
			$('.ms_rack').html(data);
		})
	})
	
	$('.ms_warehouse2').bind('change', function(){
		$.post('./ajax.rack.php', { wr_id : <?php echo $wr_id?>, warehouse : $(this).val(), mode : '' }, function(data){
			$('.ms_rack2').html(data);
		})
	})
	
	$('.del_btn').bind('click', function(){
		if(confirm('정말 해당 매출자료를 삭제하시겠습니까?\n삭제 후 데이터 복구는 불가능합니다.')) {
			let el = $(this).closest('tr');
			
			$.post('./sales1_delete.php', { seq : $(this).attr('data') }, function(data) {
				
				if(data == "y") {
					el.remove();
					alert('매출정보가 삭제되었습니다.');
				} else {
					alert('처리 중 오류가 발생했습니다.');
				}
				$('#result_form').html(data);
			})
			
		} else {
			return false;
		}
	})
})
function add_pop(sku,pname,wr_id) {
	
	window.open("/bbs/write.php?bo_table=product&sku="+sku+"&pname="+pname+"&swr_id="+wr_id, "add_item", "left=50, top=50, width=550, height=650, scrollbars=1");
	
}

function add_item(wr_code, wr_2, wr_subject){
	
	opener.window.document.addform.wr_code.value = wr_code;
	opener.window.document.addform.wr_product_name1.value = wr_2;
	opener.window.document.addform.wr_product_name2.value = wr_subject;
	
	window.close();
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
}

function chkfrm(f){
	f.act.value = document.pressed;
	
	if (document.pressed == "발주생성") {
		
		if (!confirm("선택한 매출자료를 발주생성 하시겠습니까?")) {
			return false;
		}
	}
	
	if (document.pressed == "한국창고 출고") {
		
		if (!confirm("선택한 매출자료를 한국창고에서 출고등록 하시겠습니까?")) {
			return false;
		}
	}
	
	if (document.pressed == "미국창고 출고") {
		
		if (!confirm("선택한 매출자료를 미국창고에서 출고등록 하시겠습니까?")) {
			return false;
		}
	}
}
</script>
<?php
include_once(G5_PATH.'/tail.sub.php');