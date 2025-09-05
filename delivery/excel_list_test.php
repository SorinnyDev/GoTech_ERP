<?php
include_once('./_common.php');

include_once(G5_THEME_PATH . '/head.php');

$query_string = $_GET;
$query_string['page'] = '';

?>
  <link rel="stylesheet" href="/theme/parsley/mobile/skin/board/sales/style.css?ver=2303229">
  <style>
      .cnt_left {
          padding: 5px 10px;
          border-right: 1px solid #ddd;
          word-break: text-overflow: ellipsis;
          overflow: hidden;
          white-space: nowrap;
      }

      .list_03 li {
          padding: 0
      }

      .list_03 li .cnt_left {
          line-height: 1.5em
      }

      .modify {
          cursor: pointer
      }

      .tbl_frm01 th {
          background: #6f809a;
          color: #fff;
          border: 1px solid #60718b;
          font-weight: normal;
          text-align: center;
          padding: 8px 5px;
          font-size: 0.92em
      }

      .tbl_frm01 td {
          border-bottom: 1px solid #ddd;
      }

      .tbl_frm01 td input {
          border: 1px solid #ddd;
          padding: 3px;
          width: 100%
      }

      .tbl_frm01 input.readonly {
          background: #f2f2f2
      }

      .local_ov01 {
          position: relative;
          margin: 10px 0;
      }

      .local_ov01 .ov_a {
          display: inline-block;
          line-height: 30px;
          height: 30px;
          font-size: 0.92em;
          background: #ff4081;
          color: #fff;
          vertical-align: top;
          border-radius: 5px;
          padding: 0 7px
      }

      .local_ov01 .ov_a:hover {
          background: #ff1464
      }

      .btn_ov01 {
          display: inline-block;
          line-height: 30px;
          height: 30px;
          font-size: 0.92em;
          vertical-align: top
      }

      .btn_ov01:after {
          display: block;
          visibility: hidden;
          clear: both;
          content: ""
      }

      .btn_ov01 .ov_txt {
          float: left;
          background: #9eacc6;
          color: #fff;
          border-radius: 5px 0 0 5px;
          padding: 0 5px
      }

      .btn_ov01 .ov_num {
          float: left;
          background: #ededed;
          color: #666;
          border-radius: 0 5px 5px 0;
          padding: 0 5px
      }

      a.btn_ov02, a.ov_listall {
          display: inline-block;
          line-height: 30px;
          height: 30px;
          font-size: 0.92em;
          background: #565e8c;
          color: #fff;
          vertical-align: top;
          border-radius: 5px;
          padding: 0 7px
      }

      a.btn_ov02:hover, a.ov_listall:hover {
          background: #3f51b5
      }

      .tbl_wrap table {
          border-collapse: collapse;
          width: 100%;
      }

      .tbl_wrap thead th {
          position: sticky;
          top: 0;
          z-index: 2;
      }

      .tbl_wrap tbody {
          overflow-y: scroll;
          height: 580px;
      }

      .list_03 ul {
          height: 580px;overflow:auto;
      }

  </style>
  <div id="bo_list">
    <div class="bo_list_innr">
      <h2 class="board_tit">배송비 엑셀조회</h2>
      <form name="fboardlist" id="fboardlist" action="<?php echo G5_BBS_URL; ?>/board_list_update.php" onsubmit="return fboardlist_submit(this);" method="post">
        <input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
        <input type="hidden" name="sfl" value="<?php echo $sfl ?>">
        <input type="hidden" name="stx" value="<?php echo $stx2 ?>">
        <input type="hidden" name="spt" value="<?php echo $spt ?>">
        <input type="hidden" name="sst" value="<?php echo $sst ?>">
        <input type="hidden" name="sod" value="<?php echo $sod ?>">
        <input type="hidden" name="page" value="<?php echo $page ?>">
        <input type="hidden" name="wr_code" value="<?php echo $wr_code ?>">
        <input type="hidden" name="sw" value="">

        <?php if ($is_category) { ?>
          <nav id="bo_cate">
            <h2><?php echo($board['bo_mobile_subject'] ? $board['bo_mobile_subject'] : $board['bo_subject']) ?> 카테고리</h2>
            <ul id="bo_cate_ul">
              <?php echo $category_option ?>
            </ul>
          </nav>
        <?php } ?>

        <div id="bo_li_top_op">
          <div class="bo_list_total">
            <div class="local_ov01 local_ov">
				<span class="btn_ov01">
				&nbsp;
				</span>

            </div>


          </div>
          <ul class="<?php echo isset($view) ? 'view_is_list btn_top' : 'btn_top2'; ?>">
            <?php if ($rss_href) { ?>
              <li><a href="<?php echo $rss_href ?>" class="btn_b02">RSS</a></li><?php } ?>


            <li>
              <button type="button" class="btn_b02 btn_bo_sch"><i class="fa fa-search" aria-hidden="true"></i> 검색</button>
            </li>
            <li>
              <button type="button" class="btn02 excel_form" style="height:37px"><i class="fa fa-file-excel-o" aria-hidden="true"></i> 엑셀등록</button>
            </li>
            <li>
              <button type="button" class="btn01 delivery_com" style="height:37px"><i class="fa fa-truck" aria-hidden="true"></i> 배송사 관리</button>
            </li>


          </ul>
        </div>
        <div id="bo_li_01" style="clear:both;overflow-x:scroll;overflow-y:hidden">
          <ul class="list_head" style="width:max-content;position:sticky;top:0;background:#fff;z-index:2;">
            <li style="width:150px;position:sticky;left:0;background:#fff">배송사</li>
            <li style="width:100px;position:sticky;left:150px;background:#fff">무게</li>
            <?php
            $topSql = "select * from g5_country order by wr_country_en ASC";
            $topRst = sql_query($topSql);
            for ($i = 0; $top = sql_fetch_array($topRst); $i++) {

              $country[] = $top['wr_code'];
              ?>
              <li style="width:150px"><?php echo $top['wr_country_en'] ?></li>
            <?php } ?>


          </ul>
          <div id="bo_li_01" class="list_03">
            <ul style="width:max-content; height: 580px;overflow:auto;">
              <?php

              $sql_common = " from g5_shipping_price ";
              $sql_search = " where (1) ";

              if ($wr_code) {
                $sql_search .= " and cust_code = '$wr_code'";
              }

              if ($weight_code) {
                $sql_search .= " and weight_code = '$weight_code'";
              }

              if ($stx2) {
                $sql_search .= " and  (1)  ";
              }
              if (!$sst) {
                $sst = "cust_code";
                $sod = "desc";
              }

              if ($sst == "stock")
                $sst = "(wr_32+wr_36+wr_37)";

              $sql_order = " order by $sst $sod ";
              $sql = " select count(*) as cnt from g5_shipping_price as sp inner join g5_delivery_company as dc on dc.wr_code = sp.cust_code {$sql_search} {$sql_order} ";

              $row = sql_fetch($sql);
              $total_count = $row['cnt'];

              $rows = 50;
              $total_page = ceil($total_count / $rows);  // 전체 페이지 계산
              if ($page < 1) {
                $page = 1; // 페이지가 없으면 첫 페이지 (1 페이지)
              }
              $from_record = ($page - 1) * $rows; // 시작 열을 구함

              $cur_no = $total_count - $from_record;


              $sql = " select * from g5_shipping_price as sp inner join g5_delivery_company as dc on dc.wr_code = sp.cust_code {$sql_search} {$sql_order} limit {$from_record}, {$rows} ";

              $rst = sql_query($sql);
              for ($i = 0; $row = sql_fetch_array($rst); $i++) {

                $bg = 'bg' . ($i % 2);
                ?>
                <li class="<?php echo $bg ?>">
                  <div class="cnt_left" style="width:150px;position:sticky;left:0;background:#fff"><?php echo $row['wr_name'] ?></div>
                  <div class="cnt_left" style="text-align:center;width:100px;position:sticky;left:150px;background:#fff"><?php echo $row['weight_code'] ?></div>
                  <?php foreach ($country as $crow) { ?>
                    <div class="cnt_left" style="text-align:right;width:150px"><?php echo number_format($row[$crow]) ?></div>
                  <?php } ?>
                </li>
                <?php
                $cur_no = $cur_no - 1;
              } ?>
              <?php if ($i == 0) {
                echo '<li class="empty_table">내역이 없습니다.</li>';
              } ?>
            </ul>
          </div>
        </div>

      </form>

    </div>

    <?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, $_SERVER['SCRIPT_NAME'] . '?' . http_build_query($query_string)); ?>
  </div>

  <div class="bo_sch_wrap">
    <fieldset class="bo_sch">
      <h3>검색</h3>
      <form name="fsearch" method="get">

        <div class="sch_bar" style="margin-top:3px; border:none;">

          <div style="margin-bottom: 15px;">
            <select name="wr_code" class="frm_input search_sel" style="width:100%;">
              <option value="">배송사</option>
              <?php
              $sql = "select * from g5_delivery_company where wr_use = 1";
              $result = sql_query($sql);
              while ($row = sql_fetch_array($result)) {
                ?>
                  <option value="<?=$row['wr_code']?>" <?=get_selected($wr_code, $row['wr_code'])?>><?=$row['wr_name']?></option>
              <?php
              }
              ?>
            </select>
          </div>
          <div style="margin-bottom:15px;">
            <input type="number" step="0.5" name="weight_code" value="<?=$weight_code?>" class="frm_input" style="width:100%;"
                   placeholder="무게" autocomplete="off">
          </div>
          <button type="submit" value="검색" class="btn_b01" style="width:49%;margin-top:15px"><i class="fa fa-search"
                                                                                                aria-hidden="true"></i> 검색하기
          </button>
          <button type="button" value="초기화" class="btn_b02" style="width:49%;margin-top:15px;"
                  onclick="location.href='<?php echo $_SERVER['PHP_SELF'] ?>'"><i class="fa fa-repeat" aria-hidden="true"></i>
            검색초기화
          </button>
        </div>
        <button type="button" class="bo_sch_cls" title="닫기"><i class="fa fa-times" aria-hidden="true"></i><span class="sound_only">닫기</span></button>
      </form>
    </fieldset>
    <div class="bo_sch_bg"></div>
  </div>
  <script>
    jQuery(function ($) {
      // 게시판 검색
      $(".btn_bo_sch").on("click", function () {
        $(".bo_sch_wrap").toggle();
      })
      $('.bo_sch_bg, .bo_sch_cls').click(function () {
        $('.bo_sch_wrap').hide();
      });
    });
  </script>
  <script>


    $(function () {
      $('#sorting_box').bind('change', function () {

        let sort = $(this).val();

        if (sort == "default") {
          location.href = '?sst=wr_id&sod=desc&stx2=<?php echo $stx2?>';
        } else if (sort == "up") {
          location.href = '?sst=stock&sod=desc&stx2=<?php echo $stx2?>';
        } else if (sort == "down") {
          location.href = '?sst=stock&sod=asc&stx2=<?php echo $stx2?>';
        }
      })

      $('.save_btn').bind('click', function () {

        let id = $(this).attr('data');
        let stock1 = $(this).closest('li').find('.wr_32').val();
        let stock2 = $(this).closest('li').find('.wr_36').val();
        let stock3 = $(this).closest('li').find('.wr_37').val();

        $.post('./stock_update.php', {wr_id: id, stock1: stock1, stock2: stock2, stock3: stock3}, function (data) {
          if (data == "y") {
            alert('재고수량이 저장되었습니다.');
          } else {
            alert('처리 중 오류가 발생했습니다.');
          }
        })

      });

      $('.excel_form').bind('click', function () {

        let id = $(this).attr('data');
        var _width = '600';
        var _height = '600';

        var _left = Math.ceil((window.screen.width - _width) / 2);
        var _top = Math.ceil((window.screen.height - _height) / 2);

        window.open("./excel_pop.php", "excel_pop", "left=" + _left + ", top=" + _top + ", width=" + _width + ", height=" + _height + ", scrollbars=1");

        return false;
      });

      $('.delivery_com').bind('click', function () {

        var _width = '625';
        var _height = '600';

        var _left = Math.ceil((window.screen.width - _width) / 2);
        var _top = Math.ceil((window.screen.height - _height) / 2);

        window.open("./delivery_company.php", "pop_delivery_company", "left=" + _left + ", top=" + _top + ", width=" + _width + ", height=" + _height + ", scrollbars=1");

        return false;
      });


    })

  </script>


<?php
include_once(G5_THEME_PATH . '/tail.php');