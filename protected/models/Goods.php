<?php
class Goods extends BaseModel {
    
    public function tableName() {
        return '{{goods}}';
    }

    public function loadInit($params = array()) {
        
    }
    
     public function relations(){
        return array(
            'ordergoods' => array(self::HAS_MANY, 'OrderGoods', 'goods_id'),
            'catInfo' => array(self::BELONGS_TO, 'Category', 'cat_id'),
            'suppliers' => array(self::BELONGS_TO,'Suppliers','suppliers_id'),
        );
    }

}


