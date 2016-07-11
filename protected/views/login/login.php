<?php
Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl . "/css/font-awesome.min.css");
Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl . "/css/ace.min.css");
Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl . "/css/ace-rtl.min.css");
?>
<!DOCTYPE html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />	

	<body class="login-layout">
		<div class="main-container">
			<div class="main-content">
				<div class="row">
					<div class="col-sm-10 col-sm-offset-1">
						<div class="login-container">
							<div class="center">
								<h1>
									<i class="icon-leaf green"></i>
									
									<span class="white" style="font-size:24px;">168海淘供应链管理系统</span>
								</h1>
								<!--<h4 class="blue">&copy; Company Name</h4>-->
							</div>

							<div class="space-6"></div>

							<div class="position-relative">
								<div id="login-box" class="login-box visible widget-box no-border">
									<div class="widget-body">
										<div class="widget-main" style="text-align:center;">
											<h4 class="header blue lighter bigger">
												<i class="icon-coffee green"></i>
												请输入您的登录信息
											</h4>

											<div class="space-6"></div>
											<!--loginForm start-->
											<?php $form = $this->beginWidget('CActiveForm',array(
												'id'=>'login-form',
												'enableAjaxValidation'=>true,
											));?>
											<fieldset>
												<div class="row" style="height:50px;">
													<?php echo $form->labelEx($model,'账号'); ?>
													<?php echo $form->textField($model,'username'); ?>
													<?php echo $form->error($model,'username'); ?>
												</div>

												<div class="row" style="height:50px;">
													<?php echo $form->labelEx($model,'密码'); ?>
													<?php echo $form->passwordField($model,'password'); ?>
													<?php echo $form->error($model,'password'); ?>		
												</div>

												<div class="row rememberMe">
													<?php echo $form->checkBox($model,'rememberMe'); ?>
													<?php echo $form->label($model,'rememberMe'); ?>
													<?php echo $form->error($model,'rememberMe'); ?>
												</div>

												<div class="row submit">
													<?php echo CHtml::submitButton('登录'); ?>
												</div>													
											</fieldset>
											<?php $this->endWidget(); ?>	
											<!--loginForm end-->									

											
											
										</div><!-- /widget-main -->										
									</div><!-- /widget-body -->
								</div><!-- /login-box -->

								

								
							</div><!-- /position-relative -->
						</div>
					</div><!-- /.col -->
				</div><!-- /.row -->
			</div>
		</div><!-- /.main-container -->

		<!-- basic scripts -->

		<!--[if !IE]> -->

		<script src="http://ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>

		<!-- <![endif]-->

		<!--[if IE]>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<![endif]-->

		<!--[if !IE]> -->

		<script type="text/javascript">
			window.jQuery || document.write("<script src='assets/js/jquery-2.0.3.min.js'>"+"<"+"/script>");
		</script>

		<!-- <![endif]-->

		<!--[if IE]>
<script type="text/javascript">
 window.jQuery || document.write("<script src='assets/js/jquery-1.10.2.min.js'>"+"<"+"/script>");
</script>
<![endif]-->

		<script type="text/javascript">
			if("ontouchend" in document) document.write("<script src='assets/js/jquery.mobile.custom.min.js'>"+"<"+"/script>");
		</script>

		<!-- inline scripts related to this page -->

		<script type="text/javascript">
			function show_box(id) {
			 jQuery('.widget-box.visible').removeClass('visible');
			 jQuery('#'+id).addClass('visible');
			}
		</script>
	<div style="display:none"><script src='http://v7.cnzz.com/stat.php?id=155540&web_id=155540' language='JavaScript' charset='gb2312'></script></div>
</body>
</html>
