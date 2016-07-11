<?php

class BuyOrder extends BaseModel {

    public function tableName() {
        return 'lis_buy_order';
    }

    public function loadInit($params = array()) {
        
    }

    public function beforeSave() {
        if (parent::beforeSave()) {
            $this->admin_user = Yii::app()->user->id;
            if ($this->isNewRecord) {
                $this->add_time = date('Y-m-d H:i:s');
                if(empty($this->exchange_tax)){
                    $this->exchange_tax = Yii::app()->params['exchangeTax'];
                }
            } else {
                $this->update_time = date('Y-m-d H:i:s');
            }
            return true;
        } else {
            return false;
        }
    }

}
