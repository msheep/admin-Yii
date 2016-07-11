<?php

class Suppliers extends BaseModel {

    public function tableName() {
        return '{{suppliers}}';
    }

    public function loadInit($params = array()) {
        
    }

    public function relations() {
        return array(
            'goods' => array(self::HAS_MANY, 'Goods', 'suppliers_id'),
        );
    }

}
