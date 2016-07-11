<?php
class  XUpload extends CWidget{
	/******* widget private vars *******/
	private $baseUrl			= null;
	private $jsFiles			= array(
			'/js/jquery.min.js',
			'/js/vendor/jquery.ui.widget.js',
			'/js/tmpl.min.js',
			'/js/load-image.min.js',
			'/js/canvas-to-blob.min.js',
			'/js/bootstrap.min.js',
			'/js/jquery.blueimp-gallery.min.js',
			'/js/jquery.iframe-transport.js',
			'/js/jquery.fileupload.js',
			'/js/jquery.fileupload-process.js',
			'/js/jquery.fileupload-image.js',
			'/js/jquery.fileupload-audio.js',
			'/js/jquery.fileupload-video.js',
			'/js/jquery.fileupload-validate.js',
			'/js/jquery.fileupload-ui.js',
			'/js/main.js',
	);
	private $cssFiles			= array(
		 	'/css/bootstrap.min.css',
		//	'/css/style.css',
			'/css/blueimp-gallery.min.css', 
			'/css/jquery.fileupload.css',
		//	'/css/jquery.fileupload-ui.css',
	);
	
	public $getId = 'editor';
	
	public $options = NULL;
	
	public $UEDITOR_HOME_URL = '../';
	
	/**
	 * Initialize the widget
	 */
	public function init()
	{
		//Publish assets
		$this->publishAssets();
		$this->registerClientScripts();
		parent::init(); 
	}
	
	/**
	 * Publishes the assets
	 */
	public function publishAssets()
	{
		$dir = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'assets';
		$this->baseUrl = Yii::app()->getAssetManager()->publish($dir);
	}
	
	/**
	 * Registers the external javascript files
	 */
	public function registerClientScripts()
	{
	
		if ($this->baseUrl === '')
			throw new CException(Yii::t('Ueditor', 'baseUrl must be set. This is done automatically by calling publishAssets()'));
	
		//Register the main script files
		$cs = Yii::app()->getClientScript();
		foreach($this->jsFiles as $jsFile) {
			$ueditorJsFile = $this->baseUrl . $jsFile;
			$cs->registerScriptFile($ueditorJsFile, CClientScript::POS_HEAD);
		}
	
		// add the css
		foreach($this->cssFiles as $cssFile) {
			$ueditorCssFile = $this->baseUrl . $cssFile;
			$cs->registerCssFile($ueditorCssFile);
		}
	}
	
	/**
	* Run the widget
	*/
	public function run()
	{
		$this->render('upload');
	}
}