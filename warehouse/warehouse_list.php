<?php

// warehouse_list.php
// 창고 정보를 담는 연관 배열 정의
// 각 창고 코드(키)에 해당하는 배열 안에 filed, filed_real, ware_name, quality 정보를 담습니다.
$warehouseConfig = [
    // Good Quality Warehouses
    "1000" => [
        'filed' => 'wr_32',
        'filed_real' => 'wr_32_real',
        'ware_name' => '한국창고',
        'quality' => 'good'
    ],
    "3000" => [
        'filed' => 'wr_36',
        'filed_real' => 'wr_36_real',
        'ware_name' => '미국창고',
        'quality' => 'good'
    ],
    "4000" => [
        'filed' => 'wr_42',
        'filed_real' => 'wr_42_real',
        'ware_name' => 'FBA창고',
        'quality' => 'good'
    ],
    "5000" => [
        'filed' => 'wr_43',
        'filed_real' => 'wr_43_real',
        'ware_name' => 'W-FBA창고',
        'quality' => 'good'
    ],
    "6000" => [
        'filed' => 'wr_44',
        'filed_real' => 'wr_44_real',
        'ware_name' => 'U-FBA창고',
        'quality' => 'good'
    ],

    // Refurbished Quality Warehouses (반품창고)
    "7000" => [
        'filed' => 'wr_40',
        'filed_real' => 'wr_40_real',
        'ware_name' => '한국반품창고',
        'quality' => 'refur'
    ],
    "8000" => [
        'filed' => 'wr_41',
        'filed_real' => 'wr_41_real',
        'ware_name' => '미국반품창고',
        'quality' => 'refur'
    ],
    "9000" => [
        'filed' => 'wr_47',
        'filed_real' => 'wr_47_real',
        'ware_name' => 'FBA반품창고',
        'quality' => 'refur'
    ],
    "9100" => [
        'filed' => 'wr_48',
        'filed_real' => 'wr_48_real',
        'ware_name' => 'W-FBA반품창고',
        'quality' => 'refur'
    ],
    "9200" => [
        'filed' => 'wr_49',
        'filed_real' => 'wr_49_real',
        'ware_name' => 'U-FBA반품창고',
        'quality' => 'refur'
    ],

    // Dispose Quality Warehouses (폐기창고)
    "11000" => [
        'filed' => 'wr_45',
        'filed_real' => 'wr_45_real',
        'ware_name' => '한국폐기창고',
        'quality' => 'dispose'
    ],
    "12000" => [
        'filed' => 'wr_46',
        'filed_real' => 'wr_46_real',
        'ware_name' => '미국폐기창고',
        'quality' => 'dispose'
    ],
    "13000" => [
        'filed' => 'wr_50',
        'filed_real' => 'wr_50_real',
        'ware_name' => 'FBA폐기창고',
        'quality' => 'dispose'
    ],
    "13100" => [
        'filed' => 'wr_51',
        'filed_real' => 'wr_51_real',
        'ware_name' => 'W-FBA폐기창고',
        'quality' => 'dispose'
    ],
    "13200" => [
        'filed' => 'wr_52',
        'filed_real' => 'wr_52_real',
        'ware_name' => 'U-FBA폐기창고',
        'quality' => 'dispose'
    ],
];
