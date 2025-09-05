<?php
include_once('./_common.php');
include_once(G5_THEME_PATH . '/head.php');


$qstr = $_SERVER['QUERY_STRING'];
$exDate = $ex_date;

if (!$exDate) {
  $exDate = date("Y-m-d");
}

# 날짜별 환울 정보 가져오기
if ($exDate == date("Y-m-d")) {
  $sql = "SELECT * FROM g5_excharge ORDER BY ex_eng ASC";
  $rs = sql_query($sql);
} else {
  $sql = "SELECT * FROM g5_excharge_log WHERE ex_date='" . $exDate . "' ORDER BY ex_eng ASC,wr_datetime DESC";
  $rs = sql_query($sql);
  if (sql_num_rows($rs) == 0) {
    $month = date("Y-m-d", strtotime($exDate . "+1 month"));
    $table = "g5_excharge_" . date("Ym", strtotime($month));
    $sql = "SELECT * FROM " . $table . " WHERE ex_date='" . $exDate . "' GROUP BY ex_eng ORDER BY ex_eng ASC,wr_datetime DESC";
    $rs = sql_query($sql);
  }
}

$list = array();
while ($row = sql_fetch_array($rs)) {
  $list[] = $row;
}

# 배치 파일이 안 돌아갔을 경우 대비
$now = new DateTime();
$last_updated = new DateTime($list[0]['up_datetime']);

$interval_days = $now->diff($last_updated)->days;
$required_update = $interval_days > 1 ? 'Y' : 'N';

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

      .tbl_head01 thead th, .tbl_head01 tbody td {
          border-right: 1px solid #e9e9e9 !important
      }

      .tbl_head01 thead th {
          background: #f2f2f2;
          font-weight: bold
      }

      .tbl_head01 tbody td {
          padding: 10px 5px;
          color: #222
      }

      .tbl_head01 tbody td.num {
          text-align: right
      }

      .tbl_head01 tbody td.date {
          text-align: center
      }

      .tbl_head01 tbody tr:nth-child(even) td {
          background: #eff3f9
      }

      .text-center {
          text-align: center;
      }

      .text-right {
          text-align: right;
      }

      .text-left {
          text-align: left;
      }

      /* modal css */
      #modalOpenButton, #modalCloseButton {
          cursor: pointer;
      }

      #modalContainer {
          width: 100%;
          height: 100%;
          position: fixed;
          top: 0;
          left: 0;
          display: flex;
          justify-content: center;
          align-items: center;
          background: rgba(0, 0, 0, 0.5);
      }

      #modalContent {
          position: absolute;
          background-color: #ffffff;
          width: 300px;
          height: auto;
          padding: 15px;
      }

      #modalContainer.hidden {
          display: none;
      }

      .modal_title {
          margin: 10px 0;
      }

      .modal_body > .frm_input {
          margin-bottom: 5px;
      }

      .modal_bottom {
          margin-top: 5px;
      }

      .tbl_head01 table tbody td {
          text-align: center;
      }

  </style>
  <div id="bo_list">
    <div class="bo_list_innr">
      <h2 class="board_tit">환율관리</h2>
      <form name="exchangeFrm" id="exchangeFrm" action="<?php echo G5_URL; ?>/charge/ajax.excharge_proc.php" onsubmit="return charge_frm_chk(this);" method="post">
        <input type="hidden" name="qstr" value="<?php echo $qstr ?>">
        <input type="hidden" name="mode" value="all"/>
        <input type="hidden" name="ex_date" value="<?= $exDate ?>"/>
        <input type="hidden" name="sw" value="">

        <div id="bo_li_top_op">
          <div class="bo_list_total">
            <div class="local_ov01 local_ov">
						<span class="btn_ov01">
							&nbsp;
						</span>
            </div>
          </div>
          <ul class="<?php echo isset($view) ? 'view_is_list btn_top' : 'btn_top2'; ?>">
            <li>
              <button type="button" class="btn_b01 btn_bo_sch"><i class="fa fa-search" aria-hidden="true"></i>검색</button>
            </li>
            <li>
              <button type="button" class="btn_b02" onclick="fnAllExchange();" style="background:#8e74ef;color:white;"><i class="fa fa-file-pdf-o"></i> 선택수정</button>
            </li>
          </ul>
        </div>
        <h2 style="padding-bottom:10px; font-size:20px; text-align:center">환율관리(<?= $list[0]['up_datetime'] ? date("Y-m-d", strtotime($list[0]['up_datetime'])) : '' ?>)</h2>
        <div class="tbl_head01 tbl_wrap">
          <table>
            <thead style="position:sticky;top:0;">
            <tr>
              <th>
                <input type="checkbox" id="ALLCHK">
              </th>
              <th>국가(한글명)</th>
              <th>국가(영문명)</th>
              <th>환율</th>
              <th>-</th>
            </tr>
            </thead>
            <tbody>
            <? if (count($list) > 0) { ?>
              <? foreach ($list as $k => $v) { ?>
                <tr>
                  <td>
                    <input type="checkbox" name="ex_eng_arr[]" class="chk" value="<?= $v['ex_eng'] ?>"/>
                  </td>
                  <td><?= $v['ex_kor'] ?></td>
                  <td><?= $v['ex_eng'] ?></td>
                  <td>
                    <input type="text" name="rate_arr[<?= $v['ex_eng'] ?>]" value="<?= str_replace(",", "", $v['rate']) ?>" class="frm_input"/>
                  </td>
                  <td>
                    <button onclick="fnUpUnitExchange('<?= $exDate ?>','<?= $v['ex_eng'] ?>');" class="btn_b01">개별 수정</button>
                  </td>
                </tr>
              <? } ?>
            <? } else { ?>
            <? } ?>
            </tbody>
          </table>
        </div>
      </form>
    </div>
  </div>

  <div class="bo_sch_wrap">
    <fieldset class="bo_sch" style="padding:10px">
      <h3>검색</h3>
      <form name="fsearch" method="get">

        <label for="stx" style="font-weight:bold">일자 조회<strong class="sound_only"> 필수</strong></label>
        <div class="sch_bar" style="margin-top:3px">
          <input type="date" name="ex_date" value="<?= $exDate ?>" required class="sch_input" placeholder="" style="text-align:center">
        </div>
        <button type="submit" value="검색" class="btn_b01" style="width:49%;margin-top:15px"><i class="fa fa-search" aria-hidden="true"></i> 검색하기</button>
        <button type="button" value="초기화" class="btn_b02" style="width:49%;margin-top:15px;" onclick="location.href='<?php echo $_SERVER['PHP_SELF'] ?>'"><i class="fa fa-repeat"
                                                                                                                                                             aria-hidden="true"></i> 검색초기화
        </button>
        <button type="button" class="bo_sch_cls" title="닫기" onclick="$('.bo_sch_wrap').toggle();"><i class="fa fa-times" aria-hidden="true"></i><span class="sound_only">닫기</span></button>
      </form>
    </fieldset>
    <div class="bo_sch_bg"></div>
  </div>

  <script type="text/javascript">
    const required_update = '<?= $required_update ?>';
    $(document).ready(function () {
      // 검색 모달
      $(".btn_bo_sch").on("click", function () {
        $(".bo_sch_wrap").toggle();
      });

      $("#ALLCHK").bind("click", function () {
        var chk = $(this).is(":checked");
        $(".chk").prop("checked", chk);
      });

      $(".chk").bind("click", function () {
        var chk = true;
        $(".chk").each(function () {
          if (!$(this).is(":checked")) {
            chk = false;
          }
        });
        $("#ALLCHK").prop("checked", chk);
      });

      if (required_update === 'Y') {
        init_batch();
      }
    });

    function charge_frm_chk(f) {
      return false;
    }

    // 개별 수정
    function fnUpUnitExchange(exDate, ex_eng) {
      var rate = $("input[name='rate_arr[" + ex_eng + "]']").val();
      var params = "mode=unit&ex_date=" + exDate + "&ex_eng=" + ex_eng + "&rate=" + rate;
      $.post("./ajax.excharge_proc.php", params, function (data) {
        if (isDefined(data.message)) {
          alert(data.message);
        }
        if (data.ret_code == true) {
          document.location.reload();
        }
      }, 'json');
      return false;
    }

    // 전체 수정
    function fnAllExchange() {
      $("#exchangeFrm").ajaxSubmit({
        url: "./ajax.excharge_proc.php",
        dataType: "json",
        success: function (data) {
          if (isDefined(data.message)) {
            alert(data.message);
          }
          if (data.ret_code == true) {
            document.location.reload();
          }
        }
      });
    }

    function init_batch() {
      $.post("/batch/batch_excharge.php?key=gotech-secret-key-250207");
    }

  </script>
<?php
include_once(G5_THEME_PATH . '/tail.php');