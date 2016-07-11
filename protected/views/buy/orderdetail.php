<?php
Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl . "/css/jquery-ui-1.10.3.full.min.css");
Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl . "/css/chosen.css");
Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl . "/css/datepicker.css");
Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl . "/css/daterangepicker.css");
Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl . "/css/ui.jqgrid.css");
Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl . "/css/docs.min.css");
Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl . "/css/bootstrap-datetimepicker.min.css");
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . "/js/jquery-ui-1.10.3.full.min.js", CClientScript::POS_END);
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . "/js/typeahead-bs2.min.js", CClientScript::POS_END);
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . "/js/jquery.nestable.min.js", CClientScript::POS_END);
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . "/js/yaya-template.js", CClientScript::POS_END);
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . "/js/chosen.jquery.min.js", CClientScript::POS_END);
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . "/js/date-time/bootstrap-datetimepicker.min.js", CClientScript::POS_END);
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . "/js/buy/order-detail.js", CClientScript::POS_END);
?>
<h3 class="header smaller lighter blue">
    <i class="icon-hand-right green"></i>
    订单<?php echo $orderInfo->order_sn; ?>
</h3>
<div class="col-xs-12">
    <div class="row">
        <div class="col-xs-12 col-sm-6">
            <div>
                <div class="widget-header" style="border-bottom:0px;">
                    <h4>基本信息</h4>
                </div>
                <div class="widget-body">
                    <table class="table table-striped" style="margin-bottom:0px">
                        <tbody>
                            <tr>
                                <td>
                                    <strong>订单状态：</strong>
                                    <?php
                                    $statement = array();
                                    if (isset(Order::$orderStatus[$orderInfo->order_status])) {
                                        $statement[] = Order::$orderStatus[$orderInfo->order_status];
                                    }
                                    if (isset(Order::$payStatus[$orderInfo->pay_status])) {
                                        $statement[] = Order::$payStatus[$orderInfo->pay_status];
                                    }
                                    if (isset(Order::$shipStatus[$orderInfo->shipping_status])) {
                                        $statement[] = Order::$shipStatus[$orderInfo->shipping_status];
                                    }
                                    if ($orderInfo->order_status == 1 && $orderInfo->pay_status == 2) {
                                        echo '<span class="label label-danger">' . implode('，', $statement) . '</span>';
                                    } else if ($orderInfo->order_status == 2) {
                                        echo '<span class="label label-default">' . implode('，', $statement) . '</span>';
                                    } else {
                                        echo '<span class="label label-info">' . implode('，', $statement) . '</span>';
                                    }
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <div class="dialogs" style="padding-bottom:15px;overflow: hidden; width: auto; height: 300px;">
                                        <div class="timeline-container timeline-style2" style="overflow: hidden; width: auto; margin-bottom:15px">
                                            <strong>商品信息：</strong>
                                            <ul>
                                                <?php if ($orderInfo->ordergoods) { ?>
                                                    <?php foreach ($orderInfo->ordergoods as $key => $val) { ?>
                                                        <li>
                                                            <?php echo $val['goods_name']; ?>
                                                            <?php if ($val->getIfBuy() || $orderInfo->shipping_status == 3) { ?>
                                                                <span class="badge badge-primary">已采购</span>
                                                                <!--采购信息-->
                                                                <?php $buyOrderGoods = BuyOrderGoods::model()->find('order_id = :order_id AND goods_id = :goods_id', array(':order_id' => $orderInfo->order_id, ':goods_id' => $val->goods_id)); ?>
                                                                <?php if (!empty($buyOrderGoods)) { ?>
                                                                    <ul class="list-unstyled" style="border:1px dashed #428bca;line-height: 26px">
                                                                        <li>
                                                                            <i class="icon-caret-right blue"></i>
                                                                            <span class="text-danger">购买数量：</span>
                                                                            <span id="<?php echo $buyOrderGoods->id; ?>_buy_number" ondblclick="changeInput(this, '<?php echo $buyOrderGoods->id; ?>', 'buy_number')">
                                                                                <?php echo $buyOrderGoods->buy_number; ?> 
                                                                            </span>
                                                                        </li>
                                                                        <li>
                                                                            <i class ="icon-caret-right blue"></i>
                                                                            <span class="text-danger">海外价格：</span>
                                                                            $<span id="<?php echo $buyOrderGoods->id; ?>_oversea_price" ondblclick="changeInput(this, '<?php echo $buyOrderGoods->id; ?>', 'oversea_price')"><?php echo $buyOrderGoods->oversea_price; ?> </span>
                                                                        </li>
                                                                        <li>
                                                                            <i class="icon-caret-right blue"></i>
                                                                            <span class="text-danger">运费：</span>
                                                                            $<span id="<?php echo $buyOrderGoods->id; ?>_shipping_fee" ondblclick="changeInput(this, '<?php echo $buyOrderGoods->id; ?>', 'shipping_fee')"><?php echo $buyOrderGoods->shipping_fee; ?></span>   
                                                                        </li>
                                                                        <li>
                                                                            <i class="icon-caret-right blue"></i>
                                                                            <span class="text-danger">消费税：</span>
                                                                            $<span id="<?php echo $buyOrderGoods->id; ?>_consumption_tax" ondblclick="changeInput(this, '<?php echo $buyOrderGoods->id; ?>', 'consumption_tax')"><?php echo $buyOrderGoods->consumption_tax; ?></span>
                                                                        </li>
                                                                        <li>
                                                                            <i class="icon-caret-right blue"></i>
                                                                            <span class="text-danger">优惠券：</span>
                                                                            $<span id="<?php echo $buyOrderGoods->id; ?>_use_coupon" ondblclick="changeInput(this, '<?php echo $buyOrderGoods->id; ?>', 'use_coupon')"><?php echo $buyOrderGoods->use_coupon; ?></span>
                                                                        </li>
                                                                        <li>
                                                                            <i class="icon-caret-right blue"></i>
                                                                            <span class="text-danger">购买链接：</span>
                                                                            <span id="<?php echo $buyOrderGoods->id; ?>_oversea_url" ondblclick="changeInput(this, '<?php echo $buyOrderGoods->id; ?>', 'oversea_url')">
                                                                                <?php echo $buyOrderGoods->oversea_url; ?>
                                                                            </span>
                                                                        </li>
                                                                        <li>
                                                                            <i class="icon-caret-right blue"></i>
                                                                            <span class="text-danger">备注：</span>
                                                                            <span id="<?php echo $buyOrderGoods->id; ?>_note" ondblclick="changeInput(this, '<?php echo $buyOrderGoods->id; ?>', 'note')">
                                                                                <?php echo!empty($buyOrderGoods->note) ? $buyOrderGoods->note : '/'; ?>
                                                                            </span>
                                                                        </li>
                                                                    </ul>
                                                                <?php } ?>
                                                            <?php } else { ?>
                                                                <span class="badge badge-warning">未采购</span>
                                                            <?php } ?>
                                                            <br/>
                                                        </li>
                                                    <?php } ?>
                                                <?php } ?>
                                            </ul>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <div>
                <div class="widget-header" style="border-bottom:0px;">
                    <h4>操作日志</h4>
                </div>
                <div class="widget-body">
                    <table class="table table-striped" style="margin-bottom:0px">
                        <tbody>
                            <tr>
                                <td>
                                    <strong>当前共有<span class="label label-danger"><?php echo count($orderLog); ?></span>条操作记录</strong>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <div class="dialogs" style="padding-bottom:15px;overflow: hidden; width: auto; height: 300px;">
                                        <div class="timeline-container timeline-style2" style="overflow: hidden; width: auto; margin-bottom:15px">
                                            <?php if (!empty($orderLog)) { ?>
                                                <?php foreach ($orderLog as $key => $log) { ?>
                                                    <div class="timeline-items">
                                                        <div class="timeline-item clearfix">
                                                            <div class="timeline-info">
                                                                <span class="timeline-date"><?php echo date('Y-m-d H:i:s', $key); ?></span>
                                                                <i class="timeline-indicator btn btn-info no-hover"></i>
                                                            </div>

                                                            <div class="widget-box transparent">
                                                                <div class="widget-body">
                                                                    <div class="widget-main no-padding">
                                                                        <?php
                                                                        if (isset($log[0]['action'])) {
                                                                            foreach ($log as $k => $v) {
                                                                                ?>
                                                                                <p class="bigger-110">
                                                                                    <?php if (!empty($v['operator'])) { ?>
                                                                                        <span class="purple bolder"><?php echo $v['operator']; ?></span>
                                                                                    <?php } ?>
                                                                                    <span><?php echo $v['action']; ?></span>
                                                                                </p>
                                                                            <?php } ?>
                                                                        <?php } else { ?>
                                                                            <p class="bigger-110">
                                                                                <?php if (!empty($log['operator'])) { ?>
                                                                                    <span class="purple bolder"><?php echo $log['operator']; ?></span>
                                                                                <?php } ?>
                                                                                <?php echo $log['action']; ?>
                                                                            </p>
                                                                        <?php } ?>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<div class="col-xs-12">
    <div class="row">
        <div style="margin:10px">
            <div class="widget-header">
                <h4>物流信息</h4>
            </div>
            <div class="widget-body">
                <div class="widget-main no-padding">

                    <?php if ($orderInfo->shipping_status != 0 && $orderInfo->order_status != 0) { ?>
                        <fieldset>
                            <input type="hidden" name="order-id" value="<?php echo $orderInfo->order_id; ?>">
                            <table class="table table-striped table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th class="center" width="17%">商品</th>
                                        <th class="center" width="18%">时间</th>
                                        <th class="center" width="15%">物流进度</th>
                                        <th class="center" width="30%">备注</th>
                                        <th class="center" width="10%">删除</th>
                                        <th class="center" width="10%">操作</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach ($orderInfo->ordergoods as $key => $val) {
                                        $shippingInfo = $val->shippingInfo;
                                        if (!empty($shippingInfo)) {
                                            ?>
                                            <?php foreach ($shippingInfo as $k => $v) { ?>
                                                <tr>
                                                    <?php if ($k == 0) { ?>
                                                        <td rowspan="<?php echo count($shippingInfo); ?>" class="align-middle"><?php echo $val->goods_name; ?></td>
                                                    <?php } ?>
                                                    <td class="center align-middle"><?php echo $v->time; ?></td>
                                                    <td class="center align-middle"><?php echo Order::$shipStatus[$v->shipping_status]; ?></td>
                                                    <td class="align-middle">
                                                        <ul class="list-unstyled no-margin-bottom" style="line-height:32px">
                                                            <?php if (!empty($v->delivery_company)) { ?>
                                                                <li><i class="icon-caret-right blue"></i><span class="text-danger">物流公司：</span>
                                                                    <span id="shipping_<?php echo $v->id; ?>_delivery_company" ondblclick="changeShippingInput(this, '<?php echo $v->id; ?>', 'delivery_company')">
                                                                        <?php echo $v->delivery_company; ?>
                                                                    </span>
                                                                </li>
                                                            <?php } ?>
                                                            <?php if (!empty($v->delivery_number)) { ?>
                                                                <li><i class="icon-caret-right blue"></i><span class="text-danger">运单号：</span>
                                                                    <span id="shipping_<?php echo $v->id; ?>_delivery_number" ondblclick="changeShippingInput(this, '<?php echo $v->id; ?>', 'delivery_number')">
                                                                        <?php echo $v->delivery_number; ?>
                                                                    </span>
                                                                </li>
                                                            <?php } ?>
                                                            <?php if (!empty($v->cost) && $v->cost > 0) { ?>
                                                                <li><i class="icon-caret-right blue"></i><span class="text-danger">费用：</span><?php echo $v->money_type; ?>&nbsp;
                                                                    <span id="shipping_<?php echo $v->id; ?>_cost" ondblclick="changeShippingInput(this, '<?php echo $v->id; ?>', 'cost')">
                                                                        <?php echo $v->cost; ?>
                                                                    </span>
                                                                </li>
                                                            <?php } ?>
                                                            <li><i class="icon-caret-right blue"></i><span class="text-danger">备注：</span>
                                                                <span id="shipping_<?php echo $v->id; ?>_note" ondblclick="changeShippingInput(this, '<?php echo $v->id; ?>', 'note')">
                                                                    <?php echo!empty($v->note) ? $v->note : '/'; ?>
                                                                </span>
                                                            </li>
                                                        </ul>
                                                    </td>
                                                    <td class="center align-middle" width="5%"><button class="btn btn-danger btn-minier" onclick="delAction('<?php echo $v->id; ?>');">删除</button></td>
                                                    <?php if ($k == 0) { ?>
                                                        <td rowspan="<?php echo count($shippingInfo); ?>" class="center align-middle" width="15%">
                                                            <input type="hidden" name="rec-id" value="<?php echo $val['rec_id']; ?>">
                                                            <?php
                                                            $status = array('101', '102', '103', '104', '105');
                                                            foreach ($status as $x => $j) {
                                                                $statusInfo = ShippingAction::model()->find('rec_id = :rec_id AND shipping_status = :shipping_status', array(':rec_id' => $val['rec_id'], ':shipping_status' => $j));
                                                                ?>
                                                                <p><button class="btn btn-minier <?php echo!empty($statusInfo) ? 'btn-default' : 'btn-primary action'; ?>"  value="<?php echo $j; ?>"><?php echo Order::$shipStatus[$j]; ?></button></p>
                                                            <?php } ?>
                                                        </td>
                                                    <?php } ?>
                                                </tr>
                                            <?php } ?>
                                        <?php } else { ?>
                                            <tr>
                                                <td style="vertical-align:middle"><?php echo $val->goods_name; ?></td>
                                                <td class="center align-middle">/</td>
                                                <td class="center align-middle">/</td>
                                                <td class="center align-middle">/</td>
                                                <td class="center align-middle" width="5%">/</td>
                                                <td class="center align-middle" width="15%">
                                                    <input type="hidden" name="rec-id" value="<?php echo $val['rec_id']; ?>">
                                                    <?php
                                                    $status = array('101', '102', '103', '104', '105');
                                                    foreach ($status as $x => $j) {
                                                        $statusInfo = ShippingAction::model()->find('rec_id = :rec_id AND shipping_status = :shipping_status', array(':rec_id' => $val['rec_id'], ':shipping_status' => $j));
                                                        ?>
                                                        <p><button class="btn btn-minier <?php echo!empty($statusInfo) ? 'btn-default' : 'btn-primary action'; ?>"  value="<?php echo $j; ?>"><?php echo Order::$shipStatus[$j]; ?></button></p>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </fieldset>
                        <div class="form-actions text-warning orange" style="margin:0px">
                            <p><i class="icon-bell bigger-110"></i>&nbsp;Tips：</p>
                            <p>①供应商发货：填写供应商运单号，产生费用为所在运单号的运费，不需要平摊到各个商品</p>
                            <p>②处理中心发货：填写国际运单号，产生费用为所在运单号的运费，不需要平摊到各个商品</p>
                            <p>③其余状态信息存在则填写，不存在可不填写，产品费用可选择美元、人民币</p>
                        </div>
                    <?php } else { ?>
                        <fieldset style="padding:10px">
                            <span class="text-warning orange"><i class="bigger-110 icon-warning-sign"></i>&nbsp;当前订单状态不符合物流信息填写条件</span>
                        </fieldset>
                    <?php } ?>

                </div>
            </div>
        </div>
    </div>
</div>


<div id="dialog-message" class="hide">
    <div class="alert alert-info bigger-110 no-padding-bottom no-margin-bottom">
        <form class="form-horizontal" role="form">
            <div class="form-group">
                <input type="text" placeholder="物流公司" name="delivery-company" class="col-sm-12">
            </div>
            <div class="form-group">
                <input type="text" placeholder="运单号" name="delivery-number" class="col-sm-12">
            </div>
            <div class="form-group">
                <input type="text" placeholder="发生费用" name="cost" class="col-sm-7">
                <select class="col-sm-5"  name="money-type">
                    <option value="USD">美元</option>
                    <option value="RMB">人民币</option>
                </select>
            </div>
            <div class="form-group">
                <textarea placeholder="备注信息" name="note" class="col-sm-12" style="resize: vertical;"></textarea>
            </div>
            <div class="form-group">
                <div id="datetimepicker1" class="input-append" style="cursor: pointer;">
                    <input name="time" data-format="yyyy-MM-dd hh:mm:ss" value="<?php echo date('Y-m-d H:i:s'); ?>" type="text"></input>
                    <span class="add-on">
                        <i data-time-icon="icon-time" data-date-icon="icon-calendar"></i>
                    </span>
                </div>
            </div>
        </form>
    </div>
    <div class="space-6"></div>
    <p class="bigger-110 bolder center grey">
        <i class="icon-hand-right blue bigger-120"></i>
        Are you sure?
    </p>
</div>
<style>
    .timeline-style2 .timeline-item:before{
        left: 180px;
    }
    .timeline-style2 .timeline-indicator{
        left: 175px;
    }
    .timeline-style2 .timeline-date{
        width: 145px;
    }
    .timeline-style2 .timeline-info{
        width: 202px;
    }
    .timeline-item{
        line-height: 23px;
    }
    .timeline-style2 .timeline-item .widget-box{
        margin-left: 195px;
    }
    .bigger-110 {
        font-size: 90%;
    }
    .bootstrap-datetimepicker-widget{
        z-index:1051
    }
    .bootstrap-datetimepicker-widget .picker-switch {
        text-align: center;
    }
    .accordion-toggle {
        cursor: pointer;
    }
    .dropdown-menu li > a {
        clear: both;
        color: #333333;
        display: block;
        font-weight: normal;
        line-height: 20px;
        padding: 3px 20px;
        white-space: nowrap;
    }
    .dropdown-menu li > a:hover,
    .dropdown-menu li > a:focus,
    .dropdown-submenu:hover > a {
        color: #ffffff;
        text-decoration: none;
        background-color: #0081c2;
        background-image: -moz-linear-gradient(top, #0088cc, #0077b3);
        background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#0088cc), to(#0077b3));
        background-image: -webkit-linear-gradient(top, #0088cc, #0077b3);
        background-image: -o-linear-gradient(top, #0088cc, #0077b3);
        background-image: linear-gradient(to bottom, #0088cc, #0077b3);
        background-repeat: repeat-x;
        filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#ff0088cc', endColorstr='#ff0077b3', GradientType=0);
    }
</style>


