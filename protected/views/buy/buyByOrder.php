<?php
Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl . "/css/jquery-ui-1.10.3.full.min.css");
Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl . "/css/chosen.css");
Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl . "/css/datepicker.css");
Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl . "/css/daterangepicker.css");
Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl . "/css/ui.jqgrid.css");
Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl . "/css/docs.min.css");
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . "/js/jquery-ui-1.10.3.full.min.js", CClientScript::POS_END);
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . "/js/typeahead-bs2.min.js", CClientScript::POS_END);
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . "/js/jquery.nestable.min.js", CClientScript::POS_END);
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . "/js/yaya-template.js", CClientScript::POS_END);
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . "/js/chosen.jquery.min.js", CClientScript::POS_END);
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . "/js/buy/order-buy-order.js", CClientScript::POS_END);
?>
<div class="page-content">
    <div class="page-header">
        <h1>
            采购单
            <small>

                &nbsp;<i class="normal-icon icon-eye-open green bigger-130"></i>
                当前汇率：<?php echo Yii::app()->params['exchangeTax']; ?>
            </small>
        </h1>
    </div><!-- /.page-header -->
    <div class="row">
        <div class="col-xs-12">
            <!-- PAGE CONTENT BEGINS -->
            <div class="row">
                <div>
                    <div class="dd" id="nestable" style="width:100%;max-width:none">
                        <ol class="dd-list">
                            <?php
                            $i = 0;
                            foreach ($goods as $key => $val) {
                                $i++;
                                ?>
                                <li class="dd-item dd-item-father" data-id="<?php echo ($i + 1); ?>" order-id-father="<?php echo $val['order_id'] ?>">
                                    <div class="dd-handle btn-info">
                                        <span class="order-sn">订单<?php echo $val['order_sn']; ?></span>
                                        &nbsp;<span class="badge badge-info">共<?php echo count($val['items']); ?>种</span> 
                                        <span class="text-danger Ordered List Item">
                                            &nbsp;订单总额：￥<?php echo $val['order_amount']; ?>
                                        </span>
                                        <div class="pull-right action-buttons">
                                            <a class="red" href="javascript:" order-id="<?php echo $val['order_id'] ?>" onclick="add_order_number(this);">
                                                <i class="icon-plus-sign bigger-130"></i>
                                            </a>
                                        </div>
                                    </div>
                                    <ol class="dd-list">
                                        <?php
                                        $j = 0;
                                        foreach ($val['items'] as $k => $v) {
                                            $j++;
                                            ?>
                                            <li class="dd-item" data-id="<?php echo ($i + 1); ?><?php echo ($j + 1); ?>" goods-id="<?php echo $v['goods_id']; ?>">
                                                <div class="dd-handle dd2-handle">
                                                    <i class="normal-icon icon-reorder blue bigger-130"  style="line-height:38px"></i>
                                                    <i class="drag-icon icon-move bigger-125"  style="line-height:38px"></i>
                                                </div>
                                                <div class="dd2-content no-hover">
                                                    <a href="<?php echo!empty($v['oversea_url']) ? $v['oversea_url'] : 'javascript:'; ?>" target="_blank"><?php echo $v['goods_name']; ?></a>
                                                    <?php echo $v['goods_attr'] ? '<code>' . $v['goods_attr'] . '</code>' : ''; ?>
                                                    &nbsp;<span class="badge badge-danger"><?php echo $v['goods_number']; ?></span>
                                                    <span class="text-warning Ordered List Item">
                                                        &nbsp;购买价：￥<?php echo $v['goods_price']; ?>
                                                        &nbsp;现价：￥<?php echo $v['new_shop_price']; ?>
                                                        <?php if ($v['seller_note']) { ?>
                                                            &nbsp;备注：<?php echo $v['seller_note']; ?>
                                                        <?php } ?>
                                                    </span>
                                                    <hr style="margin:10px auto"/>
                                                    <input class="input-small goods-id"  type="hidden" mame="goods-id" value="<?php echo $v['goods_id']; ?>">
                                                    <input class="input-small order-id"  type="hidden" mame="order-id" value="<?php echo $v['order_id']; ?>">
                                                    商品价格：$<input class="input-small oversea-price"  type="text" mame="oversea-price">
                                                    &nbsp;邮费：$<input class="input-small shipping-fee"  type="text" mame="shipping-fee">
                                                    &nbsp;消费税：$<input class="input-small consumption-tax"  type="text" mame="consumption-tax">
                                                    &nbsp;优惠券：$<input class="input-small use-coupon"  type="text" mame="use-coupon" value="0">
                                                    &nbsp;采购数量：<input class="input-small buy-number"  type="text" mame="buy-number" value="<?php echo $v['goods_number']; ?>">
                                                    &nbsp;海外链接：<textarea class="autosize-transition horizontal-text oversea-url" mame="oversea-url"><?php echo $v['oversea_url']; ?></textarea>
                                                    <br/><br/>
                                                    备 &nbsp; &nbsp;注：<textarea class="autosize-transition horizontal-text buy-note" mame="buy-note"></textarea>                                                  
                                                </div>
                                            </li>
                                        <?php } ?>
                                    </ol>
                                </li>   
                            <?php } ?>
                        </ol>
                    </div>
                </div>
            </div><!-- PAGE CONTENT ENDS -->
        </div><!-- /.col -->
    </div><!-- /.row -->
</div><!-- /.page-content -->

<div id="dialog-message" class="hide">
    <div class="alert alert-info bigger-110">
        <form class="form-horizontal" role="form">
            <div class="form-group">
                <label class="col-sm-4 control-label no-padding-right" for="form-field-1">订单号</label>
                <div class="col-sm-8">
                    <input id="form-field-1" class="col-xs-10" type="text" name="order-number">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label no-padding-right" for="form-field-2">订单总价</label>
                <div class="col-sm-8">
                    <input id="form-field-2" class="col-xs-10" type="text" name="order-price">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label no-padding-right" for="form-field-2">供应商</label>
                <div class="col-sm-8">
                    <select class="chosen-select" id="form-field-select" data-placeholder="选择供应商" name="supplier-id">
                        <option value="-1">选择供应商</option>
                        <?php
                        $allSuppliers = Suppliers::model()->findAll('is_check = 1');
                        foreach ($allSuppliers as $key => $val) {
                            ?>
                            <option value="<?php echo $val['suppliers_id']; ?>"><?php echo $val['suppliers_name']; ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
        </form>
    </div>
    <p class="bigger-110 bolder center grey">
        <i class="icon-hand-right blue bigger-120"></i>
        <span clas="error-message">Are you sure?</span>
    </p>
</div><!-- #dialog-message -->

<div id="dialog-confirm" class="hide">
    <div class="alert alert-info bigger-110"></div>
    <div class="space-6"></div>
    <p class="bigger-110 bolder center grey">
        <i class="icon-hand-right blue bigger-120"></i>
        Are you sure?
    </p>
</div><!-- #dialog-confirm -->

<script type="text/template" id="OrderTemplate">
    {$
    <ol class="dd-list">
    <li class="dd-item item-red" data-id="{%totalNum%}" buy-order-id="{%orderNumber%}" buy-order-sn="{%orderSN%}">
    <div class="dd-handle">
    <span>采购单号：{%orderSN%}</span>
    <span class="text-warning Ordered List Item">采购总价：$<font id="buy_id_{%orderNumber%}">{%orderPrice%}</font></span>
    <div class="pull-right action-buttons">
    <!-- <a class="blue" href="javascript:">
    <i class="icon-pencil bigger-130"></i>
    </a> -->
    <a class="green" href="javascript:" onclick="saveBuyOrder('{%totalNum%}','{%orderNumber%}', '{%orderSN%}')">
    <i class="icon-ok bigger-130"></i>
    </a>
    </a>
    </div>
    </div>
    </li>
    </ol>
    $}
</script>

<script type="text/template" id="OrderTemplate2">
    {$
    <li class="dd-item item-red" data-id="{%totalNum%}" buy-order-id="{%orderNumber%}" buy-order-sn="{%orderSN%}">
    <div class="dd-handle">
    <span>采购单号：{%orderSN%}</span>
    <span class="text-warning Ordered List Item">采购总价：$<font id="buy_id_{%orderNumber%}">{%orderPrice%}</font></span>
    <div class="pull-right action-buttons">
    <!-- <a class="blue" href="javascript:">
    <i class="icon-pencil bigger-130"></i>
    </a> -->
    <a class="green" href="javascript:" onclick="saveBuyOrder('{%totalNum%}', '{%orderNumber%}', '{%orderSN%}')">
    <i class="icon-ok bigger-130"></i>
    </a>
    </a>
    </div>
    </div>
    </li>
    $}
</script>

<script type="text/template" id="AddSupplierTemplate">
    {$
    <li class="dd-item dd-item-father" supplier-id-father="{%id%}" data-id="{%totalNum%}">
    <div class="dd-handle btn-info">
    <span class="supplier-name">{%name%}</span>
    <span class="badge badge-info"></span>
    <div class="pull-right action-buttons">
    <a class="red" href="javascript:" supplier-id="{%id%}" onclick="add_order_number(this);" >
    <i class="icon-plus-sign bigger-130"></i>
    </a>
    </div>
    </div>
    </div>
    $}
</script>

<style>
    .horizontal-text{
        overflow-x: visible; 
        overflow-y: hidden; 
        word-wrap: break-word; 
        resize: horizontal; 
        height: 29px; 
        width: 420px; 
        display: initial;
    }
    #form_field_select_chosen{
        position: fixed;
    }
</style>