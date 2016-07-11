<?php
Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl . "/css/jquery-ui-1.10.3.full.min.css");
Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl . "/css/chosen.css");
Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl . "/css/datepicker.css");
Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl . "/css/bootstrap-timepicker.css");
Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl . "/css/daterangepicker.css");
Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl . "/css/ui.jqgrid.css");
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . "/js/jquery-ui-1.10.3.custom.min.js", CClientScript::POS_END);
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . "/js/jquery-ui-1.10.3.full.min.js", CClientScript::POS_END);
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . "/js/jqGrid/i18n/grid.locale-en.js", CClientScript::POS_END);
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . "/js/jquery.ui.touch-punch.min.js", CClientScript::POS_END);
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . "/js/chosen.jquery.min.js", CClientScript::POS_END);
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . "/js/fuelux/fuelux.spinner.min.js", CClientScript::POS_END);
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . "/js/date-time/bootstrap-datepicker.min.js", CClientScript::POS_END);
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . "/js/date-time/bootstrap-timepicker.min.js", CClientScript::POS_END);
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . "/js/date-time/moment.min.js", CClientScript::POS_END);
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . "/js/date-time/daterangepicker.min.js", CClientScript::POS_END);
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . "/js/jqGrid/jquery.jqGrid.min.js", CClientScript::POS_END);
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . "/js/jqGrid/i18n/grid.locale-en.js", CClientScript::POS_END);
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . "/js/jquery.knob.min.js", CClientScript::POS_END);
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . "/js/jquery.autosize.min.js", CClientScript::POS_END);
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . "/js/jquery.inputlimiter.1.3.1.min.js", CClientScript::POS_END);
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . "/js/bootstrap-tag.min.js", CClientScript::POS_END);
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . "/js/jquery.form.js", CClientScript::POS_END);
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . "/js/Validform_v5.3.2_min.js", CClientScript::POS_END);
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . "/js/buy/order-list.js", CClientScript::POS_END);
?>
<div class="page-header">
    <div class="widget-box">
        <div class="widget-header">
            <h4>
                <i class="icon-hand-right green"></i>
                Search Form
            </h4>
        </div>
        <div class="widget-body">
            <div class="widget-main">
                <form class="form-inline" id="order-list" action="/buy/orderList" style="line-height:45px">
                    <!-- 时间选择范围 -->
                    <div class="form-group">
                        <div class="input-group" style="float:left;">
                            <span class="input-group-addon">
                                <i class="icon-calendar bigger-110"></i>
                            </span>
                            <input class="form-control" type="text" id="id-date-range-picker" value="<?php echo!empty($_GET['orderList']['dateStart']) && !empty($_GET['orderList']['dateEnd']) ? $_GET['orderList']['dateStart'] . ' - ' . $_GET['orderList']['dateEnd'] : '' ?>" placeholder="起止时间" style="width:180px"/>
                        </div>
                        <input type="hidden" id="date_start" name="orderList[dateStart]" value="">
                        <input type="hidden" id="date_end" name="orderList[dateEnd]" value="">
                    </div>    

                    <!-- 订单状态选择 -->
                    <div class="form-group">
                        <?php foreach (Order::$orderStatus as $key => $val) { ?>
                            <label class="checkbox inline">
                                <input class="ace" type="checkbox" name="orderList[checkStatus][]" value="<?php echo $key; ?>" <?php echo!empty($_GET['orderList']['checkStatus']) && in_array($key, $_GET['orderList']['checkStatus']) ? 'checked="checked"' : ''; ?>>
                                <span class="lbl">&nbsp;<?php echo $val; ?>&nbsp;</span>
                            </label>
                        <?php } ?>
                    </div>

                    <!-- 按供应商 -->
                    <!--                    <div class="form-group">
                                            <select class="chosen-select" id="form-field-select-3" data-placeholder="选择供应商" style="width:180px" name="orderList[suppliers]">
                                                <option value="-1">选择供应商</option>
                    <?php
                    $allSuppliers = Suppliers::model()->findAll('is_check = 1');
                    foreach ($allSuppliers as $key => $val) {
                        ?>
                                                            <option value="<?php echo $val['suppliers_id']; ?>" <?php echo!empty($_GET['orderList']['suppliers']) && $_GET['orderList']['suppliers'] == $key ? 'selected="selected"' : ''; ?>><?php echo $val['suppliers_name']; ?></option>
                    <?php } ?>
                                            </select>
                                        </div>
                    
                                         按品牌 
                                        <div class="form-group">
                                            <select class="chosen-select" id="form-field-select-3" data-placeholder="选择品牌" style="width:180px" name="orderList[brand]">
                                                <option value="-1">选择品牌</option>
                    <?php
                    $allBrand = Brand::model()->findAll();
                    foreach ($allBrand as $key => $val) {
                        ?>
                                                            <option value="<?php echo $val['brand_id']; ?>" <?php echo!empty($_GET['orderList']['brand']) && $_GET['orderList']['brand'] == $key ? 'selected="selected"' : ''; ?>><?php echo $val['brand_name']; ?></option>
                    <?php } ?>
                                            </select>
                                        </div>-->

                    <!-- 按条件查询 -->
                    <div class="form-group">
                        <select class="form-control" id="form-field-select-1" name="orderList[checkType]">
                            <option value="username" <?php echo!empty($_GET['orderList']['checkType']) && $_GET['orderList']['checkType'] == 'username' ? 'selected="selected"' : ''; ?>>按用户名</option>
                            <option value="ordersn" <?php echo!empty($_GET['orderList']['checkType']) && $_GET['orderList']['checkType'] == 'ordersn' ? 'selected="selected"' : ''; ?>>按订单号</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <input id="form-field-1" class="col-xs-10 col-sm-5 input-medium" type="text" name="orderList[condition]" placeholder="条件" value="<?php echo!empty($_GET['orderList']['condition']) ? $_GET['orderList']['condition'] : ''; ?>" style="line-height: 22px; height: 34px;">
                    </div>
                    <span class="input-group-btn inline">
                        <button class="btn btn-purple btn-sm" type="submit" id="submit-order-list">
                            Search
                            <i class="icon-search icon-on-right bigger-110"></i>
                        </button>
                    </span>
                </form>
            </div>
        </div>
    </div>
</div><!-- /.page-header -->
<div class="table-header">
    查询订单列表
    <button class="btn btn-minier btn-yellow" id="buy-from-supplier">按供应商采购</button>
    <button class="btn btn-minier btn-yellow" id="buy-from-order">按订单采购</button>
</div>
<?php
$this->renderPartial('_ajaxListView', array('dataProvider' => $dataProvider, 'itemView' => '_orderList',));
?> 
<div id="dialog-message" class="hide">
    <div class="alert alert-info bigger-110"></div>
    <div class="space-6"></div>
    <p class="bigger-110 bolder center grey">
        <i class="icon-hand-right blue bigger-120"></i>
        Are you sure?
    </p>
</div><!-- #dialog-confirm -->
<!--<table id="grid-table"></table>
<div id="grid-pager"></div>-->

