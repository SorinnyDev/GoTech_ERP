<?php
include_once('./_common.php');

if (!$mode) {
  $mode = 0;
}

if (!$date1) {
  $date1 = date("Y-m-01");
}

if (!$date2) {
  $date2 = date("Y-m-d");
}

$where = " (1) and ds.wr_warehouse in ('한국 폐기창고', '미국 폐기창고')";

if ($date1) {
  $where .= " and date_format(dl.wr_datetime, '%Y-%m-%d') >= '$date1' ";
}

if ($date2) {
  $where .= " and date_format(dl.wr_datetime, '%Y-%m-%d') <= '$date2' ";
}

$warehouse = '';

if ($mode == 1) {
  $warehouse = "한국 폐기창고";
} else if ($mode == 2) {
  $warehouse = "미국 폐기창고";
}

if ($warehouse) {
  $where .= " and ds.wr_warehouse like '%$warehouse%'";
}

$query = "
  select dl.id, dl.wr_datetime, dl.wr_stock, wr_subject, wr_1, wp.wr_5, ds.wr_warehouse, ds.wr_rack, dl.wr_memo, dl.product_id, di.id as 'img_id', di.wr_img1, (wp.wr_22 * dl.wr_stock) as stock_price from g5_discard_list as dl
  left join g5_write_product as wp on wp.wr_id = dl.product_id
  left join g5_discard_img as di on di.discard_id = dl.id  
  left join g5_discard_stock as ds on ds.discard_id = dl.id
  where {$where}                                                                                                                
  group by dl.id
  order by dl.wr_datetime desc
";

$list = sql_fetch_all($query);

include_once(G5_THEME_PATH . '/head.php');
?>
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <link rel="stylesheet" href="/css/mangolabs.css?ver=<?= date('Y-m-d H:i:s') ?>">

  <link rel="stylesheet" href="/theme/parsley/mobile/skin/board/sales/style.css?ver=2303229">
  <style>
  </style>
  <div id="bo_list">
    <div class="bo_list_innr">
      <h2 class="board_tit">폐기재고 관리</h2>
      <input type="hidden" name="mode" value="delete">

      <div id="bo_li_top_op">
        <ul class="<?php echo isset($view) ? 'view_is_list btn_top' : 'btn_top2'; ?>" style="margin-bottom: 15px;">
          <li>
            <button type="button" class="btn_b01 " id="excel_btn" style="background:#325422;"><i class="fa fa-file-pdf-o tw-mr-1"></i>엑셀출력</button>
          </li>
          <li>
            <button type="button" class="btn btn_b01" onclick=" pop_excel(); "><i class="fa fa-file-excel-o" aria-hidden="true"></i> 엑셀등록</button>
            <button type="button" class="btn_b02 btn_bo_sch"><i class="fa fa-search" aria-hidden="true"></i> 검색</button>
          </li>
        </ul>
      </div>

      <div id="bo_li_01" style="clear:both; overflow-x: auto;">
        <div class="tbl_head01 tbl_wrap">
          <table>
            <thead>
            <tr>
              <th>폐기일자</th>
              <th>대표코드</th>
              <th>제품명</th>
              <th style="width: 70px;">제품단가</th>
              <th>창고</th>
              <th>사진</th>
              <th>메모</th>
              <th>수량</th>
              <th>관리</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($list as $k => $item) { ?>
              <tr>
                <td style="text-align: center;"><?= $item['wr_datetime'] ?></td>
                <td><?= $item['wr_5'] ?? $item['wr_subject'] ?></td>
                <td><?= $item['wr_subject'] ?></td>
                <td style="text-align: center;"><?= number_format($item['stock_price']) ?></td>
                <td style="text-align: center;">
                  <?php echo $item['wr_warehouse'] ?> <?php echo $item['wr_rack'] ?>랙
                </td>
                <td style="text-align: center;">
                  <?php if ($item['wr_img1']) { ?>
                    <img src="<?= G5_DATA_URL . "/discard/" . $item['wr_img1'] ?>"
                         onclick="pop_img_view('<?= $item['id'] ?>')" alt="img" style="height: 50px; text-align: center; cursor: pointer;">
                  <?php } else { ?>
                    <strong>없음</strong>
                  <?php } ?>
                </td>
                <td><textarea style="width: 100%;" disabled><?= $item['wr_memo'] ?></textarea></td>
                <td style="text-align: center"><?= $item['wr_stock'] ?></td>
                <td style="text-align: center;">
                  <button type="button" class="btn btn_b01" onclick="pop_img_add('<?= $item['id'] ?>');">이미지 관리</button>
                  <button type="button" class="btn btn_b01" onclick="restore_discard('<?= $item['id'] ?>');" style="background: #FF3746;">복원</button>
                </td>
              </tr>
            <?php } ?>
            </tbody>
          </table>
        </div>
      </div>


    </div>
    <div style="clear:both"></div>
  </div>

  <div class="bo_sch_wrap">
    <fieldset class="bo_sch" style="margin-top: -280px;">
      <h3>검색</h3>
      <form name="fsearch" method="get">
        <input type="hidden" name="mode" value="<?php echo $mode ?>">
        <div style="border:1px solid #ddd; margin-bottom:20px; padding:10px">
          <select name="mode" id="mode" class="frm_input search_sel" style="width: 100%;">
            <option value="">전체 창고</option>
            <option value="1" <?= get_selected('1', $mode) ?>>한국폐기창고</option>
            <option value="2" <?= get_selected('2', $mode) ?>>미국폐기창고</option>
          </select>
        </div>

        <strong>폐기일자 조회</strong>
        <div style="border:1px solid #ddd; margin-bottom:20px; padding:10px">
          <div class="sch_bar" style="margin-top:3px">
            <input type="date" name="date1" value="<?php echo $date1 ?>" required id="stx" class="sch_input" size="25" maxlength="20" placeholder="" style="width:45%;text-align:center">
            <span style="float:left;display:inline-block;height:38px;line-height:38px; margin:0 5px">~</span>
            <input type="date" name="date2" value="<?php echo stripslashes($date2) ?>" required id="stx" class="sch_input" size="25" maxlength="20" placeholder=" " style="width:45%;text-align:center">
          </div>
        </div>
        <div style="border:1px solid #ddd; margin-bottom:20px; padding:10px">
          <button type="submit" value="검색" class="btn_b01" style="width:49%;"><i class="fa fa-search" aria-hidden="true"></i> 검색하기</button>
          <button type="button" value="초기화" class="btn_b02" style="width:49%;" onclick="location.href='<?php echo $_SERVER['PHP_SELF'] ?>'"><i class="fa fa-repeat"
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
    $(function () {
      $('.mod_btn').bind('click', function () {
        let id = $(this).attr('data-id');
        pop_img_add(id, 'u');
      });

      $("#excel_btn").bind("click", function () {

        if (!confirm("엑셀 출력을 하시겠습니까?")) {
          return false;
        }

        const currentUrl = new URL(window.location.href);
        const params = currentUrl.search;
        const newUrl = `${g5_url}/discard/discard_excel_download.php${params}`;


        location.href = newUrl;
      });

    });
  </script>

  <script>
    function open_stock(wr_id) {

      var _width = '1150';
      var _height = '850';

      var _left = Math.ceil((window.screen.width - _width) / 2);
      var _top = Math.ceil((window.screen.height - _height) / 2);

      window.open("/stock/pop_rack.php?wr_id=" + wr_id, "pop_img_view", "left=" + _left + ", top=" + _top + ", width=" + _width + ", height=" + _height + ", scrollbars=1");

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

    function pop_excel() {
      window.open("/discard/discard_stock_add_pop.php", "add", "left=50, top=50, width=500, height=550, scrollbars=1");
    }

    function pop_img_add(id, w) {

      var _width = '550';
      var _height = '720';

      var _left = Math.ceil((window.screen.width - _width) / 2);
      var _top = Math.ceil((window.screen.height - _height) / 2);
      var url = "";

      url = "./discard_img_pop.php?seq=" + id;

      window.open(url, "pop_img_add", "left=" + _left + ", top=" + _top + ", width=" + _width + ", height=" + _height + ", scrollbars=1");

      return false;


    }

    function restore_discard(id) {
      if (!id) {
        alert('잘못된 접근입니다.');
        return;
      }

      const target = prompt('복원할 렉을 입력해주세요.');

      if (!target) {
        return;
      }

      location.href = './ajax.discard_restore.php?id=' + id + '&target=' + target;
    }

    function pop_img_view(id) {
      const _width = '1150';
      const _height = '850';

      const _left = Math.ceil((window.screen.width - _width) / 2);
      const _top = Math.ceil((window.screen.height - _height) / 2);

      window.open("./pop_discard_img.php?id=" + id, "pop_img_view", "left=" + _left + ", top=" + _top + ", width=" + _width + ", height=" + _height + ", scrollbars=1");

      return false;
    }

  </script>


<?php
include_once(G5_THEME_PATH . '/tail.php');