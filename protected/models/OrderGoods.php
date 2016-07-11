<?php

class OrderGoods extends BaseModel {

    public function tableName() {
        return '{{order_goods}}';
    }

    public function loadInit($params = array()) {
        
    }

    public function relations() {
        return array(
            'goodsInfo' => array(self::BELONGS_TO, 'Goods', 'goods_id'),
            'orderInfo' => array(self::BELONGS_TO, 'Order', 'order_id'),
            'shippingInfo' => array(self::HAS_MANY, 'ShippingAction', 'rec_id', 'order' => 'time DESC', 'condition' => 'del_flag = 0'),
        );
    }
    
       /**
     * 判断订单是否采购
     * @param  
     * @return  int
     */
    public function getIfBuy(){
        //物流采购列表是否有记录
        $result = BuyOrderGoods::model()->find('goods_id = :goods_id AND order_id = :order_id AND del_flag = 0', array(':goods_id'=>$this->goods_id, ':order_id'=>$this->order_id));
        if(!empty($result)){
            return 1;
        }else{
            //ec采购列表是否有记录
            $result = DeliveryOrder::model()->find('order_id = :order_id', array(':order_id'=>$this->order_id));
            if(!empty($result)){
                return 1;
            }else{
                return 0;
            }
        }
    }
    
     /**
     * 采购信息
     * @param  
     * @return  float
     */
    public function getBuyInfo(){
        
    }

}
