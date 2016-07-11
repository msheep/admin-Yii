<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title><?php echo Yii::app()->params['title']; ?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <!-- basic styles -->
        <link href="/css/bootstrap.min.css" rel="stylesheet" />
        <link rel="stylesheet" href="/css/font-awesome.min.css" />
        <!--[if IE 7]>
          <link rel="stylesheet" href="/css/font-awesome-ie7.min.css" />
        <![endif]-->

        <!-- page specific plugin styles -->

        <!-- fonts -->
        <!-- <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Open+Sans:400,300" /> -->

        <!-- ace styles -->
        <link rel="stylesheet" href="/css/ace.min.css" />
        <link rel="stylesheet" href="/css/ace-rtl.min.css" />
        <link rel="stylesheet" href="/css/ace-skins.min.css" />

        <!--[if lte IE 8]>
          <link rel="stylesheet" href="/css/ace-ie.min.css" />
        <![endif]-->
    </head>

    <body>
        <?php $this->beginContent('//layouts/adminTopLayout'); ?>
        <?php $this->endContent(); ?>
        <div class="main-container" id="main-container">
            <script type="text/javascript">
                try {
                ace.settings.check('main-container', 'fixed')
                } catch (e) {
                }
            </script>

            <div class="main-container-inner">
                <?php $this->beginContent('//layouts/adminLeftLayout'); ?>
                <?php $this->endContent(); ?>
                <div class="main-content">
                    <?php $this->beginContent('//layouts/adminRightLayout'); ?>
                    <?php echo $content; ?>
                    <?php $this->endContent(); ?>
                </div><!-- /.main-content -->

                <div class="ace-settings-container" id="ace-settings-container">
                    <div class="btn btn-app btn-xs btn-warning ace-settings-btn" id="ace-settings-btn">
                        <i class="icon-cog bigger-150"></i>
                    </div>

                    <div class="ace-settings-box" id="ace-settings-box">
                        <div>
                            <div class="pull-left">
                                <select id="skin-colorpicker" class="hide">
                                    <option data-skin="default" value="#438EB9">#438EB9</option>
                                    <option data-skin="skin-1" value="#222A2D">#222A2D</option>
                                    <option data-skin="skin-2" value="#C6487E">#C6487E</option>
                                    <option data-skin="skin-3" value="#D0D0D0">#D0D0D0</option>
                                </select>
                            </div>
                            <span>&nbsp; 选择皮肤</span>
                        </div>

                        <div>
                            <input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-navbar" />
                            <label class="lbl" for="ace-settings-navbar"> 固定导航条</label>
                        </div>

                        <div>
                            <input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-sidebar" />
                            <label class="lbl" for="ace-settings-sidebar"> 固定滑动条</label>
                        </div>

                        <div>
                            <input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-breadcrumbs" />
                            <label class="lbl" for="ace-settings-breadcrumbs">固定面包屑</label>
                        </div>

                        <div>
                            <input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-rtl" />
                            <label class="lbl" for="ace-settings-rtl">切换到左边</label>
                        </div>

                        <div>
                            <input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-add-container" />
                            <label class="lbl" for="ace-settings-add-container">
                                切换窄屏
                                <b></b>
                            </label>
                        </div>
                    </div>
                </div><!-- /#ace-settings-container -->
            </div><!-- /.main-container-inner -->

            <a href="#" id="btn-scroll-up" class="btn-scroll-up btn btn-sm btn-inverse">
                <i class="icon-double-angle-up icon-only bigger-110"></i>
            </a>
        </div><!-- /.main-container -->

        <!-- basic scripts -->

        <!--[if !IE]> -->
        <script src="/js/jquery-2.0.3.min.js"></script>
        <!-- <![endif]-->
        <!--[if IE]>
            <script src="/js/jquery-1.10.2.min.js"></script>
        <![endif]-->

        <!--[if !IE]> -->
        <script type="text/javascript">
            window.jQuery || document.write("<script src='/js/jquery-2.0.3.min.js'>" + "<" + "script>");
        </script>
        <!-- <![endif]-->

        <!--[if IE]>
                <script type="text/javascript">
                 window.jQuery || document.write("<script src='/js/jquery-1.10.2.min.js'>"+"<"+"script>");
                </script>
        <![endif]-->

        <script type="text/javascript">
            if ("ontouchend" in document)
            document.write("<script src='/js/jquery.mobile.custom.min.js'>" + "<" + "script>");
        </script>
        <script src="/js/bootstrap.min.js"></script>

        <!-- ace settings handler -->
        <script src="/js/ace-extra.min.js"></script>

        <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
        <script src="/js/html5shiv.js"></script>
        <script src="/js/respond.min.js"></script>
        <![endif]-->

        <script src="/js/typeahead-bs2.min.js"></script>

        <!-- page specific plugin scripts -->

        <!--[if lte IE 8]>
          <script src="/js/excanvas.min.js"></script>
        <![endif]-->

        <script src="/js/jquery-ui-1.10.3.custom.min.js"></script>
        <script src="/js/jquery.ui.touch-punch.min.js"></script>
        <script src="/js/jquery.slimscroll.min.js"></script>
        <script src="/js/jquery.easy-pie-chart.min.js"></script>
        <script src="/js/jquery.sparkline.min.js"></script>
        <script src="/js/flot/jquery.flot.min.js"></script>
        <script src="/js/flot/jquery.flot.pie.min.js"></script>
        <script src="/js/flot/jquery.flot.resize.min.js"></script>

        <!-- ace scripts -->
        <script src="/js/ace-elements.min.js"></script>
        <script src="/js/ace.min.js"></script>

        <!-- inline scripts related to this page -->
        <script type="text/javascript">
            jQuery(function($) {
            var placeholder = $('#piechart-placeholder').css({'width': '90%', 'min-height': '150px'});
            var data = [
            {label: "social networks", data: 38.7, color: "#68BC31"},
            {label: "search engines", data: 24.5, color: "#2091CF"},
            {label: "ad campaigns", data: 8.2, color: "#AF4E96"},
            {label: "direct traffic", data: 18.6, color: "#DA5430"},
            {label: "other", data: 10, color: "#FEE074"}
            ]

            })
        </script>
    </body>
</html>

