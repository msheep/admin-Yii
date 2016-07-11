<?php

class DeliveryOrderGoods extends BaseModel {

    public function tableName() {
        return '{{delivery_goods}}';
    }

    public function loadInit($params = array()) {
        
    }

    public function relations() {
        return array(
            'deliveryOrder' => array(self::BELONGS_TO, 'DeliveryOrder', 'delivery_id'),
        );
    }
    
}
