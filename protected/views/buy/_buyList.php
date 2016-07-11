<div id="sample-table-2_wrapper" class="dataTables_wrapper" role="grid">
    <table class="table table-striped table-bordered table-hover dataTable ui-jqgrid-btable ">
        <thead>
            <tr>
                <th width="5%" class="center">
                    <label>
                        <input class="ace" type="checkbox">
                        <span class="lbl"></span>
                    </label>
                </th>
                <th class="center" width="8%">订单号</th>
                <th class="center" width="10%">采购单号</th>
                <th class="center" width="10%">商品名称</th>
                <th class="center" width="12%">采购商家</th>
                <th class="center" width="10%">海外链接</th>
                <th class="center" width="8%">数量</th>
                <th class="center" width="8%">消费税</th>
                <th class="center" width="8%">优惠券</th>
                <th class="center" width="8%">海外价格</th>
                <th class="center" width="8%">备忘</th>
                <th class="center" width="5%">操作</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data as $key => $val) { ?>
                <?php foreach ($val as $k => $v) { ?>
                    <tr>
                        <?php if ($k == 0) { ?>
                            <td width="5%" class="center" rowspan="<?php echo count($val); ?>">
                                <label>
                                    <input class="ace" type="checkbox" name="choose-box" order-id="<?php echo $key; ?>">
                                    <span class="lbl"></span>
                                </label>
                            </td>
                            <td width="8%" class="center" rowspan="<?php echo count($val); ?>"><?php echo $v['order_sn']; ?></td>
                        <?php } ?>
                        <td width="10%">
                            <p>
                                <label>
                                    <input class="ace choose-rec" type="checkbox" rec-id="<?php echo $key; ?>" buy-id="<?php echo $v['buy_order_goods_id']; ?>" name="rec-choose-box">
                                    <span class="lbl">&nbsp;<?php echo $v['buy_order_number']; ?></span>
                                </label>
                            </p>
                            <p><em class="text-primary">采购时间：<br/><?php echo $v['add_time']; ?></em></p>
                        </td>
                        <td width="10%"><?php echo $v['goods_name']; ?></td>
                        <td width="12%" class="center"><?php echo $v['supplier_name']; ?></td>
                        <td width="8%"><span id="<?php echo $v['buy_order_goods_id']; ?>_oversea_url" ondblclick="changeInput(this, '<?php echo $v['buy_order_goods_id']; ?>', 'oversea_url')"><?php echo $v['oversea_url']; ?></span></td>
                        <td width="8%" class="center"><span id="<?php echo $v['buy_order_goods_id']; ?>_buy_number" ondblclick="changeInput(this, '<?php echo $v['buy_order_goods_id']; ?>', 'buy_number')"><?php echo $v['buy_number']; ?></span></td>
                        <td width="8%" class="center"><span id="<?php echo $v['buy_order_goods_id']; ?>_consumption_tax" ondblclick="changeInput(this, '<?php echo $v['buy_order_goods_id']; ?>', 'consumption_tax')"><?php echo $v['consumption_tax']; ?></span></td>
                        <td width="8%" class="center"><span id="<?php echo $v['buy_order_goods_id']; ?>_use_coupon" ondblclick="changeInput(this, '<?php echo $v['buy_order_goods_id']; ?>', 'use_coupon')"><?php echo $v['use_coupon']; ?></span></td>
                        <td width="8%" class="center"><span id="<?php echo $v['buy_order_goods_id']; ?>_oversea_price" ondblclick="changeInput(this, '<?php echo $v['buy_order_goods_id']; ?>', 'oversea_price')"><?php echo $v['oversea_price']; ?></span></td>
                        <td width="8%" class="center"><span id="<?php echo $v['buy_order_goods_id']; ?>_note" ondblclick="changeInput(this, '<?php echo $v['buy_order_goods_id']; ?>', 'note')"><?php echo!empty($v['note']) ? $v['note'] : '/'; ?></span></td>
                        <td width="5%" class="center">
                            <div class="visible-md visible-lg hidden-sm hidden-xs action-buttons">
                                <a class="red" href="javascript:" onclick="deleteBuy('<?php echo $v['buy_order_goods_id']; ?>')">
                                    <i class="icon-trash bigger-130"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
            <?php } ?>
        </tbody>
    </table>
    <div class="row">
        <div class="col-sm-6">
            <div class="summary">
                <div class="total">
                    共
                    <strong id="total-number"><?php echo $dataProvider->getTotalItemCount(); ?></strong>
                    条记录
                </div>
            </div>
        </div>
        <?php
        $page = $dataProvider->getPagination();
        if ($page && $dataProvider->getTotalItemCount() > $page->pageSize) {
            ?>
            <div class="col-sm-6">
                <div class="dataTables_paginate paging_bootstrap">
                    <ul id="yw0" class="pagination">
                        <?php
                        $this->widget('CLinkPager', array(
                            'id' => 'gbox_grid-table',
                            'pages' => $dataProvider->pagination,
                            'header' => '',
                            'htmlOptions' => array('class' => 'pagination'),
                            'firstPageLabel' => '首页',
                            'prevPageLabel' => '上一页',
                            'nextPageLabel' => '下一页',
                            'lastPageLabel' => '末页',
                            'maxButtonCount' => '10'
                        ));
                        ?>

                    </ul>
                </div>
            </div>
        <?php } ?>
    </div>
</div>
