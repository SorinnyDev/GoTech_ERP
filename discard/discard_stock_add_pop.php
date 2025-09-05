<?php
include_once('./_common.php');

if ($is_guest) alert_close('로그인 후 이용하세요.');

include_once(G5_PATH . '/head.sub.php');
add_stylesheet('<link rel="stylesheet" href="' . G5_ADMIN_URL . '/css/admin.css">', 0);
?>

  <div class="new_win">
    <h1>폐기 재고 등록</h1>

    <div class="local_desc01 local_desc">
      <p>
        엑셀파일을 저장하실 때는 <strong>Excel 97 - 2003 통합문서 (*.xls)</strong> 로 저장하셔야 합니다. <br>
      </p>
    </div>

    <div class="tw-space-y-5">
      <form name="fitemexcel2" method="post" action="./pop_discard_add_update.php" enctype="MULTIPART/FORM-DATA" autocomplete="off" onsubmit="return chkfrm(this)">
        <div id="excelfile_upload">
          <label for="excelfile" style="display:block;padding-bottom:10px">폐기재고 자료</label>
          <input type="file" name="excelfile" id="excelfile2" required style="width:100%">
        </div>

        <div class="win_btn btn_confirm">
          <input type="button" value="폐기재고 엑셀파일 양식" class="btn btn_01" onclick="excel_download2();">
          <input type="submit" value="폐기재고 엑셀파일 등록" class="btn_submit btn">
        </div>
      </form>

    </div>

  </div>
  <script>
    function chkfrm(f) {

      $('.btn_submit').html('<img style="width:15px" src="/mobile/shop/img/loading.gif">');
      $('.btn_submit').attr('disabled', true);

    }

    function excel_download2() {
      location.href = "/return/download_excel.php?type=waste";
    }

  </script>

<?php
include_once(G5_PATH . '/tail.sub.php');