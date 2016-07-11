<?php

class Region extends BaseModel {

    public function tableName() {
        return '{{region}}';
    }

    public function loadInit($params = array()) {
        
    }

    public function relations() {
        return array(           
        );
    }
}