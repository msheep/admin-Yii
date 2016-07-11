<?php

class Brand extends BaseModel {
  
    public function tableName() {
        return '{{brand}}';
    }

    public function loadInit($params = array()) {
        
    }
    
    public function relations(){
        return array(
            'goods' => array(self::HAS_MANY, 'Goods', 'brand_id'),
        );
    }

}
