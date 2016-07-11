<?php

class Order extends BaseModel {

    static $orderStatus = array(
        0 => '待确认',
        1 => '已确认',
        2 => '已取消',
        3 => '无效',
        4 => '退货',
        5 => '已分单',
        6 => '部分发货',
        100 => '待付款',
        101 => '待发货',
        102 => '已完成',
    );
    static $payStatus = array(
        0 => '未付款',
        1 => '付款中',
        2 => '已付款',
    );
    static $shipStatus = array(
        0 => '未发货',
        1 => '已发货',
        2 => '收货确认',
        3 => '配货中',
        4 => '已发货（部分商品）', //(部分商品)
        5 => '发货中', //(处理分单)
        6 => '已发货', //(部分商品)
        101 => '供应商发货',
        102 => '达到处理中心',
        103 => '处理中心发货',
        104 => '清关',
        105 => '国内派送',
    );

    public function tableName() {
        return '{{order_info}}';
    }

    public function loadInit($params = array()) {
        
    }

    public function relations() {
        return array(
            'ordergoods' => array(self::HAS_MANY, 'OrderGoods', 'order_id'),
            'userInfo' => array(self::BELONGS_TO, 'Users', 'user_id'),
            'buyInfo' => array(self::HAS_MANY, 'BuyOrderGoods', 'order_id'),
            'feeInfo' => array(self::HAS_MANY, 'OrderFee', 'order_id'),
            'shippingInfo' => array(self::HAS_MANY, 'ShippingAction', 'order_id', 'order' => 'time DESC', 'condition' => 'del_flag = 0'),
            //获取国家、省、市、县的名称                        
            'countryInfo' => array(self::BELONGS_TO , 'Region',  'country'), 
            'provinceInfo' => array(self::BELONGS_TO, 'Region',  'province'),
            'cityInfo' => array(self::BELONGS_TO, 'Region', 'city'),
            'districtInfo' => array(self::BELONGS_TO, 'Region', 'district'),
            /*统计数量*/
            'orderGoodsCount' => array(self::STAT, 'OrderGoods', 'order_id'),
            'buyOrderGoodsCount' => array(self::STAT, 'BuyOrderGoods', 'order_id', 'condition' => 'del_flag = 0'),
        );
    }

    /**
     * 设置标签
     */
    public function attributeLabels() {
        return array(
            'goods_amount' => '真实姓名',
            'tax' => '身份证号'
        );
    }
    
    /**
     * 获取订单状态
     * @param  
     * @return  float
     */
    public function getOrderStatus() {
        $status = array();
        if(isset(self::$orderStatus[$this->order_status])){
            $status[] = self::$orderStatus[$this->order_status];
        }
        if(isset(self::$payStatus[$this->pay_status])){
            $status[] = self::$payStatus[$this->pay_status];
        }
        if(isset(self::$shipStatus[$this->shipping_status])){
            $status[] = self::$shipStatus[$this->shipping_status];
        }
        return $status;
    }
    
    /**
     * 获取订单总价
     * @param  
     * @return  float
     */
    public function getTotalFee() {
        $totalFee = $this->goods_amount + $this->tax + $this->shipping_fee + $this->insure_fee + $this->pay_fee + $this->pack_fee + $this->card_fee - $this->discount;
        $totalFee = self::priceFormat($totalFee);
        return $totalFee;
    }

    /**
     * 获取应付款金额
     * @param  
     * @return  float
     */
    public function getShouldPay() {
        $pay = $this->goods_amount + $this->tax + $this->shipping_fee + $this->insure_fee + $this->pay_fee + $this->pack_fee + $this->card_fee - $this->money_paid - $this->surplus - $this->integral_money - $this->bonus - $this->discount;
        if ($pay < 0) {
            return 0;
        } else {
            $pay = self::priceFormat($pay);
            return $pay;
        }
    }

    /**
     * 格式化商品价格
     *
     * @access  public
     * @param   float   $price  商品价格
     * @return  string
     */
    public static function priceFormat($price, $format = 0, $change_price = true) {
        if ($price === '') {
            $price = 0;
        }
        if ($change_price && defined('ECS_ADMIN') === false) {
            switch ($format) {
                case 0:
                    $price = number_format($price, 2, '.', '');
                    break;
                case 1: // 保留不为 0 的尾数
                    $price = preg_replace('/(.*)(\\.)([0-9]*?)0+$/', '\1\2\3', number_format($price, 2, '.', ''));

                    if (substr($price, -1) == '.') {
                        $price = substr($price, 0, -1);
                    }
                    break;
                case 2: // 不四舍五入，保留1位
                    $price = substr(number_format($price, 2, '.', ''), 0, -1);
                    break;
                case 3: // 直接取整
                    $price = intval($price);
                    break;
                case 4: // 四舍五入，保留 1 位
                    $price = number_format($price, 1, '.', '');
                    break;
                case 5: // 先四舍五入，不保留小数
                    $price = round($price);
                    break;
            }
        } else {
            $price = number_format($price, 2, '.', '');
        }
        return $price;
    }

}
