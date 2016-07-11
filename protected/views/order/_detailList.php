<?php if ($index == '0') { ?>	
    <thead>
        <tr>
            <th width="7%">订单号</th>
            <th width="10%">状态</th>
            <th width="25%">商品名称</th>
            <th width="10%">属性</th>
            <th width="3%">数量</th>
            <th width="7%">单价</th>
            <th width="18%">总价</th>
            <th width="10%">所属子类目</th>
            <th width="10%">所属父类目</th>
        </tr>
    </thead>
    <tbody>
    <?php } ?>
    <tr>
        <td class="center">
            <p><?php echo !empty($data->orderInfo->order_sn) ? $data->orderInfo->order_sn : ''; ?></p>
        </td>
        <td>
            <?php
            $statement = array();
            if(!empty($data->orderInfo->order_status)){
                if (isset(Order::$orderStatus[$data->orderInfo->order_status])) {
                    $statement[] = Order::$orderStatus[$data->orderInfo->order_status];
                }
                if (isset(Order::$payStatus[$data->orderInfo->pay_status])) {
                    $statement[] = Order::$payStatus[$data->orderInfo->pay_status];
                }
                if (isset(Order::$shipStatus[$data->orderInfo->shipping_status])) {
                    $statement[] = Order::$shipStatus[$data->orderInfo->shipping_status];
                }
            }
            echo implode('，', $statement);
            ?>
        </td>
        <td>
            <a href="http://www.168haitao.com/goods.php?id=<?php echo $data->goods_id; ?>" target="_blank"><?php echo $data->goods_name; ?></a>
        </td>
        <td>
            <?php echo $data->goods_attr; ?>
        </td>
        <td> 
            <?php echo $data->goods_number; ?>
        </td>
        <td> 
            ￥<?php echo $data->goods_price; ?>
        </td>
        <td> 
            ￥<?php echo $data->goods_price; ?> * <?php echo $data->goods_number; ?> = <span class="label label-danger">￥<?php echo $data->goods_price * $data->goods_number; ?></span>
        </td>
        <td> 
            <?php echo $data->goodsInfo->catInfo->cat_name; ?>
        </td>
        <td> 
            <?php $parentInfo = Category::model()->findByPk($data->goodsInfo->catInfo->parent_id);echo !empty($parentInfo) ? $parentInfo->cat_name : ''; ?>
        </td>
    </tr>
    <?php if ($index == $widget->dataProvider->getItemCount()) { ?>
    </tbody>
<?php } ?>
