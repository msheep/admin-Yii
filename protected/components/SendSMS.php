<?php
/**
 * 发送短信独立模块 API 2.0  支持模板的发送和群发
 * @param array $param = array(
 * 							'localSend' => 'true/false',			//默认不支持本地站点调用该接口
 * 							'content' => 'this is the text',		//不调用模板发送，必传，短信内容70字以内按一条计算，超出将按67个字符每条计算
 * 							'limit' => '20',						//限制每日发送短信最高条数，默认20
 * 							'mobile' => array('110','112'),			//一个号码可以直接字符串，多个数组形式
 * 							'sign' => '【尊贷网 www.zundai.com】',	//短信的签名，默认为【尊贷网 www.zundai.com】
 * 							'typeInfo' => array(),					//模板中的传值，数组形式
 * 						 )
 * @param string $type 模板名称，默认没有模板，发送内容自动加签名
 * @return array $result = array(
 * 								'status' = > 'success/fail',											//发送成功或失败
 * 								'error' = > 111,														//发送结果代码
 * 								'msg' = > '发送手机号码错误或者超出上限，均不符合要求',					//发送结果详情
 * 								'number' = > array(['limit']=>,['wrong']=>,['fail']=>,['success']=>),												//发送的所有手机号码的发送情况
 * 							)
 * 调用方法：
 * 		//模板版本
 * 		$param['typeInfo']['Code'] = 'E1234';
 * 		$param['mobile'] = array('15996253401');
 * 		$res = SendSMS::SendText($param,'yzm');
 * 		//非模板版本
 * 		$param['content'] = '111';
 * 		$param['mobile'] = array('15996253401');
 * 		$res = SendSMS::SendText($param);
 */
Yii::import ('application.components.PHPMailer.Mail',true);
Class SendSMS{
    const SMS_MAX = 50;			//批量发送最大人数
    const ENCODE = 'UTF8';		//短信编码
    static $error = array(
        //自定义代码
    	'0'=>'短信发送失败',
        '111' => '短信发送成功',
        /* '222' => '本地站点不支持发送短信',
         '701' => '您未选择模板发送短信，请填写短信内容',
        '702' => '您选择了模板发送短信，请输入模板参数',
        '703' => '模板参数传入不全，或者模板不存在', */
        '704' => '手机号码错误或者超出上限',
        '706' => '发送超过上限',
        //短信接口返回代码
        '-10' => '验证信息失败',
        '-20' => '短信余额不足',
        '-30' => '短信内容为空',
        '-31' => '短信内容存在敏感词',
        '-32' => '短信内容确少签名信息',
        '-40' => '错误的手机号',
        '-50' => '请求发送IP不在白名单内',
    );
    static $template = array(
        //'zd_zc'=>'注册短信',
    	'zd_zcyzm'=>'注册验证码短信',
        'zd_czmm'=>'重置密码短信',
        'zd_zzcg'=>'转账短信(收款人)',
        'zd_zzyzm'=>'转账验证码短信(到尊贷)',
        'zd_yhkyzm'=>'转账验证码短信(到银行卡)',
        'zd_zcsh'=>'资产审核成功短信',
        'zd_zcsh_sb'=>'流单短信',
        //'zd_fbcg'=>'发标成功短信',
        //'zd_jdcg_jk'=>'借款成功短信(借款人)',
        //'zd_jdcg_tz'=>'借款成功短信(理财人)',
        //'zd_lb_jk'=>'流单短信(借款人)',
        //'zd_lb_tz'=>'流单短信(理财人)',
        //'zd_hktx'=>'还款提醒短信',
        //'zd_hkcg'=>'还款成功短信',
        //'zd_jkdz'=>'借款到账提现短信',
        'zd_xgyx'=>'修改邮箱短信',
        'zd_xgsj'=>'修改手机短信',
        'zd_zhtxmm'=>'找回提现密码短信',
        'zd_hyedqr'=>'额度审核成功',
        'zd_xyedsp'=>'授信成功',
        'zd_sztb'=>'开启发标',
        'zd_mbtx'=>'满标提醒',
        'zd_cwfk'=>'放款成功',
        'zd_txcg'=>'提现成功',
        'zd_yq'=>'逾期',
        'zd_ld'=>'流单',
    	'zd_ldlcr'=>'流单-理财人',
        'zd_hkdd'=>'回款到达',
        'zd_mbtx_tender'=>'满标提醒-理财人',
        'zd_cwfk_tender'=>'放款成功-理财人',
        'zd_tqck'=>'提前催款',
        'zd_txbh'=>'提现驳回',
        'zd_diy'=>'自定义'
    );
    public static function SendText($param,$type='common',$group=1){
        //默认本地站点不调用短信接口
        if(!isset($param['localSend'])){
            $param['localSend'] = true;
        }
        if(defined('LOCALHOST') && !$param['localSend']){
            return SendSMS::getReturn('success',222);
        }
        //判断是否调用模板
        if($type == 'common'){
            if(!isset($param['content']) || trim($param['content']) == ''){
                return SendSMS::getReturn('fail',701);
                exit();
            }
        }else{
            if(!isset($param['typeInfo'])){
                return SendSMS::getReturn('fail',702);
                exit();
            }else{
                $result = SendSMS::getAllTypeSMSCont($type, $param['typeInfo']);
                if($result === 0){
                    return SendSMS::getReturn('fail',703);
                    exit();
                }else{
                    $param['content'] = $result['content'];
                    if(!isset($param['limit']) && isset($result['limit'])){
                        $param['limit'] = $result['limit'];
                    }
                }
            }
        }

        //每日每个号码最多发送短信条数，默认20条
        if(!isset($param['limit'])){
            $param['limit'] = 100;
        }
        $otherInfo = array();
        if($group == 1){
            if(!isset($param['typeInfo']['OrderNumber'])){
                if(isset($param['typeInfo']['ENumber'])){
                    $ENumbers = explode('(',$param['typeInfo']['ENumber']);
                    $orderInfoO = OrderTicket::model()->find("ElectronicOrderNumber=:ENumber",array(':ENumber'=>$ENumbers[0]));
                    if($orderInfoO){
                        $otherInfo['orderNumber'] = $orderInfoO->orderNumber;
                    }
                }
            }else{
                $otherInfo['orderNumber'] = $param['typeInfo']['OrderNumber'];

            }
            if(!isset($param['typeInfo']['ENumber'])){
                if(isset($param['typeInfo']['OrderNumber'])){
                    $orderInfoE = OrderTicket::model()->find("OrderNumber=".trim($param['typeInfo']['OrderNumber']));
                    if($orderInfoE){
                        $ENumber = explode('(',$orderInfoE->ElectronicOrderNumber);
                        $otherInfo['ENumber'] = $ENumber[0];
                    }
                }
            }else{
                $ENumber = explode('(',$param['typeInfo']['ENumber']);
                $otherInfo['ENumber'] = $ENumber[0];
            }
        }else if($group == 2){
            if(!isset($param['typeInfo']['debitId'])){
                $otherInfo['debitId'] = intval($param['typeInfo']['debitId']);
            }
        }

        $allNumbers = array();
        //验证手机号码，分为单个和批量的
        $wrongMobile = array();
        $sendMobile = array();
        $limitMobile = array();
        $insertValue = array();
        $num = 1;
        $day = intval(date('Ymd'));
        if(isset($param['mobile'])){
            if(is_array($param['mobile'])){
                $param['mobile'] = array_unique($param['mobile']);
                $num = count($param['mobile']);
                foreach($param['mobile'] as $k=>$mobile){
                    //过滤不正确的手机号码
                    if(SendSMS::checkMobile($mobile) < 1){
                        $wrongMobile[] = $mobile;
                        $insertValue[] = "(".$mobile.",'".$param['content']."',' ',707,'".$type."','".json_encode($otherInfo)."','".date('Y-m-d H:i:s',time())."')";
                        unset($param['mobile'][$k]);
                    }else{
                        //过滤超出限制的手机号码
                        if(defined('PRODUCTION')){
                            $key = sprintf("%u", crc32($day.$mobile.$type));
                            $value = Yii::app()->cache->get($key);
                            if(!empty($value)){
                                if($value < $param['limit']){
                                    $sendMobile[] = $mobile;
                                }else{
                                    $limitMobile[] = $mobile;
                                    $insertValue[] = "(".$mobile.",'".$param['content']."',' ',706,'".$type."','".json_encode($otherInfo)."','".date('Y-m-d H:i:s',time())."')";
                                    unset($param['mobile'][$k]);
                                }
                            }else{
                                $sendMobile[] = $mobile;
                            }
                        }else{
                            $sendMobile[] = $mobile;
                        }
                    }
                }
            }else{
                //过滤不正确的手机号码
                if(SendSMS::checkMobile($param['mobile']) < 1){
                    $wrongMobile[] = $param['mobile'];
                    $insertValue[] = "(".$param['mobile'].",'".$param['content']."',' ',707,'".$type."','".json_encode($otherInfo)."','".date('Y-m-d H:i:s',time())."')";
                }else{
                    //过滤超出限制的手机号码
                    if(defined('PRODUCTION')){
                        $key = sprintf("%u", crc32($day.$param['mobile'].$type));
                        $value = Yii::app()->cache->get($key);
                        if(!empty($value)){
                            if($value <= $param['limit']){
                                $sendMobile[] = $param['mobile'];
                            }else{
                                $limitMobile[] = $param['mobile'];
                                $insertValue[] = "(".$param['mobile'].",'".$param['content']."',' ',706,'".$type."','".json_encode($otherInfo)."','".date('Y-m-d H:i:s',time())."')";
                            }
                        }else{
                            $sendMobile[] = $param['mobile'];
                        }
                    }else{
                        $sendMobile[] = $param['mobile'];
                    }
                }
            }
            $allNumbers['wrong'] = $wrongMobile;
            $allNumbers['limit'] = $limitMobile;
            if(!empty($wrongMobile)){
                $wrongMobiles = implode(',',$wrongMobile);
                @YiiLog(array('mobile'=>$wrongMobiles, 'content'=>$param['content'], 'WrongNumber'=>true), 'SendSMS Wrong Phone');
                if(empty($limitMobile) && empty($sendMobile)){
                    self::InsertSendLog($insertValue);
                    return SendSMS::getReturn('fail',707,'',array_merge($wrongMobile,$limitMobile));
                    exit();
                }
            }
            if(!empty($limitMobile)){
                $limitMobiles = implode(',',$limitMobile);
                @YiiLog(array('mobile'=>$limitMobiles, 'content'=>$param['content'], 'Spite'=>true,'Times'=>$value), 'SendSMS Too Much');
                SendSMS::sendMail(706,'短信发送超过上限'.$param['limit'].'条（已经发送了'.$value.'条）',$param['content'],$limitMobile);
                if(empty($wrongMobile) && empty($sendMobile)){
                    self::InsertSendLog($insertValue);
                    return SendSMS::getReturn('fail',706,'',array_merge($wrongMobile,$limitMobile));
                    exit();
                }
            }
            if(empty($sendMobile)){
                self::InsertSendLog($insertValue);
                return SendSMS::getReturn('fail',704,'',array_merge($wrongMobile,$limitMobile));
                exit();
            }
        }else{
            return SendSMS::getReturn('fail', 705);
            exit();
        }

        //整理出发送的手机号码，目前一次最多发送50个
        if(!empty($sendMobile)){
            $allGroup = array();
            $allGroup = array_chunk($sendMobile,self::SMS_MAX,true);
        }else{
            return SendSMS::getReturn('fail', 704 );
            exit();
        }

        //发送短信内容带有签名，且短信为url编码
        if(!isset($param['sign'])){
            $param['sign'] = '【尊贷网】';
        }
        $content = $param['content'].$param['sign'];
        if(self::ENCODE == 'GBK'){
            $urlContent = urlencode(iconv('UTF-8', 'GBK', $content));
        }else{
            $urlContent = urlencode($content);
        }

        $sendSuccess = array();
        $sendFail = array();
        foreach($allGroup as $group){
            $mobiles = '';
            $mobiles = implode(',',$group);
            $file_contents = 0;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://sms-api.luosimao.com/v1/send.json");

            curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_HEADER, FALSE);

            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, TRUE);
            curl_setopt($ch, CURLOPT_SSLVERSION, 3);

            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_USERPWD, 'api:key-bbc13729a04b8606403fe26e123bc183');
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_POSTFIELDS, array('mobile' => $mobiles, 'message' => $content));

            $ret = curl_exec($ch);
            curl_close($ch);
            $ret=json_decode($ret,1);

            if(isset($ret['error'])){
                switch($ret['error']){
                    case 0:
                        $file_contents = 1;
                        break;
                    default:
                        $file_contents = $ret['error'];
                }
            }

            //短信调用
            if($file_contents > 0){
                //发送成功设置一次缓存
                foreach($group as $number){
                    if(defined('PRODUCTION')){
                        $key = sprintf("%u", crc32($day.$number.$type));
                        $value = Yii::app()->cache->get($key);
                        if($value){
                            $value = Yii::app()->cache->increment($key);
                        }else{
                            $value = Yii::app()->cache->set($key, 1, 2*24*3600);
                        }
                    }
                    $sendSuccess[] = $number;
                    $insertValue[] = "(".$number.",'".$param['content']."',' ',111,'".$type."','".json_encode($otherInfo)."','".date('Y-m-d H:i:s',time())."')";
                }
                $allSendNumber = $file_contents;
            }else{
                foreach($group as $number){
                    if(defined('PRODUCTION')){
                        $key = sprintf("%u", crc32($day.$number.$type));
                        $value = Yii::app()->cache->get($key);
                        if($value){
                            $value = Yii::app()->cache->increment($key);
                        }else{
                            $value = Yii::app()->cache->set($key, 1, 2*24*3600);
                        }
                    }
                    $sendFail[] = $number;
                    $insertValue[] = "(".$number.",'".$param['content']."',' ','".$file_contents."','".$type."','".json_encode($otherInfo)."','".date('Y-m-d H:i:s',time())."')";
                }
                $errorCode[] = $file_contents;
            }
        }

        //统计发送条数
        $allSendNum = 0;
        if(mb_strlen($content,self::ENCODE) < 70){
            $eachNum = 1;
        }else{
            $eachNum = ceil((mb_strlen($content,self::ENCODE) - 70)/67);
            $eachNum += 1;
        }
        $allSendNum = $eachNum * count($sendSuccess);

        //记录到数据库
        self::InsertSendLog($insertValue);
        $allNumbers['fail'] = $sendFail;
        $allNumbers['success'] = $sendSuccess;
        if(!empty($sendFail)){
            $sendFailNumber = implode(',',$sendFail);
            $errorCode = array_unique($errorCode);
            foreach($errorCode as $ec){
                if(isset(self::$error[$ec])){
                    $error[] = self::$error[$ec];
                }else{
                    $error[] = $ec;
                }

            }
            $errors = implode('、',$error);
            $errorCodes = implode('、',$errorCode);
            Yii::log($sendFailNumber.':'.$url.'-'.var_export($file_contents, true), CLogger::LEVEL_WARNING, 'SendSMS Gateway Error');
            SendSMS::sendMail($errorCodes,$errors,$param['content'],$sendFail);
            return SendSMS::getReturn('fail',$errorCodes,$errors,$allNumbers);
        }else{
            $sendSuccessNumber = implode(',',$sendSuccess);
            @YiiLog(array('mobile'=>$sendSuccessNumber,'result'=>$file_contents, 'content'=>$param['content'].' 【尊贷网 www.zundai.com 】 '), '.SendSMS');
//			return SendSMS::getReturn('success',111,self::$error[111].'，已经发送'.count($sendSuccess).'个号码'.$allSendNum.'条短信，余额为：'.$leftMoney,$allNumbers);
            return SendSMS::getReturn('success',111,self::$error[111].'，已经发送'.count($sendSuccess).'个号码'.$allSendNum.'条短信',$allNumbers);
        }
    }

    //设置短信模板
    public static function getAllTypeSMSCont($type,$param){
        if(!empty($param) && isset($type)){
            switch($type){
                //尊贷注册短信
               /*  case 'zd_zc':
                    $result['content'] = '尊敬的用户您好：欢迎成为尊贷网会员，请进行身份验证，让尊贷网成为您生活中可信任和依靠的伙伴！';
                    return $result;
                    break; */
                //重置密码
                case 'zd_czmm':
                    if(empty($param['code'])){
                        return 0;
                    }
                    $result['content'] = "尊敬的尊贷网用户您好！您重置密码的验证码为：{$param['code']}，如非本人操作，请忽略！";
                    return $result;
                    break;
                //收款人短信
                case 'zd_zzcg':
                    $result['content'] = "尊敬的尊贷网用户您好！{$param['sendUser']}向您转入{$param['cash']}元";
                    return $result;
                    break;
                //注册验证码
                case 'zd_zcyzm':
                    if(empty($param['code'])){
                        return 0;
                    }
                    $result['content'] = "尊敬的尊贷网用户您好！您注册的验证码为：{$param['code']}，如非本人操作，请忽略！";
                    return $result;
                    break;
                //转账到尊贷验证码
                case 'zd_zzyzm':
                    if(empty($param['code'])){
                        return 0;
                    }
                    $result['content'] = "尊敬的尊贷网用户您好！您的手机验证码为：{$param['code']}，如非本人操作，请忽略！";
                    return $result;
                    break;
                //转账到银行卡验证码
                case 'zd_yhkyzm':
                    if(empty($param['code'])){
                        return 0;
                    }
                    $result['content'] = "尊敬的尊贷网用户您好！您的手机验证码为：{$param['code']}，如非本人操作，请忽略！";
                    return $result;
                    break;
                //资产审核成功
                /* case 'zd_zcsh':
                    if(!isset($param['ctime']) || !isset($param['debitId']) || !isset($param['money'])){
                        return 0;
                    }
                    $result['content'] = '您'.$param['ctime'].'提交的'.$param['debitId'].'抵押资产申请已审核通过，批核贷款金额为：'.$param['money'].'，请在个人中心中查看详情，无疑问请“同意发布”，正式发布借款申请，开始招标。';
                    $result['limit'] = 6;
                    return $result;
                    break; */
                /* //资产审核失败
                case 'zd_zcsh_sb':
                    if(!isset($param['ctime']) || !isset($param['debitId']) || !isset($param['reason'])){
                        return 0;
                    }
                    $result['content'] = '很遗憾的通知您，由于'.$param['reason'].'原因，您'.$param['ctime'].'提交的'.$param['debitId'].'抵押资产申请未能审核通过。别灰心，再发布一个吧！';
                    $result['limit'] = 6;
                    return $result;
                    break; */
                //发标成功
                /* case 'zd_fbcg':
                    if(!isset($param['debitId']) || !isset($param['endTime'])){
                        return 0;
                    }
                    $result['content'] = '您'.$param['debitId'].'借标申请已正式发布，截标时间'.$param['endTime'].'，您可在个人账户中随时查看招标进展，祝您贷款顺利！感谢选择尊贷网！';
                    $result['limit'] = 5;
                    return $result;
                    break; */
                //借贷成功->借款者
               /*  case 'zd_jdcg_jk':
                    if(!isset($param['debitId']) || !isset($param['time'])){
                        return 0;
                    }
                    $result['content'] = '尊敬的用户您好！恭喜您的借款'.$param['debitId'].'已于'.$param['time'].'满标，一个工作日内将放款至您的尊贷账户，请注意查收！请注意按时还款，增加信用分值，以便后续贷款顺利进行！';
                    $result['limit'] = 5;
                    return $result;
                    break; */
                //借贷成功->投资
                /* case 'zd_jdcg_tz':
                    if(!isset($param['debitId']) || !isset($param['time'])){
                        return 0;
                    }
                    $result['content'] = '您投资的借款'.$param['debitId'].'已于'.$param['time'].'满标，投资合同同时生效，感谢您选择尊贷网。还款会按期返还至您账户，请注意查收！';
                    $result['limit'] = 5;
                    return $result;
                    break; */
                //流标->借款者
                /* case 'zd_lb_jk':
                    if(!isset($param['debitId']) || !isset($param['endTime']) || !isset($param['investMoney']) || !isset($param['percent'])){
                        return 0;
                    }
                    $result['content'] = '很遗憾的通知您，您'.$param['debitId'].'的借款已于'.$param['endTime'].'截止招标，投标额总计￥'.$param['investMoney'].'万元，占'.$param['type'].'招标金额的'.$param['percent'].'，招标未满额流标。别灰心，再发布一个吧！';
                    $result['limit'] = 5;
                    return $result;
                    break; */
                //流标->投资者
               /*  case 'zd_lb_tz':
                    if(!isset($param['debitId']) || !isset($param['failTime'])){
                        return 0;
                    }
                    $result['content'] = '很遗憾的通知您，您投资的理财编号'.$param['debitId'].'因招标未满额已于'.$param['failTime'].'流标，您投资的理财已返还至您在尊贷网的账户。还有很多标的在进行中，再选择一个吧！';
                    $result['limit'] = 5;
                    return $result;
                    break; */
                //还款提醒
                /* case 'zd_hktx':
                    if(!isset($param['debitId']) || !isset($param['repayMoney']) || !isset($param['repayDate'])){
                        return 0;
                    }
                    $result['content'] = '您借款编号'.$param['debitId'].'本期应还金额￥'.$param['repayMoney'].'，还款时间'.$param['repayDate'].'，请提前充值，确保账户余额足够还款。';
                    return $result;
                    break; */
                //还款成功
                /* case 'zd_hkcg':
                    if(!isset($param['debitId']) || !isset($param['repayMoney'])){
                        return 0;
                    }
                    $result['content'] = '您借款编号'.$param['debitId'].'本期应还金额￥'.$param['repayMoney'].'扣款成功。'.$param['msg'];
                    return $result;
                    break; */
                //借款到账提醒
                /* case 'zd_jkdz':
                    if(!isset($param['debitId']) || !isset($param['time'])){
                        return 0;
                    }
                    $result['content'] = '您'.$param['debitId'].'的借款已于'.$param['time'].'放款至您在尊贷网的账户，请注意查收！';
                    return $result;
                    break; */
                //修改邮箱
                case 'zd_xgyx':
                    if(empty($param['code'])){
                        return 0;
                    }
                    $result['content'] = "尊敬的尊贷网用户您好！您重置邮箱的验证码为：{$param['code']}";
                    return $result;
                    break;
                //修改手机
                case 'zd_xgsj':
                    if(empty($param['code'])){
                        return 0;
                    }
                    $result['content'] = "尊敬的尊贷网用户您好！您重置手机的验证码为：{$param['code']}";
                    return $result;
                    break;
                //找回提现密码
                case 'zd_zhtxmm':
                    if(empty($param['code'])){
                        return 0;
                    }
                    $result['content'] = "尊敬的尊贷网用户您好！您重置提现密码的验证码为：{$param['code']}";
                    return $result;
                    break;

                //信用额度审批
                case 'zd_xyedsp':
                    $result['content'] = "尊敬的 {$param['userName']}用户：您好，恭喜您通过尊贷网的授信，授信额度为 {$param['limit']} 元。";
                    return $result;
                    break;

                //会员额度确认
                case 'zd_hyedqr':
                    $result['content'] = "尊敬的{$param['userName']}用户: 您好！您的借款申请({$param['number']})已通过审核。感谢您对我们的关注和支持！";
                    return $result;
                    break;

                //设置投标
                case 'zd_sztb':
                    $result['content'] = "尊敬的{$param['userName']}用户: 您好，您的借款申请(单号：{$param['number']})已通过审核，发标时间于{$param['dateTime']}开启。";
                    return $result;
                    break;

                //满标提醒
                case 'zd_mbtx':
                    $result['content'] = "尊敬的{$param['userName']}用户:您好，您的借款申请 (单号： {$param['number']}) 于{$param['dateTime']}满标，如放款审核无误，您的借款将会于2个工作日内到达您的尊贷账户。";
                    return $result;
                    break;

                //放款成功
                case 'zd_cwfk':
                    $result['content'] = "尊敬的{$param['userName']}用户:恭喜您，您的借款 (单号： {$param['number']}) 已通过放款审核，借款已发放到您的尊贷账户。";
                    return $result;
                    break;
                //提现成功
                case 'zd_txcg':
                    $result['content'] = "尊敬的{$param['userName']}用户:您好，您于{$param['dateTime']}提交的{$param['cash']}元提现申请已审核成功，预计打款时间{$param['payTime']},请您注意查收。";
                    return $result;
                    break;
                //逾期
                case 'zd_yq':
                    $result['content'] = "尊敬的{$param['userName']}用户:您好，您有一笔借款 (单号： {$param['number']}) 本于{$param['dateTime']}完成还款，现已逾期，请及时完成还款操作，以免给您带来不必要的损失。";
                    return $result;
                    break;
                //流单
                case 'zd_ld':
                    $result['content'] = "尊敬的{$param['userName']}用户:您好，很遗憾的通知您，您于{$param['dateTime']}提交的借款申请单(单号：{$param['number']})已作流单处理，感谢您对我们的关注和支持！";
                    return $result;
                    break;
                //流单-理财人
                case 'zd_ldlcr':
                   	$result['content'] = "尊敬的{$param['userName']}用户:您好，很遗憾的通知您，您投放的标（单号：{$param['number']}），已作流单处理，您的{$param['cash']}元理财已经退还到您的尊贷账户。感谢您对我们的关注和支持！";
                    return $result;
                    break;
                //回款到达
                case 'zd_hkdd':
                    $result['content'] = "尊敬的{$param['userName']}用户:您好，您有一笔理财（单号：{$param['number']}）已于{$param['dateTime']}到账。感谢您对我们的关注和支持。";
                    return $result;
                    break;
                //满标提醒-理财人
                case 'zd_mbtx_tender':
                    $result['content'] = "尊敬的{$param['userName']}用户:您好，您投放的标（单号：{$param['number']}），现已满标，待放款审核成功之后，您的{$param['cash']}元理财即可转入借款人账户。";
                    return $result;
                    break;
                //放款成功-理财人
                case 'zd_cwfk_tender':
                    $result['content'] = "尊敬的{$param['userName']}用户:您好，感谢您对理财（单号：{$param['number']}）的帮助与支持，由于该借款已成功放款，您的{$param['cash']}元理财已成功转入借款人账户。";
                    return $result;
                    break;
                //提前催款
                case 'zd_tqck':
                    $result['content'] = "尊敬的{$param['userName']}用户:您好,您有一笔还款（单号：{$param['number']}）需于{$param{'dateTime'}}前完成还款，请及时完成还款操作。感谢您对我们的关注和支持！";
                    return $result;
                    break;
                //提现驳回
                case 'zd_txbh':
                    $result['content'] = "尊敬的{$param['userName']}用户:您好,很抱歉地通知您，您于".date("Y年m月d日 H时i分s秒",strtotime($param['cTime']))."的提现申请单‘".$param['orderNumber']."’因为银行账户信息有误，被驳回，请您核实并修改银行信息，修改完之后可重新申请提现。";
                    return $result;
                    break;
                //自定义
                case 'zd_diy':
                    $result['content'] = strip_tags($param['content']);
                    return $result;
                    break;
                //系统dayRepay测试用
                case 'zd_check_sysDayRepay':
                    $result['content'] = "{$param['content']}";
                    return $result;
                    break;
                default:
                    return 0;
                    break;
            }
        }else{
            return 0;
        }
    }

    public static function getReturn($success,$err,$msg='',$mobile=''){
        $SMSResult['status'] = $success;
        $SMSResult['error'] = $err;
        if($msg == ''){
            if(isset(self::$error[$err])){
                $SMSResult['msg'] = self::$error[$err];
            }else{
                $SMSResult['msg'] = $err;
            }

        }else{
            $SMSResult['msg'] = $msg;
        }
        if($mobile!=''){
            $SMSResult['number'] = $mobile;
        }
        return $SMSResult;
    }

    //发送记录插入数据库
    public static function InsertSendLog($insertValue){
        $insertValues = implode(',',$insertValue);
        $sql = 'INSERT INTO p2p_send_sms_log(mobile,content,result,error_code,template,other_info,insert_time) VALUES '.$insertValues;
        $command = Yii::app()->db->createCommand($sql)->execute();
        return $command;
    }

    //短信发送结果发邮件提示(备用)
    public static function sendMail($errorCode, $reason='',$cont,$mobile,$success = '0',$money = 0){
        if(defined('LOCALHOST')){
            $day = intval(date('Ymd'));
            $key = sprintf("%u", crc32($day.'smsMail'.$errorCode));
            $value = Yii::app()->cahce->get($key);
            if(!empty($value)){
                if($value < 8){
                    $sendMail['To'][0]['email'] = 'wanghui@meiti.com';
                    $sendMail['To'][1]['email'] = 'liaoxianxian@meiti.com';
                    $sendMail['typeInfo']['reason'] = $reason;
                    $sendMail['typeInfo']['cont'] = $cont;
                    $sendMail['typeInfo']['mobile'] = $mobile;
                    $num = count($mobile);
                    if(mb_strlen($cont,self::ENCODE) < 70){
                        $eachNum = 1;
                    }else{
                        $eachNum = ceil((mb_strlen($cont,self::ENCODE) - 70)/67);
                        $eachNum += 1;
                    }
                    $num = count($mobile);
                    $sendMail['typeInfo']['num'] = $eachNum * $num;
                    $sendMail['typeInfo']['totalNum'] = $num;
                    $sendMail['typeInfo']['money'] = $money;
                    $sendMail['typeInfo']['success'] = $success;
                    $res =  Mail::sendMail($sendMail,'sms');
                    return $res;
                }else{
                    $value = Yii::app()->cache->increment($key);
                }
            }else{
                $value = Yii::app()->cache->set($key, 1, 2*24*3600);
            }

        }
    }

    //验证手机号码
    public static function checkMobile($number){
        if(is_numeric($number) || strlen($number)== 11){
            if(preg_match("/^(13[0-9]|15[0-9]|18[0-9]|14[57])[0-9]{8}$/",$number)){
                return  1;
            }else{
                return 0;
            }
        }else{
            return 0;
        }
    }
}

?>