<?php

/**
 * LoginForm class.
 * LoginForm is the data structure for keeping
 * user login form data. It is used by the 'login' action of 'SiteController'.
 */
class LoginForm extends CFormModel
{
	public $username;
	public $password;
	public $rememberMe;

	private $_identity;

	/**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules()
	{		
		return array(
			// username and password are required
			array('username, password', 'required', 'message'=>'<font style="color:red;">{attribute}不允许为空</font>'),
			// rememberMe needs to be a boolean
			array('rememberMe', 'boolean'),
			// password needs to be authenticated
			array('password', 'authenticate', 'message'=>'<font style="color:red;">密码不允许为空</font>'),
		);

	}

	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
			'rememberMe'=>'记住密码',
			'username'=>'账号',
			'password'=>'密码',
		);
	}

	/**
	 * Authenticates the password.
	 * This is the 'authenticate' validator as declared in rules().
	 */
	public function authenticate($attribute,$params)
	{		
		$this->_identity=new AdminUserIdentity($this->username,$this->password);		
		if(!$this->_identity->authenticate())
			$this->addError('password','<font style="color:red;">用户名或密码有误</font>');		
	}

	/**
	 * Logs in the user using the given username and password in the model.
	 * @return boolean whether login is successful
	 */
	public function login()
	{
		if($this->_identity===null)
		{			
			$this->_identity=new AdminUserIdentity($this->username,$this->password);		
			$this->_identity->authenticate();
		}
		if($this->_identity->errorCode===AdminUserIdentity::ERROR_NONE)
		{
			$duration=$this->rememberMe ? 3600*24*30 : 0; // 30 days			
			Yii::app()->user->login($this->_identity,$duration);
			return true;
		}
		else
			return false;
	}
}
