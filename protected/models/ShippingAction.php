<?php

class ShippingAction extends BaseModel {

    public function tableName() {
        return 'lis_shipping_action';
    }

    public function loadInit($params = array()) {
        
    }

    public function relations() {
        return array(
            'orderGoodsInfo' => array(self::BELONGS_TO, 'OrderGoods', 'rec_id'),
            'orderInfo' => array(self::BELONGS_TO, 'Order', 'order_id'),
        );
    }
    
    public function beforeSave() {
        if (parent::beforeSave()) {
            $this->admin_user = Yii::app()->user->id;
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
