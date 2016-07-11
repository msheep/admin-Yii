<?php

Yii::import('application.components.PHPExcel.ExcelApi');
/*
 * 关于报表的信息
 */

class OrderController extends Controller {

    public $layout = 'adminLayout';

    /*
     * 所有销售产品汇总
     */

    public function actionIndex() {
        //各个类目的销售记录
        $dateStart = '';
        $dateEnd = '';
        $sql = 'SELECT a.goods_number, a.goods_price, c.cat_id, d.order_status, d.pay_status, d.shipping_status'
                . ' FROM ecs_order_goods AS a '
                . ' JOIN ecs_goods AS b ON a.goods_id = b.goods_id'
                . ' JOIN ecs_category AS c ON c.cat_id = b.cat_id'
                . ' JOIN ecs_order_info AS d ON d.order_id = a.order_id';
        if (!empty($_GET['orderList']['dateStart']) && !empty($_GET['orderList']['dateEnd'])) {
            $dateStart = strtotime($_GET['orderList']['dateStart'] . ' 00:00:00');
            $dateEnd = strtotime($_GET['orderList']['dateEnd'] . ' 23:59:59');
            $sql .= " WHERE d.add_time BETWEEN $dateStart AND $dateEnd";
        }
        $command = Yii::app()->db->createCommand($sql);
        $result = $command->queryAll();

        //所有销售订单
        $total = array();
        if (!empty($result)) {
            foreach ($result as $val => $key) {
                $parentId = Category::findParentId($key['cat_id']);
                $price = $key['goods_price'] * $key['goods_number'];
                $total[$key['order_status']][$parentId][] = $price;
                $result[$val]['parent_cat_id'] = $parentId;
                $result[$val]['total_price'] = $price;
            }
        }
        if (isset($_POST['excel'])) {
            ExcelApi::allOrderToExcel($total, $dateStart, $dateEnd);
        }

        //已销售记录
        $sale = array();
        foreach ($result as $key => $val) {
            if (in_array($val['order_status'], array(1, 5)) && in_array($val['shipping_status'], array(1, 2)) && in_array($val['pay_status'], array(1, 2))) {
                $sale[$val['parent_cat_id']][$val['cat_id']]['money'][] = $val['total_price'];
                $sale[$val['parent_cat_id']][$val['cat_id']]['number'][] = $val['goods_number'];
            }
        }
        foreach ($sale as $key => $val) {
            $sum = 0;
            $totalNum = 0;
            foreach ($val as $k => $v) {
                $sum += array_sum($sale[$key][$k]['money']);
                $totalNum += array_sum($sale[$key][$k]['number']);
                $sale[$key][$k]['total_money'] = array_sum($sale[$key][$k]['money']);
                $sale[$key]['total_money'] = $sum;
                $sale[$key]['total_number'] = $totalNum;
            }
        }

        if (isset($_POST['sale-excel'])) {
            ExcelApi::allSaleOrderToExcel($sale, $dateStart, $dateEnd);
        }
        $this->breadcrumbs = array(
            '销售总记录',
        );
        $this->render('index', array('total' => $total, 'sale' => $sale));
    }

    /*
     * 所有销售明细
     */

    public function actionOrderGoodsDetail() {
        $criteria = new CDbCriteria();
        $criteria->join = 'join ecs_order_info as b on b.order_id = t.order_id';
        $criteria->join .= ' join ecs_goods as c on c.goods_id = t.goods_id';
        $criteria->order = 't.rec_id desc';
        if (isset($_GET['orderList'])) {
            //时间判断
            if (isset($_GET['orderList']['dateStart']) && $_GET['orderList']['dateStart']) {
                $criteria->addCondition("b.add_time >= :dateStart");
                $criteria->params += array(':dateStart' => strtotime($_GET['orderList']['dateStart'] . ' 00:00:00'));
            }

            if (isset($_GET['orderList']['dateEnd']) && $_GET['orderList']['dateEnd']) {
                $criteria->addCondition("b.add_time <= :dateEnd");
                $criteria->params += array(':dateEnd' => strtotime($_GET['orderList']['dateEnd'] . ' 23:59:59'));
            }

            //供应商判断
            if (isset($_GET['orderList']['suppliers']) && $_GET['orderList']['suppliers'] > -1) {
                $criteria->addCondition("c.suppliers_id = :suppliers_id");
                $criteria->params += array(':suppliers_id' => $_GET['orderList']['suppliers']);
            }

            //品牌判断
            if (isset($_GET['orderList']['brand']) && $_GET['orderList']['brand'] > -1) {
                $criteria->addCondition("c.brand_id = :brand_id");
                $criteria->params += array(':brand_id' => $_GET['orderList']['brand']);
            }

            //类目判断
            if (isset($_GET['orderList']['cat']) && $_GET['orderList']['cat'] > -1) {
                $criteria->addCondition("c.cat_id = :cat_id");
                $criteria->params += array(':cat_id' => $_GET['orderList']['cat']);
            }
        }
        $dataProvider = new CActiveDataProvider('OrderGoods', array(
            'criteria' => $criteria,
            'pagination' => array(
                'pageSize' => '18',
            )
        ));
        $this->breadcrumbs = array(
            '销售明细',
        );
        if (Yii::app()->request->isAjaxRequest) {
            $this->renderPartial('_ajaxListView', array('dataProvider' => $dataProvider, 'itemView' => '_orderList',));
        } else {
            $this->render('detail', array('dataProvider' => $dataProvider));
        }
    }

    /*
     * 所有销售财务
     */

    public function actionFinancialCount() {
        $dateStart = '';
        $dateEnd = '';
        $dateStart = !empty($_GET['orderList']['dateStart']) ? strtotime($_GET['orderList']['dateStart'] . ' 00:00:00') : strtotime(date('Y-m-d 00:00:00', strtotime('-7 days')));
        $dateEnd = !empty($_GET['orderList']['dateEnd']) ? strtotime($_GET['orderList']['dateEnd'] . ' 23:59:59') : time();

        //计算销售收入
        $sql = "SELECT goods_amount, shipping_fee, surplus, integral_money, bonus, discount"
                . " FROM ecs_order_info"
                . " WHERE order_status IN (1,5,6,100,101,102) AND pay_status = 2 AND add_time BETWEEN $dateStart AND $dateEnd";
        $command = Yii::app()->db->createCommand($sql);
        $result = $command->queryAll();
        $salePrice = array();
        $salePrice['goods_amount'] = 0;
        $salePrice['shipping_fee'] = 0;
        $salePrice['surplus'] = 0;
        $salePrice['integral_money'] = 0;
        $salePrice['bonus'] = 0;
        $salePrice['discount'] = 0;
        if (!empty($result)) {
            foreach ($result as $key => $val) {
                $salePrice['goods_amount'] += $val['goods_amount'];
                $salePrice['shipping_fee'] += $val['shipping_fee'];
                $salePrice['surplus'] += $val['surplus'];
                $salePrice['integral_money'] += $val['integral_money'];
                $salePrice['bonus'] += $val['bonus'];
                $salePrice['discount'] += $val['discount'];
            }
        }

        //计算采购成本
        $sql = "SELECT oversea_price, shipping_fee, consumption_tax, use_coupon"
                . " FROM lis_buy_goods"
                . " WHERE add_time BETWEEN '" . date('Y-m-d H:i:s', $dateStart) . "' AND '" . date('Y-m-d H:i:s', $dateEnd) . "'";
        $command = Yii::app()->db->createCommand($sql);
        $result = $command->queryAll();
        $buyPrice = array();
        $buyPrice['oversea_price'] = 0;
        $buyPrice['shipping_fee'] = 0;
        $buyPrice['consumption_tax'] = 0;
        $buyPrice['use_coupon'] = 0;
        if (!empty($result)) {
            foreach ($result as $key => $val) {
                $buyPrice['oversea_price'] += $val['oversea_price'];
                $buyPrice['shipping_fee'] += $val['shipping_fee'];
                $buyPrice['consumption_tax'] += $val['consumption_tax'];
                $buyPrice['use_coupon'] += $val['use_coupon'];
            }
        }

        //计算运输过程中的费用
        $sql = "SELECT shipping_status, cost, money_type"
                . " FROM lis_shipping_action"
                . " WHERE add_time BETWEEN '" . date('Y-m-d H:i:s', $dateStart) . "' AND '" . date('Y-m-d H:i:s', $dateEnd) . "'";
        $command = Yii::app()->db->createCommand($sql);
        $result = $command->queryAll();
        $shippingPrice = array();
        $shippingPrice['USD_cost'] = 0;
        $shippingPrice['RMB_cost'] = 0;
        if (!empty($result)) {
            foreach ($result as $key => $val) {
                if ($val['money_type'] == 'USD') {
                    $shippingPrice['USD_cost'] += $val['cost'];
                }
                if ($val['money_type'] == 'RMB') {
                    $shippingPrice['RMB_cost'] += $val['cost'];
                }
            }
        }

        //计算手续费
        $sql = "SELECT fee_cat_id, fee"
                . " FROM lis_order_fee"
                . " WHERE add_time BETWEEN '" . date('Y-m-d H:i:s', $dateStart) . "' AND '" . date('Y-m-d H:i:s', $dateEnd) . "'";
        $command = Yii::app()->db->createCommand($sql);
        $result = $command->queryAll();
        $feePrice = array();
        if (!empty($result)) {
            foreach ($result as $key => $val) {
                if(isset(OrderFee::$feeCategory[$val['fee_cat_id']])){
                    if (!isset($feePrice[$val['fee_cat_id']])) {
                        $feePrice[$val['fee_cat_id']] = 0;
                    } else {
                        $feePrice[$val['fee_cat_id']] += $val['fee'];
                    }
                }
            }
        }
        $financial = array();
        $financial['salePrice'] = $salePrice;
        $financial['buyPrice'] = $buyPrice;
        $financial['shippingPrice'] = $shippingPrice;
        $financial['feePrice'] = $feePrice;
        
        if (isset($_POST['excel'])) {
            ExcelApi::allOrderToExcel($total, $dateStart, $dateEnd);
        }

        $this->breadcrumbs = array(
            '财务报表',
        );
        $this->render('financial', array('financial' => $financial, 'dateStart'=>$dateStart, 'dateEnd'=>$dateEnd));
    }

}
