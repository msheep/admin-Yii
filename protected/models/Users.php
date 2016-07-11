<?php
class Users extends BaseModel {
    
    public function tableName() {
        return '{{users}}';
    }

    public function loadInit($params = array()) {
        
    }
    
     public function relations(){
        return array(
        );
    }
    /**
	 * Checks if the given password is correct.
	 * @param string the password to be validated
	 * @return boolean whether the password is valid
	 */
	public function validatePassword($password)
	{		
        return md5(md5($password).$this->ec_salt) === $this->password;		
	}

}


