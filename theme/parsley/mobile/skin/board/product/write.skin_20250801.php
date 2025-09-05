<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
add_stylesheet('<link rel="stylesheet" href="'.$board_skin_url.'/style.css">', 0);

# 도메인 불러오기
$domainData = get_code_list("4");

# 수수료 정보 불러오기
$sql = "SELECT * FROM g5_write_product_fee WHERE wr_id='{$wr_id}'";
$feeRs = sql_query($sql);

//임시등록 모드 
if($sku) {
	$write['ca_name'] = "임시";
	$wr_1 = $sku;
	$subject = urldecode($pname);
	
	if($wr_1 == "등록하기")
		$wr_1 = "";
	
?>
<script>
$(function() {
	$(document).ready(function() {
		$('#ca_name').val('임시');
	})
})
</script>
<?php
}

?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<style>
#bo_w { max-width:100%; width:700px}
.set_sku_box input { margin-bottom:5px }
.select2-container--default .select2-selection--single { height:40px; border:1px solid #d9dee9; background:#f1f3f6 }
.select2-container--default .select2-selection--single .select2-selection__rendered { line-height:38px }
</style>
<section id="bo_w" >
	<h2 class="board_tit"><?php echo $board['bo_subject']?></h2>
    <form name="fwrite" id="fwrite" action="<?php echo $action_url ?>" onsubmit="return fwrite_submit(this);" method="post" enctype="multipart/form-data" autocomplete="off">
    <input type="hidden" name="w" value="<?php echo $w ?>">
    <input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
    <input type="hidden" name="wr_id" value="<?php echo $wr_id ?>">
    <input type="hidden" name="sca" value="<?php echo $sca ?>">
    <input type="hidden" name="sfl" value="<?php echo $sfl ?>">
    <input type="hidden" name="stx" value="<?php echo $stx ?>">
    <input type="hidden" name="spt" value="<?php echo $spt ?>">
    <input type="hidden" name="sst" value="<?php echo $sst ?>">
    <input type="hidden" name="sod" value="<?php echo $sod ?>">
    <input type="hidden" name="page" value="<?php echo $page ?>">
	<input type="hidden" name="sku" value="<?php echo $sku ?>">
	<input type="hidden" name="swr_id" value="<?php echo $swr_id ?>">
	<input type="hidden" name="mode" value="<?php echo $mode ?>">
	
    <?php
    $option = '';
    $option_hidden = '';
    if ($is_notice || $is_html || $is_secret || $is_mail) {
        $option = '';
        if ($is_notice) {
            $option .= PHP_EOL.'<input type="checkbox" id="notice" name="notice" value="1" '.$notice_checked.'>'.PHP_EOL.'<label for="notice" class="notice_ck">공지</label>';
        }

        if ($is_html) {
            if ($is_dhtml_editor) {
                $option_hidden .= '<input type="hidden" value="html1" name="html">';
            } else {
                $option .= PHP_EOL.'<input type="checkbox" id="html" name="html" onclick="html_auto_br(this);" value="'.$html_value.'" '.$html_checked.'>'.PHP_EOL.'<label for="html" class="html_ck">html</label>';
            }
        }

        if ($is_secret) {
            if ($is_admin || $is_secret==1) {
                $option .= PHP_EOL.'<input type="checkbox" id="secret" name="secret" value="secret" '.$secret_checked.'>'.PHP_EOL.'<label for="secret" class="secret_ck">비밀글</label>';
            } else {
                $option_hidden .= '<input type="hidden" name="secret" value="secret">';
            }
        }

        if ($is_mail) {
            $option .= PHP_EOL.'<input type="checkbox" id="mail" name="mail" value="mail" '.$recv_email_checked.'>'.PHP_EOL.'<label for="mail">답변메일받기</label>';
        }
    }

    echo $option_hidden;
    ?>
	
    <div class="form_inpt">
        <h2 class="sound_only"><?php echo $g5['title'] ?></h2>
		
		<ul class="bo_w_info">
	     
	        
	        <?php if (!$is_member) {  ?>
	        <li class="wli_left">
	        	<div class="wli_tit">이름</div>
		    	<?php if ($is_name) { ?>
		    	<div class="wli_cnt">
	            	<label for="wr_name" class="sound_only">이름<strong>필수</strong></label>
	            	<input type="text" name="wr_name" value="<?php echo $name ?>" id="wr_name" required class="frm_input full_input required" maxlength="20" placeholder="이름">
	        	</div>
	        	<?php } ?>
	        </li>
			<li class="wli_left">
				<div class="wli_tit">비밀번호</div>
		        <?php if ($is_password) { ?>
		        <div class="wli_cnt">	
		            <label for="wr_password" class="sound_only">비밀번호<strong>필수</strong></label>
		            <input type="password" name="wr_password" id="wr_password" <?php echo $password_required ?> class="frm_input full_input <?php echo $password_required ?>" maxlength="20" placeholder="비밀번호">
		        </div>
		        <?php } ?>
			</li>
			<li class="wli_left">
				<div class="wli_tit">이메일</div>
		        <?php if ($is_email) { ?>
		        <div class="wli_cnt">
		            <label for="wr_email" class="sound_only">이메일</label>
		            <input type="email" name="wr_email" value="<?php echo $email ?>" id="wr_email" class="frm_input full_input" email" maxlength="100" placeholder="이메일">
		        </div>
		        <?php } ?>
			</li>
			<li class="wli_left">
	        	<?php if ($is_homepage) { ?>
	        	<div class="wli_tit">홈페이지</div>
			    <div class="wli_cnt">
	            	<label for="wr_homepage" class="sound_only">홈페이지</label>
	            	<input type="text" name="wr_homepage" value="<?php echo $homepage ?>" id="wr_homepage" class="frm_input full_input" placeholder="홈페이지">
	        	</div>
	       		<?php } ?>
			</li>
			<?php } ?>
			<li class="bo_w_tit half_box">
				<div class="wli_tit">SKU(임시/확정/최종확정)</div>
		    	<div class="wli_cnt">
		    		<label for="wr_1" class="sound_only">SKU<strong>필수</strong></label>
	    			<select id="ca_name" name="ca_name" required class="full_input">
			            <option value="">선택하세요</option>
			            <?php echo $category_option ?>
			        </select>
		    	</div>
		    </li>
			<li class="bo_w_tit half_box">
				<div class="wli_tit">일반/세트</div>
		    	<div class="wli_cnt">
		    		<label for="wr_1" class="sound_only">SKU<strong>필수</strong></label>
	    			<select id="wr_33" name="wr_33" required class="full_input">
			            <option value="단품" <?php echo get_selected($write['wr_33'], '단품')?>>단품</option>
			            <option value="세트" <?php echo get_selected($write['wr_33'], '세트')?>>세트상품</option>
			        </select>
		    	</div>
				<script>
				$(function(){
					$('#wr_33').bind('change', function(){
						
						if($(this).val() == "단품") {
							$('.skuset').hide();
							//$('.sku2').show();
							//$('.sku3').show();
							//$('.sku4').show();
							//$('.sku5').show();
							//$('.sku6').show();
						} else {
							$('.skuset').show();
							//$('.sku2').hide();
							//$('.sku3').hide();
							//$('.sku4').hide();
							//$('.sku5').hide();
							//$('.sku6').hide();
						}
						
					})
				})
				</script>
		    </li>
			
			
			<li class="sku1 bo_w_tit half_box">
				<div class="wli_tit">SKU 1</div>
		    	<div class="wli_cnt">
		    		<label for="wr_1" class="sound_only">SKU<strong>필수</strong></label>
	    			<input type="text" name="wr_1" value="<?php echo $wr_1 ?>" id="wr_1"  class="frm_input " placeholder="" hname="SKU1" required>
		    	</div>
		    </li>
			<li class="skuset bo_w_tit half_box" style="<?php if($write['wr_33'] == "단품" || $w == '' || $write['wr_33'] == "") {?>display:none<?php }?>">
				<div class="wli_tit">구성상품 SKU</div>
		    	<div class="wli_cnt set_sku_box">
		    		<label for="wr_1" class="sound_only">SKU<strong>필수</strong></label>
					
					<?php 
                    for($a=0;$a<=6;$a++) {
						
						$wr_34 = explode('|@|', $write['wr_34']);
						$wr_35 = explode('|@|', $write['wr_35']);

                        if($wr_34[$a]){
                            $item = sql_fetch("SELECT * FROM g5_write_product WHERE 
                            (wr_1 = '".addslashes($wr_34[$a])."' or wr_27 = '".addslashes($wr_34[$a])."' or wr_28 = '".addslashes($wr_34[$a])."' 
                            or wr_29 = '".addslashes($wr_34[$a])."' or wr_30 = '".addslashes($wr_34[$a])."' or wr_31 = '".addslashes($wr_34[$a])."') ");

                            $hab_x += $item['wr_14'];
                            $hab_y += $item['wr_15'];
                            $hab_z += $item['wr_16'];

                            $hab_weight += $item['wr_10'];
                        }    
					?>
					<input type="text" name="wr_34[]" value="<?php echo $wr_34[$a]?>" class="frm_input" placeholder="SKU" style="width:73%">
					<input type="text" name="wr_35[]" value="<?php echo $wr_35[$a]?>" class="frm_input" placeholder="수량(숫자만)" style="width:25%;text-align:right">
					<?php }?>
					
		    	</div>
		    </li>
			<li class="sku2 bo_w_tit half_box" style="">
				<div class="wli_tit">SKU 2</div>
		    	<div class="wli_cnt">
		    		<label for="wr_27" class="sound_only">SKU<strong>필수</strong></label>
	    			<input type="text" name="wr_27" value="<?php echo $write['wr_27'] ?>" id="wr_27"  class="frm_input " placeholder="">
		    	</div>
		    </li>
			<li class="sku3 bo_w_tit half_box" style="">
				<div class="wli_tit">SKU 3</div>
		    	<div class="wli_cnt">
		    		<label for="wr_1" class="sound_only">SKU<strong>필수</strong></label>
	    			<input type="text" name="wr_28" value="<?php echo $write['wr_28'] ?>" id="wr_28"  class="frm_input " placeholder="">
		    	</div>
		    </li>
			<li class="sku4 bo_w_tit half_box" style="">
				<div class="wli_tit">SKU 4</div>
		    	<div class="wli_cnt">
		    		<label for="wr_1" class="sound_only">SKU<strong>필수</strong></label>
	    			<input type="text" name="wr_29" value="<?php echo $write['wr_29'] ?>" id="wr_29"  class="frm_input " placeholder="">
		    	</div>
		    </li>
			<li class="sku5 bo_w_tit half_box" style="">
				<div class="wli_tit">SKU 5</div>
		    	<div class="wli_cnt">
		    		<label for="wr_30" class="sound_only">SKU<strong>필수</strong></label>
	    			<input type="text" name="wr_30" value="<?php echo $write['wr_30'] ?>" id="wr_30"  class="frm_input " placeholder="">
		    	</div>
		    </li>
			<li class="sku6 bo_w_tit half_box" style="">
				<div class="wli_tit">SKU 6</div>
		    	<div class="wli_cnt">
		    		<label for="wr_31" class="sound_only">SKU<strong>필수</strong></label>
	    			<input type="text" name="wr_31" value="<?php echo $write['wr_31'] ?>" id="wr_31"  class="frm_input " placeholder="">
		    	</div>
		    </li>
		
			<li style="border-top:1px solid #ddd"></li>
			<li class="bo_w_tit half_box">
				<div class="wli_tit">제품명</div>
		    	<div class="wli_cnt">
		    		<label for="wr_subject" class="sound_only">제목<strong>필수</strong></label>
	    			<input type="text" name="wr_subject" value="<?php echo $subject ?>" id="wr_subject" required class="frm_input required" placeholder="제품명을 반드시 입력하세요.">
		    	</div>
		    </li>
			
			<li class="bo_w_tit half_box">
				<div class="wli_tit">약칭명</div>
		    	<div class="wli_cnt">
		    		<label for="wr_2" class="sound_only">약칭명<strong>필수</strong></label>
	    			<input type="text" name="wr_2" value="<?php echo $wr_2 ?>" id="wr_2"  class="frm_input " placeholder="">
		    	</div>
		    </li>
			
			<li class="bo_w_tit half_box">
				<div class="wli_tit">수출신고 명칭</div>
		    	<div class="wli_cnt">
		    		<label for="wr_3" class="sound_only">약칭명<strong>필수</strong></label>
	    			<input type="text" name="wr_3" value="<?php echo $wr_3 ?>" id="wr_3"  class="frm_input " placeholder="">
		    	</div>
		    </li>
			
			
			<li class="bo_w_tit half_box2" style="clear:both">
				<div class="wli_tit">대표코드1</div>
		    	<div class="wli_cnt">
		    		<label for="wr_5" class="sound_only">바코드<strong>필수</strong></label>
	    			<input type="text" name="wr_5" value="<?php echo $wr_5 ?>" id="wr_5"  class="frm_input " placeholder="" hname="대표코드1" required>
		    	</div>
		    </li>
			<li class="bo_w_tit half_box2">
				<div class="wli_tit">대표코드2</div>
		    	<div class="wli_cnt">
		    		<label for="wr_6" class="sound_only">대표코드6<strong>필수</strong></label>
	    			<input type="text" name="wr_6" value="<?php echo $wr_6 ?>" id="wr_6"  class="frm_input " placeholder="">
		    	</div>
		    </li>
			<li class="bo_w_tit half_box2">
				<div class="wli_tit">대표코드3</div>
		    	<div class="wli_cnt">
		    		<label for="wr_4" class="sound_only">바코드<strong>필수</strong></label>
	    			<input type="text" name="wr_4" value="<?php echo $wr_4 ?>" id="wr_4"  class="frm_input " placeholder="">
		    	</div>
		    </li>
			
			<!--<li class="bo_w_tit half_box2">
				<div class="wli_tit">창고위치1</div>
		    	<div class="wli_cnt">
		    		<label for="wr_6" class="sound_only">대표코드6<strong>필수</strong></label>
	    			<input type="text" name="wr_6" value="<?php echo $wr_6 ?>" id="wr_6"  class="frm_input " placeholder="">
		    	</div>
		    </li>
			<li class="bo_w_tit half_box2">
				<div class="wli_tit">창고위치2</div>
		    	<div class="wli_cnt">
		    		<label for="wr_7" class="sound_only">대표코드6<strong>필수</strong></label>
	    			<input type="text" name="wr_7" value="<?php echo $wr_7 ?>" id="wr_7"  class="frm_input " placeholder="">
		    	</div>
		    </li>
			<li class="bo_w_tit half_box2">
				<div class="wli_tit">창고위치3</div>
		    	<div class="wli_cnt">
		    		<label for="wr_8" class="sound_only">대표코드6<strong>필수</strong></label>
	    			<input type="text" name="wr_8" value="<?php echo $wr_8 ?>" id="wr_8"  class="frm_input " placeholder="">
		    	</div>
		    </li>-->
			<li style="clear:both;height:30px"></li>
			<li class="bo_w_tit half_box">
				<div class="wli_tit">유해성분</div>
		    	<div class="wli_cnt">
            <select name="wr_9" id="wr_9" class="frm_input ">
              <option value="">-</option>
              <option value="유" <?= get_selected($wr_9, '유') ?>>유</option>
              <option value="무" <?= get_selected($wr_9, '무') ?>>무</option>
            </select>
		    	</div>
		    </li>
			
			<li class="bo_w_tit half_box">
				<div class="wli_tit">1개당 무게</div>
		    	<div class="wli_cnt">
		    		<label for="wr_10" class="sound_only">1개당 무게<strong>필수</strong></label>
	    			<input type="text" name="wr_10" value="<?=$write['wr_33']=='세트' ? $hab_weight : $write['wr_10']?>" id="wr_10"  class="frm_input " placeholder="">
		    	</div>
		    </li>
			
			<li class="bo_w_tit half_box">
				<div class="wli_tit">무게단위</div>
		    	<div class="wli_cnt">
		    		<label for="wr_11" class="sound_only">무게단위<strong>필수</strong></label>
	    			<input type="text" name="wr_11" value="<?php echo $write['wr_11'] ?>" id="wr_11"  class="frm_input " placeholder="">
		    	</div>
		    </li>
			<li class="bo_w_tit half_box">
				<div class="wli_tit">HS코드</div>
		    	<div class="wli_cnt">
		    		<label for="wr_12" class="sound_only">HS코드<strong>필수</strong></label>
	    			<input type="text" name="wr_12" value="<?php echo $write['wr_12'] ?>" id="wr_12"  class="frm_input " placeholder="">
		    	</div>
		    </li>
			<li class="bo_w_tit half_box">
				<div class="wli_tit">카테고리</div> 
		    	<div class="wli_cnt">
		    		<label for="wr_13" class="sound_only">카테고리<strong>필수</strong></label>
	    			<select name="wr_26" id="wr_26" class="frm_input search_sel">
                    <?
                    $arr = get_code_list('2'); // 카테고리 코드 조회
                    foreach($arr as $key => $value){
                        $selected = $write['wr_26']==$value['idx'] ? "selected" : "";
                        echo "<option value=\"{$value['idx']}\" {$selected} >{$value['code_name']}</option>"; 
                    }
                    ?>
                    </select>

		    	</div>
		    </li>
			<li class="bo_w_tit half_box">
				<div class="wli_tit">제조국가</div>
		    	<div class="wli_cnt">
		    		<label for="wr_13" class="sound_only">제조국가<strong>필수</strong></label>
	    			<input type="text" name="wr_13" value="<?php echo $write['wr_13'] ?>" id="wr_13"  class="frm_input " placeholder="">
		    	</div>
		    </li>
			<li style="clear:both;height:30px"></li>
			<li class="bo_w_tit half_box2">
				<div class="wli_tit">가로(mm)</div>
		    	<div class="wli_cnt">
		    		<label for="wr_14" class="sound_only">가로<strong>필수</strong></label>
	    			<input type="text" name="wr_14" value="<?=$write['wr_33']=='세트' ? $hab_x : $write['wr_14'] ?>" id="wr_14"  class="frm_input " placeholder="">
		    	</div>
		    </li>
			<li class="bo_w_tit half_box2">
				<div class="wli_tit">세로(mm)</div>
		    	<div class="wli_cnt">
		    		<label for="wr_15" class="sound_only">세로<strong>필수</strong></label>
	    			<input type="text" name="wr_15" value="<?=$write['wr_33']=='세트' ? $hab_y : $write['wr_15'] ?>" id="wr_15"  class="frm_input " placeholder="">
		    	</div>
		    </li>
			<li class="bo_w_tit half_box2">
				<div class="wli_tit">높이(mm)</div>
		    	<div class="wli_cnt">
		    		<label for="wr_16" class="sound_only">높이<strong>필수</strong></label>
	    			<input type="text" name="wr_16" value="<?=$write['wr_33']=='세트' ? $hab_z : $write['wr_16'] ?>" id="wr_16"  class="frm_input " placeholder="">
		    	</div>
		    </li>
			<li style="clear:both;height:30px"></li>
			<li class="bo_w_tit half_box">
				<div class="wli_tit">상품구분</div>
		    	<div class="wli_cnt">
		    		<label for="wr_17" class="sound_only">세로<strong>필수</strong></label>
	    		
					<select name="wr_17" id="wr_17" class="frm_input">
						<option value="대표상품" <?php echo get_selected($write['wr_17'], '대표상품')?>>대표상품</option>
						<option value="일반상품" <?php echo get_selected($write['wr_17'], '일반상품')?>>일반상품</option>
					</select>
		    	</div>
		    </li>
			<li class="bo_w_tit half_box">
				<div class="wli_tit">중량무게1</div>
		    	<div class="wli_cnt">
		    		<label for="wr_18" class="sound_only">중량무게1<strong>필수</strong></label>
	    			<input type="text" name="wr_18" value="<?php echo $write['wr_18'] ?>" id="wr_18"  class="frm_input " placeholder="">
		    	</div>
		    </li>
			<li class="bo_w_tit half_box">
				<div class="wli_tit">중량무게2</div>
		    	<div class="wli_cnt">
		    		<label for="wr_19" class="sound_only">중량무게1<strong>필수</strong></label>
	    			<input type="text" name="wr_19" value="<?php echo $write['wr_19'] ?>" id="wr_19"  class="frm_input " placeholder="">
		    	</div>
		    </li>
			<li class="bo_w_tit half_box">
				<div class="wli_tit">배터리 유무</div>
		    	<div class="wli_cnt">
		    		<label for="wr_20" class="sound_only">중량무게1<strong>필수</strong></label>
	    			
					<select name="wr_20" id="wr_20" class="frm_input">
						<option value="유" <?php echo get_selected($write['wr_20'], '유')?>>유</option>
						<option value="무" <?php echo get_selected($write['wr_20'], '무')?>>무</option>
					</select>
		    	</div>
		    </li>
			<li class="bo_w_tit half_box">
				<div class="wli_tit">220V 유무</div>
		    	<div class="wli_cnt">
		    		<label for="wr_21" class="sound_only">중량무게1<strong>필수</strong></label>
					<select name="wr_21" id="wr_1" class="frm_input">
						<option value="FREE VOLT" <?php echo get_selected($write['wr_21'], 'FREE VOLT')?>>FREE VOLT</option>
						<option value="220V" <?php echo get_selected($write['wr_21'], '220V')?>>220V</option>
						<option value="무전기" <?php echo get_selected($write['wr_21'], '무전기')?>>무전기</option>
					</select>
		    	</div>
		    </li>
			<li class="bo_w_tit half_box">
				<div class="wli_tit">발주단가</div>
		    	<div class="wli_cnt">
		    		<label for="wr_22" class="sound_only">중량무게1<strong>필수</strong></label>
	    			<input type="text" name="wr_22" value="<?=(!$write['wr_22'])?0:$write['wr_22'] ?>" id="wr_22"  class="frm_input " placeholder="" hname="발주단가">
					<select name="taxType" id="taxType" class="frm_input search_sel">
						<option value="1">과세</option>
						<option value="2">면세</option>
					</select>
		    	</div>
		    </li>
			<li class="bo_w_tit half_box">
				<div class="wli_tit">브랜드</div>
		    	<div class="wli_cnt">
		    		<label for="wr_23" class="sound_only">브랜드<strong>필수</strong></label>
					
	    			<select name="wr_23" id="wr_23" class="frm_input search_sel">
						<?php 
                        $arr = get_code_list('1'); // 카테고리 코드 조회
                        foreach($arr as $key => $value){
                            $selected = $write['wr_23']==$value['idx'] ? "selected" : "";
                            echo "<option value=\"{$value['idx']}\" {$selected} >{$value['code_name']}</option>"; 
                        }
                        ?>
					</select>
		    	</div>
		    </li>
			<li class="bo_w_tit half_box">
				<div class="wli_tit">담당자</div>
		    	<div class="wli_cnt">
		    		<label for="wmb_id" class="sound_only">중량무게1<strong>필수</strong></label>
	    		
					<select name="wmb_id" id="wmb_id" class="frm_input search_sel">
						<option value="">선택하세요</option>
						<?php 
						$mbSql = " select mb_id, mb_name from g5_member order by mb_name asc";
						$mbRst = sql_query($mbSql);
						for($i=0; $mb=sql_fetch_array($mbRst); $i++) {
							$checked = "";
							if($w == '' && $mb['mb_id'] == '0023') {
									$checked  = "selected";
							}
							
						?>
						<option value="<?php echo $mb['mb_id']?>" <?php echo get_selected($mb['mb_id'], $write['mb_id'])?> <?php echo $checked?>><?php echo $mb['mb_name']?>(<?php echo $mb['mb_id']?>)</option>
						<?php }?>
					</select>
		    	</div>
		    </li>

			<li style="clear:both;height:30px"></li>
			<!--
			<li class="bo_w_tit">
				<div class="wli_tit">수수료<input type="button" class="add" value="+"/></div>
		    	<div class="wli_cnt">
	    			<ul id="fee_ul">
						<?if(sql_num_rows($feeRs) == 0){?>
							<li>
								<select name="domain[]" class="frm_input search_sel" style="width:20%" hname="도메인" required>
									<option value="">도메인</option>
									<?foreach($domainData as $key => $val){?>
										<option value="<?=$val['code_value']?>" <?=get_selected($val['code_value'],$feeRow['domain'])?>><?=$val['code_name']?></option>
									<?}?>
								</select>
								<select name="warehouse[]" class="frm_input search_sel" style="width:15%" hname="창고" required>
									<option value="">창고</option>
									<option value="1000" <?=get_selected("1000",$feeRow['warehouse'])?>>한국창고</option>
									<option value="3000" <?=get_selected("3000",$feeRow['warehouse'])?>>미국창고</option>
									<option value="4000" <?=get_selected("4000",$feeRow['warehouse'])?>>FBA창고</option>
									<option value="5000" <?=get_selected("5000",$feeRow['warehouse'])?>>W-FBA창고</option>
									<option value="6000" <?=get_selected("6000",$feeRow['warehouse'])?>>U-FBA창고</option>
								</select>
								<input type="text" name="product_fee[]" class="frm_input" placeholder="수수료" value="<?=$feeRow['product_fee']?>" style="width:13%" hname="수수료" required/>
								<input type="text" name="paypal_fee[]" class="frm_input" placeholder="페이팔(%)" value="<?=$feeRow['paypal_fee']?>" style="width:13%" hname="수수료" required/>
								<input type="text" name="grant_price[]" class="frm_input" placeholder="지원금" value="<?=$feeRow['grant_price']?>" style="width:13%" hname="수수료" required/>
								<input type="text" name="FBA_fee[]" class="frm_input" placeholder="FBA 수수료" value="<?=$feeRow['FBA_fee']?>" style="width:13%" hname="수수료" required/>
							</li>
						<?}else{?>
							<?while($feeRow = sql_fetch_array($feeRs)){
							?>
								<li>
									<select name="domain[]" class="frm_input search_sel" style="width:20%" hname="도메인" required>
										<option value="">도메인</option>
										<?foreach($domainData as $key => $val){?>
											<option value="<?=$val['code_value']?>" <?=get_selected($val['code_value'],$feeRow['domain'])?>><?=$val['code_name']?></option>
										<?}?>
									</select>
									<select name="warehouse[]" class="frm_input search_sel" style="width:15%" hname="창고" required>
										<option value="">창고</option>
										<option value="1000" <?=get_selected("1000",$feeRow['warehouse'])?>>한국창고</option>
										<option value="3000" <?=get_selected("3000",$feeRow['warehouse'])?>>미국창고</option>
										<option value="4000" <?=get_selected("4000",$feeRow['warehouse'])?>>FBA창고</option>
										<option value="5000" <?=get_selected("5000",$feeRow['warehouse'])?>>W-FBA창고</option>
										<option value="6000" <?=get_selected("6000",$feeRow['warehouse'])?>>U-FBA창고</option>
									</select>
									<input type="text" name="product_fee[]" class="frm_input" placeholder="수수료" value="<?=$feeRow['product_fee']?>" style="width:13%" hname="수수료" required/>
									<input type="text" name="paypal_fee[]" class="frm_input" placeholder="페이팔(%)" value="<?=$feeRow['paypal_fee']?>" style="width:13%" hname="수수료" required/>
									<input type="text" name="grant_price[]" class="frm_input" placeholder="지원금" value="<?=$feeRow['grant_price']?>" style="width:13%" hname="수수료" required/>
									<input type="text" name="FBA_fee[]" class="frm_input" placeholder="FBA 수수료" value="<?=$feeRow['FBA_fee']?>" style="width:13%" hname="수수료" required/>
									<span>
										<input type="button" class="delete" value="-" onclick="fnDeleteLi($(this));"/>
									</span>
								</li>
							<?}?>
						<?}?>
					</ul>
					<script>
						$(document).ready(function(){
							$(".add").bind('click',function(){
								const li = $(`<li>
												<select name="domain[]" class="frm_input search_sel" style="width:20%" hname="도메인" required>
													<option value="">도메인</option>
													<?foreach($domainData as $key => $val){?>
														<option value="<?=$val['code_value']?>"><?=$val['code_name']?></option>
													<?}?>
												</select>
												<select name="warehouse[]" class="frm_input search_sel" style="width:15%" hname="창고" required>
													<option value="">창고</option>
													<option value="1000">한국창고</option>
													<option value="3000">미국창고</option>
													<option value="4000">FBA창고</option>
													<option value="5000">W-FBA창고</option>
													<option value="6000">U-FBA창고</option>
												</select>
												<input type="text" name="product_fee[]" value="" class="frm_input" placeholder="수수료" style="width:13%" hname="수수료" required/>
												<input type="text" name="paypal_fee[]" value="" class="frm_input" placeholder="페이팔(%)" style="width:13%" hname="수수료" required/>
												<input type="text" name="grant_price[]" value="" class="frm_input" placeholder="지원금" style="width:13%" hname="수수료" required/>
												<input type="text" name="FBA_fee[]" value="" class="frm_input" placeholder="FBA 수수료" style="width:13%" hname="수수료" required/>
												<span>
													<input type="button" class="delete" value="-" onclick="fnDeleteLi($(this))">
												</span>
											</li>
										`);
								$("#fee_ul").append(li);
								$('.search_sel').select2();
							});
							
						});
						function fnDeleteLi(elem){
							elem.parent().parent().remove();
						}
					</script>
		    	</div>
		    </li>
			-->
			<li class="bo_w_tit half_box">
				<div class="wli_tit">담당자 지정 랙</div>
		    	<div class="wli_cnt">
	    		
					<select name="wr_warehouse" id="wr_warehouse" class="frm_input search_sel" style="width:48%">
						<option value="">선택하세요</option>
						<?php foreach(PLATFORM_TYPE as $k=>$v) {?>
						<option value="<?php echo $k?>" <?php echo get_selected($write['wr_warehouse'], $k)?> ><?php echo $v?>창고</option>
						<?php }?>
					</select>
					<select name="wr_rack" id="wr_rack" class="frm_input search_sel" style="width:50%">
						<?php if($w == "u" && $write['wr_warehouse']){
						
						$sql = " select * from g5_rack where gc_warehouse = '{$write['wr_warehouse']}' and gc_use = 1 order by gc_name asc ";
						$result = sql_query($sql);
						for($a=0; $rack=sql_fetch_array($result); $a++) {
						
						?>
						<option value="<?php echo $rack['seq']?>" <?php echo get_selected($write['wr_rack'], $rack['seq'])?>><?php echo $rack['gc_name']?></option>
						<?php }?>
						
						<?php } else {?>
						<option value="">창고를 먼저 선택하세요</option>
						<?php }?>
						
					</select>
					
					<script>
					
					$('#wr_warehouse').bind('change', function(){
						
						$.post('/stock/ajax.rack.php', { wr_id : <?php echo $wr_id?>, warehouse : $(this).val(), mode : '' }, function(data){
							$('#wr_rack').html(data);
							$('#wr_rack').select2();
						})
					})
					</script>
		    	</div>
		    </li>
			<li style="clear:both;height:30px"></li>
			<li class="bo_w_tit ">
				<div class="wli_tit">비고(메모)</div>
		    	<div class="wli_cnt">
		    		<textarea name="wr_25" class="frm_input" style="height:70px"><?php echo $write['wr_25']?></textarea>
		    	</div>
		    </li>
			
			<li style="clear:both"></li>
			<li class="bo_w_option" style="display:none">
		        <?php if ($option) { ?>
		        <div class="wli_tit"><span class="sound_only">글쓰기 옵션</span></div>
			        <div class="wli_cnt">
		            <span class="sound_only">옵션</span>
					<?php echo $option ?>
		        </div>
		        <?php } ?>
		        <script>
				$(document).ready(function(){
				    $("#notice").click(function(){
				        $(".notice_ck").toggleClass("click_on");
				    });
				    
				    $("#html").click(function(){
				        $(".html_ck").toggleClass("click_on");
				    });
				
				    $("#mail").click(function(){
				        $(".mail_ck").toggleClass("click_off");
				    });
		
				    $("#secret").click(function(){
				        $(".secret_ck").toggleClass("click_on");
				    });
				
				    $("input[type='checkbox']").each(function(){
				        var name = $(this).attr('name');
				        if($(this).prop("checked")) {
				            $(this).siblings("label[for='"+name+"']").addClass("click_on");
				        }
				    });
				});
		        </script>
			</li>
			
	        <li style="display:none">
		        <div class="wli_tit"><span class="sound_only">내용</span></div>
		        <div class="wli_cnt">
		        	<input type="hidden" name="wr_content" value="-">
	        	</div>
			</li>	
				
	        <?php for ($i=1; $is_link && $i<=0; $i++) { ?>
	        <li class="bo_w_link">
	        	<div class="wli_tit">링크</div>
	        	<div class="wli_cnt">
		            <label for="wr_link<?php echo $i ?>"><span class="sound_only">링크 #<?php echo $i ?></span></label>
		            <input type="text" name="wr_link<?php echo $i ?>" value="<?php if($w=="u"){echo $write['wr_link'.$i];} ?>" id="wr_link<?php echo $i ?>" class="frm_input wr_link" placeholder="링크를 입력하세요">
		        </div>
			</li>
	        <?php } ?>
	
	        <?php for ($i=0; $is_file && $i<$file_count; $i++) { ?>
	        <li class="bo_w_flie write_div">
	        	<div class="wli_tit">파일첨부</div>
	            <div class="file_wr wli_cnt">
	        		<label for="bf_file_<?php echo $i+1 ?>" class="lb_icon"><span class="sound_only">파일 #<?php echo $i+1 ?></span></label>
		            <input type="file" name="bf_file[]" id="bf_file_<?php echo $i+1 ?>" title="파일첨부 <?php echo $i+1 ?> : 용량 <?php echo $upload_max_filesize ?> 이하만 업로드 가능" class="frm_file ">
	        	</div>
	            <?php if ($is_file_content) { ?>
	            <input type="text" name="bf_content[]" value="<?php echo ($w == 'u') ? $file[$i]['bf_content'] : ''; ?>" title="파일 설명을 입력해주세요." class="full_input frm_input" size="50" placeholder="파일 설명을 입력해주세요.">
	            <?php } ?>
	
	            <?php if($w == 'u' && $file[$i]['file']) { ?>
	            <span class="file_del">
	                <input type="checkbox" id="bf_file_del<?php echo $i ?>" name="bf_file_del[<?php echo $i;  ?>]" value="1"> <label for="bf_file_del<?php echo $i ?>"><?php echo $file[$i]['source'].'('.$file[$i]['size'].')';  ?> 파일 삭제</label>
	            </span>
	            <?php } ?>
	        </li>
	        <?php } ?>
    	</ul>

	    <?php if ($is_use_captcha) { //자동등록방지 ?>
	    <div class="wli_cnt wli_captcha">
	        <span class="sound_only">자동등록방지</span>
	        <?php echo $captcha_html ?>
	    </div>
	    <?php } ?>
	</div>

    <div class="bo_w_btn">
        <a href="<?php echo get_pretty_url($bo_table); ?>" class="btn_cancel">취소</a>
        <button type="submit" id="btn_submit" class="btn_submit" accesskey="s">저장하기</button>
		<a href="javascript:;" onclick="fnDelProduct();" class="btn_submit" >삭제하기</a>
    </div>
    </form>
</section>

<script>
// 엔터 클릭시 submit 이벤트 방지
document.addEventListener('keydown', function(event) {
  if (event.keyCode === 13) {
    event.preventDefault();
  };
}, true);

$('#brand').select2();
function weight_calculation(type, width, height, length) 
{
	let calc = parseInt(width) * parseInt(height) * parseInt(length);
	let total = 0;
	if(type == 1) {
		//중량무게1 가로*세로*높이/5000
		total = calc / 5000000;
		$('#wr_18').val(total.toFixed(2));
	} else if(type == 2){
		//중량무게2 가로*세로*높이/6000
		total = calc / 6000000;
		$('#wr_19').val(total.toFixed(2));
	}
	
}


$(document).ready(function() {
    $('.search_sel').select2();
	
    let wr_33 = $("#wr_33 option:selected").val(),
        x = $("#wr_14").val(),
        y = $("#wr_15").val(),
        z = $("#wr_16").val();

        console.log(wr_33,x,y,z);

    if(wr_33=="세트"){
        weight_calculation(1, x, y, z); // 첫 시작시 변환
        weight_calculation(2, x, y, z);
    }

	//중량무게
	$('#wr_14, #wr_15, #wr_16').bind('keyup', function(){
		
		let width = parseInt($('#wr_14').val());
		let height = parseInt($('#wr_15').val());
		let length = parseInt($('#wr_16').val());
		
		if(isNaN(width)) {
			width = 0;
		}
		if(isNaN(height)) {
			height = 0;
		}
		if(isNaN(length)) {
			length = 0;
		}
		
		weight_calculation(1, width, height, length);
		weight_calculation(2, width, height, length);
		
	});
});
<?php if($write_min || $write_max) { ?>
// 글자수 제한
var char_min = parseInt(<?php echo $write_min; ?>); // 최소
var char_max = parseInt(<?php echo $write_max; ?>); // 최대
check_byte("wr_content", "char_count");

$(function() {
    $("#wr_content").on("keyup", function() {
        check_byte("wr_content", "char_count");
    });
	
	
});

<?php } ?>
function html_auto_br(obj)
{
    if (obj.checked) {
        result = confirm("자동 줄바꿈을 하시겠습니까?\n\n자동 줄바꿈은 게시물 내용중 줄바뀐 곳을<br>태그로 변환하는 기능입니다.");
        if (result)
            obj.value = "html2";
        else
            obj.value = "html1";
    }
    else
        obj.value = "";
}

function fwrite_submit(f)
{
    <?php echo $editor_js; // 에디터 사용시 자바스크립트에서 내용을 폼필드로 넣어주며 내용이 입력되었는지 검사함   ?>

	// 필수값 체크
	var chk = true;
	$("select[name='domain[]']").each(function(){
		if(!isDefined($(this).val())){
			chk = false;
		}
	});
	if(chk == true){
		$("select[name='warehouse[]']").each(function(){
			if(!isDefined($(this).val())){
				chk = false;
			}
		});
	}else{
		alert("도메인을 선택해주세요.");
		return false;
	}

	if(chk == true){
		$("input[name='product_fee[]']").each(function(){
			if(!isDefined($(this).val())){
				chk = false;
			}
		});

		if(chk == false){
			alert("수수료를 입력해주세요.");
			return false;
		}
	}else{
		alert("창고를 선택해주세요.");
		return false;
	}

    var subject = "";
    var content = "";
    $.ajax({
        url: g5_bbs_url+"/ajax.filter.php",
        type: "POST",
        data: {
            "subject": f.wr_subject.value,
            "content": f.wr_content.value
        },
        dataType: "json",
        async: false,
        cache: false,
        success: function(data, textStatus) {
            subject = data.subject;
            content = data.content;
        }
    });

    if (subject) {
        alert("제목에 금지단어('"+subject+"')가 포함되어있습니다");
        f.wr_subject.focus();
        return false;
    }

    if (content) {
        alert("내용에 금지단어('"+content+"')가 포함되어있습니다");
        if (typeof(ed_wr_content) != "undefined")
            ed_wr_content.returnFalse();
        else
            f.wr_content.focus();
        return false;
    }

    if (document.getElementById("char_count")) {
        if (char_min > 0 || char_max > 0) {
            var cnt = parseInt(check_byte("wr_content", "char_count"));
            if (char_min > 0 && char_min > cnt) {
                alert("내용은 "+char_min+"글자 이상 쓰셔야 합니다.");
                return false;
            }
            else if (char_max > 0 && char_max < cnt) {
                alert("내용은 "+char_max+"글자 이하로 쓰셔야 합니다.");
                return false;
            }
        }
    }

    <?php echo $captcha_js; // 캡챠 사용시 자바스크립트에서 입력된 캡챠를 검사함  ?>

    document.getElementById("btn_submit").disabled = "disabled";

    return true;
}

function fnDelProduct(){
	var wr_id = $("input[name='wr_id']").val();
	var params = "mode=DEL&wr_id="+wr_id;
	$.post("./ajax.product_proc.php",params,function(data){
		if(isDefined(data.message)){
			alert(data.message);
		}
		if(data.ret_code == true){
			document.location.href="/bbs/board.php?bo_table=product";
		}
	},'json');
}
</script>
