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
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . "/js/buy/buy-list.js", CClientScript::POS_END);
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
                <form class="form-inline" id="order-list" action="/buy/buyList" style="line-height:45px">
                    <!-- 时间选择范围 -->
                    <div class="form-group">
                        <div class="input-group" style="float:left;">
                            <span class="input-group-addon">
                                <i class="icon-calendar bigger-110"></i>
                            </span>
                            <input class="form-control" type="text" id="id-date-range-picker" value="<?php echo!empty($_GET['buyList']['dateStart']) && !empty($_GET['buyList']['dateEnd']) ? $_GET['buyList']['dateStart'] . ' - ' . $_GET['buyList']['dateEnd'] : '' ?>" placeholder="起止时间" style="width:180px"/>
                        </div>
                        <input type="hidden" id="date_start" name="buyList[dateStart]" value="">
                        <input type="hidden" id="date_end" name="buyList[dateEnd]" value="">
                    </div>   
                    
                    <!-- 按供应商 -->
                    <div class="form-group">
                        <select class="chosen-select" id="form-field-select-3" data-placeholder="选择供应商" style="width:180px" name="buyList[suppliers]">
                            <option value="-1">选择供应商</option>
                            <?php
                            $allSuppliers = Suppliers::model()->findAll('is_check = 1');
                            foreach ($allSuppliers as $key => $val) {
                                ?>
                                <option value="<?php echo $val['suppliers_id']; ?>" <?php echo!empty($_GET['buyList']['suppliers']) && $_GET['buyList']['suppliers'] == $val['suppliers_id'] ? 'selected="selected"' : ''; ?>><?php echo $val['suppliers_name']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    
                    <!-- 按条件查询 -->
                    <div class="form-group">
                        <select class="form-control" id="form-field-select-1" name="buyList[checkType]">
                            <option value="goodsname" <?php echo!empty($_GET['buyList']['checkType']) && $_GET['buyList']['checkType'] == 'goodsname' ? 'selected="selected"' : ''; ?>>按产品名</option>
                            <option value="ordersn" <?php echo!empty($_GET['buyList']['checkType']) && $_GET['buyList']['checkType'] == 'ordersn' ? 'selected="selected"' : ''; ?>>按订单号</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <input id="form-field-1" class="col-xs-10 col-sm-5 input-medium" type="text" name="buyList[condition]" placeholder="条件" value="<?php echo!empty($_GET['buyList']['condition']) ? $_GET['buyList']['condition'] : ''; ?>" style="line-height: 22px; height: 34px;">
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
    查询采购列表
    <button class="btn btn-minier btn-yellow excel" id="excel-order">导出Excel(网站订单)</button>
    <button class="btn btn-minier btn-yellow excel" id="excel-order-all-info">导出Excel(网站订单详尽版)</button>
    <button class="btn btn-minier btn-yellow excel" id="excel-buy-order">导出Excel(采购单)</button>
</div>
<?php
    if(!empty($data)){
        $this->renderPartial('_buyList', array('dataProvider' => $dataProvider,'data' => $data));
    }else{
        echo '暂无数据';
    }
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

