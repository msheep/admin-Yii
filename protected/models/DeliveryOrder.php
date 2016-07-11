<?php

class DeliveryOrder extends BaseModel {

    public function tableName() {
        return '{{delivery_order}}';
    }

    public function loadInit($params = array()) {
        
    }
    
    public function relations() {
        return array(
            'deliveryGoods' => array(self::HAS_MANY, 'DeliveryOrderGoods', 'delivery_id'),
        );
    }

  
}
