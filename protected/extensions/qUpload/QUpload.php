<?php
class QUpload extends CWidget
{
	public $jsHandlerUrl;
	public $postParams=array();
	public $config=array();
	
    public function run()
    {
        $assets = dirname(__FILE__) . '/assets';
        $baseUrl = Yii::app() -> assetManager -> publish($assets);
        if (is_dir($assets)) {
            Yii::app() -> clientScript -> registerScriptFile($baseUrl . '/fileuploader.js');
        } else {
            throw new CHttpException(500, __CLASS__ . ' - Error: Couldn\'t find assets to publish.');
        }
    } 
}