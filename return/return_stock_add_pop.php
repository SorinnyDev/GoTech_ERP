<?php
include_once('./_common.php');

if ($is_guest) alert_close('로그인 후 이용하세요.');

include_once(G5_PATH . '/head.sub.php');
add_stylesheet('<link rel="stylesheet" href="' . G5_ADMIN_URL . '/css/admin.css">', 0);
?>

  <div class="new_win">
    <h1>엑셀 반품 등록</h1>

    <div class="local_desc01 local_desc">
      <p>
        출고 처리된 주문 건만 반품 등록이 가능합니다.<br>
        <a href="#" onclick="excel_download();">샘플양식 다운로드</a>
      </p>

    </div>

    <div class="tw-space-y-5">
      <form name="fitemexcel" method="post" action="./pop_return_add_update.php" enctype="MULTIPART/FORM-DATA" autocomplete="off" onsubmit="return chkfrm(this)">
        <div id="excelfile_upload">
          <label for="excelfile" style="display:block;padding-bottom:10px">반품재고 자료</label>
          <input type="file" name="excelfile" id="excelfile" required style="width:100%">
        </div>

        <div class="win_btn btn_confirm">
          <input type="submit" value="반품재고 엑셀파일 등록" class="btn_submit btn">
        </div>
      </form>

    </div>

  </div>
  <script>
    function chkfrm(f) {

      $('.btn_submit').html('<img style="width:15px" src="/mobile/shop/img/loading.gif">');
      $('.btn_submit').attr('disabled', true);

    }

    function excel_download() {
      location.href = "/return/download_excel.php";
    }

    function excel_download2() {
      location.href = "/return/download_excel.php?type=waste";
    }

  </script>

<?php
include_once(G5_PATH . '/tail.sub.php');