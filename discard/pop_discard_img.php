<?php
include_once('./_common.php');

if ($is_guest)
  alert_close('로그인 후 이용하세요.');

include_once(G5_PATH . '/head.sub.php');
add_stylesheet('<link rel="stylesheet" href="' . G5_ADMIN_URL . '/css/admin.css">', 0);

if (!$id) {
  alert_close('잘못된 접근입니다.');
}

$sql = "
  select wr_subject, di.wr_img1, di.wr_img2, di.wr_img3, di.wr_img4, di.wr_img5 from g5_discard_list as dl
  left join g5_write_product as wp on wp.wr_id = dl.product_id
  left join g5_discard_img as di on di.discard_id = dl.id  
  left join g5_discard_stock as ds on ds.discard_id = dl.id
  where dl.id = '$id'                                                                                                         
  group by dl.id
  order by dl.wr_datetime desc
";

$discard = sql_fetch($sql);

if (!$discard) {
  alert_close('정보가 없습니다.');
}


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
    <h1>폐기상품 사진확인 - 상품명 : <?php echo $discard['wr_subject'] ?></h1>

    <div id="excelfile_upload" class="result_list" style="padding:12px;">
      <?php for ($i = 1; $i <= 5; $i++) {

        if ($discard['wr_img' . $i]) {
          echo '<img src="' . G5_DATA_URL . '/discard/' . $discard['wr_img' . $i] . '" width="100%" style="max-width:100%"><br>' . PHP_EOL;
        }
      } ?>
      <div class="win_btn btn_confirm" style="position:fixed;bottom:0;width:100%;">
        <button type="button" onclick="window.close();" class="btn_close btn">닫기</button>
      </div>
    </div>


  </div>


  </div>
  <script>

    function pop_search() {
      var _width = '650';
      var _height = '450';

      var _left = Math.ceil((window.screen.width - _width) / 2);
      var _top = Math.ceil((window.screen.height - _height) / 2);

      window.open("./return_img_search.php", "return_img_search", "left=" + _left + ", top=" + _top + ", width=" + _width + ", height=" + _height + ", scrollbars=1");

      return false;
    }

  </script>
<?php
include_once(G5_PATH . '/tail.sub.php');