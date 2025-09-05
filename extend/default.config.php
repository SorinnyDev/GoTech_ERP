<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// 유저 사이드뷰에서 아이콘 지정 안했을시 기본 no 프로필 이미지
define('G5_NO_PROFILE_IMG', '<span class="profile_img"><img src="' . G5_IMG_URL . '/no_profile.gif" alt="no_profile" width="' . $config['cf_member_icon_width'] . '" height="' . $config['cf_member_icon_height'] . '"></span>');

define('G5_USE_MEMBER_IMAGE_FILETIME', TRUE);

// 썸네일 처리 방식, 비율유지 하지 않고 썸네일을 생성하려면 주석을 풀고 값은 false 입력합니다. ( true 또는 주석으로 된 경우에는 비율 유지합니다. )
//define('G5_USE_THUMB_RATIO', false);

switch ($bo_table) {
  case "sales" :
    $menu_num = 2;
    break;
  case "product" :
    $menu_num = 1;
    break;
}

function get_rack_option($warehouse, $val = "", $temp_chk = false)
{
  $option = "";

  $sql = "select * from g5_rack where gc_use = 1 and gc_warehouse = '{$warehouse}'";
  if ($temp_chk == true) $sql .= " AND seq NOT IN(1)";

  $rst = sql_query($sql);
  for ($i = 0; $row = sql_fetch_array($rst); $i++) {
    $option .= '<option value="' . $row['seq'] . '" ' . get_selected($row['seq'], $val) . '>' . $row['gc_name'] . '</option>';
  }
  return $option;
}

function get_rack_name($seq)
{
  $row = sql_fetch("select gc_name from g5_rack where seq = '{$seq}'");

  return $row['gc_name'];
}

function get_country_code($str)
{
  $row = sql_fetch("select code_2 from g5_country_code where code_en = '{$str}'");

  return $row['code_2'];
}

function get_country_krname($str)
{
  $row = sql_fetch("select code_kr from g5_country_code where code_2 = '{$str}'");

  return $row['code_kr'];
}

function weight_findMax($value1, $value2, $value3)
{
  //max함수 대체 수동으로 값을 비교하여 가장 큰 값 ,
  $maxValue = $value1;

  if ($value2 > $maxValue) {
    $maxValue = $value2;
  }

  if ($value3 > $maxValue) {
    $maxValue = $value3;
  }

  return $maxValue;
}

//코드관리 - 도메인 완료되기전까지 임시
function get_domain_option($str)
{
  $option = "";
  $code_list = get_code_list('4');
  foreach ($code_list as $key => $value) {
    $selected = ($str == $value['code_value']) ? "selected" : "";
    $option .= "<option value=\"{$value['code_value']}\" {$selected}>{$value['code_name']}</option>";
  }

  return $option;

}

function get_domain_option_q10($str)
{
  $option = "";
  $code_list[]['code_value'] = "Qoo10";
  $code_list[]['code_value'] = "Qoo10-1";
  $code_list[]['code_value'] = "qoo10-jp";
  $code_list[]['code_value'] = "qoo10jp-1";

  foreach ($code_list as $key => $value) {
    $selected = ($str == $value['code_value']) ? "selected" : "";
    $option .= "<option value=\"{$value['code_value']}\" {$selected}>{$value['code_value']}</option>";
  }

  return $option;

}

function get_code_list($type)
{
  $sql = " SELECT * FROM g5_code_list WHERE code_type = '{$type}' and code_use = 'Y' and del_yn = 'N' ORDER BY code_name ASC";
  $rs = sql_query($sql);
  $arr = [];
  for ($i = 0; $row = sql_fetch_array($rs); $i++) {
    array_push($arr, $row);
  }
  return $arr;
}

function get_code_name($type, $idx)
{
  $code_name = sql_fetch(" SELECT * FROM g5_code_list WHERE code_type = '{$type}' AND idx = '{$idx}' ")['code_name'];
  return $code_name;
}

function get_mb_code_list($type)
{
  $search_sql = "";

  if ($type) {
    $search_sql = " AND mb_type = '{$type}' ";
  }
  $sql = " SELECT * FROM g5_member WHERE del_yn = 'N' {$search_sql}  ORDER BY mb_name ASC";
  $rs = sql_query($sql);
  $arr = [];
  for ($i = 0; $row = sql_fetch_array($rs); $i++) {
    array_push($arr, $row);
  }
  return $arr;
}

function code_increment($type)
{
  $sql = " SELECT MAX(code_value) AS max_cnt FROM g5_code_list WHERE code_type = '{$type}' AND del_yn = 'N' ";
  $max_cnt = (int)sql_fetch($sql)['max_cnt'];
  $count = str_pad(($max_cnt + 1), 4, '0', STR_PAD_LEFT);
  return $count;
}

function rack_search($wr_id, $warehouse = '', $show_expired = false)
{
  $sql = "
  SELECT distinct rs.wr_rack, rs.wr_stock, r.gc_warehouse, r.gc_name, rep.id as 'expired_id'
  FROM g5_rack_stock as rs
  left join g5_rack as r on r.seq = rs.wr_rack
  left join g5_rack_expired as rep on rep.rack_id = r.seq and rep.product_id = rs.wr_product_id
  WHERE rs.wr_product_id = '{$wr_id}'
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
    if ($value['is_expired']) {
      $result_arr .= "[" . PLATFORM_TYPE[$gc_warehouse] . "] " . $value['rack_name'] . "(주의 재고 : {$value['total']}개)<br>";
    } else {
      $result_arr .= "[" . PLATFORM_TYPE[$gc_warehouse] . "] " . $value['rack_name'] . "(재고 : {$value['total']}개)<br>";
    }
  }

  return $result_arr;
}

