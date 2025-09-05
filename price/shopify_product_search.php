<?php
// api/shopify_product_search.php (웹 서버에서 접근 가능한 경로)
include_once('./_common.php');

header('Content-Type: application/json'); // JSON 응답을 명시

// 1. dbconfig.php 로드: 그누보드 환경 로딩 또는 직접 경로 지정
// 그누보드의 common.php가 dbconfig.php를 로드한다고 가정하면
// require_once $_SERVER['DOCUMENT_ROOT'] . '/common.php';
// 만약 독립적으로 작동해야 한다면 dbconfig.php의 절대 경로 지정:
// require_once '/path/to/your/gnuboard/dbconfig.php';
// 여기서는 api/ 아래 있고 dbconfig가 루트에 있다면,

// 2. ShopifyService.php 클래스 파일 로드 (ShopifyService.php의 실제 경로 지정)
require_once __DIR__ . '/ShopifyService.php'; // 실제 경로에 맞춰 수정!

define('VALID_CLIENT_API_KEY', CLIENT_BACKEND_API_KEY); // <-- 이 키를 직접 설정

// 2. 클라이언트로부터 전송된 'X-API-KEY' 헤더 값 받기
$receivedApiKey = $_SERVER['HTTP_X_API_KEY'] ?? '';

// --- 3. API 키 검증 ---
if (empty($receivedApiKey) || $receivedApiKey !== VALID_CLIENT_API_KEY) {
    http_response_code(401); // 401 Unauthorized 응답
    echo json_encode([
        'status' => 'error',
        'message' => 'API Key가 유효하지 않습니다.'
    ]);
    exit();
}

// 3. 클라이언트로부터 검색어 받기
// 이 예제에서는 클라이언트가 GET 요청으로 'searchTerm' 파라미터를 보낸다고 가정합니다.
$searchTerm = $_GET['searchTerm'] ?? '';

if (empty($searchTerm)) {
    echo json_encode([
        'status' => 'error',
        'message' => '검색어를 입력해 주세요.'
    ]);
    exit();
}

try {
    $shopifyService = new ShopifyService();
    $response = $shopifyService->searchProductsByTitle($searchTerm);
    $products = $response['data']['products'];

    $formattedProducts = [];
    if (!empty($products)) {
        foreach ($products as $product) {
            // 모든 variants 정보를 저장할 배열 초기화
            $productVariants = [];
            if (isset($product['variants']) && is_array($product['variants'])) {
                foreach ($product['variants'] as $variant) {
                    $productVariants[] = [
                        'id' => $variant['id'],
                        'title' => $variant['title'], // 예: S / Red
                        'price' => $variant['price'] ?? 'N/A',
                        'sku' => $variant['sku'] ?? 'N/A', // SKU도 추가
                    ];
                }
            }

            // 클라이언트가 필요로 하는 정보를 가공하여 배열에 추가
            $formattedProducts[] = [
                'id' => $product['id'],
                'title' => $product['title'],
                'handle' => $product['handle'],
                'product_type' => $product['product_type'],
                'price' => $product['variants'][0]['price'] ?? 'N/A', // 메인 가격은 첫 번째 배리언트 가격으로 유지
                'image_url' => $product['image']['src'] ?? 'no_image.png',
                'online_store_url' => "https://" . SHOPIFY_SHOP_DOMAIN . "/products/{$product['handle']}",
                'api_call_limit' => $response['shopify_api_call_limit'],
                'variants' => $productVariants, // <--- 모든 variants 정보를 배열로 추가!
            ];
        }
    }

    echo json_encode([
        'status' => 'success',
        'data' => [
            'data' => [
                'products' => $formattedProducts
            ],
            'count' => [
                'count' => count($formattedProducts)
            ]
        ]
    ]);

} catch (Exception $e) {
    // 예외 발생 시 서버 오류 응답
    http_response_code(500); 
    echo json_encode([
        'status' => 'error',
        'message' => '서버 오류: ' . $e->getMessage()
    ]);
    error_log('Shopify Product Search API Exception: ' . $e->getMessage());
}
?>