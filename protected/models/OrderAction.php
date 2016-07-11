<?php

class OrderAction extends BaseModel {
  
    public function tableName() {
        return '{{order_action}}';
    }

    public function loadInit($params = array()) {
        
    }
    
    public function relations(){
        return array(
            'goods' => array(self::HAS_MANY, 'Goods', 'brand_id'),
        );
    }
    
    

}
