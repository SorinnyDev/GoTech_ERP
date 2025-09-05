<?php
include_once('./_common.php');
include_once(G5_THEME_PATH . '/head.php');

if (!$date1) $date1 = date('Y-m-01');
if (!$date2) $date2 = date('Y-m-d');

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

      .bg1 {
          background: #eff3f9
      }

      .list_03 ul {
          height: 580px;overflow:auto;
      }
  </style>
  <div id="bo_list">
    <div class="bo_list_innr">
      <h2 class="board_tit">반품사진</h2>

      <form name="fboardlist" id="fboardlist" action="" onsubmit="return fboardlist_submit(this);" method="post">
        <input type="hidden" name="mode" value="delete">
        <div id="bo_li_top_op">
          <div class="bo_list_total">
            <div class="local_ov01 local_ov">
				<span class="btn_ov01">
					<span> <button type="submit" name="btn_submit" value="선택삭제" class="btn02" onclick="document.pressed=this.value">선택삭제</button></span>
				</span>

            </div>


          </div>

          <ul class="<?php echo isset($view) ? 'view_is_list btn_top' : 'btn_top2'; ?>">
            <?php if ($rss_href) { ?>
              <li><a href="<?php echo $rss_href ?>" class="btn_b02">RSS</a></li><?php } ?>

            <li>
              <button type="button" class="btn_b01" onclick="pop_img_add()"><i class="fa fa-picture-o" aria-hidden="true"></i> 사진등록</button>
            </li>
            <li>
              <button type="button" class="btn_b02 btn_bo_sch"><i class="fa fa-search" aria-hidden="true"></i> 검색</button>
            </li>


          </ul>
        </div>

        <div id="bo_li_01" style="clear:both; overflow-x: scroll;">

          <ul class="list_head" style="width:100%;min-width:max-content;position:sticky;top:0;background:#fff;z-index:2;">
            <li style="width:30px"><input type="checkbox" id="chkall" onclick="if (this.checked) all_checked(true); else all_checked(false);"></li>
            <li style="width:200px">주문번호</li>
            <li style="width:130px">등록일시</li>
            <li style="width:200px">SKU</li>
            <li style="width:500px">상품명</li>
            <li style="width:150px">이미지</li>
            <li style="width:200px">관리</li>
          </ul>
          <div id="bo_li_01" class="list_03">
            <ul style="width:100%;min-width:max-content;">
              <?php

              if ($date1 && $date2)
                $sql_search .= " and a.wr_datetime BETWEEN '{$date1} 00:00:00' AND '{$date2} 23:59:59' ";


              if (!$sql_search) {
                $sql_search .= "  ";
              }

              if ($stx)
                $sql_search .= " and b.wr_order_num LIKE '%$stx%' ";

              if (!$sst && !$sod) {
                $sst = "a.seq";
                $sod = "desc";
              }
              $sql_order = "order by $sst $sod";

              $sql = "select a.*, a.wr_datetime as add_date, b.wr_order_num from g5_return_img a LEFT JOIN g5_return_list b ON(a.return_id=b.seq) where (1) {$sql_search} {$sql_order}";
              $rst = sql_query($sql);
              for ($i = 0; $row = sql_fetch_array($rst); $i++) {

                $item = sql_fetch("select * from g5_write_product where wr_id = '{$row['product_id']}' ");
                $bg = 'bg' . ($i % 2);
                ?>
                <li class="<?php echo $bg ?>">
                  <div class="cnt_left" style="width:30px;text-align:center">
                    <input type="checkbox" name="seq[]" value="<?php echo $row['seq'] ?>"></div>
                  <div class="cnt_left" style="width:200px;text-align:center">
                    <?php echo $row['wr_order_num'] ?></div>
                  <div class="cnt_left" style="width:130px;text-align:center">
                    <?php echo $row['add_date'] ?></div>
                  <div class="cnt_left" style="width:200px"><?php echo $item['wr_1'] ?></div>
                  <div class="cnt_left" style="width:500px"><?php echo $item['wr_subject'] ?></div>
                  <div class="cnt_left" style="text-align:center;width:150px">
                    <?php
                    for ($a = 1; $a <= 5; $a++) {

                      if ($row['wr_img' . $a]) {
                        echo '<a href="javascript:pop_img_view(' . $row['seq'] . ')"><img src="' . G5_DATA_URL . '/return/' . $row['wr_img' . $a] . '" width="50" height="50"></a>' . PHP_EOL;
                      }

                    }

                    ?>
                  </div>


                  <div class="cnt_left" style="width:200px;text-align:center">

                    <button type="button" class="btn btn_b01 mod_btn" data="<?php echo $row['seq'] ?>">수정</button>
                    <a href="./return_img_pop_update.php?w=d&seq=<?php echo $row['seq'] ?>" class="btn btn_b02" onclick="del(this.href); return false;">삭제</a>

                  </div>

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
    <div style="clear:both"></div>
    <?php //echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, $_SERVER['SCRIPT_NAME'] . '?' . $qstr . '&amp;page='); ?>
  </div>

  <div class="bo_sch_wrap">
    <fieldset class="bo_sch">
      <h3>검색</h3>
      <form name="fsearch" method="get">
        <input type="hidden" name="mode" value="<?php echo $mode ?>">
        <div class="sch_bar" style="margin-top:3px">
          <input type="date" name="date1" value="<?php echo $date1 ?>" required id="stx" class="sch_input" size="25" maxlength="20" placeholder="" style="width:45%;text-align:center">
          <span style="float:left;display:inline-block;height:38px;line-height:38px; margin:0 5px">~</span>
          <input type="date" name="date2" value="<?php echo stripslashes($date2) ?>" required id="stx" class="sch_input" size="25" maxlength="20" placeholder=" " style="width:45%;text-align:center">
          <input type="text" name="stx" value="<?php echo urldecode($_GET['stx']) ?>" class="frm_input" style="width:100%;" placeholder="주문번호로 검색">

        </div>
        <button type="submit" value="검색" class="btn_b01" style="width:49%;margin-top:15px"><i class="fa fa-search" aria-hidden="true"></i> 검색하기</button>
        <button type="button" value="초기화" class="btn_b02" style="width:49%;margin-top:15px;" onclick="location.href='<?php echo $_SERVER['PHP_SELF'] ?>'"><i class="fa fa-repeat"
                                                                                                                                                             aria-hidden="true"></i> 검색초기화
        </button>
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
    function pop_img_add(seq, w) {

      var _width = '550';
      var _height = '720';

      var _left = Math.ceil((window.screen.width - _width) / 2);
      var _top = Math.ceil((window.screen.height - _height) / 2);
      var url = "";

      if (w == 'u') {
        url = "./return_img_pop.php?w=" + w + "&seq=" + seq;
      } else {
        url = "./return_img_pop.php";
      }

      window.open(url, "pop_img_add", "left=" + _left + ", top=" + _top + ", width=" + _width + ", height=" + _height + ", scrollbars=1");

      return false;


    }

    function pop_img_view(seq) {

      var _width = '1150';
      var _height = '850';

      var _left = Math.ceil((window.screen.width - _width) / 2);
      var _top = Math.ceil((window.screen.height - _height) / 2);

      window.open("./pop_return_img.php?seq=" + seq, "pop_img_view", "left=" + _left + ", top=" + _top + ", width=" + _width + ", height=" + _height + ", scrollbars=1");

      return false;


    }

    function all_checked(sw) {
      var f = document.fboardlist;

      for (var i = 0; i < f.length; i++) {
        if (f.elements[i].name == "seq[]")
          f.elements[i].checked = sw;
      }
    }

    function fboardlist_submit(f) {
      var chk_count = 0;

      for (var i = 0; i < f.length; i++) {
        if (f.elements[i].name == "seq[]" && f.elements[i].checked)
          chk_count++;
      }

      if (!chk_count) {
        alert(document.pressed + "할 데이터를 하나 이상 선택하세요.");
        return false;
      }


      if (document.pressed == "선택삭제") {
        if (!confirm("선택한 데이터를 정말 삭제하시겠습니까?\n\n[경고] 한번 삭제한 자료는 복구할 수 없습니다."))
          return false;

        f.mode.value = "delete";
        f.removeAttribute("target");
        f.action = "./return_img_list_update.php";
      }


      return true;
    }

    $(function () {


      $('.mod_btn').bind('click', function () {

        let id = $(this).attr('data');

        pop_img_add(id, 'u');

      });

      $('.sel_type').bind('change', function () {
        if ($(this).val() == 1) {
          location.href = '?mode=1';
        } else if ($(this).val() == 2) {
          location.href = '?mode=2';
        }
      })


    })

  </script>


<?php
include_once(G5_THEME_PATH . '/tail.php');