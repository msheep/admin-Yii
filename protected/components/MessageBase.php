<?php
/**
 * 信息类统一集成接口 （包括手机，邮件，站内信）
 * User: wangmingcha
 * Date: 14-1-10
 * Time: 上午11:00
 * 用法： $obj = new MessageBase($userId,$tmpName);
 *        //$obj->addExtArr($arr);  如果需要的话，加上此行，发送信息需要的动态参数来源$arr里的参数
 *        $res = $obj->send();
 * 返回值： 为 'MESSAGE_SEND_SUCCESS' 表示发送成功，否则返回此类相应的错误常量 ,如 NO_USER_MOBILE.
 */

class MessageBase {
    public $userMod;//UserBase实例
    public $tmpName;//模板名
    protected  $sendInfoArr =array();//模板名，发送类型
    private  $_errorArr; //错误发送类型
    private $extArr = array(); //附加信息（调用邮件类模板时动态参数等）
    private $logId; //messageBaseLog 主键id
    private $logTmpName;//messageBaseLog 对应 tmpName属性
    static protected  $categoryArr =array(
        '1'=>'mobile',
        '2'=>'email',
        '3'=>'innerWeb'
    );

    //接口返回状态
    const  NO_SEND_CATEGORY = 0;
    const  MESSAGE_SEND_SUCCESS = 1;
    const  NO_FUNCTION_EXIST = 2;
    const  NO_USER_EXIST = 3;
    const  NO_USER_MOBILE = 4;
    const  NO_USER_EMAIL = 5;
    const  NO_USER_NAME = 6;
    const  NO_INNER_WEB_CONTENT = 7;
    const  SAVE_INNER_WEB_FAIL = 8;
    const  NO_DEBIT_EXIST = 9;
    const  MESSAGE_LOG_ERROR=10;

    public function __construct($userId,$tmpName){
    	Yii::import ('application.components.PHPMailer.Mail',true);
        $this->userMod = UserBase::model()->findByPk($userId);
        $this->tmpName = $tmpName;
        $baseUrl = str_replace('admin','www',Yii::app()->request->hostInfo);
        $this->extArr['baseUrl'] = $baseUrl;
        $this->extArr['userName'] = $this->userMod->realName;
        $this->getSendCategory();
    }

    //获取$userId当前节点对应的发送方式
    protected function getSendCategory(){
        $RulesMod = MessageBaseRules::getUserTypeMod($this->userMod->id,$this->tmpName);
        $sendType = explode(',',$RulesMod->sendType);
        $this->sendInfoArr['tpl'] = $this->tmpName;
        foreach($sendType as $val){
            if(array_key_exists($val,self::$categoryArr)){
                $this->sendInfoArr['funcPrefix'][]=self::$categoryArr[$val];
            }
        }
    }


    public function send(){
        if(!$this->userMod)
            return self::NO_USER_EXIST;
        if($this->sendInfoArr){
            foreach($this->sendInfoArr['funcPrefix'] as $val){
                $funcName = $val.'Send';
                if(method_exists($this,$funcName)){
                      $res =$this->$funcName();
                      if($res!='success'){
                        $this->_errorArr[] = $val;
                      }
                }else{
                    return self::NO_FUNCTION_EXIST;
                    break;
                }
            }
            //调用错误日志检查函数
            $res = $this->handleLog();
            return $res;
        }else{
            return self::NO_SEND_CATEGORY;
        }
    }



    protected function mobileSend(){
        $param = array();
        if(!$mobile=$this->userMod->mobile)
            return self::NO_USER_MOBILE;
        $param['mobile'] = $mobile;
        $param['typeInfo'] = $this->extArr;
        $smsContentArr = SendSMS::getAllTypeSMSCont($this->sendInfoArr['tpl'],$param['typeInfo']);
        $this->sendInfoArr['messageBaseLog']['mobile']['content'] = $smsContentArr['content'];
        $this->sendInfoArr['messageBaseLog']['mobile']['title'] = $smsContentArr['title'];
        $ret = SendSMS::SendText($param,$this->sendInfoArr['tpl']);
        return $ret['status'];
    }

    protected function emailSend(){
        $param = array();
        if(!$email = $this->userMod->email)
            return self::NO_USER_EMAIL;
        if(!$name = $this->userMod->nickName)
            return self::NO_USER_NAME;
        $param['To'][0]['email'] = $email;
        $param['typeInfo'] =  $this->extArr;
        $tmpInfo = Mail::getSubjectAndBody($this->sendInfoArr['tpl'],$param['typeInfo']);
        $this->sendInfoArr['messageBaseLog']['email']['title'] = $tmpInfo['Subject'];
        $this->sendInfoArr['messageBaseLog']['email']['content'] = $tmpInfo['Body'];
        $ret = Mail::sendMail($param,$this->sendInfoArr['tpl']);
        return $ret['status'];
    }

    protected function innerWebSend(){
        $userMessageMod = new UserMessage();
        $userMessageMod->sendId = 1;
        $userMessageMod->receiveId = $this->userMod->id;
        $tmpArr =  $this->extArr;
        $tmpInfo = Mail::getSubjectAndBody($this->sendInfoArr['tpl'],$tmpArr);
        $userMessageMod->content = $this->sendInfoArr['messageBaseLog']['innerWeb']['content'] = $tmpInfo['Body'];
        $userMessageMod->title =  $this->sendInfoArr['messageBaseLog']['innerWeb']['title'] = $tmpInfo['Subject'];
        $userMessageMod->type = 2;
        if($userMessageMod->save()){
            return 'success';
        }else{
            return self::SAVE_INNER_WEB_FAIL;
        }
    }

    protected function handleLog(){
        $messageBaseLogMod = new MessageBaseLog();
        $sendType = "";
        $errorType = "";
        foreach($this->sendInfoArr['funcPrefix'] as $val){
            $sendType.=array_search($val,self::$categoryArr).',';
        }
        if($this->_errorArr){
            foreach($this->_errorArr as $val){
                $errorType.=array_search($val,self::$categoryArr).",";
            }
        }
        $messageBaseLogMod->sendType = substr($sendType,0,-1);
        $messageBaseLogMod->errorType = substr($errorType,0,-1);
        $messageBaseLogMod->userId = $this->userMod->id;
        $messageBaseLogMod->tmpName = $this->getLogTmpName();
        $messageBaseLogMod->recordId = $this->getRecordId();
        $messageBaseLogMod->extJson = json_encode($this->sendInfoArr['messageBaseLog']);
        if(!$messageBaseLogMod->save(false)){
            return self::MESSAGE_LOG_ERROR;
        }
        $this->logId = $messageBaseLogMod->id;
        return self::MESSAGE_SEND_SUCCESS;
    }

    protected  function getLogTmpName(){
        return ($this->logTmpName)?$this->logTmpName:$this->tmpName;
    }

    protected function getRecordId(){
        if(isset($this->extArr['recordId'])){
            return $this->extArr['recordId'];
        }elseif(isset($this->extArr['debitId'])){
            return $this->extArr['debitId'];
        }else{
            return null;
        }
    }

    //追加额外配置数组
    public function addExtArr($arr){
        $this->extArr = array_merge($arr,$this->extArr);
    }

    //修改默认发送类型，比如信用额度审批 默认为array('mobile','email','innerWeb')，可以调用此方法修改 array('mobile')
    public function diySendType($arr){
        $this->sendInfoArr['funcPrefix'] = $arr;
    }

    public function getLogId(){
        return $this->logId;
    }

    public function setLogTmpName($logTmpName){
        $this->logTmpName = $logTmpName;
    }

    static public function getCategoryArr(){
        return self::$categoryArr;
    }
}

class DiyMessage extends  MessageBase{
    public function __construct($userId,$tmpName,$extArr,$diyTypeArr){
        parent::__construct($userId,'zd_diy');
        $this->diySendInfo($tmpName,$extArr,$diyTypeArr);
    }

    protected function diySendInfo($tmpName,$extArr,$diyTypeArr){
        $this->diySendType($diyTypeArr);
        $this->addExtArr($extArr);
        $this->setLogTmpName($tmpName);
    }

    public function diySendType($diyTypeArr){
        $arr = array();
        foreach($diyTypeArr as $val){
            if(array_key_exists($val,self::$categoryArr)){
                $arr[]= self::$categoryArr[$val];
            }
        }
        $this->sendInfoArr['funcPrefix'] = $arr;
    }
}