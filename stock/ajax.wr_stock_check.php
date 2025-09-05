<?php
include_once('./_common.php');

if ($is_guest)
    alert('로그인 후 이용하세요.');

if (!$wr_id)
    alert('잘못된 접근입니다.');


$query = "SELECT * FROM g5_write_product as wp WHERE wr_id = {$wr_id}";

$result = sql_fetch($query);

$success = true;
$message = [];

if ($is_force_update == 'Y') {
    if ((is_numeric($wr_32) || $wr_32) && $wr_32 !== $result['wr_32']) {
        $message[] = "한국창고 재고 {$result['wr_32']} >> {$wr_32}";
    }

    if ((is_numeric($wr_36) || $wr_36) && $wr_36 !== $result['wr_36']) {
        $message[] = "미국창고 재고 {$result['wr_36']} >> {$wr_36}";
    }

    if ((is_numeric($wr_42) || $wr_42) && $wr_42 !== $result['wr_42']) {
        $message[] = "FBA창고 재고 {$result['wr_42']} >> {$wr_42}";
    }

    if ((is_numeric($wr_43) || $wr_43) && $wr_43 !== $result['wr_43']) {
        $message[] = "W-FBA창고 재고 {$result['wr_43']} >> {$wr_43} ";
    }

    if ((is_numeric($wr_44) || $wr_44) && $wr_44 !== $result['wr_44']) {
        $message[] = "U-FBA창고 재고 {$result['wr_44']} >> {$wr_44}";
    }

    if ((is_numeric($wr_37) || $wr_37) && $wr_37 !== $result['wr_37']) {
        $message[] = "임시창고 재고 {$result['wr_37']} >> {$wr_37}";
    }
    if ((is_numeric($wr_40) || $wr_40) && $wr_40 !== $result['wr_40']) {
        $message[] = "한국반품창고 재고 {$result['wr_40']} >> {$wr_40}";
    }
    if ((is_numeric($wr_41) || $wr_41) && $wr_41 !== $result['wr_41']) {
        $message[] = "미국반품창고 재고 {$result['wr_41']} >> {$wr_41}";
    }
    if ((is_numeric($wr_45) || $wr_45) && $wr_45 !== $result['wr_45']) {
        $message[] = "한국폐기창고 재고 {$result['wr_45']} >> {$wr_45}";
    }
    if ((is_numeric($wr_46) || $wr_46) && $wr_46 !== $result['wr_46']) {
        $message[] = "미국폐기창고 재고 {$result['wr_46']} >> {$wr_46}";
    }

} else {
    if ((is_numeric($wr_32) || $wr_32) && $wr_32 !== $result['wr_32_real']) {
        $message[] = "한국창고 재고 {$result['wr_32_real']} >> {$wr_32}";
    }

    if ((is_numeric($wr_36) || $wr_36) && $wr_36 !== $result['wr_36_real']) {
        $message[] = "미국창고 재고 {$result['wr_36_real']} >> {$wr_36}";
    }

    if ((is_numeric($wr_42) || $wr_42) && $wr_42 !== $result['wr_42_real']) {
        $message[] = "FBA창고 재고 {$result['wr_42_real']} >> {$wr_42}";
    }

    if ((is_numeric($wr_43) || $wr_43) && $wr_43 !== $result['wr_43_real']) {
        $message[] = "W-FBA창고 재고 {$result['wr_43_real']} >> {$wr_43} ";
    }

    if ((is_numeric($wr_44) || $wr_44) && $wr_44 !== $result['wr_44_real']) {
        $message[] = "U-FBA창고 재고 {$result['wr_44_real']} >> {$wr_44}";
    }

    if ((is_numeric($wr_37) || $wr_37) && $wr_37 !== $result['wr_37']) {
        $message[] = "임시창고 재고 {$result['wr_37']} >> {$wr_37}";
    }
    if ((is_numeric($wr_40) || $wr_40) && $wr_40 !== $result['wr_40_real']) {
        $message[] = "한국반품창고 재고 {$result['wr_40_real']} >> {$wr_40}";
    }
    if ((is_numeric($wr_41) || $wr_41) && $wr_41 !== $result['wr_41_real']) {
        $message[] = "미국반품창고 재고 {$result['wr_41_real']} >> {$wr_41}";
    }
    if ((is_numeric($wr_45) || $wr_45) && $wr_45 !== $result['wr_45_real']) {
        $message[] = "한국폐기창고 재고 {$result['wr_45_real']} >> {$wr_45}";
    }
    if ((is_numeric($wr_46) || $wr_46) && $wr_46 !== $result['wr_46_real']) {
        $message[] = "미국폐기창고 재고 {$result['wr_46_real']} >> {$wr_46}";
    }


}


if (count($message) > 0) {
    $message[] = "해당 재고 수정하시겠습니까 ?";
} else {
    $success = false;
    $message[] = "수정할 재고를 입력해주세요.";
}


echo json_encode(['success' => $success, 'message' => implode("\n", $message)]);