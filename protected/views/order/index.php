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
                <form class="form-inline" id="order-list" action="/order/index" style="line-height:45px">
                    <!-- 时间选择范围 -->
                    <div class="form-group">
                        <div class="input-group" style="float:left;">
                            <span class="input-group-addon">
                                <i class="icon-calendar bigger-110"></i>
                            </span>
                            <input class="form-control" type="text" id="id-date-range-picker" value="<?php echo!empty($_GET['orderList']['dateStart']) && !empty($_GET['orderList']['dateEnd']) ? $_GET['orderList']['dateStart'] . ' 至 ' . $_GET['orderList']['dateEnd'] : '' ?>" placeholder="起止时间" style="width:180px"/>
                        </div>
                        <input type="hidden" id="date_start" name="orderList[dateStart]" value="">
                        <input type="hidden" id="date_end" name="orderList[dateEnd]" value="">
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

<div class="hr hr-18 dotted hr-double"></div>
<h4 class="pink">
    <i class="icon-hand-right green"></i>
    <a class="blue" data-toggle="modal" role="button" href="javascript:"> 所有订单 </a>
</h4>
<div class="hr hr-18 dotted hr-double"></div>
<div class="table-header form-inline">
    销售总记录（<?php
    if (!empty($_GET['orderList']['dateStart']) && !empty($_GET['orderList']['dateEnd'])) {
        echo date('Y-m-d', strtotime($_GET['orderList']['dateStart'])) . '&nbsp;至&nbsp;' . date('Y-m-d', strtotime($_GET['orderList']['dateEnd']));
    } else {
        echo '至' . date('Y-m-d');
    }
    ?>）
    <div class="form-group">
        <form method="post">
            <button type="submit" id="buy-from-supplier" class="btn btn-minier btn-yellow" name="excel">导出excel</button>
        </form>
    </div>
</div>
<div id="gbox_grid-table" class="list-view">
    <div class="table-responsive">
        <div id="sample-table-2_wrapper" class="dataTables_wrapper" role="grid">
            <table class="table table-striped table-bordered table-hover dataTable">
                <thead>
                    <tr>
                        <th>产品类目</th>
                        <?php foreach (Order::$orderStatus as $key => $val) { ?>
                            <th><?php echo $val ?></th>
                        <?php } ?>
        <!--<th>总和（不包括已取消/无效/退货）</th>-->
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $allParent = Category::model()->findAll('parent_id = 0');
                    $totalSum = 0;
                    foreach ($allParent as $key => $val) {
                        $catSum = 0;
                        ?>
                        <tr>
                            <td><?php echo $val['cat_name'] ?></td>
                            <?php foreach (Order::$orderStatus as $k => $v) { ?>
                                <td>
                                    <?php
                                    $sum = !empty($total[$k][$val['cat_id']]) ? array_sum($total[$k][$val['cat_id']]) : 0;
                                    if ($k != 4 && $k != 3 && $k != 2) {
                                        $catSum += $sum;
                                        $totalSum += $sum;
                                    }
                                    echo $sum;
                                    ?>
                                </td>
                            <?php } ?>
            <!--<td><?php echo $catSum; ?></td>-->
                        </tr>
                    <?php } ?> 
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="hr hr-18 dotted hr-double"></div>
<h4 class="pink">
    <i class="icon-hand-right green"></i>
    <a class="blue" data-toggle="modal" role="button" href="javascript:"> 已销售订单 </a>
</h4>
<div class="hr hr-18 dotted hr-double"></div>
<div class="table-header form-inline">
    销售总记录（<?php
    if (!empty($_GET['orderList']['dateStart']) && !empty($_GET['orderList']['dateEnd'])) {
        echo date('Y-m-d', strtotime($_GET['orderList']['dateStart'])) . '&nbsp;至&nbsp;' . date('Y-m-d', strtotime($_GET['orderList']['dateEnd']));
    } else {
        echo '至' . date('Y-m-d');
    }
    ?>）
    <div class="form-group">
        <?php if(!empty($sale)){?>
        <form method="post">
            <button type="submit" id="buy-from-supplier" class="btn btn-minier btn-yellow" name="sale-excel">导出excel</button>
        </form>
        <?php }?>
    </div>
</div>
<div id="gbox_grid-table" class="list-view">
    <div class="table-responsive">
        <div id="sample-table-2_wrapper" class="dataTables_wrapper" role="grid">
            <table class="table table-striped table-bordered table-hover dataTable">
                <thead>
                    <tr>
                        <th colspan="2">产品类目</th>
                        <th>已销售产品数目</th>
                        <th>已销售产品金额/元</th>
                        <th>该类目销售产品总额/元</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($sale)) { 
                        $totalNum = 0;
                        $totalMoney = 0;
                        $alltotalMoney = 0;
                    ?>
                        <?php foreach ($sale as $key => $val) { 
                            $totalNum += $val['total_number'];
                            $totalMoney += $val['total_money'];
                        ?>
                            <tr>
                                <td class="text-danger"><b><?php echo Category::model()->findByPk($key)->cat_name; ?></b></td>
                                <td></td>
                                <td class="text-danger"><b><?php echo $val['total_number']; ?></b></td>
                                <td></td>
                                <td class="text-danger"><b><?php echo $val['total_money']; ?></b></td>
                            </tr>
                            <?php foreach ($val as $k => $v) { ?>
                                <?php if (is_numeric($k)) { 
                                    $alltotalMoney += $v['total_money'];
                                ?>
                                    <tr>
                                        <td></td>
                                        <td><?php echo !empty(Category::model()->findByPk($k)->cat_name) ? Category::model()->findByPk($k)->cat_name : ''; ?></td>
                                        <td><?php echo count($v['number']) > 1 ? array_sum($v['number']) : $v['number'][0]; ?></td>
                                        <td><?php echo $v['total_money']; ?></td>
                                        <td><?php echo $v['total_money']; ?></td>
                                    </tr>
                                <?php } ?>
                            <?php } ?>
                        <?php } ?>
                        <tr>
                            <td>总计</td>
                            <td></td>
                            <td><?php echo $totalNum;?></td>
                            <td><?php echo $alltotalMoney;?></td>
                            <td><?php echo $totalMoney;?></td>
                        </tr>      
                    <?php } else { ?>
                        <tr><td colspan="5">暂无数据</td></tr>      
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>