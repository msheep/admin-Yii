<?php
class Attribute extends BaseModel {
    static $attr = array(
        'Size' => '规格',
        'Color' => '颜色'
    );
    
    public function tableName() {
        return '{{attribute}}';
    }

    public function loadInit($params = array()) {
        
    }

}


