<?php

class AmazonController extends Controller {

    public function actionGetProduct() {
        header("Access-Control-Allow-Origin: http://www.168haitao.com");
        $url = $_SERVER['REQUEST_URI'];
        if (preg_match('/\/(dp|product)\/(.*?)(\/|\?)/i', $url, $itemId)) {
            $param['ItemId'] = trim($itemId[2]);
        } else {
            echo "该链接无法自动抓取，请您检查您的链接是否正确";
            Yii::app()->end();
        }
        $overseaUrl = '';
        if (preg_match('/www\.amazon\.com(.*?)$/', $url, $overseaUrlArr)) {
            $overseaUrl = 'http://' . $overseaUrlArr[0];
        }
        //校验产品是否存在
        //$haveUrlGoods = Goods::model()->find('oversea_url = :oversea_url', array(':oversea_url' => $overseaUrl));
        if (empty($haveUrlGoods)) {
            $result = AmazonApi::GetInfo($param, $overseaUrl);
            if (!empty($result)) {
                $return = array();
                $asin = trim($result['goods_asin']);
                $haveAsinGoods = Goods::model()->find('goods_sn LIKE :goods_sn', array(':goods_sn' => "%$asin"));
                if (!empty($haveAsinGoods)) {
                    $goodsModel = Goods::model()->findByPk($haveAsinGoods->goods_id);
                } else {
                    $goodsModel = new Goods();
                }
                foreach ($result as $key => $val) {
                    if (in_array($key, $goodsModel->attributeNames())) {
                        $goodsModel->$key = $val;
                    }
                }
                foreach ($result['good'][0] as $key => $val) {
                    if (in_array($key, $goodsModel->attributeNames())) {
                        $goodsModel->$key = $val;
                    }
                }
                //海外链接
                if (preg_match('/http(.*?)\.com(.*?)$/i', $url, $overseaUrl)) {
                    $goodsModel->oversea_url = $overseaUrl[0];
                }

                //过滤重复图片---goods_img
                $showImage = array();
                foreach ($result['good'] as $key => $val) {
                    $showImage[$key] = $val['goods_img'];
                }
                $showImage = array_unique($showImage);
                //保存图片
                if (!empty($showImage)) {
                    foreach ($showImage as $key => $val) {
                        $haveShowImage = GoodsGallery::model()->find('goods_id = :goods_id AND img_url = :img_url', array(':goods_id' => $goodsModel->goods_id, ':img_url' => $val));
                        if (empty($haveShowImage)) {
                            $goodsGallery = new GoodsGallery();
                            $goodsGallery->goods_id = $goodsModel->goods_id;
                            $goodsGallery->img_url = $val;
                            $goodsGallery->save();
                        }
                    }
                }
                //产品描述
                $desc = '';
                foreach ($result['good'] as $key => $val) {
                    $desc .= '<div>';
                    if (!empty($val['review'])) {
                        $desc .= '<div>';
                        $desc .= '<p class="a-size-large">' . $val['review']['Source'] . '</p>';
                        $desc .= '<div class="a-vertical a-spacing-none">' . $val['review']['Content'] . '</div>';
                        $desc .= '</div>';
                    } else {
                        $feature = '';
                        if (!empty($val['feature'])) {
                            if (is_array($val['feature'])) {
                                foreach ($val['feature'] as $k => $v) {
                                    $feature .= '<li>' . $v . '</li>';
                                }
                            } else {
                                $feature .= '<p>' . $val['feature'] . '</p>';
                            }
                        } else {
                            if (!empty($showImage[$key])) {
                                $desc .= '<div class="a-image"><img src="' . $showImage[$key] . '"></div>';
                            }
                        }
                        if (!strpos($desc, $feature)) {
                            if ($key == 0) {
                                $desc .= '<p class="a-size-large">' . $val['title'] . '</p>';
                            } else {
                                $desc .= '<p class="a-size-large a-border">' . $val['title'] . '</p>';
                            }

                            $desc .= '<ul class="a-vertical a-spacing-none">' . $feature . '</ul>';
                        }
                        if (!empty($val['image'])) {
                            foreach ($val['image'] as $k => $v) {
                                if (!strpos($desc, $v)) {
                                    $desc .= '<div class="a-image"><img src="' . $v . '"></div>';
                                }
                            }
                        }
                    }
                    $desc .= '</div>';
                }

                $goodsModel->goods_desc = $desc;

                //库存
                $goodsModel->goods_number = 99;

                //价格
                $goodsModel->market_price = $result['good'][0]['price']['Amount'] / 100;
                if (isset($result['price'])) {
                    $goodsModel->oversea_price = $result['price'];
                } else {
                    $goodsModel->oversea_price = $result['good'][0]['min_price']['Amount'] / 100;
                }
                $goodsModel->new_shop_price = $goodsModel->oversea_price * Yii::app()->params['exchangeTax'];
                $goodsModel->shop_price = $goodsModel->new_shop_price * 1.1;

                if ($goodsModel->save()) {
                    foreach ($result['good'] as $key => $val) {
                        //保存attr
                        if (!empty($val['attr'])) {
                            $attrName = array();
                            $attrValue = array();
                            foreach ($val['attr'] as $k => $v) {
                                if (!empty($v['Name']) && !empty($v['Value'])) {
                                    $attrName[] = $v['Name'];
                                    $attrValue[] = $v['Value'];
                                }
                            }
                            $attrNameStr = implode('/', $attrName);
                            $attrValueStr = implode('/', $attrValue);
                            $attrId = 0;
                            //保存属性名称
                            $haveAttr = Attribute::model()->find('attr_name = :attr_name', array(':attr_name' => $attrNameStr));
                            if (empty($haveAttr)) {
                                $attributeModel = new Attribute();
                                $attributeModel->attr_name = $attrNameStr;
                                $attributeModel->attr_input_type = 0;
                                $attributeModel->attr_type = 1;
                                $attributeModel->cat_id = 10;
                                if ($attributeModel->save()) {
                                    $attrId = $attributeModel->attr_id;
                                }
                            } else {
                                $attrId = $haveAttr->attr_id;
                            }
                            //保存属性值
                            if ($attrId > 0) {
                                $haveGoodsAttr = GoodsAttr::model()->find('goods_id = :goods_id AND attr_id = :attr_id AND attr_value = :attr_value', array(':goods_id' => $goodsModel->goods_id, ':attr_id' => $attrId, ':attr_value' => $attrValueStr));
                                if (!empty($haveGoodsAttr)) {
                                    $goodsAttr = GoodsAttr::model()->findByPk($haveGoodsAttr->goods_attr_id);
                                } else {
                                    $goodsAttr = new GoodsAttr();
                                }
                                $goodsAttr->goods_id = $goodsModel->goods_id;
                                $goodsAttr->attr_id = $attrId;
                                $goodsAttr->attr_value = $attrValueStr;
                                $goodsAttr->attr_price = (($val['min_price']['Amount'] - $result['min_price']) / 100) * Yii::app()->params['exchangeTax'] * 1.1;
                                $goodsAttr->save();
                            }
                        }
                        $return['success'] = true;
                        $return['msg'] = $goodsModel->goods_id;
                    }
                } else {
                    $return['success'] = false;
                    $return['msg'] = $goodsModel->goods_id;
                }
            } else {
                $return['success'] = false;
                $return['msg'] = "无法抓取该商品";
            }
        } else {
            $return['success'] = true;
            $return['msg'] = $haveUrlGoods->goods_id;
        }
        echo json_encode($return);
    }

}
