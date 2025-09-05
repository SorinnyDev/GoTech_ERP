<?php
include_once("./_common.php");

if ($key !== 'gotech-secret-key-250207') {
  exit;
}

if (!$today) {
  $today = date('Y-m-d');
  $now = new DateTime();

  if ($now->format('H') < 12) {
    $yesterday = clone $now;
    $yesterday->modify('-1 day');
    $today = $yesterday->format('Y-m-d');
  } else {
    $today = $now->format('Y-m-d');
  }
}

$curl = curl_init();

$url = 'https://www.koreaexim.go.kr/site/program/financial/exchangeJSON?authkey=bZbzWkPKLR04rH0jr6rnbhB7xyFXCsBi&data=AP01&searchdate=' . $today;

curl_setopt_array($curl, array(
  CURLOPT_URL => $url,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_SSL_VERIFYPEER => false,
  CURLOPT_SSL_VERIFYHOST => false,
  CURLOPT_COOKIEJAR => dirname(__FILE__) . '/cookie.txt',
  CURLOPT_COOKIEFILE => dirname(__FILE__) . '/cookie.txt'
));

$response = curl_exec($curl);
$curl_error = curl_error($curl);

curl_close($curl);

$data = json_decode($response, true);

foreach ($data as $item) {
  $item['kftc_deal_bas_r'] = str_replace(',', '', $item['kftc_deal_bas_r']);
  switch ($item['cur_unit']) {
    case 'USD':
      $sql = "update g5_excharge 
              set ex_date = '{$today}', 
                  rate = '{$item['kftc_deal_bas_r']}',
                  up_datetime = now()
              where ex_eng = 'USD'";
      sql_query($sql);
      break;
    case 'JPY(100)':
      $sql = "update g5_excharge 
              set ex_date = '{$today}', 
                  rate = '{$item['kftc_deal_bas_r']}',
                  up_datetime = now()
              where ex_eng = 'JPY'";
      sql_query($sql);
      break;
    case 'EUR':
      $sql = "update g5_excharge 
              set ex_date = '{$today}', 
                  rate = '{$item['kftc_deal_bas_r']}',
                  up_datetime = now()
              where ex_eng = 'EUR'";
      sql_query($sql);
      break;
    case 'GBP':
      $sql = "update g5_excharge 
              set ex_date = '{$today}', 
                  rate = '{$item['kftc_deal_bas_r']}',
                  up_datetime = now()
              where ex_eng = 'GBP'";
      sql_query($sql);
      break;
    case 'CAD':
      $sql = "update g5_excharge 
              set ex_date = '{$today}', 
                  rate = '{$item['kftc_deal_bas_r']}',
                  up_datetime = now()
              where ex_eng = 'CAD'";
      sql_query($sql);
      break;
    case 'AUD':
      $sql = "update g5_excharge 
              set ex_date = '{$today}', 
                  rate = '{$item['kftc_deal_bas_r']}',
                  up_datetime = now()
              where ex_eng = 'AUD'";
      sql_query($sql);
      break;
    case 'SGD':
      $sql = "update g5_excharge 
              set ex_date = '{$today}', 
                  rate = '{$item['kftc_deal_bas_r']}',
                  up_datetime = now()
              where ex_eng = 'SGD'";
      sql_query($sql);
      break;
    case 'MYR':
      $sql = "update g5_excharge 
              set ex_date = '{$today}', 
                  rate = '{$item['kftc_deal_bas_r']}',
                  up_datetime = now()
              where ex_eng = 'MYR'";
      sql_query($sql);
      break;
  }
}
