<?php
/*
 * @version: 2.1.5
 */
class AFancyBox extends CWidget
{
	// @ string the id of the widget, since version 2.0
	public $id;
	// @ string the taget element on DOM
	public $target;
	// @ boolean whether to enable the easing functions. You must set the eansing on $config.
	public $thumbsEnabled=false;
	// @ boolean whether to enable mouse interaction
	public $mouseEnabled=false;
	// @ array of config settings for fancybox
	public $config=array();
	
	// function to init the widget
	public function init()
	{
		// if not informed will generate Yii defaut generated id, since version 1.6
		if(!isset($this->id))
			$this->id=$this->getId();
		// publish the required assets
		$this->publishAssets();
	}
	
	// function to run the widget
    public function run()
    {
		//$config = CJavaScript::encode($this->config);
/* 		Yii::app()->clientScript->registerScript($this->getId(), "
			$('$this->target').fancybox($config);
		"); */
	}
	
	// function to publish and register assets on page 
	public function publishAssets()
	{
		$assets = dirname(__FILE__).'/assets';
		$baseUrl = Yii::app()->assetManager->publish($assets);
		if(is_dir($assets)){
			if ($this->mouseEnabled) {
				Yii::app()->clientScript->registerScriptFile($baseUrl .'/lib/jquery.mousewheel-3.0.6.pack.js', CClientScript::POS_END);
			}
			//Yii::app()->clientScript->registerScriptFile($baseUrl . '/lib/jquery-1.10.1.min.js?v=2.1.5', CClientScript::POS_END);
			Yii::app()->clientScript->registerScriptFile($baseUrl . '/source/jquery.fancybox.pack.js?v=2.1.5', CClientScript::POS_END);
			Yii::app()->clientScript->registerCssFile($baseUrl . '/source/jquery.fancybox.css?v=2.1.5');
			
			if($this->thumbsEnabled){
				Yii::app()->clientScript->registerScriptFile($baseUrl .'/source/helpers/jquery.fancybox-thumbs.js', CClientScript::POS_END);
				Yii::app()->clientScript->registerCssFile($baseUrl . '/source/helpers/jquery.fancybox-thumbs.css?v=2.1.5');
			}
		} else {
			throw new Exception('EFancyBox - Error: Couldn\'t find assets to publish.');
		}
	}
}