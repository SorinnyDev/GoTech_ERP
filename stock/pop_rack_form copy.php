<?php
include_once('./_common.php');

if ($is_guest)
    alert_close('로그인 후 이용하세요.');

include_once(G5_PATH.'/head.sub.php');
add_stylesheet('<link rel="stylesheet" href="'.G5_ADMIN_URL.'/css/admin.css">', 0);

$it = sql_fetch("select * from g5_write_product where wr_id = '{$wr_id}'");
?>

<style>
.not_item td { background:red; color:#fff }
.pg a { margin:0 5px }
.tbl_head01 td { background:none }
.move_stock th{ width:100px;background:#ddd }
.move_stock select{ width:30% !important }
.move_stock button { width:30% !important; height:35px; line-height:35px; border:1px solid #2aba8a; background:#2aba8a; color:#fff }
.move_stock2 td {padding:15px }
.down_arrow{position:relative}
.down_arrow::after{content:"↓"; position:absolute; top:-14px;font-size:20px;font-weight:600;left:50%;background:#fff; margin-left:-20px;color:#2aba8a }
.diabled_btn { background:#ddd; cursor:not-allowed }
</style>

<div class="new_win">
    <h1>창고 별 랙관리</h1>

    <form name="addFrm" action="./rack_form_update.php" method="post" autocomplete="off">
        <input type="hidden" name="mode" value="add">
        <div id="excelfile_upload">
            <h2 style="margin-left:0">랙 추가</h2>
            <select name="warehouse">
                <? foreach(PLATFORM_TYPE as $key => $value){ ?>
                <option value="<?=$key?>"><?=$value?>창고(<?=$key?>)</option>
                <? } ?>
            </select>
            <input type="text" name="rack_name" value="" class="frm_input required" required placeholder="랙 이름">
            <button class="btn_b01">랙 추가</button>
        </div>
	</form>
	
	<!--<form name="frm" action="./sales1_search_update.php" method="post" onsubmit="return chkfrm(this);">-->
      
    <div id="excelfile_upload" class="result_list" style="padding:12px;">
        <div style="clear:both"></div>
        <? foreach(PLATFORM_TYPE as $key => $value){ ?>  
            <div class="tbl_head01 tbl_wrap" style="width:20%;height:550px;overflow-y:scroll;float:left">
                <table>
                    <thead style="position:sticky;top:0;">
                        <tr>
                            <th style="width:100%" colspan="3"><strong><?=$value?>창고</strong></th>
                        </tr>
                        <tr >
                            <th>랙 이름</th>
                            <th>총 재고</th>
                            <th>관리</th>
                        </tr>
                    </thead>
                    <tbody >
                        <?php
                        $kor_stock = 0;
                        $sql_common = " from g5_rack ";
                        $sql_search = " where gc_warehouse = '{$key}' and gc_use = 1 order by gc_name asc";
                        $sql = " select * {$sql_common} {$sql_search}  ";
                        $result = sql_query($sql);
                        for($i=0; $row=sql_fetch_array($result); $i++) {
                            
                            $stock = sql_fetch("select *, SUM(wr_stock) as total from g5_rack_stock where wr_warehouse = '{$row['gc_warehouse']}' and wr_rack = '{$row['seq']}' ");
                            
                            $bg = 'bg' . ($i % 2);
                            
                        ?>
                        <tr class="<?php echo $bg; ?>">
                            <td><input type="text" class="rack_name frm_input" value="<?php echo $row['gc_name']?>"></td>
                            <td><?php echo number_format($stock['total'])?></td>
                            <td>
                                <button type="button" class="btn02 modify" data="<?php echo $row['seq']?>">수정</button>
                                
                                <?php if($stock['total'] > 0 ){?>
                                <button type="button" class="diabled_btn btn01" disabled title="재고가 있어 삭제가 불가능합니다." onclick="error_msg()">삭제</button>
                                <?php } else {?>
                                <button type="button" class="btn01 delete" data="<?php echo $row['seq']?>">삭제</button>
                                <?php }?>
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
</div>

<script>
function error_msg(){
	alert('해당 랙에 재고가 존재하여 삭제가 불가합니다.');
	return false;
}
$(function(){
	
	
	$('.modify').bind('click', function(){
		
		let rack_name = $(this).closest('tr').find('.rack_name').val();
		if(!rack_name) {
			alert('랙이름을 입력하세요');
			$(this).closest('tr').find('.rack_name').focus();
			return false;
		}
		$.post('./rack_form_update.php', { rack_name : rack_name, seq : $(this).attr('data'), mode : 'mod' }, function(data){
			
			if(data == "y") {
				alert('랙 이름이 수정되었습니다.');
			} else {
				alert('처리 중 오류가 발생했습니다.');
			}
			
		})
	})
	
	$('.delete').bind('click', function(){
		
		if(confirm('정말 해당 랙을 삭제하시겠습니까?\n삭제 후 데이터 복구는 불가능합니다.')) {
			
			$(this).closest('tr').css('display', 'none');
			
			$.post('./rack_form_update.php', { seq : $(this).attr('data'), mode : 'del' }, function(data){
				
				if(data == "y") {
					alert('랙이 삭제되었습니다.');
				} else {
					alert('처리 중 오류가 발생했습니다.');
				}
				
			})
		} else {
			return false;
		}
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