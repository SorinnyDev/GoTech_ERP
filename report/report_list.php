<?php
include_once('./_common.php');
include_once(G5_THEME_PATH . '/head.php');

function _rack_search($wr_id, $warehouse = '', $show_expired = false, $expired_st_date = '', $expired_ed_date = '')
{
    $sql_where = ' ';

    if (!empty($expired_st_date)) {
        $sql_where .= " and expired_date >= '$expired_st_date'";
    }

    if (!empty($expired_ed_date)) {
        $sql_where .= " and expired_date <= '$expired_ed_date'";
    }

    if ($show_expired === 'Y') {
        $sql_where .= " and rep.id is not null";
    }

    $sql = "
  SELECT distinct rs.wr_rack, rs.wr_stock, r.gc_warehouse, r.gc_name, rep.id as 'expired_id'
  FROM g5_rack_stock as rs
  left join g5_rack as r on r.seq = rs.wr_rack
  left join g5_rack_expired as rep on rep.rack_id = r.seq and rep.product_id = rs.wr_product_id
  WHERE rs.wr_product_id = '{$wr_id}' {$sql_where}
  ORDER BY rs.seq ASC  
  ";

    $result = sql_fetch_all($sql);

    $arr = [];
    foreach ($result as $row) {
        if ($warehouse) {
            if ($row['gc_warehouse'] === $warehouse && $row['wr_stock'] > 0) {
                $arr[] = $row;
            }
        } else {
            $arr[] = $row;
        }
    }

    $result_arr = "";
    $sort_arr = [];

    foreach ($arr as $item) {
        $wr_rack = $item['wr_rack'];

        $sql = "SELECT SUM(wr_stock) AS total FROM g5_rack_stock WHERE wr_product_id = '{$wr_id}' AND wr_rack = '{$wr_rack}' ORDER BY seq ASC";
        $total = sql_fetch($sql)['total'];

        if ($total > 0) {
            $sort_arr[$wr_rack]['rack_name'] = $item['gc_name'];
            $sort_arr[$wr_rack]['warehouse'] = $item['gc_warehouse'];
            $sort_arr[$wr_rack]['is_expired'] = !!$item['expired_id'];
            $sort_arr[$wr_rack]['seq'] = $wr_rack;
            $sort_arr[$wr_rack]['total'] = number_format($total);
        }
    }

    sort($sort_arr);

    foreach ($sort_arr as $key => $value) {
        $gc_warehouse = $value['warehouse'];

        // popup.report_expiration_manage.php와 동일한 로직으로 유통기한 정보 가져오기
        $sql = "
        select rs.wr_rack, rs.wr_warehouse, wp.wr_1, wp.wr_subject, r.gc_name, rep.id as 'expired_id', wp.wr_id, rep.expired_date 
        from g5_rack_stock as rs
        left join g5_write_product as wp on rs.wr_product_id = wp.wr_id
        left join g5_rack as r on r.seq = rs.wr_rack
        left join g5_rack_expired as rep on rep.rack_id = r.seq and rep.product_id = rs.wr_product_id                                                                                                     
        where rs.wr_product_id = '{$wr_id}' AND rs.wr_rack = '{$value['seq']}'";

        $rack_list = sql_fetch_all($sql);

        $expired_date_display = '';
        foreach ($rack_list as $rack) {
            if (!empty($rack['expired_date'])) {
                $expired_date_display = $rack['expired_date'];
                break;
            }
        }

        if ($value['is_expired']) {
            $result_arr .= "[" . PLATFORM_TYPE[$gc_warehouse] . "] " . $value['rack_name'] . "(주의 재고 : {$value['total']}개) {$expired_date_display} <br>";
        } else {
            $result_arr .= "[" . PLATFORM_TYPE[$gc_warehouse] . "] " . $value['rack_name'] . "(재고 : {$value['total']}개) <br>";
        }
    }
    return $result_arr;
}


$sql_common = " from g5_write_product as p ";
$sql_join = " ";
$sql_search = " where (1) ";
if ($return_type) {
    $sql_join .= " left join g5_rack_stock as rs on rs.wr_product_id = p.wr_id ";
    $sql_search = " where (1) ";
}
$sql_add = " ";

if ($stx2) {
    $stx2 = trim($stx2);
    $sql_search .= " AND (wr_subject LIKE '%$stx2%' or wr_1 LIKE '%$stx2%' or wr_27 LIKE '%$stx2%' or wr_28 LIKE '%$stx2%' or wr_29 LIKE '%$stx2%' or wr_30 LIKE '%$stx2%' or wr_31 LIKE '%$stx2%' or wr_5 LIKE '%$stx2%' or wr_6 LIKE '%$stx2%' or wr_4 LIKE '%$stx2%' )  ";
    $sql_add .= " AND (wr_subject LIKE '%$stx2%' or wr_1 LIKE '%$stx2%' or wr_27 LIKE '%$stx2%' or wr_28 LIKE '%$stx2%' or wr_29 LIKE '%$stx2%' or wr_30 LIKE '%$stx2%' or wr_31 LIKE '%$stx2%' or wr_5 LIKE '%$stx2%' or wr_6 LIKE '%$stx2%' or wr_4 LIKE '%$stx2%' )  ";
}

if ($report_type) {
    $report_type = trim($report_type);
    $sql_search .= " AND $report_type != 0 ";
}

if ($report_category) {
    $report_category = trim($report_category);
    $sql_search .= "  AND wr_26 = '$report_category' ";
    $sql_add .= " AND wr_26 = '$report_category'  ";
}

if ($report_mb_id) {
    $report_mb_id = trim($report_mb_id);
    $sql_search .= "  AND mb_id = '$report_mb_id' ";
    $sql_add .= " AND mb_id = '$report_mb_id'  ";
}

if ($search_brand) {
    $sql_search .= "  AND wr_23 = '$search_brand' ";
    $sql_add .= " AND wr_23 = '$search_brand'  ";
}

if ($search_wr_26) {
    $sql_search .= "  AND wr_26 = '$search_wr_26' ";
    $sql_add .= " AND wr_26 = '$search_wr_26'  ";
}

if (!empty($expired_st_date) || !empty($expired_ed_date)) {
    $search_expired = 'Y';
}

if (isset($search_expired) && $search_expired === 'Y') {
    $expired_sql_where = ' 1 ';
    if (!empty($expired_st_date)) {
        $expired_sql_where .= " and expired_date >= '$expired_st_date'";
    }
    if (!empty($expired_ed_date)) {
        $expired_sql_where .= " and expired_date <= '$expired_ed_date'";
    }
    $sql = "select product_id from g5_rack_expired where {$expired_sql_where}";
    $expired_list = sql_fetch_all($sql);

    $expired_product_id_list = array_column($expired_list, 'product_id');
    $search_implode_query = implode("','", $expired_product_id_list);
    $sql_search .= " AND wr_id in ('{$search_implode_query}') ";
    $sql_add .= " AND wr_id in ('{$search_implode_query}') ";
}

if ($search_warehouse) {

    switch ($search_warehouse) {
        case 'wr_32':
            $warehouse_code = '1000';
            break;

        case 'wr_36':
            $warehouse_code = '3000';
            break;

        case 'wr_42':
            $warehouse_code = '4000';
            break;

        case 'wr_43':
            $warehouse_code = '5000';
            break;

        case 'wr_44':
            $warehouse_code = '6000';
            break;

        case 'wr_40':
            $warehouse_code = '7000';
            break;

        case 'wr_41':
            $warehouse_code = '8000';
            break;

        case 'wr_45':
            $warehouse_code = '11000';
            break;

        case 'wr_46':
            $warehouse_code = '12000';
            break;
    }

    if (!$search_min && !$search_max) {
        $sql_search .= " AND $search_warehouse > 0";
        $sql_add .= " AND $search_warehouse > 0";
    }

    if ($search_min) {
        $sql_search .= " AND $search_warehouse >= $search_min";
        $sql_add .= " AND $search_warehouse >= $search_min";
    }

    if ($search_max) {
        $sql_search .= " AND $search_warehouse <= $search_max";
        $sql_add .= " AND $search_warehouse <= $search_max";
    }
} else {
    if ($search_min) {
        $sql_search .= " AND (wr_32 >= $search_min OR wr_36 >= $search_min OR wr_42 >= $search_min OR wr_43 >= $search_min OR wr_44 >= $search_min OR wr_37 >= $search_min OR wr_40 >= $search_min OR wr_41 >= $search_min OR wr_45 >= $search_min OR wr_46 >= $search_min 
                OR wr_32_real >= $search_min OR wr_36_real >= $search_min OR wr_42_real >= $search_min OR wr_43_real >= $search_min OR wr_44_real >= $search_min OR wr_40_real >= $search_min  OR wr_41_real >= $search_min OR wr_45_real >= $search_min  OR wr_46_real >= $search_min)";
        $sql_add .= " AND (wr_32 >= $search_min OR wr_36 >= $search_min OR wr_42 >= $search_min OR wr_43 >= $search_min OR wr_44 >= $search_min OR wr_37 >= $search_min OR wr_40 >= $search_min OR wr_41 >= $search_min OR wr_45 >= $search_min OR wr_46 >= $search_min
        OR wr_32_real >= $search_min OR wr_36_real >= $search_min OR wr_42_real >= $search_min OR wr_43_real >= $search_min OR wr_44_real >= $search_min OR wr_40_real >= $search_min  OR wr_41_real >= $search_min OR wr_45_real >= $search_min  OR wr_46_real >= $search_min)";
    }

    if ($search_max) {
        $sql_search .= " AND (wr_32 <= $search_max OR wr_36 <= $search_max OR wr_42 <= $search_max OR wr_43 <= $search_max OR wr_44 <= $search_max OR wr_37 <= $search_max OR wr_40 <= $search_max OR wr_41 <= $search_max OR wr_45 <= $search_max OR wr_46 <= $search_max    
                OR wr_32_real <= $search_max OR wr_36_real <= $search_max OR wr_42_real <= $search_max OR wr_43_real <= $search_max OR wr_44_real <= $search_max OR wr_40_real <= $search_max OR wr_41_real <= $search_max OR wr_45_real <= $search_max OR wr_46_real <= $search_max)";
        $sql_add .= " AND (wr_32 <= $search_max OR wr_36 <= $search_max OR wr_42 <= $search_max OR wr_43 <= $search_max OR wr_44 <= $search_max OR wr_37 <= $search_max  OR wr_40 <= $search_max OR wr_41 <= $search_max OR wr_45 <= $search_max OR wr_46 <= $search_max
                OR wr_32_real <= $search_max OR wr_36_real <= $search_max OR wr_42_real <= $search_max OR wr_43_real <= $search_max OR wr_44_real <= $search_max OR wr_40_real <= $search_max OR wr_41_real <= $search_max OR wr_45_real <= $search_max OR wr_46_real <= $search_max)";
    }
}

if ($return_type === "ko_return") {
    $sql_search .= " and rs.wr_warehouse in ('7000')";
} else if ($return_type === "us_return") {
    $sql_search .= " and rs.wr_warehouse in ('8000')";
}

//$sql_search .= " and wr_32+wr_36+wr_37+wr_42+wr_43+wr_44+wr_40+wr_41+wr_45+wr_46 > 0";
$sql_search .= " and (wr_32 > 0 OR wr_36 > 0 OR wr_37 > 0 OR wr_42 > 0 OR wr_43 > 0 OR wr_44 > 0 OR wr_40 > 0 OR wr_41 > 0 OR wr_45 > 0 OR wr_46 > 0)";


if (!$sst) {
    $sst = "wr_id";
    $sod = "desc";
}

if ($sst == "stock") {
    $sst = "(wr_32+wr_36+wr_37+wr_42+wr_43+wr_44+wr_40+wr_41+wr_45+wr_46)";
}

if ($sst === "stock_amount") {
    $sst = "(wr_32_amount + wr_36_amount + wr_42_amount + wr_43_amount + wr_44_amount + wr_37_amount + wr_40_amount + wr_41_amount + wr_45_amount + wr_46_amount)";
}

$sql_order = " order by $sst $sod ";

if ($return_type) {
    $sql = " select count(cnt) as cnt from (
     select count(*) as cnt {$sql_common} {$sql_join} {$sql_search} group by p.wr_id {$sql_order}
 ) as A ";
} else {
    $sql = " select count(*) as cnt {$sql_common} {$sql_search} {$sql_order} ";
}

$row = sql_fetch($sql);
$total_count = $row['cnt'];

$rows = 50;
$total_page = ceil($total_count / $rows);
if ($page < 1) {
    $page = 1;
}
$from_record = ($page - 1) * $rows;

$cur_no = $total_count - $from_record;

if ($return_type) {
    $sql = "select *, 
        wr_32 * wr_22 as wr_32_amount, 
        wr_36 * wr_22 as wr_36_amount,  
        wr_42 * wr_22 as wr_42_amount,  
        wr_43 * wr_22 as wr_43_amount,  
        wr_44 * wr_22 as wr_44_amount,  
        wr_37 * wr_22 as wr_37_amount,
        wr_40 * wr_22 as wr_40_amount,
        wr_41 * wr_22 as wr_41_amount,
        wr_45 * wr_22 as wr_45_amount,
        wr_46 * wr_22 as wr_46_amount
        {$sql_common} {$sql_join} {$sql_search} group by p.wr_id {$sql_order} limit {$from_record}, {$rows} 
";
} else {
    $sql = "select *, 
        wr_32 * wr_22 as wr_32_amount, 
        wr_36 * wr_22 as wr_36_amount,  
        wr_42 * wr_22 as wr_42_amount,  
        wr_43 * wr_22 as wr_43_amount,  
        wr_44 * wr_22 as wr_44_amount,  
        wr_37 * wr_22 as wr_37_amount,
        wr_40 * wr_22 as wr_40_amount,
        wr_41 * wr_22 as wr_41_amount,
        wr_45 * wr_22 as wr_45_amount,
        wr_46 * wr_22 as wr_46_amount
        {$sql_common} {$sql_search} {$sql_order} limit {$from_record}, {$rows} 
";
}

$rst = sql_query($sql);

$cnt = sql_fetch("SELECT SUM(wr_32) as kor,SUM(wr_32_real) AS kor_real, SUM(wr_36) as usa, SUM(wr_36_real) AS usa_real, SUM(wr_37) as tmp, SUM(wr_42) as fba , SUM(wr_42_real) AS fba_real, SUM(wr_43) as wfba , SUM(wr_43_real) AS wfba_real, SUM(wr_44) as ufba ,SUM(wr_44_real) AS ufba_real {$sql_common} WHERE (1) {$sql_add} ");

$return_cnt = sql_fetch("select sum(IF(wr_warehouse = '7000', wr_stock, 0)) as korea, sum(IF(wr_warehouse = '8000', wr_stock, 0)) as us from g5_rack_stock where wr_warehouse = '7000' or wr_warehouse = '8000'");

?>
    <link rel="stylesheet" href="/theme/parsley/mobile/skin/board/sales/style.css?ver=2303229">
    <style>
        .cnt_left {
            padding: 5px 10px;
            border-right: 1px solid #ddd;
            word-break: normal;
            text-overflow: ellipsis;
            overflow: hidden;
            white-space: nowrap;
        }

        .list_03 li {
            padding: 0;
            min-height: 60px;
            height: 100%;
        }

        .list_03 li .cnt_left {
            line-height: 43px;
            min-height: 60px;
            height: 100%;
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
            vertical-align: top;
            cursor: pointer;
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

        .ov_txt.select_btn {
            background: blue;
        }

        a.btn_ov02,
        a.ov_listall {
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

        a.btn_ov02:hover,
        a.ov_listall:hover {
            background: #3f51b5
        }

        @media (max-width: 767px) {
            .btn_top2 {
                margin-bottom: 5px
            }

            .stock_list_mtb {
                margin: 15px 0;
                font-size: 13px
            }

            .stock_list_mtb th {
                width: 60px
            }

            .stock_list_mtb td {
                padding: 10px
            }

            .stock_list_mtb .stock_table td {
                width: 33.333%;
                text-align: center;
                border-right: 1px solid #ddd
            }
        }

        .modal_frm {
            display: none;
            width: 100%;
            height: 100%;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 999;
        }

        .modal_body {
            position: absolute;
            top: 50%;
            left: 50%;
            background: #fff;
            text-align: left;
            width: 250px;
            margin-left: -165px;
            margin-top: -180px;
            border-radius: 5px;
            -webkit-box-shadow: 1px 1px 18px rgba(0, 0, 0, 0.2);
            -moz-box-shadow: 1px 1px 18px rgba(0, 0, 0, 0.2);
            box-shadow: 1px 1px 18px rgba(0, 0, 0, 0.2);
            border: 1px solid #dde7e9;
            background: #fff;
            border-radius: 3px;
        }

        .modal_body_frm {
            width: 250px;
            height: 140px;
            padding: 10px;
            background: white;
            border: 1px solid #eee;
        }

        .modal_bg {
            background: #000;
            background: rgba(0, 0, 0, 0.1);
            width: 100%;
            height: 100%;
        }

        .modal_cls_btn {
            position: absolute;
            right: 0;
            top: 0;
            color: #b5b8bb;
            border: 0;
            padding: 12px 15px;
            font-size: 16px;
            background: #fff;
        }
    </style>
    <div id="bo_list">
        <div class="bo_list_innr">
            <h2 class="board_tit">재고보고서</h2>
            <form name="fboardlist" id="fboardlist" action="<?php echo G5_BBS_URL; ?>/board_list_update.php"
                  onsubmit="return fboardlist_submit(this);" method="post">
                <input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
                <input type="hidden" name="sfl" value="<?php echo $sfl ?>">
                <input type="hidden" name="stx" value="<?php echo $stx2 ?>">
                <input type="hidden" name="spt" value="<?php echo $spt ?>">
                <input type="hidden" name="sst" value="<?php echo $sst ?>">
                <input type="hidden" name="sod" value="<?php echo $sod ?>">
                <input type="hidden" name="page" value="<?php echo $page ?>">
                <input type="hidden" name="sw" value="">

                <?php if ($is_category) { ?>
                    <nav id="bo_cate">
                        <h2><?php echo ($board['bo_mobile_subject'] ? $board['bo_mobile_subject'] : $board['bo_subject']) ?>
                            카테고리</h2>
                        <ul id="bo_cate_ul">
                            <?php echo $category_option ?>
                        </ul>
                    </nav>
                <?php } ?>

                <div id="bo_li_top_op">
                    <div class="bo_list_total">
                        <div class="local_ov01 local_ov">
            <span class="btn_ov01"
                  onclick="location.href='<?= G5_URL ?>/report/report_list.php?report_type=&report_category=<?= $report_category ?>&report_mb_id=<?= $report_mb_id ?>&stx2=<?= $stx2 ?>'">
              <span class="ov_txt <?= $report_type == "" ? "select_btn" : "" ?>">전체</span>
              <span
                      class="ov_num"><?php echo number_format($cnt['kor'] + $cnt['usa'] + $cnt['fba'] + $cnt['wfba'] + $cnt['ufba'] + $cnt['tmp']) ?>개</span>
            </span>
                            <span class="btn_ov01"
                                  onclick="location.href='<?= G5_URL ?>/report/report_list.php?report_type=wr_32&report_category=<?= $report_category ?>&report_mb_id=<?= $report_mb_id ?>&stx2=<?= $stx2 ?>'">
              <span class="ov_txt <?= $report_type == "wr_32" ? "select_btn" : "" ?>">한국창고</span>
              <span class="ov_num"><?php echo number_format($cnt['kor']) ?>개</span>
            </span>
                            <span class="btn_ov01"
                                  onclick="location.href='<?= G5_URL ?>/report/report_list.php?report_type=wr_36&report_category=<?= $report_category ?>&report_mb_id=<?= $report_mb_id ?>&stx2=<?= $stx2 ?>'">
              <span class="ov_txt <?= $report_type == "wr_36" ? "select_btn" : "" ?>">미국창고</span>
              <span class="ov_num"><?php echo number_format($cnt['usa']) ?>개</span>
            </span>
                            <span class="btn_ov01"
                                  onclick="location.href='<?= G5_URL ?>/report/report_list.php?report_type=wr_42&report_category=<?= $report_category ?>&report_mb_id=<?= $report_mb_id ?>&stx2=<?= $stx2 ?>'">
              <span class="ov_txt <?= $report_type == "wr_42" ? "select_btn" : "" ?>">FBA창고</span>
              <span class="ov_num"><?php echo number_format($cnt['fba']) ?>개</span>
            </span>
                            <span class="btn_ov01"
                                  onclick="location.href='<?= G5_URL ?>/report/report_list.php?report_type=wr_43&report_category=<?= $report_category ?>&report_mb_id=<?= $report_mb_id ?>&stx2=<?= $stx2 ?>'">
              <span class="ov_txt <?= $report_type == "wr_43" ? "select_btn" : "" ?>">W-FBA창고</span>
              <span class="ov_num"><?php echo number_format($cnt['wfba']) ?>개</span>
            </span>
                            <span class="btn_ov01"
                                  onclick="location.href='<?= G5_URL ?>/report/report_list.php?report_type=wr_44&report_category=<?= $report_category ?>&report_mb_id=<?= $report_mb_id ?>&stx2=<?= $stx2 ?>'">
              <span class="ov_txt <?= $report_type == "wr_44" ? "select_btn" : "" ?>">U-FBA창고</span>
              <span class="ov_num"><?php echo number_format($cnt['ufba']) ?>개</span>
            </span>
                            <span class="btn_ov01"
                                  onclick="location.href='<?= G5_URL ?>/report/report_list.php?report_type=wr_37&report_category=<?= $report_category ?>&report_mb_id=<?= $report_mb_id ?>&stx2=<?= $stx2 ?>'">
              <span class="ov_txt <?= $report_type == "wr_37" ? "select_btn" : "" ?>">임시창고</span>
              <span class="ov_num"><?php echo number_format($cnt['tmp']) ?>개</span>
            </span>
                            <span class="btn_ov01"
                                  onclick="location.href='<?= G5_URL ?>/report/report_list.php?return_type=ko_return'">
              <span class="ov_txt <?= $return_type == "ko_return" ? "select_btn" : "" ?>">한국반품창고</span>
              <span class="ov_num"><?php echo number_format($return_cnt['korea']) ?>개</span>
            </span>
                            <span class="btn_ov01"
                                  onclick="location.href='<?= G5_URL ?>/report/report_list.php?return_type=us_return'">
              <span class="ov_txt <?= $return_type == "us_return" ? "select_btn" : "" ?>">미국반품창고</span>
              <span class="ov_num"><?php echo number_format($return_cnt['us']) ?>개</span>
            </span>
                        </div>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <ul class="btn_top2" style="display: flex; align-items: center;">
                            <li>
                                <button type="button" class="btn_b02" onclick="expiration_stock_manage();">유통기한 임박 관리</button>
                            </li>
                        </ul>
                        <ul class="<?php echo isset($view) ? 'view_is_list btn_top' : 'btn_top2'; ?>">
                            <?php if ($rss_href) { ?>
                                <li><a href="<?php echo $rss_href ?>" class="btn_b02">RSS</a></li>
                            <?php } ?>
                            <li>
                                <select id="sorting_box" class="frm_input" style="height:37px">
                                    <option value="default" <?= ($_GET['sst'] == "wr_id") ? "selected" : "" ?>>기본정렬</option>
                                    <option value="up" <?= ($_GET['sst'] == "stock" && $sod == "desc") ? "selected" : "" ?>>재고많은순
                                    </option>
                                    <option value="down" <?= ($_GET['sst'] == "stock" && $sod == "asc") ? "selected" : "" ?>>재고적은순
                                    </option>
                                </select>
                            </li>
                            <li>
                                <select id="mb_id_box" class="frm_input" style="height:37px">
                                    <option value="">전체 담당자</option>
                                    <?
                                    $arr = get_mb_code_list('');
                                    foreach ($arr as $key => $value) {
                                        $selected = ($value['mb_id'] == $report_mb_id) ? "selected" : "";
                                        echo "<option value='{$value['mb_id']}' {$selected} >{$value['mb_name']}</option>";
                                    }
                                    ?>
                                </select>
                            </li>

                            <li>
                                <button type="button" class="btn_b02 btn_bo_sch"><i class="fa fa-search" aria-hidden="true"></i>
                                    검색
                                </button>
                            </li>
                            <li>
                                <button type="button" class="btn_b01 " id="excel_btn" style="background:#325422;">엑셀출력</button>
                            </li>
                            <li>
                                <button type="button" class="btn_b01 rack_write_btn" style="margin:10px;">랙별 실사재고조사</button>
                            </li>
                        </ul>
                    </div>

                </div>

                <div id="bo_li_01" style="clear:both;overflow-x:scroll;overflow-y:hidden;overflow-x: auto;">
                    <ul class="list_head"
                        style="width:100%;min-width:2486px;position:sticky;top:0;background:#fff;z-index:2;">
                        <li style="width:40px"></li>
                        <li style="width:50px">순번</li>
                        <li style="width:150px"><?php echo subject_sort_link('wr_5', $qstr2, 1) ?>대표코드</a></li>
                        <li style="width:100px"><?php echo subject_sort_link('wr_1', $qstr2, 1) ?>SKU</a></li>
                        <li style="width:200px"><?php echo subject_sort_link('wr_subject', $qstr2, 1) ?>상품명</a></li>
                        <li style="width:270px">랙번호</li>
                        <li style="width:150px"><?php echo subject_sort_link('wr_32_amount', $qstr2, 1) ?>한국창고
                            금액<br>(재고/실재고)</a></li>
                        <li style="width:150px"><?php echo subject_sort_link('wr_36_amount', $qstr2, 1) ?>미국창고
                            금액<br>(재고/실재고)</a></li>
                        <li style="width:150px"><?php echo subject_sort_link('wr_42_amount', $qstr2, 1) ?>FBA창고
                            금액<br>(재고/실재고)</a></li>
                        <li style="width:150px"><?php echo subject_sort_link('wr_43_amount', $qstr2, 1) ?>W-FBA창고
                            금액<br>(재고/실재고)</a></li>
                        <li style="width:150px"><?php echo subject_sort_link('wr_44_amount', $qstr2, 1) ?>U-FBA창고
                            금액<br>(재고/실재고)</a></li>
                        <li style="width:150px"><?php echo subject_sort_link('wr_37_amount', $qstr2, 1) ?>임시창고 금액</a></li>
                        <li style="width:150px"><?php echo subject_sort_link('wr_40_amount', $qstr2, 1) ?>한국반품창고
                            금액<br>(재고/실재고)</a></li>
                        <li style="width:150px"><?php echo subject_sort_link('wr_41_amount', $qstr2, 1) ?>미국반품창고
                            금액<br>(재고/실재고)</a></li>
                        <li style="width:150px"><?php echo subject_sort_link('wr_45_amount', $qstr2, 1) ?>한국폐기창고
                            금액<br>(재고/실재고)</a></li>
                        <li style="width:150px"><?php echo subject_sort_link('wr_46_amount', $qstr2, 1) ?>미국폐기창고
                            금액<br>(재고/실재고)</a></li>
                        <li style="width:150px"><?php echo subject_sort_link('stock_amount', $qstr2, 1) ?>총
                            재고금액<br>(재고/실재고)</a></li>
                    </ul>
                    <div id="bo_li_01" class="list_03" style="width: 100%; min-width: 2486px;">
                        <ul style="width:100%;">
                            <?php

                            for ($i = 0; $row = sql_fetch_array($rst); $i++) {

                                $text = _rack_search($row['wr_id'], $warehouse_code, $search_expired, $expired_st_date, $expired_ed_date);

                                if (!$text && $warehouse_code) {
                                    $cur_no--;
                                    continue;
                                }

                                $hab_cnt = $row['wr_32'] + $row['wr_36'] + $row['wr_42'] + $row['wr_43'] + $row['wr_44'] + $row['wr_37'] + $row['wr_40'] + $row['wr_41'] + $row['wr_45'] + $row['wr_46'];
                                $hab_cnt_real = $row['wr_32_real'] + $row['wr_36_real'] + $row['wr_42_real'] + $row['wr_43_real'] + $row['wr_44_real'] + $row['wr_37'] + $row['wr_40_real'] + $row['wr_41_real'] + $row['wr_45_real'] + $row['wr_46_real'];

                                $row['wr_22'] = $row['wr_22'] ?: '1';

                                $hab_amount = $hab_cnt * $row['wr_22'];
                                $hab__real_amount = $hab_cnt_real * $row['wr_22'];

                                ?>
                                <li class="modify" data="<?php echo $row['seq'] ?>">
                                    <div class="cnt_left" style="width:40px"><input type="checkbox" name="seq[]" value="<?php echo $row['wr_id'] ?>"></div>
                                    <div class="num cnt_left" style="width:50px"><?php echo abs($cur_no) ?></div>
                                    <div class="cnt_left" style="width:150px"><?php echo $row['wr_5'] ?> <a
                                                href="/bbs/write.php?bo_table=product&w=u&wr_id=<?php echo $row['wr_id'] ?>"
                                                target="_blank" title="제품관리 바로가기"><i class="fa fa-link" aria-hidden="true"></i></a>
                                    </div>
                                    <div class="cnt_left" style="width:120px" title="<?php echo $row['wr_1'] ?>">
                                        <?php echo $row['wr_1'] ?>
                                    </div>
                                    <div class="cnt_left" style="width:200px; border-right: unset;"
                                         title="<?php echo $row['wr_subject'] ?>"><?php echo $row['wr_subject'] ?></div>
                                    <div class="cnt_left" style="width:270px;text-align:center;border-left: 1px solid #ddd;">
                                        <?php
                                        // $text = _rack_search($row['wr_id'], $warehouse_code, $search_expired, $expired_st_date, $expired_ed_date);
                                        $text_list = explode('<br>', $text);

                                        $print_list = [];
                                        foreach ($text_list as $item) {
                                            if (strpos($item, '주의 재고') !== false) {
                                                $print_list[] = "<span style='color: red;'>{$item}</span>";
                                            } else {
                                                $print_list[] = $item;
                                            }
                                        }

                                        echo implode('<br>', $print_list);
                                        ?>
                                    </div>
                                    <div class="cnt_left" style="width:150px;text-align:right">
                                        <?= number_format($row['wr_32'] * $row['wr_22']) ?> /
                                        <?= number_format($row['wr_32_real'] * $row['wr_22']) ?>원
                                    </div>
                                    <div class="cnt_left" style="width:150px;text-align:right">
                                        <?= number_format($row['wr_36'] * $row['wr_22']) ?> /
                                        <?= number_format($row['wr_36_real'] * $row['wr_22']) ?>원
                                    </div>
                                    <div class="cnt_left" style="width:150px;text-align:right">
                                        <?= number_format($row['wr_42'] * $row['wr_22']) ?> /
                                        <?= number_format($row['wr_42_real'] * $row['wr_22']) ?>원
                                    </div>
                                    <div class="cnt_left" style="width:150px;text-align:right">
                                        <?= number_format($row['wr_43'] * $row['wr_22']) ?> /
                                        <?= number_format($row['wr_43_real'] * $row['wr_22']) ?>원
                                    </div>
                                    <div class="cnt_left" style="width:150px;text-align:right">
                                        <?= number_format($row['wr_44'] * $row['wr_22']) ?> /
                                        <?= number_format($row['wr_44_real'] * $row['wr_22']) ?>원
                                    </div>
                                    <div class="cnt_left" style="width:150px;text-align:right">
                                        <?= number_format($row['wr_37'] * $row['wr_22']) ?>원
                                    </div>
                                    <div class="cnt_left" style="width:150px;text-align:right">
                                        <?= number_format($row['wr_40'] * $row['wr_22']) ?> /
                                        <?= number_format($row['wr_40_real'] * $row['wr_22']) ?>원
                                    </div>
                                    <div class="cnt_left" style="width:150px;text-align:right">
                                        <?= number_format($row['wr_41'] * $row['wr_22']) ?> /
                                        <?= number_format($row['wr_41_real'] * $row['wr_22']) ?>원
                                    </div>
                                    <div class="cnt_left" style="width:150px;text-align:right">
                                        <?= number_format($row['wr_45'] * $row['wr_22']) ?> /
                                        <?= number_format($row['wr_45_real'] * $row['wr_22']) ?>원
                                    </div>
                                    <div class="cnt_left" style="width:150px;text-align:right">
                                        <?= number_format($row['wr_46'] * $row['wr_22']) ?> /
                                        <?= number_format($row['wr_46_real'] * $row['wr_22']) ?>원
                                    </div>

                                    <div class="cnt_left" style="width:150px;text-align:right">
                                        <?= number_format($hab_amount) ?> / <?= number_format($hab__real_amount) ?>원
                                    </div>
                                </li>
                                <?php
                                $cur_no = $cur_no - 1;
                            } ?>
                            <?php if (sql_num_rows($rst) == 0) {
                                echo '<li class="empty_table">내역이 없습니다.</li>';
                            } ?>
                        </ul>
                    </div>
                </div>
            </form>
        </div>

        <?php
        $query_string = http_build_query(array_merge($_GET, ['page' => '']));

        echo get_paging(
                G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'],
                $page,
                $total_page,
                $_SERVER['SCRIPT_NAME'] . '?' . $query_string
        );
        ?>
    </div>

    <div class="bo_sch_wrap">
        <fieldset class="bo_sch" style="top: 35%;">
            <h3>검색</h3>
            <form name="fsearch" method="get">
                <input type="hidden" name="report_type" value="<?= $report_type ?>" />
                <input type="hidden" name="report_category" value="<?= $report_category ?>" />
                <input type="hidden" name="report_mb_id" value="<?= $report_mb_id ?>" />

                <div style="border:1px solid #ddd; margin-bottom:20px; padding:10px">
                    <select name="search_wr_26" id="search_wr_26" class="frm_input">
                        <option value="">카테고리</option>
                        <?
                        $arr = get_code_list('2'); // 카테고리 코드 조회
                        foreach ($arr as $key => $value) {
                            $selected = $search_wr_26 == $value['idx'] ? "selected" : "";
                            echo "<option value=\"{$value['idx']}\" {$selected} >{$value['code_name']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div style="border:1px solid #ddd; margin-bottom:20px; padding:10px">
                    <div class="wli_cnt">
                        <select name="search_brand" id="search_brand" class="frm_input search_sel">
                            <option value="">전체 브랜드</option>
                            <?php
                            $arr = get_code_list('1'); // 카테고리 코드 조회
                            foreach ($arr as $key => $value) {
                                $selected = $search_brand == $value['idx'] ? "selected" : "";
                                echo "<option value=\"{$value['idx']}\" {$selected} >{$value['code_name']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div style="border:1px solid #ddd; margin-bottom:20px; padding:10px">
                    <select name="search_warehouse" id="search_warehouse" class="frm_input search_sel" style="width: 100%;">
                        <option value="">전체 창고</option>
                        <option value="wr_32" <?= get_selected('wr_32', $search_warehouse) ?>>한국창고</option>
                        <option value="wr_36" <?= get_selected('wr_36', $search_warehouse) ?>>미국창고</option>
                        <option value="wr_42" <?= get_selected('wr_42', $search_warehouse) ?>>FBA창고</option>
                        <option value="wr_43" <?= get_selected('wr_43', $search_warehouse) ?>>W-FBA창고</option>
                        <option value="wr_44" <?= get_selected('wr_44', $search_warehouse) ?>>U-FBA창고</option>
                        <option value="wr_37" <?= get_selected('wr_37', $search_warehouse) ?>>임시창고</option>
                        <option value="wr_40" <?= get_selected('wr_40', $search_warehouse) ?>>한국반품창고</option>
                        <option value="wr_41" <?= get_selected('wr_41', $search_warehouse) ?>>미국반품창고</option>
                        <option value="wr_45" <?= get_selected('wr_45', $search_warehouse) ?>>한국폐기창고</option>
                        <option value="wr_46" <?= get_selected('wr_46', $search_warehouse) ?>>미국폐기창고</option>
                    </select>
                </div>

                <div style="border:1px solid #ddd; margin-bottom:20px; padding:10px; display:flex; align-items: center;">
                    <input type="number" name="search_min" min="0" value="<?= $search_min ?>" class="frm_input"
                           placeholder="수량 최소값" />
                    <span>~</span>
                    <input type="number" name="search_max" value="<?= $search_max ?>" class="frm_input"
                           placeholder="수량 최대값" />
                </div>


                <div style="border:1px solid #ddd; margin-bottom:20px; padding:10px">
                    <input type="text" name="stx2" value="<?php echo stripslashes($stx2) ?>" class="frm_input"
                           style="width:100%;" placeholder="대표코드/SKU/상품명으로 검색">
                </div>
                <strong>유통기한 검색</strong>
                <div style="border:1px solid #ddd; margin-bottom:20px; padding:10px">
                    <select name="search_expired" id="search_expired" class="frm_input">
                        <option value="">모든 재고</option>
                        <option value="Y" <?= get_selected($search_expired, 'Y') ?>>유통기한 임박 재고</option>
                    </select>
                </div>
                <strong>유통기한 일자</strong>
                <div style="border:1px solid #ddd; margin-bottom:20px; height:38px;">
                    <input type="date" name="expired_st_date" value="<?php echo $expired_st_date ?>" class="sch_input" size="25"
                           maxlength="20" placeholder="" style="width:45%;text-align:center">
                    <span style="display:inline-block;height:38px;line-height:38px; margin:0 5px">~</span>
                    <input type="date" name="expired_ed_date" value="<?php echo stripslashes($expired_ed_date) ?>" class="sch_input"
                           size="25" maxlength="20" placeholder=" " style="width:45%;text-align:center">
                </div>

                <button type="submit" value="검색" class="btn_b01" style="width:49%;margin-top:15px"><i class="fa fa-search"
                                                                                                      aria-hidden="true"></i>
                    검색하기
                </button>
                <button type="button" value="초기화" class="btn_b02" style="width:49%;margin-top:15px;"
                        onclick="location.href='<?php echo $_SERVER['PHP_SELF'] ?>'"><i class="fa fa-repeat"
                                                                                        aria-hidden="true"></i>
                    검색초기화
                </button>
                <button type="button" class="bo_sch_cls" title="닫기"><i class="fa fa-times" aria-hidden="true"></i><span
                            class="sound_only">닫기</span></button>
            </form>

        </fieldset>
        <div class="bo_sch_bg"></div>
    </div>


    <div class="modal_frm">
        <div class="modal_body">
            <div class="modal_body_frm">
                <button type="button" class="btn_b01 rack_write_btn" data="" style="margin:10px;">랙별 재고조사</button>
            </div>
            <button type="button" onclick="modal_action()" class="modal_cls_btn" title="닫기"><i class="fa fa-times"
                                                                                               aria-hidden="true"></i><span
                        class="sound_only">닫기</span></button>
        </div>
        <div class="modal_bg"></div>
    </div>

    <script>
        function modal_action(wr_id) {
            if ("<?= $member['mb_id'] ?>" != "test") {
                alert("개발 중 입니다.");
                return false;
            }

            if ($(".modal_frm").css("display") == "block") {
                $(".modal_frm").css("display", "none");
            } else {
                $(".modal_frm").css("display", "block");
                $(".mb_write_btn,.rack_write_btn").attr("data", wr_id);
            }
        }


        $(function() {

            $("#excel_btn").bind("click", function() {

                if (!confirm("엑셀 출력을 하시겠습니까?")) {
                    return false;
                }

                const currentUrl = new URL(window.location.href); // 현재 URL을 가져옴
                const params = currentUrl.search; // 쿼리스트링만 가져옴
                const newUrl = `${g5_url}/report/report_excel.php${params}`; // 기존 쿼리스트링을 새로운 URL에 붙임


                location.href = newUrl;
            });
            // 게시판 검색
            $(".btn_bo_sch").on("click", function() {
                $(".bo_sch_wrap").toggle();
            });

            $('.bo_sch_bg, .bo_sch_cls').click(function() {
                $('.bo_sch_wrap').hide();
            });

            $('#sorting_box').bind('change', function() {

                let sort = $(this).val();

                if (sort == "default") {
                    location.href = '?sst=wr_id&sod=desc&stx2=<?php echo $stx2 ?>&report_type=<?= $report_type ?>&report_category=<?= $report_category ?>&report_mb_id=<?= $report_mb_id ?>';
                } else if (sort == "up") {
                    location.href = '?sst=stock&sod=desc&stx2=<?php echo $stx2 ?>&report_type=<?= $report_type ?>&report_category=<?= $report_category ?>&report_mb_id=<?= $report_mb_id ?>';
                } else if (sort == "down") {
                    location.href = '?sst=stock&sod=asc&stx2=<?php echo $stx2 ?>&report_type=<?= $report_type ?>&report_category=<?= $report_category ?>&report_mb_id=<?= $report_mb_id ?>';
                }
            });


            $('#mb_id_box').bind('change', function() {

                let val = $(this).val();

                location.href = '?sst=<?= $_GET['sst'] ?>&sod=<?= $_GET['sod'] ?>&stx2=<?php echo $_GET['stx2'] ?>&report_type=<?= $report_type ?>&report_category=<?= $report_category ?>&report_mb_id=' + val;

            });

            $('#category_box').bind('change', function() {
                let val = $(this).val();
                location.href = `?sst=<?= $_GET['sst'] ?>&sod=<?= $_GET['sod'] ?>&stx2=<? $_GET['stx2'] ?>&report_type=<?= $report_type ?>&report_category=${val}&report_mb_id=<?= $report_mb_id ?>`;
            });

            $('.mb_write_btn').bind('click', function() {
                let id = $(this).attr('data');
                var _width = '1800';
                var _height = '800';

                var _left = Math.ceil((window.screen.width - _width) / 2);
                var _top = Math.ceil((window.screen.height - _height) / 2);

                window.open("./report_mb.php?sst=<?= $_GET['sst'] ?>&sod=<?= $_GET['sod'] ?>&stx2=<?php echo $_GET['stx2'] ?>&report_type=<?= $report_type ?>&report_category=<?= $report_category ?>&report_mb_id=<?= $report_mb_id ?>", "pop_rack" + id, "left=" + _left + ", top=" + _top + ", width=" + _width + ", height=" + _height + ", scrollbars=1");

                return false;
            });

            $('.rack_write_btn').bind('click', function() {
                //alert("개발 중 입니다.");
                //return false;

                let id = $(this).attr('data');
                var _width = '1600';
                var _height = '800';

                var _left = Math.ceil((window.screen.width - _width) / 2);
                var _top = Math.ceil((window.screen.height - _height) / 2);

                window.open("./report_rack_list.php?wr_id=" + id, "pop_rack" + id, "left=" + _left + ", top=" + _top + ", width=" + _width + ", height=" + _height + ", scrollbars=1");

                return false;
            });

        })

        function expiration_stock_manage() {
            const seq_list = document.querySelectorAll('[name="seq[]"]:checked');

            if (seq_list.length < 1) {
                alert('하나 이상의 재고를 선택해주세요.');
                return;
            }

            const seq_values = Array.from(seq_list).map(checkbox => checkbox.value);

            const popup_url = `popup.report_expiration_manage.php?seqs=${seq_values.join(',')}`;
            const popup = window.open(popup_url, 'expirationManagePopup', 'width=900,height=600,resizable=yes');
        }
    </script>


<?php
include_once(G5_THEME_PATH . '/tail.php');
