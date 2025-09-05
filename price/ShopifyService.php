<?php
class ShopifyService
{
  protected string $baseUrl;
  protected string $apiVersion;
  protected string $accessToken;

  public function __construct(string $apiVersion = null, string $accessToken = null)
  {
    $this->apiVersion = SHOPIFY_API_VERSION;
    $this->accessToken = SHOPIFY_ACCESS_TOKEN;

    $base_domain = SHOPIFY_SHOP_DOMAIN; // dbconfig.php에서 정의한 상수
    $this->baseUrl = "https://{$base_domain}/admin/api/{$this->apiVersion}/";
  }

  protected function request(string $method, string $endpoint, array $data = []): array
  {
    $url = $this->baseUrl . $endpoint;
    $ch = curl_init();

    switch (strtoupper($method)) {
      case 'POST':
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        break;
      case 'PUT':
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        break;
      case 'DELETE':
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        // DELETE 리퀘스트는 보통 바디를 포함하지 않지만, 필요에 따라 추가할 수 있습니다.
        // if ($data !== null) {
        //     curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        // }
        break;
      case 'GET':
        // GET 요청 데이터가 있을 경우에만 쿼리 파라미터를 추가합니다.
        if (!empty($data)) {
          $parsed_url = parse_url($url);
          $query_string = http_build_query($data);

          // URL에 이미 쿼리 문자열이 있는지 확인합니다.
          // 있다면 '&'로 연결하고, 없다면 '?'로 연결합니다.
          if (isset($parsed_url['query']) && $parsed_url['query'] !== '') {
            $url .= '&' . $query_string;
          } else {
            $url .= '?' . $query_string;
          }
        }
        break;
      default:
        // GET은 특별한 cURL 설정이 필요 없습니다.
        break;
    }

    // if ($method === 'GET' && !empty($data)) {
    //   $url .= '?' . http_build_query($data);
    // }

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
      "Content-Type: application/json",
      "X-Shopify-Access-Token: {$this->accessToken}",
      "X-Shopify-Api-Version: {$this->apiVersion}"
    ]);

    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);

    $response_raw  = curl_exec($ch);
    $curl_error = curl_error($ch);
    $curl_errno = curl_errno($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    // 통일된 반환 구조 초기화
    $result = [
      'data' => null,
      'headers' => [],
      'http_code' => $http_code,
      'error' => null,
      'shopify_api_call_limit' => null
    ];

    // cURL 실행 실패 시
    if ($response_raw === false) {
      $result['error'] = "cURL Error ({$curl_errno}): " . $curl_error;
      error_log($result['error']); // 로그 기록
      curl_close($ch);
      return $result; // 오류 정보와 함께 바로 반환
    }

    // 헤더와 바디 분리
    $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $headers_raw = substr($response_raw, 0, $header_size);
    $body = substr($response_raw, $header_size);

    // 헤더 파싱
    $header_lines = explode("\r\n", $headers_raw);
    foreach ($header_lines as $line) {
      $trimmed_line = trim($line);
      if (strpos($trimmed_line, ':') !== false) {
        list($key, $value) = explode(':', $trimmed_line, 2);
        $result['headers'][trim($key)] = trim($value);
      }
    }

    // X-Shopify-Shop-Api-Call-Limit 헤더 추출
    if (isset($result['headers']['x-shopify-shop-api-call-limit'])) {
      $result['shopify_api_call_limit'] = $result['headers']['x-shopify-shop-api-call-limit'];
      error_log("Shopify API Call Limit: " . $result['shopify_api_call_limit']); // 로그 기록
    }

    // JSON 응답 바디 디코딩
    $responseData = json_decode($body, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
      $result['error'] = "JSON Decode Error: " . json_last_error_msg() . " | Raw body: " . $body;
      error_log($result['error']); // 로그 기록
      // JSON 파싱 실패시 'data'는 null로 유지
    } else {
      $result['data'] = $responseData;
    }

    // HTTP 상태 코드가 400 이상일 경우 API 에러로 처리
    if ($http_code >= 400) {
      $api_error_message = ($responseData['errors'] ?? 'Unknown API Error');
      if (is_array($api_error_message)) { // 에러 메시지가 배열인 경우 문자열로 변환
        $api_error_message = json_encode($api_error_message);
      }
      $result['error'] = "Shopify API Error ({$http_code}): " . $api_error_message;
      error_log($result['error']); // 로그 기록
    }

    curl_close($ch);
    return $result; // 최종 결과 반환

    // if (in_array($method, ['POST', 'PUT', 'DELETE']) && !empty($data)) {
    //   curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    // }

    // $response = curl_exec($ch);
    // $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    // curl_close($ch);

    // if ($httpCode < 200 || $httpCode >= 300) {
    //   return ['error' => "HTTP Error $httpCode", 'response' => $response];
    // }

    // return json_decode($response, true);
  }

  public function graphqlRequest(string $query, array $variables = []): array
  {
    $url = $this->baseUrl . "graphql.json";

    $requestData = ['query' => $query];

    if (!empty($variables)) {
      $requestData['variables'] = $variables;
    }

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
      "Content-Type: application/json",
      "X-Shopify-Access-Token: {$this->accessToken}",
      "X-Shopify-Api-Version: {$this->apiVersion}"
    ]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode < 200 || $httpCode >= 300) {
      return ['error' => "HTTP Error $httpCode", 'response' => $response];
    }

    return json_decode($response, true);
  }

  /**
   * GET 요청 (조회)
   */
  public function get(string $endpoint, array $params = []): array
  {
    return $this->request('GET', $endpoint, $params);
  }

  public function post(string $endpoint, array $data): array
  {
    return $this->request('POST', $endpoint, $data);
  }

  public function put(string $endpoint, array $data): array
  {
    return $this->request('PUT', $endpoint, $data);
  }

  public function delete(string $endpoint): array
  {
    return $this->request('DELETE', $endpoint);
  }

  /**
   * 상품명으로 쇼피파이 상품을 검색합니다.
   * Admin REST API의 `products.json` 엔드포인트는 `title` 파라미터로 필터링 가능합니다.
   * @param string $title 검색할 상품명 (일치 또는 부분 일치)
   * @return array 검색된 상품 배열
   */
  public function searchProductsByTitle(string $title): array
  {
    // products.json 엔드포인트는 title 파라미터로 검색을 지원
    $response = $this->get('products.json', ['title' => $title]);

    if ($response) {
      return $response;
    }
    return [];
  }

  // /**
  //  * Mailgun을 통해 이메일 발송
  //  *
  //  * @param string $to 받는 사람 이메일
  //  * @param string $subject 제목
  //  * @param string $text 텍스트 내용 (선택)
  //  * @param string $html HTML 내용 (선택)
  //  * @param string $from 보내는 사람 이메일 (기본값 사용 가능)
  //  * @param array $attachments 첨부파일 배열 (선택)
  //  * @return array 발송 결과
  //  */
  // public function sendEmail(
  //   string $to,
  //   string $subject,
  //   string $text = null,
  //   string $html = null,
  //   string $from = null,
  //   string $replyTo = null,
  //   array  $attachments = []
  // ): array
  // {
  //   $url = "https://api.mailgun.net/v3/{$this->mailgunDomain}/messages";

  //   // 기본 발신자 설정
  //   $from = $from ?? env('MAIL_FROM_ADDRESS', 'noreply@' . $this->mailgunDomain);
  //   $replyTo = $replayTo ?? 'noreply@dodoskin.com';

  //   // 필수 데이터 검증
  //   if (!$text && !$html) {
  //     return ['error' => '텍스트 또는 HTML 내용이 필요합니다.'];
  //   }

  //   $postData = [
  //     'from' => $from,
  //     'to' => $to,
  //     'subject' => $subject,
  //   ];

  //   // 텍스트 내용 추가
  //   if ($text) {
  //     $postData['text'] = $text;
  //   }

  //   // HTML 내용 추가
  //   if ($html) {
  //     $postData['html'] = $html;
  //   }

  //   if ($replyTo) {
  //     $postData['h:Reply-To'] = $replyTo;
  //   }

  //   $ch = curl_init();
  //   curl_setopt($ch, CURLOPT_URL, $url);
  //   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  //   curl_setopt($ch, CURLOPT_POST, true);
  //   curl_setopt($ch, CURLOPT_USERPWD, "api:{$this->mailgunApiKey}");
  //   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  //   curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

  //   // 첨부파일이 있는 경우 multipart/form-data로 전송
  //   if (!empty($attachments)) {
  //     $postData['attachment'] = [];
  //     foreach ($attachments as $attachment) {
  //       if (is_array($attachment) && isset($attachment['path'], $attachment['name'])) {
  //         $postData['attachment'][] = new \CURLFile($attachment['path'], null, $attachment['name']);
  //       } elseif (is_string($attachment) && file_exists($attachment)) {
  //         $postData['attachment'][] = new \CURLFile($attachment);
  //       }
  //     }
  //     curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
  //   } else {
  //     // 일반 POST 데이터로 전송
  //     curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
  //   }

  //   $response = curl_exec($ch);
  //   $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  //   curl_close($ch);

  //   if ($httpCode < 200 || $httpCode >= 300) {
  //     return ['error' => "HTTP Error $httpCode", 'response' => $response];
  //   }

  //   return json_decode($response, true);
  // }

  // /**
  //  * 간단한 텍스트 이메일 발송
  //  */
  // public function sendTextEmail(string $to, string $subject, string $message, string $from = null): array
  // {
  //   return $this->sendEmail($to, $subject, $message, null, $from);
  // }

  // /**
  //  * HTML 이메일 발송
  //  */
  // public function sendHtmlEmail(string $to, string $subject, string $htmlContent, string $from = null): array
  // {
  //   return $this->sendEmail($to, $subject, null, $htmlContent, $from);
  // }

  // /**
  //  * 템플릿을 사용한 이메일 발송
  //  */
  // public function sendTemplateEmail(string $to, string $subject, string $template, array $variables = [], string $from = null): array
  // {
  //   $url = "https://api.mailgun.net/v3/{$this->mailgunDomain}/messages";

  //   $from = $from ?? env('MAIL_FROM_ADDRESS', 'noreply@' . $this->mailgunDomain);

  //   $postData = [
  //     'from' => $from,
  //     'to' => $to,
  //     'subject' => $subject,
  //     'template' => $template
  //   ];

  //   // 템플릿 변수 추가
  //   foreach ($variables as $key => $value) {
  //     $postData["v:{$key}"] = $value;
  //   }

  //   $ch = curl_init();
  //   curl_setopt($ch, CURLOPT_URL, $url);
  //   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  //   curl_setopt($ch, CURLOPT_POST, true);
  //   curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
  //   curl_setopt($ch, CURLOPT_USERPWD, "api:{$this->mailgunApiKey}");
  //   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  //   curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

  //   $response = curl_exec($ch);
  //   $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  //   curl_close($ch);

  //   if ($httpCode < 200 || $httpCode >= 300) {
  //     return ['error' => "HTTP Error $httpCode", 'response' => $response];
  //   }

  //   return json_decode($response, true);
  // }


  /**
   * 쿠폰 이메일 HTML 템플릿 생성
   */
  public function generateCouponEmailHtml($coupon, $recipientName = 'Customer')
  {
    $discountText = $coupon->discount_type === 'percentage'
      ? $coupon->discount_value * 100 . "% OFF"
      : "$" . number_format($coupon->discount_value) . " OFF";

    $minimumAmountText = $coupon->minimum_amount
      ? "Minimum purchase: $" . number_format($coupon->minimum_amount)
      : "No minimum purchase required";

    return "
    <!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>DODOSKIN Discount Coupon</title>
    </head>
    <body style='font-family: Arial, Helvetica, sans-serif; margin: 0; padding: 0; background-color: #f5f5f5;'>
        <div style='max-width: 600px; margin: 0 auto; background: white;'>
            <div style='background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center;'>
                <h1 style='margin: 0; font-size: 28px; font-weight: bold;'>🎁 DODOSKIN</h1>
                <p style='margin: 10px 0 0 0; font-size: 16px;'>Your Special Discount Coupon Has Arrived!</p>
            </div>

            <div style='padding: 40px 30px;'>
                <div style='font-size: 18px; color: #333; margin-bottom: 20px;'>
                    Hello, <strong>{$recipientName}</strong>!
                </div>

                <p style='font-size: 16px; line-height: 1.6; color: #333; margin-bottom: 30px;'>We've prepared a special discount just for you at DODOSKIN. Use the coupon below to enjoy great savings on your next purchase!</p>

                <div style='background: linear-gradient(135deg, #ffeaa7 0%, #fab1a0 100%); border-radius: 15px; padding: 30px; text-align: center; margin: 30px 0; box-shadow: 0 10px 30px rgba(0,0,0,0.1);'>
                    <div style='display: inline-block; background: #6c5ce7; color: white; padding: 5px 15px; border-radius: 20px; font-size: 12px; margin-bottom: 15px;'>{$coupon->segment_name} Tier</div>
                    <div style='font-size: 24px; font-weight: bold; color: #2d3436; margin-bottom: 15px;'>{$coupon->title}</div>
                    <div style='font-size: 32px; font-weight: bold; color: #e17055; background: white; padding: 15px 25px; border-radius: 10px; margin: 20px 0; letter-spacing: 3px; border: 3px dashed #e17055; display: inline-block;'>{$coupon->code}</div>
                    <div style='font-size: 20px; color: #2d3436; margin: 15px 0; font-weight: bold;'>{$discountText}</div>
                    <div style='font-size: 14px; color: #636e72; margin-top: 10px;'>{$minimumAmountText}</div>
                </div>

                <div style='text-align: center; margin: 30px 0;'>
                    <a href='https://www.dodoskin.com/' style='display: inline-block; background: linear-gradient(135deg, #00b894 0%, #00cec9 100%); color: white; padding: 15px 40px; text-decoration: none; border-radius: 25px; font-weight: bold; font-size: 16px; box-shadow: 0 5px 15px rgba(0,0,0,0.2);'>Shop Now</a>
                </div>

                <div style='margin-top: 30px; padding: 20px; background: #f8f9fa; border-radius: 10px; font-size: 14px; color: #636e72; border: 1px solid #e9ecef;'>
                    <h4 style='color: #2d3436; margin-top: 0; margin-bottom: 15px; font-size: 16px;'>📋 How to Use</h4>
                    <table style='width: 100%; border-collapse: collapse;'>
                        <tr>
                            <td style='padding: 5px 0; color: #636e72; font-size: 14px; line-height: 1.5;'>• Enter the coupon code at checkout</td>
                        </tr>
                        <tr>
                            <td style='padding: 5px 0; color: #636e72; font-size: 14px; line-height: 1.5;'>• This coupon can only be used once</td>
                        </tr>
                        <tr>
                            <td style='padding: 5px 0; color: #636e72; font-size: 14px; line-height: 1.5;'>• Can be combined with other discounts</td>
                        </tr>
                        <tr>
                            <td style='padding: 5px 0; color: #636e72; font-size: 14px; line-height: 1.5;'>• Please check the coupon expiration date</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div style='background: #2d3436; color: white; padding: 20px; text-align: center; font-size: 12px;'>
                <p style='margin: 0 0 10px 0;'>© 2025 DODOSKIN. All rights reserved.</p>
                <p style='margin: 0;'>If you have any questions, please contact our customer service.</p>
            </div>
        </div>
    </body>
    </html>";
  }
}
