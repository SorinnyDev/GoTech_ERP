<?php
include_once('./_common.php');

include_once(G5_THEME_PATH . '/head.php');

# 환율 정보
$sql = "select rate from g5_excharge where ex_eng = 'JPY'";
$result = sql_fetch($sql);
$ex_jpy = $result['rate'] / 100;

?>
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


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
          line-height: 43px
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
          padding: 10px 10px
      }

      .tbl_frm01 td input {
          border: 1px solid #ddd;
          padding: 3px;
          width: 100%;
          height: 30px
      }

      .tbl_frm01 .btn_b02 {
          height: 30px;
          line-height: 30px
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

      .list_03 ul {
          height: 348px;
      }


  </style>
  <div id="bo_list">
    <div class="bo_list_innr">
      <h2 class="board_tit">배송비조회</h2>


      <div id="bo_li_top_op">

      </div>
      <div class="tw-flex tw-space-x-10 tw-justify-around">
        <div id="bo_li_01" class="" style="float:left; overflow-y:hidden;  min-height:500px ">
          <div class="tbl_frm01 tbl_wrap" style="width:400px">
            <form name="searchFrm" id="searchFrm" method="get">
              <table>
                <tr>
                  <th>나라</th>
                  <td>
                    <select name="country" class="search_sel">
                      <?php
                      $topSql = "select * from g5_country order by wr_country_en ASC";
                      $topRst = sql_query($topSql);
                      for ($i = 0; $top = sql_fetch_array($topRst); $i++) {

                        ?>
                        <option value="<?php echo $top['wr_code'] ?>" <?php echo get_selected($top['wr_code'], $country) ?>><?php echo $top['wr_country_ko'] ?>(<?php echo $top['wr_code'] ?>)</option>
                      <?php } ?>
                    </select>
                  </td>
                </tr>
                <tr>
                  <th>상품선택</th>
                  <td><input type="text" name="product" class="frm_input" style="width:80%" value="<?php echo urldecode($product) ?>">
                    <button type="button" class="btn btn_b02" onclick="add_pop()"><i class="fa fa-search" aria-hidden="true"></i></button>
                  </td>
                </tr>
                <tr>
                  <th>무게</th>
                  <td><input type="text" name="weight" class="frm_input" style="width:20%" value="<?php echo urldecode($weight) ?>"> kg
                  </td>
                </tr>
                <tr>
                  <th>가로(W)</th>
                  <td><input type="text" name="width" id="width" class="frm_input" style="width:20%" value="<?php echo urldecode($width) ?>"> mm
                  </td>
                </tr>
                <tr>
                  <th>길이(L)</th>
                  <td><input type="text" name="length" id="length" class="frm_input" style="width:20%" value="<?php echo urldecode($length) ?>"> mm
                  </td>
                </tr>
                <tr>
                  <th>높이(H)</th>
                  <td><input type="text" name="height" id="height" class="frm_input" style="width:20%" value="<?php echo urldecode($height) ?>"> mm
                  </td>
                </tr>
                <tr>
                  <th>중량무게1</th>
                  <td><input type="text" name="weight1" id="weight1" class="frm_input" style="width:20%" value="<?php echo urldecode($weight1) ?>"> kg
                  </td>
                </tr>
                <tr>
                  <th>중량무게2</th>
                  <td><input type="text" name="weight2" id="weight2" class="frm_input" style="width:20%" value="<?php echo urldecode($weight2) ?>"> kg
                  </td>
                </tr>
              </table>
              <div style="width:100%;text-align:center;margin-top:20px">
                <button class="btn btn_b01">배송비 검색</button>
                <button class="btn btn_b02" onclick="location.href='./search.php'" style="height:37px">초기화</button>
              </div>
            </form>
          </div>
        </div>
        <div id="bo_li_01" class="tw-h-[405px] tw-overflow-x-auto" style="border:1px solid #ddd">
          <ul class="list_head tw-min-w-[900px]" style="position:sticky;top:0;background:#fff;z-index:2;">
            <li style="width:150px">코드</li>
            <li style="width:150px">배송사</li>
            <li style="width:100px">무게</li>
            <li style="width:150px">배송비</li>
            <li style="width:150px">유류할증료</li>
            <li style="width:150px">합계</li>
          </ul>
          <div id="bo_li_01" class="list_03  tw-min-w-[900px]">
            <ul>
              <?php

              # 환율 정보
              $sql = "select rate, ex_eng from g5_excharge";
              $result = sql_fetch_all($sql);

              $ex_list = array_column($result, 'rate', 'ex_eng');

              //24.01.10 dhl, 페덱스, 쉽터, UPS : 무게, 중량무게 1~2 무거운걸로 / ems, k팩, s팩: 무게로만
              $weight = max(number_format($weight, 2), number_format($weight1, 2), number_format($weight2, 2));

              $sql_where = " AND C.wr_use = '1'";
              $sql_group_by = '';
              $total_weight = 0;
              $volume = 0;
              $max_weight = 0;

              if ($product) {
                $wr_weight3 = sql_fetch("SELECT *,IF(wr_18 > wr_19,wr_18,wr_19) AS wr_weight3 FROM g5_write_product WHERE wr_subject LIKE '%{$product}%' limit 1");

                $total_weight += (float)$wr_weight3['wr_10'];
                $volume += ((float)$wr_weight3['wr_weight3']);

                $weight = max($total_weight, $volume);

              }

              if ($weight) {
                $sql_where .= " and A.weight_code + 0 >= '$weight'";
                $sql_group_by .= " group by cust_code";
              }

              $sql = "select {$country} as price, cust_code, weight_code, C.wr_percent, C.wr_name from g5_shipping_price A
              inner join g5_delivery_company as C on C.wr_code = A.cust_code
              where (1)
              {$sql_where} {$sql_group_by}
              order by price, cust_code, weight_code asc";

              $rst = sql_fetch_all($sql);

              $zeroPriceList = [];
              $list = [];
              foreach ($rst as $k => &$item) {
                if ($item['cust_code'] === '1021') {
                  $item['price'] = $item['price'] * $ex_jpy;
                }

                if ($item['cust_code'] === '1029') {
                  $item['price'] = round($item['price'] * $ex_list['USD']);
                }

                $oil_percent = $item['wr_percent'];
                $oil_price = round($item['price'] * $oil_percent);
                $item['oil_price'] = $oil_price;
                $item['total_price'] = $oil_price + $item['price'];


                if ($item['price'] == 0) {
                  $zeroPriceList[] = $item;
                  unset($rst[$k]);
                }
              }

              usort($rst, function ($a, $b) {
                return $a['total_price'] - $b['total_price'];
              });

              $list = array_merge($rst, $zeroPriceList);

              foreach ($list as $row) {

                ?>
                <li class="<?php echo $bg ?>">
                  <div class="cnt_left tw-text-center" style="width:150px;"><?php echo $row['cust_code'] ?></div>
                  <div class="cnt_left" style="width:150px"><?php echo $row['wr_name'] ?></div>
                  <div class="cnt_left" style="text-align:center;width:100px"><?php echo $row['weight_code'] ?></div>
                  <div class="cnt_left" style="text-align:right;width:150px;"><?php echo number_format($row['price']) ?></div>
                  <div class="cnt_left" style="text-align:right;width:150px;"><?= number_format($row['oil_price']) ?></div>
                  <div class="cnt_left" style="text-align:right;width:150px;border-right:0"><?= number_format($row['total_price']) ?></div>
                </li>
                <?php
              } ?>
              <?php if ($i == 0) {
                echo '<li class="empty_table" style="width:570px;">내역이 없습니다.</li>';
              } ?>
            </ul>
          </div>
        </div>
      </div>


    </div>
    <div style="clear:both"></div>
    <?php //echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, $_SERVER['SCRIPT_NAME'] . '?' . $qstr . '&amp;page='); ?>
  </div>

  <div class="bo_sch_wrap">
    <fieldset class="bo_sch">
      <h3>검색</h3>
      <form name="fsearch" method="get">

        <div class="sch_bar" style="margin-top:3px">

          <input type="text" name="stx2" value="<?php echo stripslashes($stx2) ?>" required id="stx" class="sch_input" size="25" maxlength="255" placeholder="배송사 검색">
          <button type="submit" value="검색" class="sch_btn"><i class="fa fa-search" aria-hidden="true"></i><span class="sound_only">검색</span></button>
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
    function weight_calculation(type, width, height, length) {
      let calc = parseInt(width) * parseInt(height) * parseInt(length);
      let total = 0;
      if (type == 1) {
        //중량무게1 가로*세로*높이/5000
        total = calc / 5000000;
        $('#weight1').val(total.toFixed(2));
      } else if (type == 2) {
        //중량무게2 가로*세로*높이/6000
        total = calc / 6000000;
        $('#weight2').val(total.toFixed(2));
      }

    }

    function add_pop() {

      window.open("./search_item_pop.php", "item_pop", "left=50, top=50, width=1100, height=650, scrollbars=1");

    }

    $(function () {

      $(document).ready(function () {
        $('.search_sel').select2();

        $('#width, #height, #length').bind('keyup', function () {

          let width = parseInt($('#width').val());
          let height = parseInt($('#height').val());
          let length = parseInt($('#length').val());

          if (isNaN(width)) {
            width = 0;
          }
          if (isNaN(height)) {
            height = 0;
          }
          if (isNaN(length)) {
            length = 0;
          }

          weight_calculation(1, width, height, length);
          weight_calculation(2, width, height, length);

        })
      });


    })

  </script>


<?php
include_once(G5_THEME_PATH . '/tail.php');