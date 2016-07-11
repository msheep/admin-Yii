<?php

class BuyOrderGoods extends BaseModel {

    public function tableName() {
        return 'lis_buy_goods';
    }

    public function loadInit($params = array()) {
        
    }

    public function relations() {
        return array(
            'orderInfo' => array(self::BELONGS_TO, 'BuyOrder', 'order_number'),
        );
    }
    
    public function beforeSave() {
        if (parent::beforeSave()) {
            if ($this->isNewRecord) {
                $this->add_time = date('Y-m-d H:i:s');
            } else {
                $this->update_time = date('Y-m-d H:i:s');
            }
            return true;
        } else {
            return false;
        }
    }
    
   

}
