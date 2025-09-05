<?php

namespace App\Services;

use App\DTOs\CrawledProductDTO;
use App\Http\Controllers\ShopifyController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\DomCrawler\Crawler;

class CrawlingService
{
  private string $cookieDir;

  public function __construct()
  {
    $this->cookieDir = storage_path('app/cookies');

    if (!is_dir($this->cookieDir)) {
      mkdir($this->cookieDir, 0755, true);
    }
  }

  public function crawlStyleKorea($url): ?CrawledProductDTO
  {

    $htmlContent = $this->commonFetchHTML($url);

    $crawler = new Crawler($htmlContent);

    $productName = '';
    $productSalePrice = '';

    $crawler->filter('script')->each(function (Crawler $script) use (&$productName, &$productPrice, &$productSalePrice) {
      $text = $script->text();

      if (!str_contains($text, "ttq.track('ViewContent'")) {
        return false;
      }

      if (preg_match("/content_name:\s\"(.+)\"/U", $text, $matches)) {
        $productName = $matches[1];
      }

      if (preg_match("/price:\s(.+),/U", $text, $matches)) {
        $productSalePrice = $matches[1];
      }

      if ($productName && $productSalePrice) {
        return false;
      }

      return true;
    });

    $setting = $this->getSiteSetting('styleKorea');

    $standardPrice = $expressPrice = 0;

    # 일반 배송비 추가
    if ($productSalePrice <= $setting->deli_min_price && $setting->deli_min_fee > 0) {
      $standardPrice = $setting->deli_min_fee;
    } else if ($productSalePrice > $setting->deli_max_price && $setting->deli_max_fee > 0) {
      $standardPrice = $setting->deli_max_fee;
    }

    # 특송 배송비 추가
    if ($productSalePrice <= $setting->deli_express_min_price && $setting->deli_express_min_fee > 0) {
      $expressPrice = $setting->deli_express_min_fee;
    } else if ($productSalePrice > $setting->deli_express_max_price && $setting->deli_express_max_fee > 0) {
      $expressPrice = $setting->deli_express_max_fee;
    }


    $image = $crawler->filter('.stv_item img')->each(function (Crawler $img) {
      return $img->attr('src');
    });

    $image = end($image);

    return CrawledProductDTO::create(
      name: $productName,
      salePrice: $productSalePrice,
      price: $productPrice,
      expressPrice: $expressPrice,
      standardPrice: $standardPrice,
      img: $image,
    );

  }

  public function crawlJolse($url): ?CrawledProductDTO
  {
    $htmlContent = $this->commonFetchHTML($url);

    $crawler = new Crawler($htmlContent);

    $productName = '';
    $productPrice = '';
    $productSalePrice = '';

    $crawler->filter('script')->each(function (Crawler $script) use (&$productName, &$productPrice, &$productSalePrice) {
      $text = $script->text();

      if (preg_match("/var product_name\s*=\s*'([^']+)'/U", $text, $matches)) {
        $productName = $matches[1];
      }

      if (preg_match("/var product_sale_price\s=\s(.+);/U", $text, $matches)) {
        $productSalePrice = $matches[1];
      }

      if (preg_match("/var product_price\s*=\s*'([^']+)'/U", $text, $matches)) {
        $productPrice = $matches[1];
      }

      if ($productName && $productPrice && $productSalePrice) {
        return false;
      }

      return true;
    });

    $setting = $this->getSiteSetting('jolse');

    $standardPrice = $expressPrice = 0;

    # 일반 배송비 추가
    if ($productSalePrice <= $setting->deli_min_price && $setting->deli_min_fee > 0) {
      $standardPrice = $setting->deli_min_fee;
    } else if ($productSalePrice > $setting->deli_max_price && $setting->deli_max_fee > 0) {
      $standardPrice = $setting->deli_max_fee;
    }

    # 특송 배송비 추가
    if ($productSalePrice <= $setting->deli_express_min_price && $setting->deli_express_min_fee > 0) {
      $expressPrice = $setting->deli_express_min_fee;
    } else if ($productSalePrice > $setting->deli_express_max_price && $setting->deli_express_max_fee > 0) {
      $expressPrice = $setting->deli_express_max_fee;
    }


    $image = $crawler->filter('.BigImage')->attr('src');

    return CrawledProductDTO::create(
      name: $productName,
      salePrice: $productSalePrice,
      price: $productPrice,
      expressPrice: round($expressPrice, 2),
      standardPrice: round($standardPrice, 2),
      img: $image
    );
  }

  public function crawlAmazon($url): ?CrawledProductDTO
  {
    $maxRetries = 3;
    $lastException = null;

    for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
      try {
        $htmlContent = $this->commonFetchHTML($url);

        $crawler = new Crawler($htmlContent);

        $productName = $crawler->filter('#productTitle')->text();
        $productPrice = $crawler->filter('#apex_offerDisplay_desktop .a-price .a-offscreen')->text();
        $productPrice = str_replace('$', '', $productPrice);
        $productSalePrice = trim(str_replace('US', '', $productPrice));
        $image = $crawler->filter('#landingImage')->attr('src');

        $setting = $this->getSiteSetting('amazon');

        $standardPrice = $expressPrice = 0;

        # 일반 배송비 추가
        if ($productSalePrice <= $setting->deli_min_price && $setting->deli_min_fee > 0) {
          $standardPrice = $setting->deli_min_fee;
        } else if ($productSalePrice > $setting->deli_max_price && $setting->deli_max_fee > 0) {
          $standardPrice = $setting->deli_max_fee;
        }

        # 특송 배송비 추가
        if ($productSalePrice <= $setting->deli_express_min_price && $setting->deli_express_min_fee > 0) {
          $expressPrice = $setting->deli_express_min_fee;
        } else if ($productSalePrice > $setting->deli_express_max_price && $setting->deli_express_max_fee > 0) {
          $expressPrice = $setting->deli_express_max_fee;
        }

        return CrawledProductDTO::create(
          name: $productName,
          salePrice: round($productSalePrice, 2),
          price: round($productSalePrice, 2),
          expressPrice: round($expressPrice, 2),
          standardPrice: round($standardPrice, 2),
          img: $image
        );

      } catch (\Exception $e) {
        $lastException = $e;
        error_log("크롤링 시도 {$attempt}/{$maxRetries} 실패: " . $e->getMessage());

        // 마지막 시도가 아니면 딜레이 후 재시도
        if ($attempt < $maxRetries) {
          sleep(rand(2, 5)); // 2-5초 랜덤 딜레이
          continue;
        }

      } catch (\Error $e) {
        $lastException = $e;
        error_log("시스템 오류 시도 {$attempt}/{$maxRetries}: " . $e->getMessage());

        // 마지막 시도가 아니면 딜레이 후 재시도
        if ($attempt < $maxRetries) {
          sleep(rand(2, 5)); // 2-5초 랜덤 딜레이
          continue;
        }
      }
    }

    // 모든 시도 실패 시 마지막 예외 던지기
    if ($lastException instanceof \Error) {
      throw new \Error("시스템 오류 (3회 시도 후 실패): " . $lastException->getMessage());
    } else {
      throw new \Exception("크롤링 실패 (3회 시도 후 실패): " . $lastException->getMessage());
    }
  }

  public function crawlOliveyoung($url): ?CrawledProductDTO
  {
    parse_str(parse_url($url)['query'], $query);

    if (!$query['prdtNo']) {
      return null;
    }

    $requestData = ['prdtNo' => $query['prdtNo']];

    $htmlContent = $this->commonFetchHTMLPost('https://global.oliveyoung.com/product/detail-data', $requestData);

    $data = json_decode($htmlContent);

    $productName = $data->product->prdtName;
    $productPrice = $data->product->saleAmt;
    $productSalePrice = $data->product->saleAmt;
    $image = 'https://image.globaloliveyoungshop.com/' . $data->product->imagePath;

    $setting = $this->getSiteSetting('oliveyoung');

    $standardPrice = $expressPrice = 0;

    # 일반 배송비 추가
    if ($productSalePrice <= $setting->deli_min_price && $setting->deli_min_fee > 0) {
      $standardPrice = $setting->deli_min_fee;
    } else if ($productSalePrice > $setting->deli_max_price && $setting->deli_max_fee > 0) {
      $standardPrice = $setting->deli_max_fee;
    }

    # 특송 배송비 추가
    if ($productSalePrice <= $setting->deli_express_min_price && $setting->deli_express_min_fee > 0) {
      $expressPrice = $setting->deli_express_min_fee;
    } else if ($productSalePrice > $setting->deli_express_max_price && $setting->deli_express_max_fee > 0) {
      $expressPrice = $setting->deli_express_max_fee;
    }

    return CrawledProductDTO::create(
      name: $productName,
      salePrice: $productSalePrice,
      price: $productPrice,
      expressPrice: $expressPrice,
      standardPrice: $standardPrice,
      img: $image
    );
  }

  public function crawlStylevana($url): ?CrawledProductDTO
  {
    try {
      $htmlContent = $this->commonFetchHTML($url);

      $crawler = new Crawler($htmlContent);


      $productName = '';
      $productPrice = 0;
      $productSalePrice = 0;
      $productImage = '';

      $crawler->filter('script')->each(function (Crawler $script) use (&$productName, &$productPrice, &$productSalePrice, &$productImage) {
        $text = $script->text();

        $scriptPattern = '/(.*?product_addtocart_form.*?)/Us';

        if (!preg_match($scriptPattern, $text, $match)) {
          return true;
        }

        $productObject = json_decode($match[1], true);

        if (!$productObject) {
          return true;
        }

        $productName = $productObject['#product_addtocart_form']['configurable']['spConfig']['productInfo']['name'];
        $productPrice = $productSalePrice = $productObject['#product_addtocart_form']['configurable']['spConfig']['prices']['finalPrice']['amount'];
        $images = $productObject['#product_addtocart_form']['configurable']['spConfig']['images'];
        $productImage = current($images)[0]['img'];

        if ($productName && $productPrice) {
          return false;
        }
      });

      return CrawledProductDTO::create(
        name: $productName,
        salePrice: $productSalePrice,
        price: $productPrice,
        img: $productImage
      );
    } catch (\Exception $e) {
      throw new \Exception("크롤링 실패");
    }
  }

  public function crawlYesstyle($url): ?CrawledProductDTO
  {
    try {
      $validCookies = 'locale=en; yshsdb=true; yshccmfg=true; yscmds=false; yscmda=false; yscmdp=false; lvni=69; lantern=3be089fb-f534-43f7-b506-829150d92a25; ysfeid=882e02e25639066ed1194d2350b957dff8cbf8ca3b4a0c955342aa23f95fce08; _fbp=fb.1.1750293359022.233329709377255296; _gcl_au=1.1.875395036.1750293359; _tt_enable_cookie=1; _ttp=01JY2STANXPF0Q79EWEAN7A182.tt.1; _ga=GA1.1.1775533457.1750293360; COOKIE_SHARING=%7B%22actualValue%22%3Afalse%2C%22MOE_DATA_TYPE%22%3A%22boolean%22%7D; orderChannel=2; ysbph=1131583092; tcurrency=19; coid=226; __cf_bm=SHmfxgtDiP.juNUcwzZhtdyJUoXyL2vKnOqcEpOJxs4-1750643294-1.0.1.1-.q9U.QE.7LuHNVP1URmlcHMTLJs1LjrH6UFiSojyV0zpaFIb1XYycHsqZUdApQpGdTOMLd_dlPSFiCx_9oh3Yyyb7YLRbcQeoLMW6lvwvpk; cf_clearance=W84sGC0HSBKhlJOue8VTEfuMdiYliWpRXxTGyKQ9yq8-1750643296-1.2.1.1-6dvBO2HTQnlmrD98qI9Wee8kzKNc4U6x14hNG80cps_e8MzKosiQSsN0kTofl2BYZbfM3_xZqio8prD9sNasEuvM1stc.Lb8Cqy5HQqPt.zaMiuvHx_2cMjYJ.Hsz1iMz.U7q_bKpP8D3DQNZchV1jOMQldA7ubJ5ErDmVeDRb_e5H9p4noh69mqIw0U9uTgESsvu9fR6P9E8HmCLBQyRbdcmP7uoYKHd4aRibmuNi0jVBIOtA1tA0SsEjttKx5SGSXvVVJEKGLnYXcNRtHEy23wkAN4N_qFsF0GiSbHgWncT2cXrSZNePexG7Tfi_6MIvmGFWlCfagwuXv6XpfOys6fNq9FYluN1DoT_Cgl6yE; moe_uuid=b62656c3-a975-43c2-b0ea-dc447f319f78; ysv2_cookie=""; ysgeln=Purito+SEOUL+brand; ysntjtk=ShQPynvGIiw47TKLDUp0SjIPa2yuzLekeQdAeqcjYQJ7jpH3SGeXTKx2jkz1wGRh; yslasturl=https%3A%2F%2Fwww.yesstyle.com%2Fen%2Fpurito-seoul-wonder-releaf-centella-bb-cream-6-colors-13-neutral-ivory%2Finfo.html%2Fpid.1131583092; _uetsid=21790ba04fd411f0a6e23b8dd50bd334; _uetvid=5ea098204ca511f0a75d8f55e5fbfec0';

      $htmlContent = $this->fetchHTMLForYesstyle($url, false, 3, $validCookies);

      $scriptPattern = '/application\/ld\+json">({.+})<\/script>/U';
      preg_match_all($scriptPattern, $htmlContent, $match);

      if (empty($match[1][1])) {
        Log::error('yesstyle json 없음');
        throw new \Exception("크롤링 실패");
      }

      $productObject = json_decode($match[1][1], true);

      if (empty($productObject)) {
        Log::error('yesstyle json 오류');
        throw new \Exception("크롤링 실패");
      }

      $productName = $productObject['offers'][0]['name'];
      $productPrice = $productObject['offers'][0]['price'];
      $productSalePrice = $productObject['offers'][0]['price'];
      $productImage = $productObject['image'];

      return CrawledProductDTO::create(
        name: $productName,
        salePrice: $productSalePrice,
        price: $productPrice,
        img: $productImage
      );
    } catch (\Exception $e) {
      throw new \Exception("크롤링 실패");
    }
  }


  private function getRandomUserAgent(): string
  {
    $userAgents = [
      "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36",
      "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36",
      "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/121.0",
      "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.2 Safari/605.1.15",
      "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36 Edg/120.0.0.0"
    ];

    return $userAgents[array_rand($userAgents)];
  }

  private function getRandomDelay(): void
  {
    // 0.1-0.3초 랜덤 딜레이
    usleep(rand(100000, 300000));
  }

  private function commonFetchHTML($url, $needCookie = false): bool|string
  {
    $this->getRandomDelay(); // 요청 간 딜레이

    $ch = curl_init();

    $headers = [
      "User-Agent: " . $this->getRandomUserAgent(),
      "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7",
      "Accept-Language: ko-KR,ko;q=0.9,en-US;q=0.8,en;q=0.7",
      "Accept-Encoding: gzip, deflate, br",
      "DNT: 1",
      "Connection: keep-alive",
      "Upgrade-Insecure-Requests: 1",
      "Sec-Fetch-Dest: document",
      "Sec-Fetch-Mode: navigate",
      "Sec-Fetch-Site: none",
      "Sec-Fetch-User: ?1",
      "Cache-Control: max-age=0"
    ];

    // 아마존의 경우 Referer 헤더 제거 (첫 방문처럼 보이게)
    if (str_contains($url, 'amazon')) {
      $headers[] = "Referer: https://www.google.com/";
    }

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
    curl_setopt($ch, CURLOPT_TIMEOUT, 45); // 타임아웃 증가
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
    curl_setopt($ch, CURLOPT_ENCODING, ''); // gzip 압축 해제
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_AUTOREFERER, true);

    // HTTP/2 사용
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_2_0);

    // TCP_NODELAY 활성화 (성능 향상)
    curl_setopt($ch, CURLOPT_TCP_NODELAY, true);

    if ($needCookie) {
      $cookieFile = $this->getCookieFilePath($url);
      curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
      curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
    }

    $html = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch)) {
      $error = curl_error($ch);
      curl_close($ch);
      error_log("cURL Error: " . $error);
      return false;
    }

    curl_close($ch);

    // HTTP 상태 코드 확인
    if ($httpCode === 503 || $httpCode === 403) {
      error_log("Amazon blocked request. HTTP Code: " . $httpCode);
      return false;
    }

    // CAPTCHA 체크
    if (str_contains($html, 'api/auth/captcha') || str_contains($html, 'Enter the characters you see below')) {
      error_log("CAPTCHA detected");
      return false;
    }

    return $html;
  }

  private function commonFetchHTMLPost($url, array $postData = []): bool|string
  {
    $ch = curl_init();

    $jsonData = json_encode($postData); // JSON 인코딩

    $headers = [
      "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36",
      "Referer: $url",
      "Accept: application/json", // JSON 응답을 기대
      "Accept-Language: en-US,en;q=0.9",
      "Connection: keep-alive",
      "Content-Type: application/json", // JSON 데이터 전송
      "Content-Length: " . strlen($jsonData) // 데이터 길이 설정
    ];

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_ENCODING, '');
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

    // POST 요청 설정
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData); // JSON 데이터 전송

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
      return false;
    }

    curl_close($ch);

    return $response;
  }

  private function fetchHTMLForYesstyle($url, $needCookie = true, $retryCount = 3, $customCookies = null)
  {
    for ($attempt = 1; $attempt <= $retryCount; $attempt++) {
      Log::error("Attempt {$attempt} for URL: {$url}");

      $this->getRandomDelay($attempt); // 재시도 시 더 긴 딜레이

      $ch = curl_init();

      // YesStyle용 특화 헤더
      $headers = [
        "User-Agent: " . $this->getRandomUserAgent(),
        "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7",
        "Accept-Language: en-US,en;q=0.9,ko;q=0.8",
        "Accept-Encoding: gzip, deflate", // br(Brotli) 제거
        "Cache-Control: max-age=0",
        "DNT: 1",
        "Connection: keep-alive",
        "Upgrade-Insecure-Requests: 1",
        "Sec-Fetch-Dest: document",
        "Sec-Fetch-Mode: navigate",
        "Sec-Fetch-Site: same-origin", // cross-site에서 same-origin으로 변경
        "Sec-Fetch-User: ?1",
        "Sec-CH-UA: \"Not_A Brand\";v=\"8\", \"Chromium\";v=\"120\", \"Google Chrome\";v=\"120\"",
        "Sec-CH-UA-Mobile: ?0",
        "Sec-CH-UA-Platform: \"Windows\"",
        "Referer: https://www.yesstyle.com/" // Google에서 YesStyle로 변경
      ];

      curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 60,
        CURLOPT_CONNECTTIMEOUT => 30,
        CURLOPT_ENCODING => '',
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_AUTOREFERER => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_TCP_NODELAY => true,
        CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4, // IPv4 강제
        CURLOPT_FRESH_CONNECT => false,
        CURLOPT_FORBID_REUSE => false,
      ]);

      // 쿠키 설정
      if ($needCookie || $customCookies) {
        if ($customCookies) {
          // 직접 쿠키 문자열 설정
          curl_setopt($ch, CURLOPT_COOKIE, $customCookies);
          Log::error("Using custom cookies");
        } else {
          $cookieFile = $this->getCookieFilePath($url);
          curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
          curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
        }
      }

      // TLS 설정 강화
      curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
      curl_setopt($ch, CURLOPT_SSL_CIPHER_LIST, 'ECDHE+AESGCM:ECDHE+CHACHA20:DHE+AESGCM:DHE+CHACHA20:!aNULL:!MD5:!DSS');

      $html = curl_exec($ch);
      $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
      $info = curl_getinfo($ch);

      if (curl_errno($ch)) {
        $error = curl_error($ch);
        curl_close($ch);
        Log::error("cURL Error on attempt {$attempt}: " . $error);
        continue;
      }

      curl_close($ch);

      // 성공적인 응답 체크
      if ($httpCode === 200 && !$this->isBlocked($html)) {
        Log::error("Success on attempt {$attempt}");
        return $html;
      }

      // Cloudflare Challenge 감지
      if ($this->isCloudflareChallenge($html)) {
        Log::error("Cloudflare challenge detected on attempt {$attempt}");

        // JavaScript Challenge 우회 시도
        if ($attempt < $retryCount) {
          $this->solveCloudflareChallengeJS($url);
        }
        continue;
      }

      Log::error("Failed attempt {$attempt}: HTTP {$httpCode}");
    }

    return false;

  }

  /**
   * 차단 감지 강화
   */
  private function isBlocked($html): bool
  {
    if (empty($html) || strlen($html) < 100) {
      return true;
    }

    $blockPatterns = [
      'Sorry, you have been blocked',
      'Access Denied',
      'Blocked',
      'bot detection',
      '403 Forbidden',
      'Too Many Requests',
      'Rate limit exceeded',
      'security check',
      'Please verify you are human'
    ];

    foreach ($blockPatterns as $pattern) {
      if (stripos($html, $pattern) !== false) {
        return true;
      }
    }

    return false;
  }

  /**
   * Cloudflare JS Challenge 우회 시도
   */
  private function solveCloudflareChallengeJS($url): void
  {
    // 간단한 JS Challenge 우회
    sleep(5); // Cloudflare 대기 시간 시뮬레이션

    // 추가 헤더로 재시도
    $cookieFile = $this->getCookieFilePath($url);
    if (file_exists($cookieFile)) {
      $cookies = file_get_contents($cookieFile);
      if (strpos($cookies, 'cf_clearance') !== false) {
        Log::error("Cloudflare clearance cookie found");
      }
    }
  }

  /**
   * Cloudflare Challenge 페이지 감지
   */
  private function isCloudflareChallenge($html): bool
  {
    $cfPatterns = [
      'Checking your browser before accessing',
      'DDoS protection by Cloudflare',
      'cf-browser-verification',
      'cf_challenge_response',
      'jschl_vc',
      'jschl_answer',
      '__cf_chl_jschl_tk__'
    ];

    foreach ($cfPatterns as $pattern) {
      if (stripos($html, $pattern) !== false) {
        return true;
      }
    }

    return false;
  }


  private function getSiteSetting(string $siteName)
  {
    $setting = DB::table('cms_crawling_site')->where('site_name', $siteName)->first();

    if (!$setting) {
      throw new \Error('잘못된 사이트 정보입니다.');
    }

    return $setting;
  }

  /**
   * 쿠키 파일 경로 생성
   */
  private function getCookieFilePath(string $url): string
  {
    $domain = parse_url($url, PHP_URL_HOST);
    $domain = str_replace(['www.', '.'], ['', '_'], $domain);
    $filename = $domain . '_cookies.txt';

    return $this->cookieDir . '/' . $filename;
  }

}
