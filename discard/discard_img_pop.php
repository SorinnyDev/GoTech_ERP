<?php
include_once('./_common.php');

if ($is_guest) {
  alert_close('로그인 후 이용하세요.');
}

include_once(G5_PATH . '/head.sub.php');
add_stylesheet('<link rel="stylesheet" href="' . G5_ADMIN_URL . '/css/admin.css">', 0);

$title = "폐기상품 관리";
$discard = sql_fetch("select * from g5_discard_list where id = '{$seq}'");
$discard_img = sql_fetch("select * from g5_discard_img where discard_id = '{$seq}'");
$item = sql_fetch("select wr_subject from g5_write_product where wr_id = '{$discard['product_id']}'");

?>
  <style>
      .not_item td {
          background: red;
          color: #fff
      }

      .pg a {
          margin: 0 5px
      }

      .tbl_head01 td {
          background: none
      }

      .move_stock th {
          width: 100px;
          background: #ddd
      }

      .move_stock select {
          width: 30% !important
      }

      .move_stock button {
          width: 30% !important;
          height: 35px;
          line-height: 35px;
          border: 1px solid #2aba8a;
          background: #2aba8a;
          color: #fff
      }

      .move_stock2 td {
          padding: 15px
      }

      .down_arrow {
          position: relative
      }

      .down_arrow::after {
          content: "↓";
          position: absolute;
          top: -14px;
          font-size: 20px;
          font-weight: 600;
          left: 50%;
          background: #fff;
          margin-left: -20px;
          color: #2aba8a
      }

      .diabled_btn {
          background: #ddd;
          cursor: not-allowed
      }

      #excelfile_upload strong {
          display: inline-block;
          width: 70px;
          margin-bottom: 5px
      }
  </style>
  <div class="new_win">
    <h1><?php echo $title ?></h1>


    <form name="frm" action="./discard_img_pop_update.php" method="post" enctype="multipart/form-data">
      <input type="hidden" name="discard_img_id" value="<?php echo $discard_img['id'] ?>">
      <input type="hidden" name="discard_id" value="<?php echo $discard['id'] ?>">
      <input type="hidden" name="product_id" value="<?php echo $discard['product_id'] ?>">


      <div id="excelfile_upload" class="result_list" style="padding:12px;">

        <div style="clear:both"></div>
        <div class="tbl_frm01 tbl_wrap" style="">
          <table>

            <tbody>
            <?php for ($i = 1; $i <= 5; $i++) { ?>
              <tr class="<?php echo $bg; ?>">
                <td>사진 <?php echo $i ?></td>
                <td><input type="file" name="wr_img<?php echo $i ?>">

                  <?php if ($discard_img['wr_img' . $i]) { ?>
                    <label>
                      <img src="<?php echo G5_DATA_URL ?>/discard/<?php echo $discard_img['wr_img' . $i] ?>" width="50" height="50">
                      <input type="checkbox" name="wr_img_del<?php echo $i ?>" value="1"> 사진삭제
                    </label>
                    <a href="./discard_img_download.php?file=<?= $discard_img['wr_img' . $i] ?>" class="btn btn_01 tw-ml-2">다운로드</a>
                  <?php } ?>
                </td>
              </tr>
            <?php } ?>
            </tbody>


          </table>
        </div>
        <div class="win_btn btn_confirm tw-mt-2" style="">
          <input type="submit" value="확인" class="btn_submit btn">
          <button type="button" onclick="window.close();" class="btn_close btn">닫기</button>
        </div>
      </div>


  </div>
<?php
include_once(G5_PATH . '/tail.sub.php');