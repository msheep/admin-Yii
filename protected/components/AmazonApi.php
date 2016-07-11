<?php

Class AmazonApi {

    const AWS_API_KEY = 'AKIAJTAFGBGWUNZHWBOQ';
    const AWS_API_SECRET_KEY = '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';
    const AWS_ASSOCIATE_TAG = 'haitao';
    const BAIDU_API_KEY = 'k3qbYFU7oqgBICpxEsqCM8Vc';
    const BAIDU_API_SECRET_KEY = 'rq5QOm5IUPnoOewCZZTG8dxPOzi7jsZi';

    static $translateUrl = 'http://openapi.baidu.com/public/2.0/bmt/translate';
    static $host = 'webservices.amazon.com';
    static $hostWhole = 'http://webservices.amazon.com/onca/xml?';
    static $service = 'AWSECommerceService';
    static $version = '2011-08-01';
    static $useragent = 'AmazonECS';
    static $connecttimeout = 30;
    static $timeout = 30;
    static $ssl_verifypeer = FALSE;

    public static function GetInfo($param, $overseaUrl = '', $function = 'ItemLookup') {
        $result = array();
        $postfields = array();
        $postfields['AWSAccessKeyId'] = 'AWSAccessKeyId=' . self::AWS_API_KEY;
        $postfields['AssociateTag'] = 'AssociateTag=' . self::AWS_ASSOCIATE_TAG;
        $postfields['Operation'] = 'Operation=' . $function;
        $postfields['Service'] = 'Service=' . self::$service;
        $postfields['Version'] = 'Version=' . self::$version;
        switch ($function) {
            //查找某个产品详情
            case 'ItemLookup':
                if (empty($param['ItemId'])) {
                    $result['success'] = false;
                } else {
                    $condition = array('All', 'New', 'Used', 'Refurbished', 'Collectible');
                    $postfields['Condition'] = !empty($param['Condition']) && in_array($param['Condition'], $condition) ? 'Condition=' . $param['Condition'] : 'Condition=All';

                    $idType = array('ASIN', 'ISBN', 'UPC', 'EAN');
                    $postfields['IdType'] = !empty($param['IdType']) && in_array($param['IdType'], $idType) ? 'IdType=' . $param['IdType'] : 'IdType=ASIN';

                    $postfields['ItemId'] = 'ItemId=' . $param['ItemId'];
                    $postfields['MerchantId'] = 'MerchantId=All';

                    $responseGroup = array('Images', 'ItemAttributes', 'Offers', 'Accessories', 'AlternateVersions', 'BrowseNodes',
                        'ItemAttributes', 'EditorialReview', 'BrowseNodes', 'EditorialReview', 'OfferFull', 'OfferSummary', 'Reviews',
                        'SalesRank', 'Similarities', 'Tracks', 'VariationImages', 'VariationMatrix', 'VariationSummary', 'Variations', 'ItemIds');
                    $group = array();
                    if (!empty($param['ResponseGroup'])) {
                        foreach ($param['ResponseGroup'] as $key => $val) {
                            if (in_array($val, $responseGroup)) {
                                $group[] = $val;
                            }
                        }
                    }
                    $postfields['ResponseGroup'] = !empty($group) ? implode(',', $group) : implode(',', $responseGroup);
                    //URL encode the request's comma (,) and colon (:) characters
                    $postfields['ResponseGroup'] = 'ResponseGroup=' . rawurlencode($postfields['ResponseGroup']);
                }
                break;
        }

        //获取xml信息
        $response = AmazonApi::amazonHttp($postfields);

        if (!empty($response->Items->Request->Errors)) {
            return array();
            die();
        }
        $goodInfo = array();
        //判断是否存在ParentASIN
        if (!empty($response->Items->Item->ParentASIN)) {
            //判断是否当前ASIN是否为ParentASIN，非ParentASIN则再次请求
            if (trim($response->Items->Item->ParentASIN) != trim($response->Items->Item->ASIN)) {
                $postfields['ItemId'] = 'ItemId=' . $response->Items->Item->ParentASIN;
                //防止API報錯
                $newResponse = AmazonApi::amazonHttp($postfields);
                if ($newResponse && empty($newResponse->Items->Request->Errors)) {
                    $response = $newResponse;
                    $goodInfo['goods_asin'] = (string) $response->Items->Item->ParentASIN;
                    $goodInfo['goods_sn'] = 'US' . date('md') . '00AMAZON' . (string) $response->Items->Item->ParentASIN;
                } else {
                    $result = AmazonApi::delWithParam($response->Items->Item);
                    $goodInfo['good'][0] = $result;
                    $goodInfo['goods_asin'] = (string) $response->Items->Item->ASIN;
                    $goodInfo['goods_sn'] = 'US' . date('md') . '00AMAZON' . (string) $response->Items->Item->ASIN;
                }
            }
            if (!empty($response->Items->Item->Variations)) {
                //商品分类属性
                foreach ($response->Items->Item->Variations->Item as $key => $val) {
                    $goodInfo['good'][] = AmazonApi::delWithParam($val);
                }
                $goodInfo['goods_asin'] = (string) $response->Items->Item->ParentASIN;
                $goodInfo['goods_sn'] = 'US' . date('md') . '00AMAZON' . (string) $response->Items->Item->ParentASIN;
            }
        } else {
            //不存在即不存在属性
            $result = AmazonApi::delWithParam($response->Items->Item);
            $goodInfo['good'][0] = $result;
            $goodInfo['goods_asin'] = (string) $response->Items->Item->ASIN;
            $goodInfo['goods_sn'] = 'US' . date('md') . '00AMAZON' . (string) $response->Items->Item->ASIN;
        }
        //供应商
        $goodInfo['suppliers_id'] = 3; //amazon(美国)供应商ID
        //类目
        $goodInfo['cat_id'] = 10; //暂时为用户提交
        //产品名称
        $goodInfo['goods_name'] = (string)($response->Items->Item->ItemAttributes->Title); 
        //shipping weight
        $goodInfo['goods_weight'] = round($response->Items->Item->ItemAttributes->PackageDimensions->Weight / 100, 1);

        //获取页面价格
        if (!empty($overseaUrl)) {
            $content = AmazonApi::http($overseaUrl);
            if (preg_match('/(("actualPriceValue"><b class="priceLarge">)|("priceblock_ourprice" class="a-size-medium a-color-price">))\$(.*?)(\<| \-)/', $content, $price)) {
                $goodInfo['price'] = round($price[4], 2);
            }
        }
        //获取最低价
        if(!empty($response->Items->Item->VariationSummary->LowestPrice->Amount)){
            $goodInfo['min_price'] = (string)$response->Items->Item->VariationSummary->LowestPrice->Amount;
        }
        return $goodInfo;
    }

    public static function getShopPrice($url) {
        $content = AmazonApi::http($url);
        $price = 0;
        if (preg_match_all("/<span id=\"priceblock_ourprice\"(.*?)>(.*?)<\/span>/", $content, $salePrice)) {
            $price = $salePrice[2][0];
        }
        return $price;
    }

    /**
     * lanuage translate
     * 调用方法：AmazonApi::delWithParam($goodInfo['goods_name'])
     *
     * @return json
     */
    public static function delWithParam($response) {
        $goodInfo = array();
        $goodInfo['title'] = (string) $response->ItemAttributes->Title;
        //产品brand
        if (!empty($response->ItemAttributes->Brand)) {
            $brandName = trim($response->ItemAttributes->Brand);
            $haveBrand = Brand::model()->find('brand_name LIKE :brand_name', array(':brand_name' => "%$brandName%"));
            if (!isset($haveBrand->brand_id)) {
                $brandModel = new Brand();
                $brandModel->brand_name = $response->ItemAttributes->Brand;
                if ($brandModel->save()) {
                    $goodInfo['brand_id'] = $brandModel->brand_id;
                    $goodInfo['brand_desc'] = '';
                }
            } else {
                $goodInfo['brand_id'] = $haveBrand->brand_id;
                $goodInfo['brand_desc'] = $haveBrand->brand_desc;
            }
        }
        //产品图片
        if (!empty($response->MediumImage)) {
            $goodInfo['goods_thumb'] = (string) ($response->MediumImage->URL);
        }
        if (!empty($response->LargeImage)) {
            $goodInfo['original_img'] = (string) ($response->LargeImage->URL);
            $goodInfo['goods_img'] = (string) ($response->LargeImage->URL);
        }
        if (!empty($response->ImageSets)) {
            $goodInfo['image'] = array();
            $imageSet = (array) $response->ImageSets;
            if (isset($imageSet['ImageSet'][0])) {
                foreach ($imageSet['ImageSet'] as $key => $val) {
                    if (!empty($val->LargeImage)) {
                        $LargeImage = (array) ($val->LargeImage);
                        $goodInfo['image'][] = $LargeImage['URL'];
                    }
                }
            } else {
                $goodInfo['image'][] = $response->ImageSets->ImageSet->LargeImage->URL;
            }
        }
        $goodInfo['image'] = array_unique($goodInfo['image']);

        //产品属性
        $goodInfo['attr'] = array();
        if (!empty($response->VariationAttributes->VariationAttribute)) {
            $attr = (array) $response->VariationAttributes;
            foreach ($attr['VariationAttribute'] as $key => $val) {
                if (count($val) > 1) {
                    array_push($goodInfo['attr'], (array) $val);
                } else {
                    $goodInfo['attr'][0][$key] = (string) $val;
                }
            }
        }
        //description
        $goodInfo['feature'] = array();
        if (!empty($response->ItemAttributes->Feature)) {
            $itemAttributes = (array) $response->ItemAttributes;
            $goodInfo['feature'] = $itemAttributes['Feature'];
        }
        if (!empty($response->EditorialReviews->EditorialReview)) {
            $goodInfo['review'] = (array) $response->EditorialReviews->EditorialReview;
        }
        //海外原价
        $goodInfo['price'] = array();
        if (!empty($response->ItemAttributes->ListPrice)) {
            $goodInfo['price'] = (array) $response->ItemAttributes->ListPrice;
        }
        //海外最低价
        $goodInfo['min_price'] = array();
        if (!empty($response->Offers->Offer->OfferListing->Price)) {
            $goodInfo['min_price'] = (array) $response->Offers->Offer->OfferListing->Price;
        }
        if (empty($goodInfo['price'])) {
            $goodInfo['price'] = $goodInfo['min_price'];
        }
        return $goodInfo;
    }

    /**
     * lanuage translate
     * 调用方法：AmazonApi::translate($goodInfo['goods_name'])
     *
     * @return json
     */
    public static function translate($content, $from = 'en', $to = 'zh') {
        $params = array();
        if (strlen(trim($content)) > 0) {
            $url = self::$translateUrl . '?client_id=' . self::BAIDU_API_KEY . '&from=' . $from . '&to=' . $to . '&q=' . urlencode($content);
            $result = json_decode(AmazonApi::http($url), true);
            if (!empty($result['trans_result'])) {
                return $result['trans_result'];
            } else {
                return array();
            }
        } else {
            return array();
        }
    }

    /**
     * amazon http
     * 调用方法：AmazonApi::amazonHttp($param)
     *
     * @return object
     */
    public static function amazonHttp($param) {
        $timeStamp = AmazonApi::getTimestamp();
        //URL encode the request's comma (,) and colon (:) characters
        $param['Timestamp'] = 'Timestamp=' . rawurlencode($timeStamp);

        //根据键值字节排序（升序）
        ksort($param);

        $baseStr = "GET\n" . self::$host . "\n/onca/xml\n";
        //unsigned字符串
        $postStr = $baseStr . str_replace("%7E", "~", implode('&', $param));
        //signed
        $signature = AmazonApi::buildSignature($postStr);
        $param['Signature'] = 'Signature=' . str_replace("%7E", "~", rawurlencode($signature));

        //获取xml信息
        $url = self::$hostWhole . implode('&', $param);
        $response = AmazonApi::http($url);
        $response = simplexml_load_string($response);
        return $response;
    }

    /**
     * Make an HTTP request
     *
     * @return string API results
     * @ignore
     */
    public static function http($url, $params = array(), $postType = false) {
        /* Curl settings */
        $ci = curl_init();
        curl_setopt($ci, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
        curl_setopt($ci, CURLOPT_URL, $url);
        curl_setopt($ci, CURLOPT_USERAGENT, self::$useragent);
        curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, self::$connecttimeout);
        curl_setopt($ci, CURLOPT_TIMEOUT, self::$timeout);
        curl_setopt($ci, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ci, CURLOPT_ENCODING, "");
        curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, self::$ssl_verifypeer);
        curl_setopt($ci, CURLOPT_SSL_VERIFYHOST, 1);
        if ($postType) {
            curl_setopt($ci, CURLOPT_POST, TRUE);
            curl_setopt($ci, CURLOPT_POSTFIELDS, $params);
        }
        curl_setopt($ci, CURLINFO_HEADER_OUT, TRUE);
        $response = curl_exec($ci);
        curl_close($ci);
        return ($response);
    }

    /**
     * provides current gm date
     *
     * primary needed for the signature
     *
     * @return string
     */
    public static function getTimestamp() {
        return gmdate('Y-m-d\TH:i:s\Z'); //ISO8601 format gmdate(DATE_ISO8601)
    }

    /**
     * provides the signature
     *
     * @return string
     */
    public static function buildSignature($request) {
        return base64_encode(hash_hmac("sha256", $request, self::AWS_API_SECRET_KEY, true));
    }

}

?>