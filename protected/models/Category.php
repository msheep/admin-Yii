<?php

class Category extends BaseModel {
  
    public function tableName() {
        return '{{category}}';
    }

    public function loadInit($params = array()) {
        
    }
    
    public static function findParentId($catId){
        $result = Category::model()->find('cat_id = :cat_id', array(':cat_id' => $catId));
        if(isset($result->parent_id)){
            if($result->parent_id != 0){
                $parentId = Category::findParentId($result->parent_id);
            }else{
                $parentId = $result->cat_id;
            }
        }
        return $parentId;
    }
}
