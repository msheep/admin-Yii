<?php
Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl . "/css/jquery-ui-1.10.3.full.min.css");
Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl . "/css/datepicker.css");
Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl . "/css/daterangepicker.css");
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . "/js/jquery-ui-1.10.3.full.min.js", CClientScript::POS_END);
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . "/js/date-time/bootstrap-datepicker.min.js", CClientScript::POS_END);
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . "/js/date-time/moment.min.js", CClientScript::POS_END);
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . "/js/chosen.jquery.min.js", CClientScript::POS_END);
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . "/js/date-time/daterangepicker.min.js", CClientScript::POS_END);
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . "/js/Validform_v5.3.2_min.js", CClientScript::POS_END);
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . "/js/order/order-list.js", CClientScript::POS_END);
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
                <form class="form-inline" id="order-list" action="/order/financialCount" style="line-height:45px">
                    <!-- 时间选择范围 -->
                    <div class="form-group">
                        <div class="input-group" style="float:left;">
                            <span class="input-group-addon">
                                <i class="icon-calendar bigger-110"></i>
                            </span>
                            <input class="form-control" type="text" id="id-date-range-picker" value="<?php echo!empty($dateStart) && !empty($dateEnd) ? date('Y-m-d', $dateStart) . ' 至 ' . date('Y-m-d', $dateEnd) : '' ?>" placeholder="起止时间" style="width:180px"/>
                        </div>
                        <input type="hidden" id="date_start" name="orderList[dateStart]" value="">
                        <input type="hidden" id="date_end" name="orderList[dateEnd]" value="">
                    </div>    

                    <span class="input-group-btn inline">
                        <button class="btn btn-purple btn-sm" type="submit" id="submit-order-list">
                            Search
                            <i class="icon-search icon-on-right bigger-110"></i>
                        </button>
                        <button type="submit" id="buy-from-supplier" class="btn btn-sm btn-yellow" name="excel">导出excel</button>
                    </span>
                </form>
            </div>
        </div>
    </div>
</div><!-- /.page-header -->

<div class="table-header form-inline">
    销售收入（<?php
    if (!empty($dateStart) && !empty($dateEnd)) {
        echo date('Y-m-d', $dateStart) . '&nbsp;至&nbsp;' . date('Y-m-d', $dateEnd);
    } else {
        echo '至' . date('Y-m-d');
    }
    ?>）
</div>
<div id="gbox_grid-table" class="list-view">
    <div class="table-responsive">
        <div id="sample-table-2_wrapper" class="dataTables_wrapper" role="grid">
            <table class="table table-striped table-bordered table-hover dataTable">
                <thead>
                    <tr>
                        <th width="30%" class="text-danger">商品销售</th>
                        <th>RMB</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>产品销售价格</td>
                        <td><?php echo $financial['salePrice']['goods_amount']; ?></td>
                    </tr>
                    <tr>
                        <td>减：使用余额</td>
                        <td><?php echo $financial['salePrice']['surplus']; ?></td>
                    </tr>
                    <tr>
                        <td>减：使用金币</td>
                        <td><?php echo $financial['salePrice']['integral_money']; ?></td>
                    </tr>
                    <tr>
                        <td>减：使用红包</td>
                        <td><?php echo $financial['salePrice']['bonus']; ?></td>
                    </tr>
                    <tr>
                        <td>减：折扣</td>
                        <td><?php echo $financial['salePrice']['discount']; ?></td>
                    </tr>
                    <tr>
                        <td>实际收入</td>
                        <td><?php echo ($financial['salePrice']['goods_amount'] - $financial['salePrice']['surplus'] - $financial['salePrice']['integral_money'] - $financial['salePrice']['bonus'] - $financial['salePrice']['discount']); ?></td>
                    </tr>
                </tbody>
                <thead>
                    <tr>
                        <th class="text-danger">物流销售价格</th>
                        <th>RMB</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>物流销售价格</td>
                        <td><?php echo $financial['salePrice']['shipping_fee']; ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="hr hr-18 dotted hr-double"></div>

<div class="table-header form-inline">
    营业成本（<?php
    if (!empty($dateStart) && !empty($dateEnd)) {
        echo date('Y-m-d', $dateStart) . '&nbsp;至&nbsp;' . date('Y-m-d', $dateEnd);
    } else {
        echo '至' . date('Y-m-d');
    }
    ?>）
</div>
<div id="gbox_grid-table" class="list-view">
    <div class="table-responsive">
        <div id="sample-table-2_wrapper" class="dataTables_wrapper" role="grid">
            <table class="table table-striped table-bordered table-hover dataTable">
                <thead>
                    <tr>
                        <th width="30%" class="text-danger">商品采购</th>
                        <th colspan="2">USD</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>商品价格</td>
                        <td colspan="2"><?php echo $financial['buyPrice']['oversea_price']; ?></td>
                    </tr>
                    <tr>
                        <td>加：商品消费税</td>
                        <td colspan="2"><?php echo $financial['buyPrice']['consumption_tax']; ?></td>
                    </tr>
                    <tr>
                        <td>加：海外本土物流费用</td>
                        <td colspan="2"><?php echo $financial['buyPrice']['shipping_fee']; ?></td>
                    </tr>
                    <tr>
                        <td>减：优惠券使用</td>
                        <td colspan="2"><?php echo $financial['buyPrice']['use_coupon']; ?></td>
                    </tr>
                    <tr>
                        <td>实际成本</td>
                        <td colspan="2"><?php echo ($financial['buyPrice']['oversea_price'] + $financial['buyPrice']['consumption_tax'] + $financial['buyPrice']['shipping_fee'] - $financial['buyPrice']['use_coupon']); ?></td>
                    </tr>
                </tbody>
                <thead>
                    <tr>
                        <th class="text-danger">运输物流费用</th>
                        <th>USD</th>
                        <th>RMB</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>物流销售价格</td>
                        <td><?php echo $financial['shippingPrice']['USD_cost']; ?></td>
                        <td><?php echo $financial['shippingPrice']['RMB_cost']; ?></td>
                    </tr>
                </tbody>
                <thead>
                    <tr>
                        <th class="text-danger">管理费用</th>
                        <th colspan="2">RMB</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($financial['feePrice'])) { ?>
                        <?php foreach ($financial['feePrice'] as $key => $val) { ?>
                            <tr>
                                <td><?php echo isset(OrderFee::$feeCategory[$key]) ? OrderFee::$feeCategory[$key] : $key; ?></td>
                                <td colspan="2"><?php echo $val; ?></td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td colspan="3">暂无相关信息</td>
                        </tr>
                    <?php } ?>
                </tbody> 
            </table>
        </div>
    </div>
</div>