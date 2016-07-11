<?php
class LoginController extends Controller{
	/**
	 * Displays the login page
	 */
	public function actionLogin()
	{
		if (!defined('CRYPT_BLOWFISH')||!CRYPT_BLOWFISH)
			throw new CHttpException(500,"This application requires that PHP was compiled with Blowfish support for crypt().");

		$model=new LoginForm;

		// if it is ajax validation request
		if(isset($_POST['ajax']) && $_POST['ajax']==='login-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

		// collect user input data
		if(isset($_POST['LoginForm']))
		{			
			$model->attributes=$_POST['LoginForm'];			
			// validate user input and redirect to the previous page if valid
			//var_dump($model);exit();
			if($model->validate() && $model->login())				
				$this->redirect(Yii::app()->user->returnUrl);
		}
		// display the login form
		$this->render('login',array('model'=>$model));
	}
	/**
	*  Logs out current users and redirect to loginpage
	*/
	public function actionLogout()
	{		
		Yii::app()->user->logout();				
		$this->redirect(array('/login/login'));
	}
}
?>