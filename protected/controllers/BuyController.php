<?php

class BuyController extends Controller {

    public $layout = 'adminLayout';

    /*
     * 订单列表
     */ 

    public function actionOrderList() {
        $criteria = new CDbCriteria();
        $criteria->order = 't.pay_time desc,t.order_id desc';
        //$criteria->join = ' join ecs_users as b on b.user_id = t.user_id';                       
        
        if (isset($_GET['orderList'])) {
            //时间判断
            if (isset($_GET['orderList']['dateStart']) && $_GET['orderList']['dateStart']) {
                $criteria->addCondition("t.add_time >= :dateStart");
                $criteria->params += array(':dateStart' => strtotime($_GET['orderList']['dateStart'] . ' 00:00:00'));
            }

            if (isset($_GET['orderList']['dateEnd']) && $_GET['orderList']['dateEnd']) {
                $criteria->addCondition("t.add_time <= :dateEnd");
                $criteria->params += array(':dateEnd' => strtotime($_GET['orderList']['dateEnd'] . ' 23:59:59'));
            }

            //订单状态判断
            if (isset($_GET['orderList']['checkStatus']) && !empty($_GET['orderList']['checkStatus'])) {
                $criteria->compare("t.order_status", $_GET['orderList']['checkStatus']);
            }

            //付款状态判断
            if (isset($_GET['orderList']['checkPayStatus']) && !empty($_GET['orderList']['checkPayStatus'])) {
                $criteria->compare("t.pay_status", $_GET['orderList']['checkPayStatus']);
            }

            //shipping状态判断
            if (isset($_GET['orderList']['checkShippingStatus']) && !empty($_GET['orderList']['checkShippingStatus'])) {
                $criteria->compare("t.shipping_status", $_GET['orderList']['checkShippingStatus']);
            }

            //条件判断
            if (isset($_GET['orderList']['condition']) && trim($_GET['orderList']['condition'])) {
                $condition = trim($_GET['orderList']['condition']);
                if ($_GET['orderList']['checkType'] == 'username') {
                    $criteria->compare("b.user_name", $condition);
                } else if ($_GET['orderList']['checkType'] == 'ordersn') {
                    $criteria->compare("t.order_sn", $condition);
                } else if ($_GET['orderList']['checkType'] == 'goodsname') {
                    $criteria->compare("c.goods_name,", $condition);
                }
            }
        }
        //导表
        if (isset($_GET['excel'])) {
            Yii::import('application.components.PHPExcel.ExcelApi');
            if (trim($_GET['excel']) == 'all') {
                $data = Order::model()->findAll($criteria);
            } else {
                $data = Order::model()->findAll('order_id IN (' . mysql_escape_string($_GET['excel']) . ')');
            }
            if (isset($_GET['type']) && trim($_GET['type']) != 'excel-all') {
                ExcelApi::OrderDetailSimpleToExcel($data);
            } else {
                ExcelApi::OrderDetailAllToExcel($data);
            }
        }
        //var_dump($criteria);die();
        //分页显示
        $dataProvider = new CActiveDataProvider('Order', array(
            'criteria' => $criteria,
            'pagination' => array(
                'pageSize' => '18',
            )
        ));        
        $this->breadcrumbs = array(
            '订单列表',
        );
        //var_dump($dataProvider);die();
        if (Yii::app()->request->isAjaxRequest) {
            $this->renderPartial('_ajaxListView', array('dataProvider' => $dataProvider, 'itemView' => '_orderList',));
        } else {
            $this->render('orderList', array('dataProvider' => $dataProvider));
        }
    }

    /*
     * 订单采购：按供应商
     */

    public function actionBuyBySupplier() {
        $id = Yii::app()->request->getParam('id');
        $sql = 'SELECT a.goods_id, a.order_id, a.goods_name, a.goods_number, a.goods_price, a.goods_attr, b.oversea_url, b.seller_note, b.oversea_price, b.oversea_price, b.new_shop_price, c.suppliers_id, c.suppliers_name'
                . ' FROM ecs_order_goods AS a'
                . ' LEFT JOIN ecs_goods AS b ON a.goods_id = b.goods_id'
                . ' LEFT JOIN ecs_suppliers AS c ON b.suppliers_id = c.suppliers_id'
                . ' WHERE a.rec_id IN (' . mysql_escape_string($id) . ')';
        $result = Yii::app()->db->createCommand($sql);
        $allGoods = $result->queryAll();
        $goodsInfo = array();
        $totalSupplier = array();
        if (!empty($allGoods)) {
            foreach ($allGoods as $key => $val) {
                if (!empty($val['suppliers_id'])) {
                    if (empty($val['suppliers_name'])) {
                        $val['suppliers_name'] = Suppliers::model()->findByPk($val['suppliers_id'])->suppliers_name;
                    }
                } else {
                    $domainName = '';
                    if (!empty($val['oversea_url'])) {
                        if (preg_match('/[\w][\w-]*\.(?:com\.cn|com|cn|co|net|org|gov|cc|biz|info)(\/|$)/isU', $val['oversea_url'], $domain)) {
                            if (!empty($domain[0])) {
                                $domainArr = explode('.', rtrim($domain[0], '/'));
                                $domainName = $domainArr[0];
                                $val['suppliers_name'] = $domainName;
                            }
                        }
                    }
                }
                if ($val['suppliers_name'] == '') {
                    $val['suppliers_name'] = '尚未确定供应商';
                } else {
                    $totalSupplier[$val['suppliers_name']] = $val['suppliers_name'];
                }
                $goodsInfo[$val['suppliers_name']]['name'] = $val['suppliers_name'];
                $goodsInfo[$val['suppliers_name']]['suppliers_id'] = $val['suppliers_id'];
                $goodsInfo[$val['suppliers_name']]['items'][] = $val;
            }
        }
        $this->breadcrumbs = array(
            '订单列表 ' => '/buy/orderList',
            '按供应商采购',
        );
        $this->render('buyBySupplier', array('goods' => $goodsInfo, 'totalSupplier' => $totalSupplier, 'totalNum' => count($allGoods)));
    }

    /*
     * 订单采购：按供应商
     */

    public function actionBuyByOrder() {
        $id = Yii::app()->request->getParam('id');
        $sql = 'SELECT a.goods_id, a.order_id, a.goods_name, a.goods_number, a.goods_price, a.goods_attr, b.oversea_url, b.seller_note, b.oversea_price, b.oversea_price, b.new_shop_price, c.suppliers_id, c.suppliers_name, d.order_sn, d.order_amount'
                . ' FROM ecs_order_goods AS a'
                . ' LEFT JOIN ecs_goods AS b ON a.goods_id = b.goods_id'
                . ' LEFT JOIN ecs_suppliers AS c ON b.suppliers_id = c.suppliers_id'
                . ' LEFT JOIN ecs_order_info AS d ON a.order_id = d.order_id'
                . ' WHERE a.rec_id IN (' . mysql_escape_string($id) . ') ORDER BY c.suppliers_id';
        $result = Yii::app()->db->createCommand($sql);
        $allGoods = $result->queryAll();
        $goodsInfo = array();
        if (!empty($allGoods)) {
            foreach ($allGoods as $key => $val) {
                $goodsInfo[$val['order_id']]['order_id'] = $val['order_id'];
                $goodsInfo[$val['order_id']]['order_sn'] = $val['order_sn'];
                $goodsInfo[$val['order_id']]['order_amount'] = $val['order_amount'];
                $goodsInfo[$val['order_id']]['items'][] = $val;
            }
        }
        $this->breadcrumbs = array(
            '订单列表 ' => '/buy/orderList',
            '按订单采购',
        );
        $this->render('buyByOrder', array('goods' => $goodsInfo));
    }

    /*
     * 添加新采购单
     */

    public function actionAddBuyOrder() {
        $result = array();
        $orderNumber = Yii::app()->request->getParam('order_number');
        if (empty($orderNumber)) {
            $result['status'] = false;
            $result['message'] = '请填写订单号';
        } else {
            $orderPrice = Yii::app()->request->getParam('order_price');
            $supplierId = Yii::app()->request->getParam('supplier_id');
            $supplierInfo = Suppliers::model()->findByPk($supplierId);
            if (!isset($supplierInfo) || empty($supplierInfo)) {
                $result['status'] = false;
                $result['message'] = '不存在该供应商';
            } else {
//                $haveOrder = BuyOrder::model()->find('order_number = :order_number AND supplier_id = :supplier_id', array(':order_number' => $orderNumber, ':supplier_id' => $supplierId));
//                if (empty($haveOrder)) {
                $buyOrderModel = new BuyOrder();
                $buyOrderModel->order_number = $orderNumber;
                $buyOrderModel->order_price = $orderPrice;
                $buyOrderModel->supplier_id = $supplierId;
                if ($buyOrderModel->save()) {
                    $result['status'] = true;
                    $result['message'] = $buyOrderModel->id;
                } else {
                    $result['status'] = false;
                    $result['message'] = '添加失败，请稍后重试';
                }
//                } else {
//                    $result['status'] = false;
//                    $result['message'] = '该采购单号已经存在';
//                }
            }
        }
        echo json_encode($result);
    }

    /*
     * 购买时添加供应商
     */

    public function actionAddSupplierWhenBuy() {
        $supplier = trim(Yii::app()->request->getParam('supplier'));
        $type = trim(Yii::app()->request->getParam('type'));
        $result = array();
        if ($type == 1) {
            $result['status'] = false;
            $result['message'] = '请填写供应商';
        } else {
            if (is_string($supplier) && !empty($supplier)) {
                $haveSupplier = Suppliers::model()->find('suppliers_name = :suppliers_name', array(':suppliers_name' => $supplier));
                if (empty($haveSupplier)) {
                    $supplierModel = new Suppliers();
                    $supplierModel->suppliers_name = $supplier;
                    $supplierModel->is_check = 1;
                    if ($id = $supplierModel->save()) {
                        $result['status'] = true;
                        $result['message'] = $supplierModel->attributes['suppliers_id'];
                    } else {
                        $result['status'] = false;
                        $result['message'] = '供应商添加失败！';
                    }
                } else {
                    $result['status'] = false;
                    $result['message'] = '供应商已经存在！';
                }
            } else {
                $result['status'] = false;
                $result['message'] = '请填写供应商！';
            }
        }
        echo json_encode($result);
    }

    /*
     * 购买商品记录
     */

    public function actionAddBuyProducts() {
        $orderNum = trim(Yii::app()->request->getParam('orderNum'));
        $data = trim(Yii::app()->request->getParam('data'));
        $orderInfo = json_decode($data, true);
        $result = array();
        //校验ordernumber的合法性
        $haveOrderNumber = BuyOrder::model()->findByPk($orderNum);
        if (empty($haveOrderNumber) || !isset($haveOrderNumber)) {
            $result['status'] = false;
            $result['message'] = '该订单不存在！';
        } else {
            if (!empty($orderInfo)) {
                $successGoods = array();
                $errorMsg = array();
                $successAll = 1;
                foreach ($orderInfo as $key => $val) {
                    //校验该订单产品是否存在
                    $haveOrderGoods = BuyOrderGoods::model()->find('order_id = :order_id AND buy_order_id = :buy_order_id AND goods_id = :goods_id AND del_flag = 0', array(':order_id' => $val['order_id'], ':buy_order_id' => $orderNum, ':goods_id' => $val['goods_id']));
                    if (empty($haveOrderGoods)) {
                        $buyOrderGoodsModel = new BuyOrderGoods();
                        $buyOrderGoodsModel->buy_order_id = $orderNum;
                        foreach ($val as $i => $good) {
                            if (isset($good) && in_array($i, $buyOrderGoodsModel->attributeNames())) {
                                if (!is_array($good)) {
                                    $good = addslashes(trim($good));
                                }
                                $buyOrderGoodsModel->$i = $good;
                            }
                        }
                        if ($buyOrderGoodsModel->save()) {
                            $successGoods[] = $val['goods_id'];
                            /* 若商品全部购买，则更改订单状态 */
                            $webOrderInfo = Order::model()->findByPk($val['order_id']);
                            if ($webOrderInfo->orderGoodsCount == $webOrderInfo->buyOrderGoodsCount) {
                                $webOrderInfo->shipping_status = 3;
                                $webOrderInfo->save();
                            }
                            if ($successAll == 1) {
                                $successAll = 1;
                            }
                        } else {
                            $successAll = 0;
                            $errorMsg['saveError'][] = $val['goods_id'];
                        }
                    } else {
                        if ($successAll == 1) {
                            $successAll = 1;
                        }
                        $errorMsg['haveBought'][] = $val['goods_id'];
                    }
                }
                if ($successAll == 1) {
                    $result['status'] = true;
                    $result['success'] = $successGoods;
                } else {
                    $result['status'] = false;
                    $msg = '';
                    if (!empty($errorMsg['saveError'])) {
                        $msg .= '商品ID为' . implode(',', $errorMsg['saveError']) . '保存失败！';
                    }
                    if (!empty($errorMsg['haveBought'])) {
                        $msg .= '商品ID为' . implode(',', $errorMsg['haveBought']) . '已经购买！';
                    }
                    $result['message'] = $msg;
                    $result['success'] = $successGoods;
                }
            } else {
                $result['status'] = false;
                $result['message'] = '信息不全！';
            }
        }
        echo json_encode($result);
    }

    /*
     * 订单详情列表
     */

    public function actionOrderDetail() {
        $orderId = trim(Yii::app()->request->getParam('orderId'));
        //检验ID的合法性
        $orderInfo = Order::model()->findByPk($orderId);         
        if($orderInfo->user_id){
            $userInfo = Users::model()->findByPk($orderInfo->user_id);
        }else{
            $userInfo = new Users();
            $userInfo->user_name = '匿名用户';
        }         
        //订单日志，整合用户操作、管理员操作等
        $orderLog = array();
        /* 下单 */
        $orderLog[$orderInfo->add_time]['time'] = $orderInfo->add_time;
        $orderLog[$orderInfo->add_time]['operator'] = $userInfo->user_name;
        $orderLog[$orderInfo->add_time]['action'] = "下单";
        //print_r($orderLog);die();
        /* 确认 */
        if (!empty($orderInfo->confirm_time)) {
            $orderAction_1 = $this->delOrderAction($orderInfo->confirm_time);
            if (!empty($orderAction_1)) {
                $confirmInfo = $orderAction_1;
            } else {
                $confirmInfo['time'] = $orderInfo->confirm_time;
                $confirmInfo['operator'] = $userInfo->user_name;
                $confirmInfo['action'] = "订单确认";
            }
            $orderLog[$orderInfo->confirm_time] = array();
            if ($orderInfo->confirm_time == $orderInfo->pay_time && !empty($orderInfo->pay_time)) {
                array_push($orderLog[$orderInfo->confirm_time], $confirmInfo);
            } else {
                $orderLog[$orderInfo->confirm_time] = $confirmInfo;
            }
        }
        /* 付款 */
        if (!empty($orderInfo->pay_time)) {
            $payInfo = array();
            $orderAction_2 = $this->delOrderAction($orderInfo->pay_time);
            if (!empty($orderAction_2)) {
                $payInfo = $orderAction_2;
            } else {
                $payInfo['time'] = $orderInfo->pay_time;
                $payInfo['operator'] = $userInfo->user_name;
                $payInfo['action'] = "付款";
            }
            if ($orderInfo->confirm_time == $orderInfo->pay_time) {
                array_push($orderLog[$orderInfo->pay_time], $payInfo);
            } else {
                $orderLog[$orderInfo->pay_time] = $payInfo;
            }
        }
        /* 发货 */
        if (!empty($orderInfo->shipping_time)) {
            $orderAction_3 = $this->delOrderAction($orderInfo->shipping_time);
            if (!empty($orderAction_3)) {
                $orderLog[$orderInfo->shipping_time] = $orderAction_3;
            } else {
                $orderLog[$orderInfo->shipping_time]['time'] = $orderInfo->shipping_time;
                $orderLog[$orderInfo->shipping_time]['operator'] = $userInfo->user_name;
                $orderLog[$orderInfo->shipping_time]['action'] = "发货";
            }
        }
        /* 物流信息 */
        if (!empty($orderInfo->shippingInfo)) {
            foreach ($orderInfo->shippingInfo as $key => $val) {
                $adminInfo = AdminUser::model()->findByPk($val->admin_user);
                $time = strtotime($val->add_time);
                $orderLog[$time]['time'] = $time;
                $orderLog[$time]['operator'] = $adminInfo->user_name . '（管理员）';
                $orderGoods = OrderGoods::model()->findByPk($val->rec_id);
                if (!empty($orderGoods)) {
                    $orderLog[$time]['action'] = "更改【" . $orderGoods->goods_name . "】物流状态：<span class='text-danger'>" . Order::$shipStatus[$val->shipping_status] . '</span>';
                } else {
                    $orderLog[$time]['action'] = "更改商品ID【" . $val->rec_id . "】物流状态：<span class='text-danger'>" . Order::$shipStatus[$val->shipping_status] . '</span>';
                }
            }
        }
        /* 操作日志 */
        $actionLog = OrderAction::model()->findAll('order_id = :order_id', array(':order_id' => $orderInfo->order_id));
        if (!empty($actionLog)) {
            foreach ($actionLog as $key => $val) {
                $orderLog[$val['log_time']] = $this->delOrderAction($val['log_time']);
            }
        }
        ksort($orderLog);
        $this->breadcrumbs = array(
            '订单列表 ' => '/buy/orderList',
            '订单详情',
        );
        //print_r($orderInfo->order_id);die();
        $this->render('orderdetail', array('orderInfo' => $orderInfo, 'orderLog' => $orderLog));
    }

    /*
     * 更新商品的shipping status
     */

    function actionChangeShippingStatus() {
        $shippingAction = new ShippingAction();
        $shippingAction->rec_id = trim(Yii::app()->request->getParam('recId'));
        $shippingAction->order_id = trim(Yii::app()->request->getParam('orderId'));
        $shippingAction->shipping_status = trim(Yii::app()->request->getParam('actionValue'));
        $shippingAction->delivery_company = trim(Yii::app()->request->getParam('deliveryCompany'));
        $shippingAction->delivery_number = trim(Yii::app()->request->getParam('deliveryNumber'));
        $shippingAction->cost = trim(Yii::app()->request->getParam('cost'));
        if ($shippingAction->cost != '') {
            $shippingAction->money_type = trim(Yii::app()->request->getParam('moneyType'));
        }
        $shippingAction->note = trim(Yii::app()->request->getParam('note'));
        $shippingAction->time = trim(Yii::app()->request->getParam('time'));
        if ($shippingAction->save()) {
            $orderInfo = Order::model()->findByPk($shippingAction->order_id);
            //若购买商品均在当前状态下，则改变状态
            $orderStatusCount = ShippingAction::model()->count('order_id = :order_id AND shipping_status = :shipping_status AND del_flag = 0', array(':order_id' => $orderInfo->order_id, ':shipping_status' => $shippingAction->shipping_status));
            if ($orderInfo->orderGoodsCount == $orderStatusCount) {
                $orderInfo->shipping_status = $shippingAction->shipping_status;
                if ($orderInfo->save()) {
                    echo 1;
                } else {
                    echo 0;
                }
            } else {
                echo 1;
            }
        } else {
            echo 0;
        }
    }

    /*
     * 更新商品的shipping status
     */

    function actionUpdateShippingStatus() {
        $result = array();
        $id = trim(Yii::app()->request->getParam('id'));
        $cont = trim(Yii::app()->request->getParam('cont'));
        $column = trim(Yii::app()->request->getParam('column'));
        $shippingStatus = ShippingAction::model()->find('id = :id', array(':id' => $id));
        if (!empty($shippingStatus)) {
            $shippingStatus->$column = $cont;
            if ($shippingStatus->save()) {
                $result['status'] = true;
                $result['message'] = '更新成功';
            } else {
                $result['status'] = false;
                $result['message'] = '更新失败';
            }
        } else {
            $result['status'] = false;
            $result['message'] = '该信息不存在';
        }
        echo json_encode($result);
    }

    /*
     * 删除商品的shipping status
     */

    function actionDelShippingStatus() {
        $id = trim(Yii::app()->request->getParam('id'));
        $shippingStatus = ShippingAction::model()->findByPk($id);
        $result = array();
        if (!empty($shippingStatus)) {
            $shippingStatus->del_flag = 1;
            if ($shippingStatus->save()) {
                $result['status'] = true;
                $result['message'] = '删除成功！';
            } else {
                $result['status'] = false;
                $result['message'] = '删除失败！';
            }
        } else {
            $result['status'] = false;
            $result['message'] = '不存在该数据！';
        }
        echo json_encode($result);
    }

    /*
     * 处理订单操作
     */

    function delOrderAction($time) {
        $result = array();
        $haveAction = OrderAction::model()->find('log_time = :log_time', array(':log_time' => $time));
        if (!empty($haveAction)) {
            $result['time'] = $time;
            $result['operator'] = $haveAction->action_user;
            $result['action'] = Order::$orderStatus[$haveAction->order_status] . '，' . Order::$payStatus[$haveAction->pay_status] . '，' . Order::$shipStatus[$haveAction->shipping_status];
            if (!empty($haveAction->action_note)) {
                $result['action'] .= '（' . $haveAction->action_note . '）';
            }
        }
        return $result;
    }

    /*
     * 删除购物单
     */

    function actionDeleteBuyOrder() {
        $result = array();
        $id = trim(Yii::app()->request->getParam('id'));
        $buyOrderGoods = BuyOrderGoods::model()->find('id = :id', array(':id' => $id));
        if (!empty($buyOrderGoods)) {
            $buyOrderGoods->del_flag = 1;
            if ($buyOrderGoods->save()) {
                $result['status'] = true;
                $result['message'] = '删除成功';
            } else {
                $result['status'] = false;
                $result['message'] = '删除失败';
            }
        } else {
            $result['status'] = false;
            $result['message'] = '无法删除信息';
        }
        echo json_encode($result);
    }

    /*
     * 更新购物订单
     */

    function actionUpdateBuyOrder() {
        $result = array();
        $id = trim(Yii::app()->request->getParam('id'));
        $cont = trim(Yii::app()->request->getParam('cont'));
        $column = trim(Yii::app()->request->getParam('column'));
        $buyOrderGoods = BuyOrderGoods::model()->find('id = :id', array(':id' => $id));
        if (!empty($buyOrderGoods)) {
            $buyOrderGoods->$column = $cont;
            if ($buyOrderGoods->save()) {
                $result['status'] = true;
                $result['message'] = '更新成功';
            } else {
                $result['status'] = false;
                $result['message'] = '更新失败';
            }
        } else {
            $result['status'] = false;
            $result['message'] = '该信息不存在';
        }
        echo json_encode($result);
    }

    /*
     * 采购列表
     */

    public function actionBuyList() {
        $criteria = new CDbCriteria();
        $criteria->condition = 't.del_flag = 0';
        $criteria->order = 't.add_time desc';
        $criteria->join = 'join lis_buy_order as b on b.id = t.buy_order_id';
        $criteria->join .= ' join ecs_goods as c on c.goods_id = t.goods_id';
        if (isset($_GET['buyList'])) {
            //时间判断
            if (isset($_GET['buyList']['dateStart']) && $_GET['buyList']['dateStart']) {
                $criteria->addCondition("t.add_time >= :dateStart");
                $criteria->params += array(':dateStart' => strtotime($_GET['buyList']['dateStart'] . ' 00:00:00'));
            }

            if (isset($_GET['buyList']['dateEnd']) && $_GET['buyList']['dateEnd']) {
                $criteria->addCondition("t.add_time <= :dateEnd");
                $criteria->params += array(':dateEnd' => strtotime($_GET['buyList']['dateEnd'] . ' 23:59:59'));
            }

            //供应商判断
            if (isset($_GET['buyList']['suppliers']) && $_GET['buyList']['suppliers'] > 0) {
                $criteria->compare("b.supplier_id", $_GET['buyList']['suppliers']);
            }

            //条件判断
            if (isset($_GET['buyList']['condition']) && !empty($_GET['buyList']['condition'])) {
                $condition = trim($_GET['buyList']['condition']);
                if ($_GET['buyList']['checkType'] == 'ordersn') {
                    $criteria->compare("t.order_id", $condition);
                } else if ($_GET['buyList']['checkType'] == 'goodsname') {
                    $criteria->addSearchCondition("c.goods_name", $condition, true);
                }
            }
        }
        //导表
        if (isset($_GET['excel'])) {
            Yii::import('application.components.PHPExcel.ExcelApi');
            if (trim($_GET['excel']) == 'all') {
                $data = BuyOrderGoods::model()->findAll($criteria);
            } else {
                $data = BuyOrderGoods::model()->findAll('id IN (' . mysql_escape_string($_GET['excel']) . ')');
            }
            if (isset($_GET['type']) && trim($_GET['type']) == 'excel-order') {
                ExcelApi::ListForOrderToExcel($data);
            } else if (isset($_GET['type']) && trim($_GET['type']) == 'excel-buy-order') {
                ExcelApi::ListForBuyOrderToExcel($data);
            } else if (isset($_GET['type']) && trim($_GET['type']) == 'excel-order-all-info') {
                ExcelApi::ListForOrderAllInfoToExcel($data);
            }
        }
        //分页显示
        $dataProvider = new CActiveDataProvider('BuyOrderGoods', array(
            'criteria' => $criteria,
            'pagination' => array(
                'pageSize' => '18',
            )
        ));
        $postData = $dataProvider->getData();
        $data = array();
        if (!empty($postData)) {
            //按照网站订单显示
            foreach ($postData as $key => $val) {
                $eachArr = array();
                $eachArr['goods_id'] = $val->goods_id;
                $eachArr['goods_name'] = Goods::model()->findByPk($val->goods_id)->goods_name;
                $orderInfo = Order::model()->findByPk($val->order_id);
                $eachArr['order_id'] = $val->order_id;
                $eachArr['order_sn'] = !empty($orderInfo) ? $orderInfo->order_sn : '';
                $eachArr['buy_order_id'] = $val->buy_order_id;
                $eachArr['buy_order_goods_id'] = $val->id;
                $eachArr['buy_number'] = $val->buy_number;
                $eachArr['oversea_price'] = $val->oversea_price;
                $eachArr['shipping_fee'] = $val->shipping_fee;
                $eachArr['consumption_tax'] = $val->consumption_tax;
                $eachArr['use_coupon'] = $val->use_coupon;
                $eachArr['oversea_url'] = $val->oversea_url;
                $eachArr['note'] = $val->note;
                $eachArr['add_time'] = $val->add_time;
                $buyOrder = BuyOrder::model()->findByPk($val->buy_order_id);
                if (!empty($buyOrder)) {
                    $suppliers = Suppliers::model()->findByPk($buyOrder->supplier_id);
                    $eachArr['supplier_id'] = $buyOrder->supplier_id;
                    $eachArr['supplier_name'] = !empty($suppliers) ? $suppliers->suppliers_name : '';
                    $eachArr['order_price'] = $buyOrder->order_price;
                    $eachArr['buy_order_number'] = $buyOrder->order_number;
                    $eachArr['exchange_tax'] = $buyOrder->exchange_tax;
                }
                $data[$val->order_id][] = $eachArr;
            }
        }
        $this->breadcrumbs = array(
            '订单列表',
        );
        $this->render('buyList', array('dataProvider' => $dataProvider, 'data' => $data));
    }

    /*
     * 更新手续费
     */

    function actionUpdateOrderFee() {
        $result = array();
        $orderId = trim(Yii::app()->request->getParam('order_id'));
        $cat = trim(Yii::app()->request->getParam('cat'));
        $price = trim(Yii::app()->request->getParam('price'));
        $feeInfo = OrderFee::model()->find('order_id = :order_id AND fee_cat_id = :fee_cat_id', array(':order_id' => $orderId, ':fee_cat_id' => $cat));
        if (!empty($feeInfo)) {
            $feeInfo->fee = $price;
            if ($feeInfo->save()) {
                $result['status'] = true;
                $result['message'] = '更新成功';
            } else {
                $result['status'] = false;
                $result['message'] = '更新失败';
            }
        } else {
            $orderFeeModel = new OrderFee();
            $orderFeeModel->order_id = $orderId;
            $orderFeeModel->fee_cat_id = $cat;
            $orderFeeModel->fee = $price;
            if ($orderFeeModel->save()) {
                $result['status'] = true;
                $result['message'] = '更新成功';
            } else {
                $result['status'] = false;
                $result['message'] = '更新失败';
            }
        }
        echo json_encode($result);
    }

}
