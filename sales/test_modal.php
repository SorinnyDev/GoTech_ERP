<?php
include_once("./_common.php");

?>

<button type="button" class="btn_calc " id="btn_calc">계산</button>

<!-- 부피 및 무게 계산 팝업 -->
<div class="calc_popup" style="display:none;">
  <fieldset class="tw-fixed tw-top-1/2 tw-left-1/2 tw-transform tw--translate-x-1/2 tw--translate-y-1/2 tw-bg-white tw-text-center tw-p-6 tw-rounded-lg tw-shadow-[0_10px_30px_rgba(0,0,0,0.3)] tw-backdrop-blur-md tw-border tw-border-gray-300">
    <p class="tw-font-bold tw-text-base">최적 배송사 검색</p>
    <hr>
    <div class="calc_div">
      <label for="stx" style="font-weight:bold">가로<strong class="sound_only"> 필수</strong></label>
      <div style="border:1px solid #ddd; margin-bottom:20px; padding:10px">
        <input type="text" name="wr_hab_x" id="hab_x" value="<?=(int)$row['wr_hab_x']?>" placeholder="가로" class="frm_input number_fmt_list" style="background:#FFFFFF;"/>mm
      </div>

      <label for="stx" style="font-weight:bold">세로<strong class="sound_only"> 필수</strong></label>
      <div style="border:1px solid #ddd; margin-bottom:20px; padding:10px">
        <input type="text" name="wr_hab_y" id="hab_y" value="<?=(int)$row['wr_hab_y']?>" placeholder="세로" class="frm_input number_fmt_list" style="background:#FFFFFF;"/>mm
      </div>

      <label for="stx" style="font-weight:bold">높이<strong class="sound_only"> 필수</strong></label>
      <div style="border:1px solid #ddd; margin-bottom:20px; padding:10px">
        <input type="text" name="wr_hab_z" id="hab_z" value="<?=(int)$row['wr_hab_z']?>" placeholder="높이" class="frm_input number_fmt_list" style="background:#FFFFFF;"/>mm
      </div>

      <label for="stx" style="font-weight:bold">부피무게1<strong class="sound_only"> 필수</strong></label>
      <div style="border:1px solid #ddd; margin-bottom:20px; padding:10px">
        <input type="text" name="wr_weight_sum1" id="hab_weight1" value="<?=(float)$row['wr_weight_sum1']?>" class="frm_input number_fmt_list"  style="background:#FFFFFF;"/>kg
      </div>

      <label for="stx" style="font-weight:bold">부피무게2<strong class="sound_only"> 필수</strong></label>
      <div style="border:1px solid #ddd; margin-bottom:20px; padding:10px">
        <input type="text" name="wr_weight_sum2" id="hab_weight2" value="<?=(float)$row['wr_weight_sum2']?>" class="frm_input number_fmt_list" style="background:#FFFFFF;" />kg
      </div>

      <label for="stx" style="font-weight:bold">총 무게<strong class="sound_only"> 필수</strong></label>
      <div style="border:1px solid #ddd; margin-bottom:20px; padding:10px">
        <input type="text" name="wr_weight_sum3" id="hab_weight3" value="<?=(float)$row['wr_weight_sum3']?>" class="frm_input number_fmt_list" style="background:#FFFFFF;" />kg
      </div>

      <button type="button" value="계산하기" id="hab_cal_btn" class="btn_b01" style="width:49%;margin-top:15px"><i class="fa fa-search" aria-hidden="true"></i>무게 자동 계산하기</button>
      <button type="button" value="계산하기" id="delivery_cal_btn" class="btn_b01" style="width:49%;margin-top:15px"><i class="fa fa-search" aria-hidden="true"></i>배송비 계산하기</button>
      <button type="button" class="calc_popup_cls" title="닫기"><i class="fa fa-times" aria-hidden="true"></i><span class="sound_only">닫기</span></button>
    </div>
  </fieldset>
  <div class="bo_sch_bg"></div>
</div>
<!--// 부피 및 무게 계산 팝업 -->

<script>
  $(function () {
    alert(123);
  })
  $(document).ready(function(){
    $("#btn_calc ").on("click",function(){
      $(".calc_popup").show();
    });
    $(".calc_popup_cls").on("click",function(){
      $(".calc_popup").hide();
    });
  });
</script>
