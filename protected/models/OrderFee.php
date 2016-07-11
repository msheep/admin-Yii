<?php

class OrderFee extends BaseModel {
    static $feeCategory = array(
        1 => '支付宝手续费',
        2 => '快钱手续费'
    );
    
    public function tableName() {
        return 'lis_order_fee';
    }

    public function loadInit($params = array()) {
        
    }

    public function relations() {
        return array(
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
