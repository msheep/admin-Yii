<?php if ($index == '0') { ?>	
    <thead>
        <tr>
            <th width="5%" class="center">
                <label>
                    <input class="ace" type="checkbox">
                    <span class="lbl"></span>
                </label>
            </th>
            <th width="11%">订单号</th>
            <th width="15%">购买用户</th>
            <th width="20%">订单商品</th>
            <th width="10%">订单状态</th>
            <th width="20%">订单价格</th>
            <th width="14%">手续费</th>
            <th width="5%">操作</th>
        </tr>
    </thead>
    <tbody>
    <?php } ?>
    <tr>
        <td class="center">
            <label>
                <input class="ace" type="checkbox" name="choose-box" order-id="<?php echo $data->order_id; ?>">
                <span class="lbl"></span>
            </label>
        </td>
        <td>
            <p><a href="/buy/orderDetail/orderId/<?php echo $data->order_id; ?>"><?php echo $data->order_sn; ?></a></p>
            <p><em class="text-primary">下单时间：<br/><?php echo date('Y-m-d H:i:s', $data->add_time); ?></em></p>
        </td>
        <td>
            <p><?php 
                if($data->userInfo){
                    echo $data->userInfo->user_name;
                }                
                ?></p>
            <p>
                <em class="text-primary">
                    <!--邮寄信息：<br/><span><?php echo $data->consignee . '&nbsp;' . $data->tel . '&nbsp;' . $data->mobile . '&nbsp;' . $data->address; ?></span><br />-->
                    邮寄信息：<br/><span><?php echo $data->consignee . '&nbsp;' . $data->tel . '&nbsp;' . $data->mobile . '&nbsp;['.$data->countryInfo->region_name .'&nbsp'.$data->provinceInfo->region_name.
                            '&nbsp' . $data->cityInfo->region_name . '&nbsp;' . $data->districtInfo->region_name . ']&nbsp;'.$data->address; ?></span>
                </em>
            </p>
        </td>
        <td>
            <?php
            $buyGoods = array();
            foreach ($data->ordergoods as $goods) {
                //取消订单
                if ($data->order_status == 2) {
                    $good = '<label><i class="icon-remove bigger-120 red"></i>';
                } else {
                    //是否采购
                    if ($goods->getIfBuy() || $data->shipping_status == 3) {
                        $good = '<label><i class="icon-ok bigger-120 red"></i>';
                    } else {
                        $good = '<label><input class="ace choose-rec" type="checkbox" name="rec-choose-box" order-id="' . $data->order_id . '" rec-id="' . $goods['rec_id'] . '">';
                    }
                }
                $good .= '<span class="lbl">&nbsp;' . $goods['goods_name'];
                if ($goods['goods_attr']) {
                    $good .= '<code>' . $goods['goods_attr'] . '</code>';
                }
                $good .= '&nbsp;<span class="badge badge-info">' . $goods['goods_number'] . ' * ￥' . $goods['goods_price'] . '</span></span></label>';
                $buyGoods[] = $good;
            }
            echo implode('<hr/>', $buyGoods)
            ?>
        </td>
        <td>
            <?php
            $statement = array();
            if (isset(Order::$orderStatus[$data->order_status])) {
                $statement[] = Order::$orderStatus[$data->order_status];
            }
            if (isset(Order::$payStatus[$data->pay_status])) {
                $statement[] = Order::$payStatus[$data->pay_status];
            }
            if (isset(Order::$shipStatus[$data->shipping_status])) {
                $statement[] = Order::$shipStatus[$data->shipping_status];
            }
            if ($data->order_status == 1 && $data->pay_status == 2) {
                echo '<span class="label label-danger">' . implode('，', $statement) . '</span>';
            } else if ($data->order_status == 2) {
                echo '<span class="label label-default">' . implode('，', $statement) . '</span>';
            } else {
                echo '<span class="label label-info">' . implode('，', $statement) . '</span>';
            }
            ?>
        </td>
        <td>
            <p>
                <abbr title="<?php echo Yii::app()->params['order']['goods_amount']; ?>"><?php echo Yii::app()->params['order']['goods_amount']; ?></abbr>：￥<?php echo $data->goods_amount; ?>
                +&nbsp;<abbr title="<?php echo Yii::app()->params['order']['tax']; ?>"><?php echo Yii::app()->params['order']['tax']; ?></abbr>：￥<?php echo $data->tax; ?>
                +&nbsp;<abbr title="<?php echo Yii::app()->params['order']['shipping_fee']; ?>"><?php echo Yii::app()->params['order']['shipping_fee']; ?></abbr>：￥<?php echo $data->shipping_fee; ?>
                +&nbsp;<abbr title="<?php echo Yii::app()->params['order']['insure_fee']; ?>"><?php echo Yii::app()->params['order']['insure_fee']; ?></abbr>：￥<?php echo $data->insure_fee; ?>
                +&nbsp;<abbr title="<?php echo Yii::app()->params['order']['pay_fee']; ?>"><?php echo Yii::app()->params['order']['pay_fee']; ?></abbr>：￥<?php echo $data->pay_fee; ?>
                +&nbsp;<abbr title="<?php echo Yii::app()->params['order']['pack_fee']; ?>"><?php echo Yii::app()->params['order']['pack_fee']; ?></abbr>：￥<?php echo $data->pack_fee; ?>
                +&nbsp;<abbr title="<?php echo Yii::app()->params['order']['card_fee']; ?>"><?php echo Yii::app()->params['order']['card_fee']; ?></abbr>：￥<?php echo $data->card_fee; ?>
                -&nbsp;<abbr title="<?php echo Yii::app()->params['order']['discount']; ?>"><?php echo Yii::app()->params['order']['discount']; ?></abbr>：￥<?php echo $data->discount; ?>
                =&nbsp;<abbr title="<?php echo Yii::app()->params['order']['total_fee']; ?>"><?php echo Yii::app()->params['order']['total_fee']; ?></abbr>：<span class="label label-danger">￥<?php echo $data->getTotalFee(); ?></span>
            </p>
            <p>
                <abbr title="<?php echo Yii::app()->params['order']['money_paid']; ?>"><?php echo Yii::app()->params['order']['money_paid']; ?></abbr>：￥<?php echo $data->money_paid; ?>
                -&nbsp;<abbr title="<?php echo Yii::app()->params['order']['surplus']; ?>"><?php echo Yii::app()->params['order']['surplus']; ?></abbr>：￥<?php echo $data->surplus; ?>
                -&nbsp;<abbr title="<?php echo Yii::app()->params['order']['integral_money']; ?>"><?php echo Yii::app()->params['order']['integral_money']; ?></abbr>：￥<?php echo $data->integral_money; ?>
                -&nbsp;<abbr title="<?php echo Yii::app()->params['order']['bonus']; ?>"><?php echo Yii::app()->params['order']['bonus']; ?></abbr>：￥<?php echo $data->bonus; ?>
                =&nbsp;<abbr title="<?php echo Yii::app()->params['order']['should_pay']; ?>"><?php echo Yii::app()->params['order']['should_pay']; ?></abbr>：<span class="label label-danger">￥<?php echo $data->getShouldPay(); ?></span>
            </p>
        </td>
        <td>
            <?php foreach(OrderFee::$feeCategory as $key=>$val){?>
            <p><span class="label label-warning"><?php echo $val;?></span>：</p>
            <?php 
                $feeInfo = OrderFee::model()->find('order_id = :order_id AND fee_cat_id = :fee_cat_id', array(':order_id' => $data->order_id, ':fee_cat_id' => $key));
            ?>
            <p>￥<span id="<?php echo $data->order_id; ?>_<?php echo $key; ?>_fee" ondblclick="changeInput(this, '<?php echo $data->order_id; ?>', '<?php echo $key; ?>')"><?php echo !empty($feeInfo) ? $feeInfo->fee : '0.00';?></span></p>
            <?php }?>
        </td>
        <td class="center">
            <div class="visible-md visible-lg hidden-sm hidden-xs action-buttons">
                <a class="blue" href="/buy/orderDetail/orderId/<?php echo $data->order_id; ?>">
                    <i class="icon-zoom-in bigger-130" title="查看"></i>
                </a>
            </div>
        </td>
    </tr>
    <?php if ($index == $widget->dataProvider->getItemCount()) { ?>
    </tbody>
<?php } ?>
