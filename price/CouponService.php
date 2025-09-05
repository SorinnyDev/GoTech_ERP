<?php

namespace App\Services;

class CouponService
{
    private ShopifyService $shopifyService;

    public function __construct(ShopifyService $shopifyService)
    {
        $this->shopifyService = $shopifyService;
    }

    public function getShopifyService(): ShopifyService
    {
      return $this->shopifyService;
    }

    /**
     * BON_bronze 태그만 가진 고객들을 조회
     *
     * @param int $first 조회할 고객 수 (기본값: 50)
     * @param string|null $after 페이지네이션을 위한 커서
     * @return array
     */
    public function getBonBronzeCustomers(int $first = 50, string $after = null): array
    {
        $afterCursor = $after ? ", after: \"$after\"" : '';

        $query = "
      {
        customers(first: $first, query: \"tag:BON_bronze\"$afterCursor) {
          edges {
            node {
              id
              firstName
              lastName
              email
              phone
              tags
              createdAt
              updatedAt
              acceptsMarketing
              ordersCount
              totalSpent
            }
            cursor
          }
          pageInfo {
            hasNextPage
            hasPreviousPage
            startCursor
            endCursor
          }
        }
      }
    ";

        $response = $this->shopifyService->graphqlRequest($query);

        if (isset($response['error'])) {
            return $response;
        }

        // BON_bronze 태그만 있는 고객들을 필터링
        $filteredCustomers = [];

        if (isset($response['data']['customers']['edges'])) {
            foreach ($response['data']['customers']['edges'] as $edge) {
                $customer = $edge['node'];
                $tags = $customer['tags'] ?? [];

                // 태그가 정확히 ['BON_bronze']인 경우만 포함
                if (count($tags) === 1 && in_array('BON_bronze', $tags)) {
                    $filteredCustomers[] = [
                        'cursor' => $edge['cursor'],
                        'customer' => $customer
                    ];
                }
            }
        }

        return [
            'customers' => $filteredCustomers,
            'pageInfo' => $response['data']['customers']['pageInfo'] ?? null,
            'totalFound' => count($filteredCustomers)
        ];
    }

    /**
     * BON_bronze 태그만 가진 고객들을 모두 조회 (페이지네이션 포함)
     *
     * @return array
     */
    public function getAllBonBronzeCustomers(): array
    {
        $allCustomers = [];
        $hasNextPage = true;
        $after = null;

        while ($hasNextPage) {
            $result = $this->getBonBronzeCustomers(50, $after);

            if (isset($result['error'])) {
                return $result;
            }

            $allCustomers = array_merge($allCustomers, $result['customers']);

            $pageInfo = $result['pageInfo'];
            $hasNextPage = $pageInfo['hasNextPage'] ?? false;
            $after = $pageInfo['endCursor'] ?? null;

            // 무한루프 방지를 위한 안전장치
            if (count($allCustomers) > 10000) {
                break;
            }
        }

        return [
            'customers' => $allCustomers,
            'total' => count($allCustomers)
        ];
    }

    /**
     * 특정 고객의 태그 조회
     *
     * @param string $customerId 고객 ID (gid://shopify/Customer/123456789 형식)
     * @return array
     */
    public function getCustomerTags(string $customerId): array
    {
        $query = "
      {
        customer(id: \"$customerId\") {
          id
          firstName
          lastName
          email
          tags
        }
      }
    ";

        return $this->shopifyService->graphqlRequest($query);
    }

    /**
     * BON_bronze 태그만 가진 고객 수 조회
     *
     * @return array
     */
    public function getBonBronzeCustomersCount(): array
    {
        $query = "
      {
        customers(first: 1, query: \"tag:BON_bronze\") {
          edges {
            node {
              id
            }
          }
        }
      }
    ";

        $response = $this->shopifyService->graphqlRequest($query);

        if (isset($response['error'])) {
            return $response;
        }

        // 실제 카운트를 위해서는 모든 고객을 조회해야 함
        $result = $this->getAllBonBronzeCustomers();

        return [
            'count' => $result['total'] ?? 0
        ];
    }

    /**
     * 세그먼트별 할인 쿠폰 발급
     *
     * @param string $title 할인 제목
     * @param string|null $prefix 할인 코드
     * @param string $segmentId 고객 세그먼트 ID (gid://shopify/Segment/123456)
     * @param array $discountValue 할인값 ['type' => 'percentage|fixed', 'value' => 0.1 또는 1000]
     * @param float|null $minimumAmount 최소 구매 금액 (null이면 제한 없음)
     * @param bool $oncePerCustomer 고객당 1회 사용 제한 (기본: true)
     * @param array $combinesWith 조합 가능한 할인 ['product' => true, 'order' => true, 'shipping' => true]
     * @param string|null $startsAt 시작일 (ISO 8601 형식, null이면 즉시)
     * @param string|null $endsAt 종료일 (ISO 8601 형식, null이면 1개월 후)
     * @return array
     */
    public function createSegmentDiscount(
        string  $title,
        ?string  $prefix,
        string  $segmentId,
        string $code,
        array   $discountValue = ['type' => 'percentage', 'value' => 0.1],
        ?float  $minimumAmount = null,
        bool    $oncePerCustomer = true,
        array   $combinesWith = ['product' => true, 'order' => true, 'shipping' => true],
        ?string $startsAt = null,
        ?string $endsAt = null
    ): array
    {
        // 기본값 설정
        $startsAt = $startsAt ?? date('c');

        // 할인값 구성
        $customerGetsValue = $this->buildDiscountValue($discountValue);

        // 기본 할인 데이터 구성
        $discountData = [
            'title' => $title,
            'code' => $code,
            'startsAt' => $startsAt,
            'endsAt' => $endsAt,
            'customerSelection' => [
                'customerSegments' => [
                    'add' => [$segmentId]
                ]
            ],
            'customerGets' => [
                'value' => $customerGetsValue,
                'items' => ['all' => true]
            ],
            'appliesOncePerCustomer' => $oncePerCustomer,
            'combinesWith' => [
                'orderDiscounts' => $combinesWith['order'] ?? true,
                'productDiscounts' => $combinesWith['product'] ?? true,
                'shippingDiscounts' => $combinesWith['shipping'] ?? true
            ]
        ];

        // 최소 구매 조건 설정
        if ($minimumAmount !== null) {
            $discountData['minimumRequirement'] = [
                'subtotal' => [
                    'greaterThanOrEqualToSubtotal' => (string)$minimumAmount
                ]
            ];
        }

        return $this->createDiscount($discountData);
    }

    /**
     * 할인값 구성
     *
     * @param array $discountValue
     * @return array
     */
    private function buildDiscountValue(array $discountValue): array
    {
        $type = $discountValue['type'] ?? 'percentage';
        $value = $discountValue['value'] ?? 0.1;

        if ($type === 'percentage') {
            return ['percentage' => $value];
        } elseif ($type === 'fixed') {
            return [
                'discountAmount' => [
                    'amount' => $value,
                    'appliesOnEachItem' => false
                ]
            ];
        }

        throw new \InvalidArgumentException('할인 타입은 percentage 또는 fixed여야 합니다.');
    }

    /**
     * GraphQL로 할인 코드 생성
     *
     * @param array $discountData
     * @return array
     */
    private function createDiscount(array $discountData): array
    {
        $query = "
            mutation discountCodeBasicCreate(\$basicCodeDiscount: DiscountCodeBasicInput!) {
                discountCodeBasicCreate(basicCodeDiscount: \$basicCodeDiscount) {
                    codeDiscountNode {
                        id
                        codeDiscount {
                            ... on DiscountCodeBasic {
                                title
                                summary
                                codes(first: 1) {
                                    edges {
                                        node {
                                            code
                                        }
                                    }
                                }
                                startsAt
                                endsAt
                                status
                                customerSelection {
                                    ... on DiscountCustomerSegments {
                                        segments {
                                            id
                                            name
                                        }
                                    }
                                }
                                customerGets {
                                    value {
                                        ... on DiscountPercentage {
                                            percentage
                                        }
                                        ... on DiscountAmount {
                                            amount {
                                                amount
                                            }
                                        }
                                    }
                                }
                                appliesOncePerCustomer
                                combinesWith {
                                    orderDiscounts
                                    productDiscounts
                                    shippingDiscounts
                                }
                                minimumRequirement {
                                    ... on DiscountMinimumSubtotal {
                                        greaterThanOrEqualToSubtotal {
                                            amount
                                        }
                                    }
                                }
                            }
                        }
                    }
                    userErrors {
                        field
                        message
                    }
                }
            }
        ";

        return $this->shopifyService->graphqlRequest($query, ['basicCodeDiscount' => $discountData]);
    }

    /**
     * 무작위 쿠폰 코드 생성
     *
     * @param string|null $prefix 접두사 (선택)
     * @return string
     */
    public function generateCouponCode(?string $prefix = null): string
    {
        // 기본 접두사 설정
        $prefix = $prefix ?? 'DODO';

        // 8자리 무작위 문자열 생성 (대문자 + 숫자)
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $randomString = '';

        for ($i = 0; $i < 8; $i++) {
            $randomString .= $characters[random_int(0, strlen($characters) - 1)];
        }

        // 현재 시간을 기반으로 한 추가 무작위성
        $timestamp = substr(str_replace('.', '', microtime(true)), -4);

        return $prefix . $randomString . $timestamp;
    }


}
