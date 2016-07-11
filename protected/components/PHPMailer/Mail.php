<?php
/*
 * $param array $sendInfo = array(
 *                  'CharSet' => 'utf-8',       //可传，设置编码，默认utf-8
 *                  'Port' => '25',             //可传，设置SMTP主机端口号，默认25
 *                  'Host' => '25',             //可传，设置SMTP主机服务器，默认25
 *                  'Username' => '******@qq.com',      //可传，设置 SMTP服务器用户名（填写完整的Email地址）
 *                  'Password' => '******',             //可传，设置 SMTP服务器用户名
 *                  'From' => '******@qq.com',          //可传，设置发件人地址，默认用户名地址（填写完整的Email地址）
 *                  'FromName' => 'test',       //可传，设置发件人名称，默认为发件人地址@前半部分，或者用户名@前半部分
 *                  'To' => array(
 *                          [0]=>array('email'=>'onemail@qq.com','name'=>'onename'),
 *                          [1]=>array('email'=>'twomail@qq.com','name'=>'twoname'),
 *                          [2]=>array('email'=>'twomail@qq.com','name'=>'twoname'),
 *                      )                   //可传，设置收件人邮箱和地址，二维数组形式，email必填，name可选
 *                  'AddBCC' => array(
 *                          [0]=>array('email'=>'onemail@qq.com','name'=>'onename'),
 *                          [1]=>array('email'=>'twomail@qq.com','name'=>'twoname'),
 *                          [2]=>array('email'=>'twomail@qq.com','name'=>'twoname'),
 *                      )                   //可传，设置密送邮箱和地址，二维数组形式，email必填，name可选
 *                  'AddCC' => array(
 *                          [0]=>array('email'=>'onemail@qq.com','name'=>'onename'),
 *                          [1]=>array('email'=>'twomail@qq.com','name'=>'twoname'),
 *                          [2]=>array('email'=>'twomail@qq.com','name'=>'twoname'),
 *                      )                   //可传，设置抄送邮箱和地址，二维数组形式，email必填，name可选
 *                  'AddAttachment' => array(
 *                                  [0]=>array('attach'=>'D:/www/theone/protected/components/PHPMailer/360log.png','name'=>'360log.png'),
 *                              )       //可传，设置附件和附件名称，二维数组形式，attach必填，name可选
 *                  'Subject' => 'Test Mail',   //可传，设置邮件主题
 *                  'Body' => 'This is a test mail!',   //可传，设置邮件的内容
 *                  'AddReplyTo' => 'xxx@sina.com','xxxx',  //可传，设置回复地址
 *                  'typeInfo' => array(),  //可传，设置模板参数
 *          )
 * $param string $type   设置邮件模板('cpl':十分便民火车票出票量日报表 )
 * return array $mailResult array('status'=>'success/fail','msg'=>'')
 * 调用方法：
 *  //模板版本
 *  Mail::sendMail($cpl,'cpl');
 *  //非模板版本
 *  Yii::import ('application.components.PHPMailer.Mail',true);
 *  $cpl['Subject'] = 'Test' ;
 *  $cpl['Body'] = 'This Is An Test Mail' ;
 *  $cpl['To'][0]['email'] = 'yangjing@meiti.com' ;
 *  $cpl['To'][1]['email'] = 'ningyangjing@126.com';
 *  Mail::sendMail($cpl); 
 */

require_once 'class.phpmailer.php';
class Mail{
    public static function getContent($data){
        $body = '<div style="font-family:Arial,宋体;font-size:12px;">';
        $body .= '<p>尊敬的用户'.$data['nickName'].':</p>';
        $body .= '<p>您于'.date('Y年m月d日 H时i分s秒',strtotime($data['cTime'])).'的申请单‘'.$data['number'].'’已经审核通过</p>';
        $body .= '<p>请尽快去 【我的尊贷】-【借款管理】- 确认订单<a target="_blank" href="'.str_replace('admin','www',Yii::app()->request->hostInfo).'/ucenter/debit/s/3">'.$data['number'].'</a></p>';
        $body .= '<p>尊贷网</p>';
        $body .= '</div>';
        return $body;
    }
    public static function sendMail($sendInfo=array(),$type=''){
        if($type == ''){
            if(!isset($sendInfo['Subject']) || !isset($sendInfo['Body'])){
                $mailResult['status'] = 'fail';
                $mailResult['msg'] = "您未使用模板发送，请填写邮件主题和内容再发送！";
                return $mailResult;
                exit();
            }
        }else{
            if(!isset($sendInfo['typeInfo']) || empty($sendInfo['typeInfo'])){
                $mailResult['status'] = 'fail';
                $mailResult['msg'] = "您使用模板发送邮件，请输入参数！";
                return $mailResult;
                exit();
            }else{
                $result = Mail::getSubjectAndBody($type,$sendInfo['typeInfo']);
                if(!empty($result)){
                    $sendInfo['Subject'] = $result['Subject'];
                    $sendInfo['Body'] = $result['Body'];
                }else{
                    $mailResult['status'] = 'fail';
                    $mailResult['msg'] = "调用模板失败，请输入完整的参数！";
                    return $mailResult;
                    exit();
                }
            }
        }
        //实例化
        $mail = new PHPMailer();
        //赋予变量默认值
        if(!isset($sendInfo['Username'])){
            $sendInfo['Username'] = 'noreply@zundai.com';
        }
        $fromName = array();
        $fromName = explode("@",$sendInfo['Username']);
        if(!isset($sendInfo['Password'])){
            $sendInfo['Password'] = 'zundai520';
        }
        if(!isset($sendInfo['CharSet'])){
            $sendInfo['CharSet'] = 'utf-8';
        }
        if(!isset($sendInfo['Port'])){
            $sendInfo['Port'] = 25;
        }
        if(!isset($sendInfo['Host'])){
            $host = trim($fromName[1]);
            if($host == 'zundai.com'){
                $host = 'qq.com';
            }/* else if($host == 'zundai.com'){
                $host = 'exmail.qq.com';
            } */
            $sendInfo['Host'] = "smtp.".$host;
        }
        if(!isset($sendInfo['From'])){
            $sendInfo['From'] = $sendInfo['Username'];
        }

        $sendInfo['FromName']="尊贷网".$sendInfo['UserName'];

        if(!isset($sendInfo['FromName'])){
            $fromMail = array();
            $fromMail = explode("@",$sendInfo['From']);
            $sendInfo['FromName'] = $fromMail[0];
        }

        //是否通过SMTP协议发送
        $mail -> ISSMTP();
        //SMTP服务器是否需要验证(验证为true 不验证为false)
        $mail -> SMTPAuth = true;
        //设置用户名
        $mail -> Username = $sendInfo['Username'];
        //设置密码
        $mail -> Password = $sendInfo['Password'];
        //设置编码
        $mail -> CharSet = $sendInfo['CharSet'];
        //设置端口
        $mail -> Port = $sendInfo['Port'];
        //设置主机服务器
        $mail -> Host = $sendInfo['Host'];
        //发件人地址
        $mail -> From = $sendInfo['From'];
        //发件人
        $mail -> FromName = $sendInfo['FromName'];
        //是否以HTML格式发送
        $mail -> IsHTML(true);
        //主题
        $mail -> Subject = $sendInfo['Subject'];
        //内容
        $mail -> Body = $sendInfo['Body'];
        //添加附件
        if(isset($sendInfo['AddAttachment'])){
            if(is_array($sendInfo['AddAttachment'])){
                foreach($sendInfo['AddAttachment'] as $attach){
                    if(isset($attach['name'])){
                        $mail -> AddAttachment($attach['attach'],$attach['name']);
                    }else{
                        $mail -> AddAttachment($attach['attach']);
                    }
                }
            }
        }
        //调用回复方法,添加回复对象
        if(isset($sendInfo['AddReplyTo'])){
            $mail -> AddReplyTo($sendInfo['AddReplyTo']);
        }
        //添加收件人，支持群发
        $success = true;
        if(isset($sendInfo['To'])){
            foreach($sendInfo['To'] as $addr){
                if(isset($addr['name'])){
                    $mail -> AddAddress($addr['email'],$addr['name']);
                }else{
                    $email = array();
                    $email = explode("@",$addr['email']);
                    $mail -> AddAddress($addr['email'],$email[0]);
                }
            }
        }
        //密送
        if(isset($sendInfo['AddBCC'])){
            foreach($sendInfo['AddBCC'] as $addrB){
                if(isset($addrB['name'])){
                    $mail -> AddBCC($addrB['email'],$addrB['name']);
                }else{
                    $email = array();
                    $email = explode("@",$addrB['email']);
                    $mail -> AddBCC($addrB['email'],$email[0]);
                }
            }
        }
        //抄送
        if(isset($sendInfo['AddCC'])){
            foreach($sendInfo['AddCC'] as $addrC){
                if(isset($addrC['name'])){
                    $mail -> AddCC($addrC['email'],$addrC['name']);
                }else{
                    $email = array();
                    $email = explode("@",$addrC['email']);
                    $mail -> AddCC($addrC['email'],$email[0]);
                }
            }

        }
        $error = array();
        if (!$mail->Send()){
            $success = false;
            $error[] = "发送失败，原因：".$mail->ErrorInfo;
        }
        $mail -> ClearAddresses();
        if(!isset($sendInfo['To']) && !isset($sendInfo['AddCC']) && !isset($sendInfo['AddBCC']) ){
            $mail -> AddAddress('tanweiwei@meiti.com','tanweiwei');
            if (!$mail->Send()){
                $success = false;
                $error[] = "tanweiwei@meiti.com发送失败，原因：".$mail->ErrorInfo;
            }
        }
        $mailResult = array();
        if($success){
            $mailResult['status'] = 'success';
            $mailResult['msg'] = '发送邮件成功！';
        }else{
            $mailResult['status'] = 'fail';
            if(!empty($error)){
                $mailResult['msg'] = $error;
            }else{
                $mailResult['msg'] = '发送邮件失败！';
            }

        }
        return $mailResult;

    }

    //配置不同模板的邮件
    public static function getSubjectAndBody($type,$content){
        if(!empty($content) && isset($type)){
            switch($type){
                case 'sms':
                    if(!isset($content['connectEmail'])){
                        $content['connectEmail'] = 'tanweiwei@meiti.com';
                    }
                    if(!isset($content['success'])){
                        $content['success'] = '0';
                    }
                    if(!isset($content['subject'])){
                        if($content['success'] == '0'){
                            $content['subject'] = '注意：短信发送失败！';
                        }else{
                            $content['subject'] = '短信发送成功！';
                        }
                    }
                    $result['Subject'] = $content['subject'];
                    $body = '';
                    $body .= '<html><div style="font-family:Arial,宋体;font-size:12px;">';
                    $body .= '<div>';
                    if($content['success']){
                        $body .= '<p>短信发送成功，共发送'.$content['totalNum'].'个号码'.$content['num'].'条短信，短信余额为'.$content['money'].'！发送内容如下：</p>';
                    }else{
                        $body .= '<p>由于<span style="color:red">'.$content['reason'].'</span>原因，'.$content['totalNum'].'个号码'.$content['num'].'条短信发送失败！发送内容如下：</p>';
                    }
                    $body .= '<p>'.$content['cont'].'</p>';
                    $body .= '<p>发送手机号码如下：</p>';
                    if(is_array($content['mobile'])){
                        foreach($content['mobile'] as $mobile){
                            $body .= '<p>'.$mobile.'</p>';
                        }
                    }else{
                        $body .= '<p>'.$content['mobile'].'</p>';
                    }

                    $body .= '<p style="color:rgb(51, 51, 51);text-align:right">尊贷网自动发送，任何问题请联系<a href="mailto:'.$content['connectEmail'].'">'.$content['connectEmail'].'</a></p>';
                    $body .= '</div>';
                    $body .= '</div></html>';
                    $result['Body'] = $body;
                    return $result;
                    break;
                case 'zd_yxyz':
                    if(!isset($content['url']) || !isset($content['username'])){
                        return array();
                    }
                    $result['Subject'] = '尊贷网-邮箱验证';
                    $body = '';
                    $body .= '<html><div style="font-family:Arial,宋体;font-size:12px;">';
                    $body .= '<div>';
                    $body .= "<p>尊敬的用户{$content['username']}：</p>";
                    $body .= '<p>您于 '.date('Y年m月d日 H时i分s秒').' 申请验证邮箱，点击以下链接，即可完成安全验证：<a href="'.$content['url'].'" target="_blank">'.$content['url'].'</a></p>';
                    $body .= '<p>您也可以将链接复制到浏览器地址栏访问。</p>';
                    $body .= '<p>为保障您的帐号安全，请在24小时内点击该链接。若您没有申请过验证邮箱 ，请您忽略此邮件 ，由此给您带来的不便请谅解。</p>';
                    $body .= '<p>尊贷网</p>';
                    $body .= '</div>';
                    $body .= '</html>';
                    $result['Body'] = $body;
                    return $result;
                    break;
                case 'zd_wjmm':
                    if(!isset($content['code']) || !isset($content['username'])){
                        return array();
                    }
                    $result['Subject'] = '尊贷网-密码找回';
                    $body = '';
                    $body .= '<html><div style="font-family:Arial,宋体;font-size:12px;">';
                    $body .= '<div>';
                    $body .= "<p>尊敬的用户{$content['username']}：</p>";
                    $body .= '<p>您好，您在人人贷网站申请找回密码操作的验证码为:'.$content['code'].'</p>';
                    $body .= '<p>为保障您的帐号安全性，验证码有效期为60分钟，验证成功后自动失效。</p>';
                    $body .= '<p>尊贷网</p>';
                    $body .= '</div>';
                    $body .= '</html>';
                    $result['Body'] = $body;
                    return $result;
                    break;
                case 'zd_ymrz':
                    //域名认证
                    if(!isset($content['url']) || !isset($content['username'])){
                        return array();
                    }
                    $result['Subject'] = '尊贷网-域名贷款验证';
                    $body = '';
                    $body .= '<html><div style="font-family:Arial,宋体;font-size:12px;">';
                    $body .= '<div>';
                    $body .= '<p>您于 '.date('Y年m月d日 H时i分s秒').' 申请域名认证邮箱，点击以下链接，即可完成域名认证：<a href="'.$content['url'].'" target="_blank">'.$content['url'].'</a></p>';
                    $body .= '<p>您也可以将链接复制到浏览器地址栏访问。</p>';
                    $body .= '<p>为保障您的帐号安全，请在24小时内点击该链接。若您没有申请过验证邮箱 ，请您忽略此邮件 ，由此给您带来的不便请谅解。</p>';
                    $body .= '<p>尊贷网</p>';
                    $body .= '</div>';
                    $body .= '</html>';
                    $result['Body'] = $body;
                    return $result;
                    break;

                case 'zd_xyedsp':
                    //授信成功
                    $result['Subject'] = '尊贷网-授信成功';
                    $body = '';
                    $body .= '<html>';
                    $body .= '<div>';
                    $body .= "<p style='text-indent:0px;'>尊敬的{$content['userName']}用户：</p>";
                    $body .= "<p style='text-indent:24px;'>您好，恭喜您通过尊贷网的授信，授信额度为 {$content['limit']}元，您可以通过【我的尊贷】查看相关信息。感谢您对我们的关注和支持！</p>";
                    $body .= '<p style="float: right;">尊贷网</p>';
                    $body .= '</div>';
                    $body .= '</html>';
                    $result['Body'] = $body;
                    return $result;
                    break;

                case 'zd_hyedqr':
                    //额度审核成功
                    $result['Subject'] = '尊贷网-额度审核成功';
                    $body = '';
                    $body .= '<html>';
                    $body .= '<div>';
                    $body .= "<p style='text-indent:0px;'>尊敬的{$content['userName']}用户：</p>";
                    $body .= '<p style="text-indent:24px;">您好， 您的借款申请 (单号：<a href="'.$content['baseUrl'].'/ucenter/debit/s/3">'.$content['number'].'</a>) 已通过审核，请您及时通过【我的尊贷】-<a href="'.$content['baseUrl'].'/ucenter/debit">【借款管理】</a>确认借款。感谢您对我们的关注和支持！';
                    $body .= '<p style="float: right;">尊贷网</p>';
                    $body .= '</div>';
                    $body .= '</html>';
                    $result['Body'] = $body;
                    return $result;
                    break;

                case 'zd_sztb':
                    //开启发标
                    $result['Subject'] = '尊贷网-投标提醒';
                    $body = '';
                    $body .= '<html>';
                    $body .= '<div>';
                    $body .= "<p style='text-indent:0px;'>尊敬的{$content['userName']}用户：</p>";
                    $body .= '<p style="text-indent:24px;">您好，您的借款申请(单号：<a href="'.$content['baseUrl'].'/ucenter/debit/s/3">'.$content['number'].'</a>)已通过审核，发标时间于'.$content['dateTime'].'开启，您可以登陆尊贷网，通过【我要理财】-<a href="'.$content['baseUrl'].'/tender/list">【借款列表】</a>关注投标进程，谢谢。<p>';
                    $body .= '<p style="float: right;">尊贷网</p>';
                    $body .= '</div>';
                    $body .= '</html>';
                    $result['Body'] = $body;
                    return $result;
                    break;

                case 'zd_mbtx':
                    //满标提醒
                    $result['Subject'] = '尊贷网-满标提醒';
                    $body = '';
                    $body .= '<html>';
                    $body .= '<div>';
                    $body .= "<p style='text-indent:0px;'>尊敬的{$content['userName']}用户：</p>";
                    $body .= '<p style="text-indent:24px;">您好，您的借款申请 (单号： <a href="'.$content['baseUrl'].'/ucenter/debit/s/3">'.$content['number'].'</a>) 于'.$content['dateTime'].'满标，如放款审核无误，您的贷款资金将会于2个工作日内到达您的尊贷账户。<p>';
                    $body .= '<p style="text-indent:24px;">点击<a href="'.$content['baseUrl'].'/ucenter/index">【我的尊贷】</a>查看您的账户信息及相关交易记录。</p>';
                    $body .= '<p style="text-indent:24px;">点击【我的尊贷】-<a href="'.$content['baseUrl'].'/ucenter/debit/s/3">【借款管理】</a>查看您的理财详细信息。</p>';
                    $body .='<p style="text-indent:24px;">感谢您对我们的关注和支持！</p>';
                    $body .= '<p style="float: right;">尊贷网</p>';
                    $body .= '</div>';
                    $body .= '</html>';
                    $result['Body'] = $body;
                    return $result;
                    break;

                case 'zd_cwfk':
                    //放款成功
                    $result['Subject'] = '尊贷网-放款成功';
                    $body = '';
                    $body .= '<html>';
                    $body .= '<div>';
                    $body .= "<p style='text-indent:0px;'>尊敬的{$content['userName']}用户：</p>";
                    $body .= '<p style="text-indent:24px;">您好，恭喜您，您的借款 (单号： <a href="'.$content['baseUrl'].'/ucenter/debit/s/3">'.$content['number'].'</a>) 已通过放款审核，款项已发放到您的尊贷账户。</p>';
                    $body .= '<p style="text-indent:24px;">点击<a href="'.$content['baseUrl'].'/ucenter/index">【我的尊贷】</a>查看您的账户信息及相关交易记录。</p>';
                    $body .= '<p style="text-indent:24px;">点击【我的尊贷】-<a href="'.$content['baseUrl'].'/ucenter/debit/s/6">【借款管理】</a>查看您的借款详细信息。</p>';
                    $body .= '<p style="text-indent:24px;">感谢您对我们的关注和支持！</p>';
                    $body .= '<p style="float: right;">尊贷网</p>';
                    $body .= '</div>';
                    $body .= '</html>';
                    $result['Body'] = $body;
                    return $result;
                    break;

                case 'zd_txcg':
                    //提现成功
                    $result['Subject'] = '尊贷网-提现成功';
                    $body = '';
                    $body .= '<html>';
                    $body .= '<div>';
                    $body .= "<p style='text-indent:0px;'>尊敬的{$content['userName']}用户：</p>";
                    $body .= '<p style="text-indent:24px;">您好，您于'.$content['dateTime'].'提交的'.$content['cash'].'元提现申请已审核成功，预计打款时间'.$content['payTime'].'，请您注意查收，您可点击<a href="'.$content['baseUrl'].'/ucenter/index">【我的尊贷】</a>查看您的交易记录。</p>';
                    $body .= '<p style="text-indent:24px;">感谢您对我们的关注和支持！</p>';
                    $body .= '<p style="float: right;">尊贷网</p>';
                    $body .= '</div>';
                    $body .= '</html>';
                    $result['Body'] = $body;
                    return $result;
                    break;

                case 'zd_yq':
                    //逾期
                    $result['Subject'] = '尊贷网-逾期还款提醒';
                    $body = '';
                    $body .= '<html>';
                    $body .= '<div>';
                    $body .= "<p style='text-indent:0px;'>尊敬的{$content['userName']}用户：</p>";
                    $body .= '<p style="text-indent:24px;">您好，您有一笔借款 (单号：<a href="'.$content['baseUrl'].'/ucenter/debit/s/yuqi">'.$content['number'].'</a>) 本于'.$content['dateTime'].'完成还款，现已逾期，请及时完成还款操作，以免给您带来不必要的损失。</p>';
                    $body .= '<p>感谢您对我们的关注和支持！</p>';
                    $body .= '<p style="float: right;">尊贷网</p>';
                    $body .= '</div>';
                    $body .= '</html>';
                    $result['Body'] = $body;
                    return $result;
                    break;

                case 'zd_ld':
                    //流单
                    $result['Subject'] = '尊贷网-流单';
                    $body = '';
                    $body .= '<html>';
                    $body .= '<div>';
                    $body .= "<p style='text-indent:0px;'>尊敬的{$content['userName']}用户：</p>";
                    $body .= '<p style="text-indent:24px;">您好，很遗憾的通知您，您于'.$content['dateTime'].'提交的借款申请(单号：'.$content['number'].')已作流单处理.感谢您对我们的关注和支持！</p>';
                    $body .= '<p>感谢您对我们的关注和支持！</p>';
                    $body .= '<p style="float: right;">尊贷网</p>';
                    $body .= '</div>';
                    $body .= '</html>';
                    $result['Body'] = $body;
                    return $result;
                    break;
                    
                case 'zd_ldlcr':
                    //流单-理财人
                    $result['Subject'] = '尊贷网-流单';
                    $body = '';
                    $body .= '<html>';
                    $body .= '<div>';
                    $body .= "<p style='text-indent:0px;'>尊敬的{$content['userName']}用户：</p>";
                    $body .= '<p style="text-indent:24px;">您好，很遗憾的通知您，您投放的标(单号：'.$content['number'].')，已作流单处理。您的'.$content['cash'].'元理财已经退还到您的尊贷账户。感谢您对我们的关注和支持！</p>';
                    $body .= '<p style="float: right;">尊贷网</p>';
                    $body .= '</div>';
                    $body .= '</html>';
                    $result['Body'] = $body;
                    return $result;
                    break;

                case 'zd_hkdd':
                    //回款到达
                    $result['Subject'] = '尊贷网-回款到账';
                    $body = '';
                    $body .= '<html>';
                    $body .= '<div>';
                    $body .= "<p style='text-indent:0px;'>尊敬的{$content['userName']}用户：</p>";
                    $body .= '<p style="text-indent:24px;">您好，您有一笔理财（单号：'.$content['number'].'）已于'.$content['dateTime'].'到账，您可通过【我的尊贷】-<a href="'.$content['baseUrl'].'/ucenter/tender">【理财管理】</a>查看详细信息。</p>';
                    $body .= '<p style="text-indent:24px;">点击<a href="'.$content['baseUrl'].'/ucenter/index">【我的尊贷】</a>查看您的账户信息及相关交易记录。</p>';
                    $body .= '<p style="text-indent:24px;">感谢您对我们的关注和支持！</p>';
                    $body .= '<p style="float: right;">尊贷网</p>';
                    $body .= '</div>';
                    $body .= '</html>';
                    $result['Body'] = $body;
                    return $result;
                    break;

                case 'zd_mbtx_tender':
                    //满标提醒-理财人
                    $result['Subject'] = '尊贷网-满标提醒';
                    $body = '';
                    $body .= '<html>';
                    $body .= '<div>';
                    $body .= "<p style='text-indent:0px;'>尊敬的{$content['userName']}用户：</p>";
                    $body .= '<p style="text-indent:24px;">您好，您投放的标（单号：'.$content['number'].'），现已满标，待放款审核成功之后，您的'.$content['cash'].'元理财即可转入借款人账户。</p>';
                    $body .= '<p style="text-indent:24px;">点击<a href="'.$content['baseUrl'].'/ucenter/index">【我的尊贷】</a>查看您的账户信息及相关交易记录。</p>';
                    $body .= '<p style="text-indent:24px;">点击【我的尊贷】-<a href="'.$content['baseUrl'].'/ucenter/tender/s/3">【理财管理】</a>查看您的理财详细信息。</p>';
                    $body .= '<p style="text-indent:24px;">感谢您对我们的关注和支持！</p>';
                    $body .= '<p style="float: right;">尊贷网</p>';
                    $body .= '</div>';
                    $body .= '</html>';
                    $result['Body'] = $body;
                    return $result;
                    break;

                case 'zd_cwfk_tender':
                    //放款成功-理财人
                    $result['Subject'] = '尊贷网-放款成功';
                    $body = '';
                    $body .= '<html>';
                    $body .= '<div>';
                    $body .= "<p style='text-indent:0px;'>尊敬的{$content['userName']}用户：</p>";
                    $body .= '<p style="text-indent:24px;">感谢您对理财（单号：'.$content['number'].'）的帮助与支持，由于该借款已成功放款，您的'.$content['cash'].'元理财已成功转入借款人账户。</p>';
                    $body .= '<p style="text-indent:24px;">点击<a href="'.$content['baseUrl'].'/ucenter/index">【我的尊贷】</a>查看您的账户信息及相关交易记录。</p>';
                    $body .= '<p style="text-indent:24px;">点击【我的尊贷】-<a href="'.$content['baseUrl'].'/ucenter/tender/s/6">【理财管理】</a>查看您的理财详细信息。</p>';
                    $body .= '<p style="text-indent:24px;">感谢您对我们的关注和支持！</p>';
                    $body .= '<p style="float: right;">尊贷网</p>';
                    $body .= '</div>';
                    $body .= '</html>';
                    $result['Body'] = $body;
                    return $result;
                    break;


                case 'zd_tqck':
                    //提前催款
                    $result['Subject'] = '尊贷网-预还款通知';
                    $body = '';
                    $body .= '<html>';
                    $body .= '<div>';
                    $body .= "<p style='text-indent:0px;'>尊敬的{$content['userName']}用户：</p>";
                    $body .= '<p style="text-indent:24px;">您好，您有一笔还款（单号：<a href="'.$content['baseUrl'].'/ucenter/debit/s/6">'.$content['number'].'</a>）需于'.$content['dateTime'].'前完成还款，请及时完成还款操作。</p>';
                    $body .= '<p style="text-indent:24px;">点击【我的尊贷】-<a href="'.$content['baseUrl'].'/ucenter/debit">【借款管理】</a>查看您的还款详细信息。</p>';
                    $body .= '<p style="text-indent:24px;">感谢您对我们的关注和支持！</p>';
                    $body .= '<p style="float: right;">尊贷网</p>';
                    $body .= '</div>';
                    $body .= '</html>';
                    $result['Body'] = $body;
                    return $result;
                    break;

                case 'zd_txbh':
                    //提现驳回
                    $result['Subject'] = '尊贷网-提现驳回通知';
                    $body = '';
                    $body .= '<html>';
                    $body .= '<div>';
                    $body .= "<p style='text-indent:0px;'>尊敬的{$content['userName']}用户：</p>";
                    $body .= '<p style="text-indent:24px;">您好，很抱歉地通知您，您于'.date("Y年m月d日 H时i分s秒",strtotime($content['cTime'])).'的提现申请单‘'.$content['orderNumber'].'’因为银行账户信息有误，被驳回，请您核实并修改银行信息，修改完之后可重新申请提现。</p>';
                    $body .= '<p style="text-indent:24px;">点击【我的尊贷】-<a href="'.$content['baseUrl'].'/accessCash/rechargeOnline">【充值提现】</a>-<a href="'.$content['baseUrl'].'/bankInfo/bankList">【银行卡管理】</a>修改银行卡信息。</p>';
                    $body .= '<p style="text-indent:24px;">感谢您对我们的关注和支持！</p>';
                    $body .= '<p style="float: right;">尊贷网</p>';
                    $body .= '</div>';
                    $body .= '</html>';
                    $result['Body'] = $body;
                    return $result;
                    break;

                case 'zd_diy':
                    $result['Subject'] = $content['title'];
                    $body = '';
                    $body .= '<html>';
                    $body .= '<div>';
                    $body .= "<p  style='text-indent: 24px;'>{$content['content']}</p>";
                    $body .= '</div>';
                    $body .= '</html>';
                    $result['Body'] = $body;
                    return $result;
                    break;
            }
        }else{
            return array();
        }
    }

}
?>