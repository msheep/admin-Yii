<?php
class SiteController extends Controller{
	public $layout='adminLayout';
	public $rightUrl;
	
	public function actionIndex(){				
            $this->render('index');			
	}
	
	public function actionAdminLeft(){
	    $this->layout = '';
	    $this->render('adminLeftLayout');
	}

	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError()
	{
	    if($error=Yii::app()->errorHandler->error)
	    {
	    	if(Yii::app()->request->isAjaxRequest)
	    		echo $error['message'];
	    	else
	        	$this->render('error', $error);
	    }
	}

	
}
