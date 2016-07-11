<?php

set_time_limit(0);
ini_set('memory_limit', '256M');
Yii::import('application.components.PHPExcel.PHPExcel', true);

class ExcelApi {

    static $item = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI');

    /*
     * 销售总记录导表
     */

    public static function allOrderToExcel($data, $dateStart = '', $dateEnd = '') {
        //新建 
        $objectPHPExcel = new PHPExcel();
        if (!empty($dateStart) && !empty($dateEnd)) {
            $title = '销售总记录表（' . date('Y-m-d', $dateStart) . ' 至 ' . date('Y-m-d', $dateEnd) . '）';
        } else {
            $title = '销售总记录表（至' . date('Y-m-d') . '）';
        }

        //报表头设置  
        $endColumn = self::$item[count(Order::$orderStatus)] . '1';
        $objectPHPExcel->getActiveSheet()->mergeCells("A1:$endColumn");
        $objectPHPExcel->getActiveSheet()->setCellValue('A1', $title);
        $objectPHPExcel->setActiveSheetIndex(0)->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        //表格头的输出
        $num = 0;
        $objectPHPExcel->getActiveSheet()->setCellValue('A2', '产品类目');
        foreach (Order::$orderStatus as $key => $val) {
            $num++;
            $objectPHPExcel->getActiveSheet()->setCellValue(self::$item[$num] . '2', $val);
        }
        //$objectPHPExcel->getActiveSheet()->setCellValue(self::$item[$num + 1] . '2', '总和（不包括已取消/无效/退货）');
        //列项目
        $num = 2;
        $totalSum = 0;
        $allParent = Category::model()->findAll('parent_id = 0');
        foreach ($allParent as $key => $val) {
            $num++;
            $objectPHPExcel->getActiveSheet()->setCellValue("A$num", $val['cat_name']);
            $catSum = 0;
            $itemNum = 0;
            foreach (Order::$orderStatus as $k => $v) {
                $itemNum++;
                $sum = !empty($data[$k][$val['cat_id']]) ? array_sum($data[$k][$val['cat_id']]) : 0;
                if ($k != 4 && $k != 3 && $k != 2) {
                    $catSum += $sum;
                    $totalSum += $sum;
                }
                $objectPHPExcel->getActiveSheet()->setCellValue(self::$item[$itemNum] . $num, $sum);
            }
            //$objectPHPExcel->getActiveSheet()->setCellValue(self::$item[$itemNum + 1] . $num, $catSum);
        }
        //设置边框
        for ($x = 0; $x <= count(Order::$orderStatus); $x++) {
            for ($y = 2; $y <= $num; $y++) {
                $objectPHPExcel->getActiveSheet()->getStyle(self::$item[$x] . $y . ':' . self::$item[$x] . $y)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                $objectPHPExcel->getActiveSheet()->getStyle(self::$item[$x] . $y . ':' . self::$item[$x] . $y)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                $objectPHPExcel->getActiveSheet()->getStyle(self::$item[$x] . $y . ':' . self::$item[$x] . $y)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                $objectPHPExcel->getActiveSheet()->getStyle(self::$item[$x] . $y . ':' . self::$item[$x] . $y)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            }
        }

        $objectPHPExcel->getActiveSheet()->setTitle('销售总记录表');

        //设置导出文件名  
        $xlsWriter = new PHPExcel_Writer_Excel5($objectPHPExcel);

        ob_end_clean();
        ob_start();
        header("Pragma: public");
        header("Expires: 0");
        header('Content-Type: application/vnd.ms-excel;charset=utf8');
        header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header("Content-Disposition:inline;filename=销售总记录表.xls");
        header("Content-Transfer-Encoding: binary");
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Pragma: no-cache");
        $xlsWriter->save("php://output");
    }

    /*
     * 已销售总记录导表
     */

    public static function allSaleOrderToExcel($data, $dateStart = '', $dateEnd = '') {
        //新建 
        $objectPHPExcel = new PHPExcel();
        if (!empty($dateStart) && !empty($dateEnd)) {
            $title = '销售总记录表（' . date('Y-m-d', $dateStart) . ' 至 ' . date('Y-m-d', $dateEnd) . '）';
        } else {
            $title = '销售总记录表（至' . date('Y-m-d') . '）';
        }

        //报表头设置  
        $objectPHPExcel->getActiveSheet()->mergeCells("A1:E1");
        $objectPHPExcel->getActiveSheet()->setCellValue('A1', $title);
        $objectPHPExcel->setActiveSheetIndex(0)->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        //表格头的输出
        $objectPHPExcel->getActiveSheet()->mergeCells("A2:B2");
        $objectPHPExcel->getActiveSheet()->setCellValue('A2', '产品类目');
        $objectPHPExcel->getActiveSheet()->setCellValue('C2', '已销售产品目录');
        $objectPHPExcel->getActiveSheet()->setCellValue('D2', '已销售产品金额/元');
        $objectPHPExcel->getActiveSheet()->setCellValue('E2', '该类目销售产品总额/元');

        $totalNum = 0;
        $totalMoney = 0;
        $alltotalMoney = 0;
        $num = 3;
        foreach ($data as $key => $val) {
            $totalNum += $val['total_number'];
            $totalMoney += $val['total_money'];
            $objectPHPExcel->getActiveSheet()->setCellValue('A' . $num, Category::model()->findByPk($key)->cat_name);
            $objectPHPExcel->getActiveSheet()->setCellValue('B' . $num, '');
            $objectPHPExcel->getActiveSheet()->setCellValue('C' . $num, $val['total_number']);
            $objectPHPExcel->getActiveSheet()->setCellValue('D' . $num, '');
            $objectPHPExcel->getActiveSheet()->setCellValue('E' . $num, $val['total_money']);
            foreach ($val as $k => $v) {
                if (is_numeric($k)) {
                    $num++;
                    $alltotalMoney += $v['total_money'];
                    $objectPHPExcel->getActiveSheet()->setCellValue('A' . $num, '');
                    $objectPHPExcel->getActiveSheet()->setCellValue('B' . $num, !empty(Category::model()->findByPk($k)->cat_name) ? Category::model()->findByPk($k)->cat_name : '');
                    $objectPHPExcel->getActiveSheet()->setCellValue('C' . $num, count($v['number']) > 1 ? array_sum($v['number']) : $v['number'][0]);
                    $objectPHPExcel->getActiveSheet()->setCellValue('D' . $num, $v['total_money']);
                    $objectPHPExcel->getActiveSheet()->setCellValue('E' . $num, $v['total_money']);
                }
            }
            $num++;
        }
        $objectPHPExcel->getActiveSheet()->mergeCells('A' . $num . ":" . 'B' . $num);
        $objectPHPExcel->getActiveSheet()->setCellValue('A' . $num, '总计');
        $objectPHPExcel->getActiveSheet()->setCellValue('B' . $num, '');
        $objectPHPExcel->getActiveSheet()->setCellValue('C' . $num, $totalNum);
        $objectPHPExcel->getActiveSheet()->setCellValue('D' . $num, $alltotalMoney);
        $objectPHPExcel->getActiveSheet()->setCellValue('E' . $num, $totalMoney);

        for ($x = 1; $x <= $num; $x++) {
            $objectPHPExcel->getActiveSheet()->getRowDimension($x)->setRowHeight(25);
        }
        //设置边框
        for ($x = 0; $x < 5; $x++) {
            for ($y = 2; $y <= $num; $y++) {
                $objectPHPExcel->getActiveSheet()->getStyle(self::$item[$x] . $y . ':' . self::$item[$x] . $y)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                $objectPHPExcel->getActiveSheet()->getStyle(self::$item[$x] . $y . ':' . self::$item[$x] . $y)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                $objectPHPExcel->getActiveSheet()->getStyle(self::$item[$x] . $y . ':' . self::$item[$x] . $y)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                $objectPHPExcel->getActiveSheet()->getStyle(self::$item[$x] . $y . ':' . self::$item[$x] . $y)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            }
        }

        //设置对齐方式
        $objectPHPExcel->getActiveSheet()->getStyle('A1:F' . $num)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objectPHPExcel->getActiveSheet()->getStyle('A1:F' . $num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        //设置字体
        $objectPHPExcel->getActiveSheet()->getStyle('A1:F' . $num)->getFont()->setSize(9);
        //设置宽/高度
        $objectPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $objectPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $objectPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $objectPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $objectPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);

        $objectPHPExcel->getActiveSheet()->setTitle('销售总记录表');

        //设置导出文件名  
        $xlsWriter = new PHPExcel_Writer_Excel5($objectPHPExcel);

        ob_end_clean();
        ob_start();
        header("Pragma: public");
        header("Expires: 0");
        header('Content-Type: application/vnd.ms-excel;charset=utf8');
        header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header("Content-Disposition:inline;filename=销售总记录表.xls");
        header("Content-Transfer-Encoding: binary");
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Pragma: no-cache");
        $xlsWriter->save("php://output");
    }

    /*
     * 订单详情表（简要版）
     */

    public static function OrderDetailSimpleToExcel($data) {
        //数据处理 order
        $orderDetail = array();
        $orderTime = array();
        $payTime = array();
        foreach ($data as $key => $val) {
            $detail = array();
            $detail['order_sn'] = $val->order_sn;
            $detail['consignee'] = $val->consignee;
            //$detail['address'] = $val->address;
            $detail['address'] = '['.$val->countryInfo->region_name .' '.$val->provinceInfo->region_name.' '.$val->cityInfo->region_name.' ' .$val->districtInfo->region_name.'] '.$val->address;
            $detail['tel'] = $val->tel;
            $detail['mobile'] = $val->mobile;
            $detail['pay_time'] = $val->pay_time;
            $detail['add_time'] = $val->add_time;
            if (!empty($val->add_time)) {
                $orderTime[] = $val->add_time;
            }
            if (!empty($val->pay_time)) {
                $payTime[] = $val->pay_time;
            }
            /*
             * 订单产品
             */
            if (!empty($val->ordergoods)) {
                foreach ($val->ordergoods as $k => $v) {
                    $goods = array();
                    $goods['goods_name'] = $v->goods_name;
                    $goods['goods_attr'] = trim($v->goods_attr);
                    $goods['goods_number'] = $v->goods_number;
                    $goods['goods_price'] = $v->goods_price;
                    if (!empty($v->goodsInfo->suppliers)) {
                        $goods['supplier'] = $v->goodsInfo->suppliers->suppliers_name;
                    } else {
                        $goods['supplier'] = '/';
                    }
                    $detail['goods_info'][] = $goods;
                }
            }
            $orderDetail[] = $detail;
        }
        if (!empty($orderTime)) {
            $dateStart = date('m月d日', min($orderTime));
            $dateEnd = date('m月d日', max($orderTime));
        }
        //新建 
        $objectPHPExcel = new PHPExcel();
        $title = '订单详情表';
        $title .= '（';
        if (!empty($orderTime)) {
            $dateStart = date('m月d日', min($orderTime));
            $dateEnd = date('m月d日', max($orderTime));
            $title .= '下单时间：' . $dateStart . ' 至 ' . $dateEnd . ' ';
        }
        if (!empty($payTime)) {
            $dateStart = date('m月d日', min($payTime));
            $dateEnd = date('m月d日', max($payTime));
            $title .= ' 付款时间：' . $dateStart . ' 至 ' . $dateEnd;
        }
        $title .= '）';
        $objectPHPExcel->setActiveSheetIndex(0);
        //报表头设置  
        $objectPHPExcel->getActiveSheet()->mergeCells("A1:F1");
        $objectPHPExcel->getActiveSheet()->setCellValue('A1', $title);
        $objectPHPExcel->setActiveSheetIndex(0)->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        //表格头的输出
        $objectPHPExcel->getActiveSheet()->setCellValue('A2', '订单号');
        $objectPHPExcel->getActiveSheet()->setCellValue('B2', '联系方式');
        $objectPHPExcel->getActiveSheet()->setCellValue('C2', '产品名称');
        $objectPHPExcel->getActiveSheet()->setCellValue('D2', '数量');
        $objectPHPExcel->getActiveSheet()->setCellValue('E2', '单价/元');
        $objectPHPExcel->getActiveSheet()->setCellValue('F2', '采购商家');

        $horNum = 3;
        foreach ($orderDetail as $k => $v) {
            if (!empty($v['goods_info'])) {
                $num = count($v['goods_info']);
            } else {
                $num = 1;
            }
            if ($num > 1) {
                $objectPHPExcel->getActiveSheet()->mergeCells('A' . $horNum . ":" . 'A' . ($horNum + $num - 1));
                $objectPHPExcel->getActiveSheet()->mergeCells('B' . $horNum . ":" . 'B' . ($horNum + $num - 1));
            }

            $objectPHPExcel->getActiveSheet()->setCellValueExplicit('A' . $horNum, $v['order_sn'], PHPExcel_Cell_DataType::TYPE_STRING);

            //联系方式
            $contact = "收货人：" . $v['consignee'] . "\n";
            $contact .= "地址：" . $v['address'] . "\n";
            $contact .= '电话：' . $v['tel'];
            $objectPHPExcel->getActiveSheet()->setCellValue('B' . $horNum, $contact);
            $objectPHPExcel->getActiveSheet()->getStyle('B' . $horNum)->getAlignment()->setWrapText(true);

            if (!empty($v['goods_info'])) {
                foreach ($v['goods_info'] as $i => $j) {
                    $goods = $j['goods_name'];
                    if (!empty($j['goods_attr'])) {
                        $goods .= '（' . $j['goods_attr'] . '）';
                    }
                    $objectPHPExcel->getActiveSheet()->setCellValue('C' . ($horNum + $i), $goods);
                    $objectPHPExcel->getActiveSheet()->getStyle('C' . ($horNum + $i))->getAlignment()->setWrapText(true);
                    $objectPHPExcel->getActiveSheet()->setCellValue('D' . ($horNum + $i), $j['goods_number']);
                    $objectPHPExcel->getActiveSheet()->setCellValue('E' . ($horNum + $i), $j['goods_price']);
                    $objectPHPExcel->getActiveSheet()->setCellValue('F' . ($horNum + $i), $j['supplier']);
                    $objectPHPExcel->getActiveSheet()->getStyle('F' . ($horNum + $i))->getAlignment()->setWrapText(true);
                }
            }
            $horNum += $num;
        }
        //设置对齐方式
        $objectPHPExcel->getActiveSheet()->getStyle('A1:F' . $horNum)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objectPHPExcel->getActiveSheet()->getStyle('A1:F' . $horNum)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objectPHPExcel->getActiveSheet()->getStyle('B3:C' . $horNum)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        //设置字体
        $objectPHPExcel->getActiveSheet()->getStyle('A1:F' . $horNum)->getFont()->setSize(9);
        //设置宽/高度
        $objectPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
        $objectPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(18);
        $objectPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
        $objectPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(5);
        $objectPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(8);
        $objectPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(13);
        $objectPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(30);
        $objectPHPExcel->getActiveSheet()->getRowDimension('2')->setRowHeight(30);
        //设置边框
        if (count($orderDetail) < 45) {
            for ($x = 0; $x < 6; $x++) {
                for ($y = 2; $y < $horNum; $y++) {
                    $objectPHPExcel->getActiveSheet()->getStyle(self::$item[$x] . $y . ':' . self::$item[$x] . $y)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                    $objectPHPExcel->getActiveSheet()->getStyle(self::$item[$x] . $y . ':' . self::$item[$x] . $y)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                    $objectPHPExcel->getActiveSheet()->getStyle(self::$item[$x] . $y . ':' . self::$item[$x] . $y)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                    $objectPHPExcel->getActiveSheet()->getStyle(self::$item[$x] . $y . ':' . self::$item[$x] . $y)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                }
            }
        }

        $objectPHPExcel->getActiveSheet()->setTitle('订单详情表');

        //设置导出文件名  
        $xlsWriter = new PHPExcel_Writer_Excel5($objectPHPExcel);

        ob_end_clean();
        ob_start();
        header("Pragma: public");
        header("Expires: 0");
        header("Content-Type: text/html; charset=UTF-8");
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header("Content-Disposition:attachment;filename=订单详情表.xls");
        header("Content-Transfer-Encoding: binary");
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Pragma: no-cache");
        $xlsWriter->save("php://output");
    }

    /*
     * 订单详情表(详细版)
     */

    public static function OrderDetailAllToExcel($data) {
        //数据处理 order
        $orderDetail = array();
        $orderTime = array();
        $payTime = array();
        foreach ($data as $key => $val) {
            $detail = array();
            $detail['order_sn'] = $val->order_sn;
            $detail['user_id'] = $val->user_id;
            $detail['user_name'] = $val->userInfo->user_name;
            $detail['mobile_phone'] = $val->userInfo->mobile_phone;
            $detail['consignee'] = $val->consignee;
            //$detail['address'] = $val->address;
            $detail['address'] = '['.$val->countryInfo->region_name .' '.$val->provinceInfo->region_name.' '.$val->cityInfo->region_name.' ' .$val->districtInfo->region_name.'] '.$val->address;
            $detail['tel'] = $val->tel;
            $detail['mobile'] = $val->mobile;
            $detail['pay_time'] = $val->pay_time;
            $detail['add_time'] = $val->add_time;
            $detail['goods_amount'] = $val->goods_amount;
            $detail['surplus'] = $val->surplus;
            $detail['integral_money'] = $val->integral_money;
            $detail['bonus'] = $val->bonus;
            $detail['discount'] = $val->discount;
            $detail['shipping_fee'] = $val->shipping_fee;
            $detail['should_pay'] = $val->getShouldPay();
            $detail['total_fee'] = $val->getTotalFee();
            $detail['status'] = implode(',', $val->getOrderStatus());
            if (!empty($val->add_time)) {
                $orderTime[] = $val->add_time;
            }
            if (!empty($val->pay_time)) {
                $payTime[] = $val->pay_time;
            }
            /*
             * 订单产品
             */
            if (!empty($val->ordergoods)) {
                foreach ($val->ordergoods as $k => $v) {
                    $goods = array();
                    $goods['goods_name'] = $v->goods_name;
                    $goods['goods_attr'] = trim($v->goods_attr);
                    $goods['goods_number'] = $v->goods_number;
                    $goods['goods_price'] = $v->goods_price;
                    if (!empty($v->goodsInfo->catInfo)) {
                        $catInfo = $v->goodsInfo->catInfo;
                        $goods['cat_name'] = !empty($catInfo->cat_name) ? $catInfo->cat_name : '/';
                        $parentId = Category::findParentId($catInfo->cat_id);
                        $goods['parent_name'] = !empty($parentId) ? Category::model()->findByPk($parentId)->cat_name : '/';
                    } else {
                        $goods['cat_name'] = '/';
                        $goods['parent_name'] = '/';
                    }
                    if (!empty($v->goodsInfo->suppliers)) {
                        $goods['supplier'] = $v->goodsInfo->suppliers->suppliers_name;
                    } else {
                        $goods['supplier'] = '/';
                    }
                    $detail['goods_info'][] = $goods;
                }
            }
            $orderDetail[$val->user_id][] = $detail;
        }

        //新建 
        $objectPHPExcel = new PHPExcel();
        $title = '订单详情表';
        $title .= '（';
        if (!empty($orderTime)) {
            $dateStart = date('m月d日', min($orderTime));
            $dateEnd = date('m月d日', max($orderTime));
            $title .= '下单时间：' . $dateStart . ' 至 ' . $dateEnd . ' ';
        }
        if (!empty($payTime)) {
            $dateStart = date('m月d日', min($payTime));
            $dateEnd = date('m月d日', max($payTime));
            $title .= ' 付款时间：' . $dateStart . ' 至 ' . $dateEnd;
        }
        $title .= ' 共' . count($orderDetail) . ' 名用户 ';
        $title .= '）';
        $objectPHPExcel->setActiveSheetIndex(0);
        //报表头设置  
        $objectPHPExcel->getActiveSheet()->mergeCells("A1:T1");
        $objectPHPExcel->getActiveSheet()->setCellValue('A1', $title);
        // 加粗
        $objectPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);

        $objectPHPExcel->setActiveSheetIndex(0)->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        //表格头的输出
        $titleArr = array('下单用户', '订单号', '订单状态', '下单时间', '付款时间', '使用余额', '使用金币', '使用红包', '折扣', '商品总金额', '运费', '应付款', '订单总额', '收货人信息', '产品名称', '数量', '单价', '产品总价', '采购商家', '所属类目', '所属父类目');
        foreach ($titleArr as $key => $val) {
            $objectPHPExcel->getActiveSheet()->setCellValue(self::$item[$key] . '2', $val);
        }
        $horNum = 3;
        foreach ($orderDetail as $key => $val) {
            $startNum = $horNum;
            foreach ($val as $k => $v) {
                if (!empty($v['goods_info'])) {
                    $num = count($v['goods_info']);
                } else {
                    $num = 1;
                }
                if ($num > 1) {
                    //合并单元格
                    for ($i = 1; $i < 14; $i++) {
                        $objectPHPExcel->getActiveSheet()->mergeCells(self::$item[$i] . $horNum . ":" . self::$item[$i] . ($horNum + $num - 1));
                    }
                    $objectPHPExcel->getActiveSheet()->mergeCells(self::$item[count($titleArr)] . $horNum . ":" . self::$item[count($titleArr)] . ($horNum + $num - 1));
                }
                //设置单元格的值
                //避免科学计数法
                $objectPHPExcel->getActiveSheet()->setCellValueExplicit('B' . $horNum, $v['order_sn'], PHPExcel_Cell_DataType::TYPE_STRING);
                $objectPHPExcel->getActiveSheet()->setCellValue('C' . $horNum, $v['status']);
                $objectPHPExcel->getActiveSheet()->setCellValue('D' . $horNum, !empty($v['add_time']) ? date('Y-m-d H:i:s', $v['add_time']) : '/');
                $objectPHPExcel->getActiveSheet()->setCellValue('E' . $horNum, !empty($v['pay_time']) ? date('Y-m-d H:i:s', $v['pay_time']) : '/');
                $objectPHPExcel->getActiveSheet()->setCellValue('F' . $horNum, !empty($v['surplus']) ? $v['surplus'] : '0');
                $objectPHPExcel->getActiveSheet()->setCellValue('G' . $horNum, !empty($v['integral_money']) ? $v['integral_money'] : '0');
                $objectPHPExcel->getActiveSheet()->setCellValue('H' . $horNum, !empty($v['bonus']) ? $v['bonus'] : '0');
                $objectPHPExcel->getActiveSheet()->setCellValue('I' . $horNum, !empty($v['discount']) ? $v['discount'] : '/');
                $objectPHPExcel->getActiveSheet()->setCellValue('J' . $horNum, !empty($v['goods_amount']) ? $v['goods_amount'] : '0');
                $objectPHPExcel->getActiveSheet()->setCellValue('K' . $horNum, !empty($v['shipping_fee']) ? $v['shipping_fee'] : '0');
                $objectPHPExcel->getActiveSheet()->setCellValue('L' . $horNum, !empty($v['should_pay']) ? $v['should_pay'] : '0');
                $objectPHPExcel->getActiveSheet()->setCellValue('M' . $horNum, !empty($v['total_fee']) ? $v['total_fee'] : '0');
                //收货人信息
                $contact = "收货人：" . $v['consignee'] . "\n";
                $contact .= "地址：" . $v['address'] . "\n";
                $contact .= '电话：' . $v['tel'];
                $objectPHPExcel->getActiveSheet()->setCellValue('N' . $horNum, $contact);
                $objectPHPExcel->getActiveSheet()->getStyle('N' . $horNum)->getAlignment()->setWrapText(true);
                if (!empty($v['goods_info'])) {
                    foreach ($v['goods_info'] as $i => $j) {
                        $goods = $j['goods_name'];
                        if (!empty($j['goods_attr'])) {
                            $goods .= '（' . $j['goods_attr'] . '）';
                        }
                        $objectPHPExcel->getActiveSheet()->setCellValue('O' . ($horNum + $i), $goods);
                        $objectPHPExcel->getActiveSheet()->getStyle('O' . ($horNum + $i))->getAlignment()->setWrapText(true);
                        $objectPHPExcel->getActiveSheet()->setCellValue('P' . ($horNum + $i), $j['goods_number']);
                        $objectPHPExcel->getActiveSheet()->setCellValue('Q' . ($horNum + $i), $j['goods_price']);
                        $objectPHPExcel->getActiveSheet()->setCellValue('R' . ($horNum + $i), $j['goods_number'] * $j['goods_price']);
                        $objectPHPExcel->getActiveSheet()->setCellValue('S' . ($horNum + $i), $j['supplier']);
                        $objectPHPExcel->getActiveSheet()->getStyle('S' . ($horNum + $i))->getAlignment()->setWrapText(true);
                        $objectPHPExcel->getActiveSheet()->setCellValue('T' . ($horNum + $i), $j['cat_name']);
                        $objectPHPExcel->getActiveSheet()->getStyle('T' . ($horNum + $i))->getAlignment()->setWrapText(true);
                        $objectPHPExcel->getActiveSheet()->setCellValue('U' . ($horNum + $i), $j['parent_name']);
                        $objectPHPExcel->getActiveSheet()->getStyle('U' . ($horNum + $i))->getAlignment()->setWrapText(true);
                    }
                }
                $horNum += $num;
            }
            $objectPHPExcel->getActiveSheet()->mergeCells('A' . $startNum . ":" . 'A' . ($horNum - 1));
            $text = $val[0]['user_name'] . "\nID：" . $key;
            if (!empty($val[0]['mobile_phone'])) {
                $text .= "\nM：" . $val[0]['mobile_phone'];
            }
            $text .= "\n共" . count($val) . '个订单';
            $objectPHPExcel->getActiveSheet()->setCellValue('A' . $startNum, $text);
            $objectPHPExcel->getActiveSheet()->getStyle('A' . $startNum . ":" . 'A' . ($horNum - 1))->getAlignment()->setWrapText(true);
        }
        //最后一行
        $objectPHPExcel->getActiveSheet()->setCellValue('A' . $horNum, '总价');
        $objectPHPExcel->getActiveSheet()->setCellValue('F' . $horNum, '=SUM(F3:F' . ($horNum - 1) . ')');
        $objectPHPExcel->getActiveSheet()->setCellValue('G' . $horNum, '=SUM(G3:G' . ($horNum - 1) . ')');
        $objectPHPExcel->getActiveSheet()->setCellValue('H' . $horNum, '=SUM(H3:H' . ($horNum - 1) . ')');
        $objectPHPExcel->getActiveSheet()->setCellValue('I' . $horNum, '=SUM(I3:I' . ($horNum - 1) . ')');
        $objectPHPExcel->getActiveSheet()->setCellValue('J' . $horNum, '=SUM(J3:J' . ($horNum - 1) . ')');
        $objectPHPExcel->getActiveSheet()->setCellValue('K' . $horNum, '=SUM(K3:K' . ($horNum - 1) . ')');
        $objectPHPExcel->getActiveSheet()->setCellValue('L' . $horNum, '=SUM(L3:L' . ($horNum - 1) . ')');
        $objectPHPExcel->getActiveSheet()->setCellValue('M' . $horNum, '=SUM(M3:M' . ($horNum - 1) . ')');
        $objectPHPExcel->getActiveSheet()->setCellValue('P' . $horNum, '=SUM(P3:P' . ($horNum - 1) . ')');
        $objectPHPExcel->getActiveSheet()->setCellValue('Q' . $horNum, '=SUM(Q3:Q' . ($horNum - 1) . ')');
        $objectPHPExcel->getActiveSheet()->setCellValue('R' . $horNum, '=SUM(R3:R' . ($horNum - 1) . ')');
        //设置对齐方式
        $objectPHPExcel->getActiveSheet()->getStyle('A1:T' . $horNum)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objectPHPExcel->getActiveSheet()->getStyle('A1:T' . $horNum)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objectPHPExcel->getActiveSheet()->getStyle('N3:O' . $horNum)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        //设置字号
        $objectPHPExcel->getActiveSheet()->getStyle('A1:U' . $horNum)->getFont()->setSize(9);
        //设置宽/高度
        $objectPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
        $objectPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $objectPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $objectPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(16);
        $objectPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(16);
        $objectPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(25);
        $objectPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(35);
        $objectPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(12);
        $objectPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(12);
        $objectPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(12);
        //设置边框
        if (count($orderTime) < 45) {
            for ($x = 0; $x < 21; $x++) {
                for ($y = 2; $y <= $horNum; $y++) {
                    $objectPHPExcel->getActiveSheet()->getStyle(self::$item[$x] . $y . ':' . self::$item[$x] . $y)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                    $objectPHPExcel->getActiveSheet()->getStyle(self::$item[$x] . $y . ':' . self::$item[$x] . $y)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                    $objectPHPExcel->getActiveSheet()->getStyle(self::$item[$x] . $y . ':' . self::$item[$x] . $y)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                    $objectPHPExcel->getActiveSheet()->getStyle(self::$item[$x] . $y . ':' . self::$item[$x] . $y)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                }
            }
        }

        $objectPHPExcel->getActiveSheet()->setTitle('订单详情表');

        //设置导出文件名  
        $xlsWriter = new PHPExcel_Writer_Excel5($objectPHPExcel);

        ob_end_clean();
        ob_start();
        header("Pragma: public");
        header("Expires: 0");
        header("Content-Type: text/html; charset=UTF-8");
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header("Content-Disposition:attachment;filename=订单详情表.xls");
        header("Content-Transfer-Encoding: binary");
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Pragma: no-cache");
        $xlsWriter->save("php://output");
    }

    /*
     * 按照网站订单导出Excel
     */

    public static function ListForOrderToExcel($data) {
        $orderList = array();
        $buyTime = array();
        //按照网站订单显示
        foreach ($data as $key => $val) {
            $eachArr = array();
            $eachArr['goods_id'] = $val->goods_id;
            $eachArr['goods_name'] = Goods::model()->findByPk($val->goods_id)->goods_name;
            $eachArr['order_id'] = $val->order_id;
            $orderInfo = Order::model()->findByPk($val->order_id);
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
            $eachArr['total_fee'] = $eachArr['oversea_price'] + $eachArr['shipping_fee'] + $eachArr['consumption_tax'] - $eachArr['use_coupon'];
            $buyTime[] = strtotime($val->add_time);
            $buyOrder = BuyOrder::model()->findByPk($val->buy_order_id);
            if (!empty($buyOrder)) {
                $suppliers = Suppliers::model()->findByPk($buyOrder->supplier_id);
                $eachArr['supplier_id'] = $buyOrder->supplier_id;
                $eachArr['supplier_name'] = !empty($suppliers) ? $suppliers->suppliers_name : '';
                $eachArr['order_price'] = $buyOrder->order_price;
                $eachArr['buy_order_number'] = $buyOrder->order_number;
                $eachArr['exchange_tax'] = $buyOrder->exchange_tax;
            }
            $orderList[$val->order_id][] = $eachArr;
        }
        //新建 
        $objectPHPExcel = new PHPExcel();
        $title = '采购订单表 USD';
        $title .= '（';
        if (!empty($buyTime)) {
            $dateStart = date('m月d日', min($buyTime));
            $dateEnd = date('m月d日', max($buyTime));
            $title .= '采购时间：' . $dateStart . ' 至 ' . $dateEnd . ' ';
        }
        $title .= ' 共完成' . count($orderList) . ' 张订单采购 ';
        $title .= '）';
        $objectPHPExcel->setActiveSheetIndex(0);
        //报表头设置  
        $objectPHPExcel->getActiveSheet()->mergeCells("A1:L1");
        $objectPHPExcel->getActiveSheet()->setCellValue('A1', $title);
        // 加粗
        $objectPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);

        $objectPHPExcel->setActiveSheetIndex(0)->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        //表格头的输出
        $objectPHPExcel->getActiveSheet()->setCellValue('A2', '订单号');
        $objectPHPExcel->getActiveSheet()->setCellValue('B2', '采购单号');
        $objectPHPExcel->getActiveSheet()->setCellValue('C2', '采购时间');
        $objectPHPExcel->getActiveSheet()->setCellValue('D2', '商品名称');
        $objectPHPExcel->getActiveSheet()->setCellValue('E2', '采购商家');
        $objectPHPExcel->getActiveSheet()->setCellValue('F2', '数量');
        $objectPHPExcel->getActiveSheet()->setCellValue('G2', '海外价格');
        $objectPHPExcel->getActiveSheet()->setCellValue('H2', '消费税');
        $objectPHPExcel->getActiveSheet()->setCellValue('I2', '邮费');
        $objectPHPExcel->getActiveSheet()->setCellValue('J2', '优惠券');
        $objectPHPExcel->getActiveSheet()->setCellValue('K2', '采购总额');
        $objectPHPExcel->getActiveSheet()->setCellValue('L2', '订单采购总额');
        $objectPHPExcel->getActiveSheet()->getStyle('A2:L2')->getAlignment()->setWrapText(true);
        $horNum = 3;
        foreach ($orderList as $key => $val) {
            $num = count($val);
            if ($num > 1) {
                $objectPHPExcel->getActiveSheet()->mergeCells('A' . $horNum . ":" . 'A' . ($horNum + $num - 1));
                $objectPHPExcel->getActiveSheet()->mergeCells('L' . $horNum . ":" . 'L' . ($horNum + $num - 1));
            }

            //设置非数字
            $objectPHPExcel->getActiveSheet()->setCellValueExplicit('A' . $horNum, $val[0]['order_sn'], PHPExcel_Cell_DataType::TYPE_STRING);
            if (!empty($val)) {
                foreach ($val as $k => $v) {
                    $objectPHPExcel->getActiveSheet()->setCellValueExplicit('B' . ($horNum + $k), $v['buy_order_number'], PHPExcel_Cell_DataType::TYPE_STRING);
                    $objectPHPExcel->getActiveSheet()->getStyle('B' . ($horNum + $k))->getAlignment()->setWrapText(true);
                    $objectPHPExcel->getActiveSheet()->setCellValue('C' . ($horNum + $k), $v['add_time']);
                    $objectPHPExcel->getActiveSheet()->setCellValue('D' . ($horNum + $k), $v['goods_name']);
                    $objectPHPExcel->getActiveSheet()->getStyle('D' . ($horNum + $k))->getAlignment()->setWrapText(true);
                    $objectPHPExcel->getActiveSheet()->setCellValue('E' . ($horNum + $k), $v['supplier_name']);
                    $objectPHPExcel->getActiveSheet()->setCellValue('F' . ($horNum + $k), $v['buy_number']);
                    $objectPHPExcel->getActiveSheet()->setCellValue('G' . ($horNum + $k), $v['oversea_price']);
                    $objectPHPExcel->getActiveSheet()->setCellValue('H' . ($horNum + $k), $v['consumption_tax']);
                    $objectPHPExcel->getActiveSheet()->setCellValue('I' . ($horNum + $k), $v['shipping_fee']);
                    $objectPHPExcel->getActiveSheet()->setCellValue('J' . ($horNum + $k), $v['use_coupon']);
                    $objectPHPExcel->getActiveSheet()->setCellValue('K' . ($horNum + $k), '=(G' . ($horNum + $k) . '+H' . ($horNum + $k) . '+I' . ($horNum + $k) . '-J' . ($horNum + $k) . ')');
                }
            }
            $objectPHPExcel->getActiveSheet()->setCellValue('L' . $horNum, '=SUM(K' . $horNum . ':K' . ($horNum + $k) . ')');
            $horNum += $num;
        }
        $objectPHPExcel->getActiveSheet()->setCellValue('A' . $horNum, '总计');
        $objectPHPExcel->getActiveSheet()->setCellValue('F' . $horNum, '=SUM(F3:F' . ($horNum - 1) . ')');
        $objectPHPExcel->getActiveSheet()->setCellValue('G' . $horNum, '=SUM(G3:G' . ($horNum - 1) . ')');
        $objectPHPExcel->getActiveSheet()->setCellValue('H' . $horNum, '=SUM(H3:H' . ($horNum - 1) . ')');
        $objectPHPExcel->getActiveSheet()->setCellValue('I' . $horNum, '=SUM(I3:I' . ($horNum - 1) . ')');
        $objectPHPExcel->getActiveSheet()->setCellValue('J' . $horNum, '=SUM(J3:J' . ($horNum - 1) . ')');
        $objectPHPExcel->getActiveSheet()->setCellValue('K' . $horNum, '=SUM(K3:K' . ($horNum - 1) . ')');
        $objectPHPExcel->getActiveSheet()->setCellValue('L' . $horNum, '=SUM(L3:L' . ($horNum - 1) . ')');
        //设置对齐方式
        $objectPHPExcel->getActiveSheet()->getStyle('A1:L' . $horNum)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objectPHPExcel->getActiveSheet()->getStyle('A1:L' . $horNum)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objectPHPExcel->getActiveSheet()->getStyle('D3:D' . $horNum)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        //设置字体
        $objectPHPExcel->getActiveSheet()->getStyle('A1:L' . $horNum)->getFont()->setSize(9);
        //设置宽/高度
        $objectPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
        $objectPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
        $objectPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
        $objectPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
        $objectPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
        $objectPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(8);
        $objectPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(8);
        $objectPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(8);
        $objectPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(8);
        $objectPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(8);
        $objectPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(8);
        $objectPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(12);
        $objectPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(30);
        $objectPHPExcel->getActiveSheet()->getRowDimension('2')->setRowHeight(30);
        //设置边框
        if (count($orderList) < 45) {
            for ($x = 0; $x < 12; $x++) {
                for ($y = 2; $y <= $horNum; $y++) {
                    $objectPHPExcel->getActiveSheet()->getStyle(self::$item[$x] . $y . ':' . self::$item[$x] . $y)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                    $objectPHPExcel->getActiveSheet()->getStyle(self::$item[$x] . $y . ':' . self::$item[$x] . $y)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                    $objectPHPExcel->getActiveSheet()->getStyle(self::$item[$x] . $y . ':' . self::$item[$x] . $y)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                    $objectPHPExcel->getActiveSheet()->getStyle(self::$item[$x] . $y . ':' . self::$item[$x] . $y)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                }
            }
        }

        $objectPHPExcel->getActiveSheet()->setTitle('采购订单表');

        //设置导出文件名  
        $xlsWriter = new PHPExcel_Writer_Excel5($objectPHPExcel);

        ob_end_clean();
        ob_start();
        header("Pragma: public");
        header("Expires: 0");
        header("Content-Type: text/html; charset=UTF-8");
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header("Content-Disposition:attachment;filename=采购订单表.xls");
        header("Content-Transfer-Encoding: binary");
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Pragma: no-cache");
        $xlsWriter->save("php://output");
    }

    /*
     * 按照网站订单导出采购Excel（详尽版）
     */

    public static function ListForOrderAllInfoToExcel($data) {
        $orderList = array();
        $buyTime = array();
        //按照网站订单显示
        foreach ($data as $key => $val) {
            $eachArr = array();
            $orderInfo = Order::model()->findByPk($val->order_id);
            //网站订单信息
            $orderList[$val->order_id]['order_id'] = $val->order_id;
            $orderList[$val->order_id]['order_sn'] = !empty($orderInfo) ? $orderInfo->order_sn : '';
            $orderList[$val->order_id]['consignee'] = $orderInfo->consignee;
            $orderList[$val->order_id]['address'] = $orderInfo->address;
            $orderList[$val->order_id]['tel'] = $orderInfo->tel;
            $orderList[$val->order_id]['mobile'] = $orderInfo->mobile;
            $orderList[$val->order_id]['goods_amount'] = $orderInfo->goods_amount;
            $orderList[$val->order_id]['surplus'] = $orderInfo->surplus;
            $orderList[$val->order_id]['integral_money'] = $orderInfo->integral_money;
            $orderList[$val->order_id]['bonus'] = $orderInfo->bonus;
            $orderList[$val->order_id]['discount'] = $orderInfo->discount;
            $orderList[$val->order_id]['shipping_fee'] = $orderInfo->shipping_fee;
            $orderList[$val->order_id]['total_fee'] = $orderInfo->getTotalFee();
            //手续费
            foreach (OrderFee::$feeCategory as $k => $v) {
                $feeInfo = OrderFee::model()->find('order_id = :order_id AND fee_cat_id = :fee_cat_id', array(':order_id' => $val->order_id, ':fee_cat_id' => $k));
                if (!empty($feeInfo)) {
                    $orderList[$val->order_id]['other_fee'][$k] = $feeInfo->fee;
                } else {
                    $orderList[$val->order_id]['other_fee'][$k] = '0';
                }
            }
            //采购信息
            $eachArr['goods_id'] = $val->goods_id;
            $eachArr['goods_name'] = Goods::model()->findByPk($val->goods_id)->goods_name;
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
            $eachArr['total_fee'] = $eachArr['oversea_price'] + $eachArr['shipping_fee'] + $eachArr['consumption_tax'] - $eachArr['use_coupon'];
            $eachArr['add_time'] = $val->add_time;
            //购买时间
            $buyTime[] = strtotime($val->add_time);
            $buyOrder = BuyOrder::model()->findByPk($val->buy_order_id);
            if (!empty($buyOrder)) {
                $suppliers = Suppliers::model()->findByPk($buyOrder->supplier_id);
                $eachArr['supplier_id'] = $buyOrder->supplier_id;
                $eachArr['supplier_name'] = !empty($suppliers) ? $suppliers->suppliers_name : '';
                $eachArr['order_price'] = $buyOrder->order_price;
                $eachArr['buy_order_number'] = $buyOrder->order_number;
                $eachArr['exchange_tax'] = $buyOrder->exchange_tax;
            }
            $orderList[$val->order_id]['buy_info'][] = $eachArr;
        }
       
        //新建 
        $objectPHPExcel = new PHPExcel();
        $title = '采购订单表（详尽版）';
        $title .= '（';
        if (!empty($buyTime)) {
            $dateStart = date('m月d日', min($buyTime));
            $dateEnd = date('m月d日', max($buyTime));
            $title .= '采购时间：' . $dateStart . ' 至 ' . $dateEnd . ' ';
        }
        $title .= ' 共完成' . count($orderList) . ' 张订单采购 ';
        $title .= '）';
        $objectPHPExcel->setActiveSheetIndex(0);

        //表格头的输出
        $titleArr = array('订单号', '采购单号', '采购时间', '商品名称', '采购商家', '数量', '海外价格/USD', '消费税/USD', '邮费/USD', '优惠券/USD', '采购总额/USD', '订单采购总额/USD', '收货人信息', '折扣', '使用余额/RMB', '使用金币/RMB', '使用红包/RMB', '商品总金额/RMB', '运费/RMB', '应付款/RMB', '订单总额/RMB');
        $titleArr = array_merge($titleArr, OrderFee::$feeCategory);
        foreach ($titleArr as $key => $val) {
            $objectPHPExcel->getActiveSheet()->setCellValue(self::$item[$key] . '2', $val);
        }
        $totalColumn = self::$item[count($titleArr)-1];
        //报表头设置  
        $objectPHPExcel->getActiveSheet()->mergeCells("A1:" . $totalColumn . "1");
        $objectPHPExcel->getActiveSheet()->setCellValue('A1', $title);
        //加粗
        $objectPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);

        $objectPHPExcel->setActiveSheetIndex(0)->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $objectPHPExcel->getActiveSheet()->getStyle('A2:' . $totalColumn . '2')->getAlignment()->setWrapText(true);

        $horNum = 3;
        foreach ($orderList as $key => $val) {
            $num = count($val['buy_info']);
            if ($num > 1) {
                $objectPHPExcel->getActiveSheet()->mergeCells('A' . $horNum . ":" . 'A' . ($horNum + $num - 1));
                for ($i = 11; $i < count($titleArr); $i++) {
                    $objectPHPExcel->getActiveSheet()->mergeCells(self::$item[$i] . $horNum . ":" . self::$item[$i] . ($horNum + $num - 1));
                }
            }

            //设置非数字
            $objectPHPExcel->getActiveSheet()->setCellValueExplicit('A' . $horNum, $val['order_sn'], PHPExcel_Cell_DataType::TYPE_STRING);
            if (!empty($val['buy_info'])) {
                foreach ($val['buy_info'] as $k => $v) {
                    $objectPHPExcel->getActiveSheet()->setCellValueExplicit('B' . ($horNum + $k), $v['buy_order_number'], PHPExcel_Cell_DataType::TYPE_STRING);
                    $objectPHPExcel->getActiveSheet()->getStyle('B' . ($horNum + $k))->getAlignment()->setWrapText(true);
                    $objectPHPExcel->getActiveSheet()->setCellValue('C' . ($horNum + $k), $v['add_time']);
                    $objectPHPExcel->getActiveSheet()->setCellValue('D' . ($horNum + $k), $v['goods_name']);
                    $objectPHPExcel->getActiveSheet()->getStyle('D' . ($horNum + $k))->getAlignment()->setWrapText(true);
                    $objectPHPExcel->getActiveSheet()->setCellValue('E' . ($horNum + $k), $v['supplier_name']);
                    $objectPHPExcel->getActiveSheet()->setCellValue('F' . ($horNum + $k), $v['buy_number']);
                    $objectPHPExcel->getActiveSheet()->setCellValue('G' . ($horNum + $k), $v['oversea_price']);
                    $objectPHPExcel->getActiveSheet()->setCellValue('H' . ($horNum + $k), $v['consumption_tax']);
                    $objectPHPExcel->getActiveSheet()->setCellValue('I' . ($horNum + $k), $v['shipping_fee']);
                    $objectPHPExcel->getActiveSheet()->setCellValue('J' . ($horNum + $k), $v['use_coupon']);
                    $objectPHPExcel->getActiveSheet()->setCellValue('K' . ($horNum + $k), '=(G' . ($horNum + $k) . '+H' . ($horNum + $k) . '+I' . ($horNum + $k) . '-J' . ($horNum + $k) . ')');
                }
            }
            $objectPHPExcel->getActiveSheet()->setCellValue('L' . $horNum, '=SUM(K' . $horNum . ':K' . ($horNum + $k) . ')');
            //收货人信息
            $contact = "收货人：" . $val['consignee'] . "\n";
            $contact .= "地址：" . $val['address'] . "\n";
            $contact .= '电话：' . $val['tel'];
            $objectPHPExcel->getActiveSheet()->setCellValue('M' . $horNum, $contact);
            $objectPHPExcel->getActiveSheet()->getStyle('M' . $horNum)->getAlignment()->setWrapText(true);
            $objectPHPExcel->getActiveSheet()->setCellValue('N' . $horNum, !empty($val['discount']) ? $val['discount'] : '/');
            $objectPHPExcel->getActiveSheet()->setCellValue('O' . $horNum, !empty($val['surplus']) ? $val['surplus'] : '0');
            $objectPHPExcel->getActiveSheet()->setCellValue('P' . $horNum, !empty($val['integral_money']) ? $val['integral_money'] : '0');
            $objectPHPExcel->getActiveSheet()->setCellValue('Q' . $horNum, !empty($val['bonus']) ? $val['bonus'] : '0');
            $objectPHPExcel->getActiveSheet()->setCellValue('R' . $horNum, !empty($val['goods_amount']) ? $val['goods_amount'] : '0');
            $objectPHPExcel->getActiveSheet()->setCellValue('S' . $horNum, !empty($val['shipping_fee']) ? $val['shipping_fee'] : '0');
            $objectPHPExcel->getActiveSheet()->setCellValue('T' . $horNum, !empty($val['should_pay']) ? $val['should_pay'] : '0');
            $objectPHPExcel->getActiveSheet()->setCellValue('U' . $horNum, !empty($val['total_fee']) ? $val['total_fee'] : '0');
            //其他手续费
            foreach($val['other_fee'] as $x=>$y){
                $objectPHPExcel->getActiveSheet()->setCellValue(self::$item[(20+$x)] . $horNum, $y);
            }
            $horNum += $num;
        }
        $objectPHPExcel->getActiveSheet()->setCellValue('A' . $horNum, '总计');
        for($i = 5; $i < 12 ; $i++){
            $objectPHPExcel->getActiveSheet()->setCellValue(self::$item[$i] . $horNum, '=SUM('.self::$item[$i].'3:'. self::$item[$i] . ($horNum - 1) . ')');
        }
        for($i = 14; $i < count($titleArr) ; $i++){
            $objectPHPExcel->getActiveSheet()->setCellValue(self::$item[$i] . $horNum, '=SUM('.self::$item[$i].'3:'. self::$item[$i] . ($horNum - 1) . ')');
        }
  
        //设置对齐方式
        $objectPHPExcel->getActiveSheet()->getStyle('A1:'.$totalColumn . $horNum)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objectPHPExcel->getActiveSheet()->getStyle('A1:'.$totalColumn . $horNum)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objectPHPExcel->getActiveSheet()->getStyle('D3:D' . $horNum)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $objectPHPExcel->getActiveSheet()->getStyle('M3:M' . $horNum)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        //设置字体
        $objectPHPExcel->getActiveSheet()->getStyle('A1:'.$totalColumn . $horNum)->getFont()->setSize(9);
        //设置宽/高度
        $objectPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
        $objectPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
        $objectPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
        $objectPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
        $objectPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
        $objectPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(10);
        $objectPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(25);
        $objectPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(30);
        $objectPHPExcel->getActiveSheet()->getRowDimension('2')->setRowHeight(30);
        //设置边框
        if (count($orderList) < 45) {
            for ($x = 0; $x < count($titleArr); $x++) {
                for ($y = 2; $y <= $horNum; $y++) {
                    $objectPHPExcel->getActiveSheet()->getStyle(self::$item[$x] . $y . ':' . self::$item[$x] . $y)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                    $objectPHPExcel->getActiveSheet()->getStyle(self::$item[$x] . $y . ':' . self::$item[$x] . $y)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                    $objectPHPExcel->getActiveSheet()->getStyle(self::$item[$x] . $y . ':' . self::$item[$x] . $y)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                    $objectPHPExcel->getActiveSheet()->getStyle(self::$item[$x] . $y . ':' . self::$item[$x] . $y)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                }
            }
        }

        $objectPHPExcel->getActiveSheet()->setTitle('采购订单表（详尽版）');

        //设置导出文件名  
        $xlsWriter = new PHPExcel_Writer_Excel5($objectPHPExcel);

        ob_end_clean();
        ob_start();
        header("Pragma: public");
        header("Expires: 0");
        header("Content-Type: text/html; charset=UTF-8");
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header("Content-Disposition:attachment;filename=采购订单表（详尽版）.xls");
        header("Content-Transfer-Encoding: binary");
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Pragma: no-cache");
        $xlsWriter->save("php://output");
    }

    /*
     * 按照采购单导出Excel
     */

    public static function ListForBuyOrderToExcel($data) {
        $orderList = array();
        $buyTime = array();
        //按照网站订单显示
        foreach ($data as $key => $val) {
            $eachArr = array();
            $eachArr['goods_id'] = $val->goods_id;
            $eachArr['goods_name'] = Goods::model()->findByPk($val->goods_id)->goods_name;
            $eachArr['order_id'] = $val->order_id;
            $orderInfo = Order::model()->findByPk($val->order_id);
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
            $eachArr['total_fee'] = $eachArr['oversea_price'] + $eachArr['shipping_fee'] + $eachArr['consumption_tax'] - $eachArr['use_coupon'];
            $buyTime[] = strtotime($val->add_time);
            $buyOrder = BuyOrder::model()->findByPk($val->buy_order_id);
            if (!empty($buyOrder)) {
                $suppliers = Suppliers::model()->findByPk($buyOrder->supplier_id);
                $eachArr['supplier_id'] = $buyOrder->supplier_id;
                $eachArr['supplier_name'] = !empty($suppliers) ? $suppliers->suppliers_name : '';
                $eachArr['order_price'] = $buyOrder->order_price;
                $eachArr['buy_order_number'] = $buyOrder->order_number;
                $eachArr['exchange_tax'] = $buyOrder->exchange_tax;
            }
            $orderList[$val->buy_order_id][] = $eachArr;
        }

        //新建 
        $objectPHPExcel = new PHPExcel();
        $title = '采购订单表 USD';
        $title .= '（';
        if (!empty($buyTime)) {
            $dateStart = date('m月d日', min($buyTime));
            $dateEnd = date('m月d日', max($buyTime));
            $title .= '采购时间：' . $dateStart . ' 至 ' . $dateEnd . ' ';
        }
        $title .= ' 共' . count($orderList) . ' 张采购订单 ';
        $title .= '）';
        $objectPHPExcel->setActiveSheetIndex(0);
        //报表头设置  
        $objectPHPExcel->getActiveSheet()->mergeCells("A1:L1");
        $objectPHPExcel->getActiveSheet()->setCellValue('A1', $title);
        // 加粗
        $objectPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);

        $objectPHPExcel->setActiveSheetIndex(0)->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        //表格头的输出
        $objectPHPExcel->getActiveSheet()->setCellValue('A2', '采购时间');
        $objectPHPExcel->getActiveSheet()->setCellValue('B2', '采购单号');
        $objectPHPExcel->getActiveSheet()->setCellValue('C2', '订单号');
        $objectPHPExcel->getActiveSheet()->setCellValue('D2', '商品名称');
        $objectPHPExcel->getActiveSheet()->setCellValue('E2', '采购商家');
        $objectPHPExcel->getActiveSheet()->setCellValue('F2', '数量');
        $objectPHPExcel->getActiveSheet()->setCellValue('G2', '海外价格');
        $objectPHPExcel->getActiveSheet()->setCellValue('H2', '消费税');
        $objectPHPExcel->getActiveSheet()->setCellValue('I2', '邮费');
        $objectPHPExcel->getActiveSheet()->setCellValue('J2', '优惠券');
        $objectPHPExcel->getActiveSheet()->setCellValue('K2', '单项采购额');
        $objectPHPExcel->getActiveSheet()->setCellValue('L2', '采购总额');
        $objectPHPExcel->getActiveSheet()->getStyle('A2:L2')->getAlignment()->setWrapText(true);
        $horNum = 3;
        foreach ($orderList as $key => $val) {
            $num = count($val);
            if ($num > 1) {
                $objectPHPExcel->getActiveSheet()->mergeCells('A' . $horNum . ":" . 'A' . ($horNum + $num - 1));
                $objectPHPExcel->getActiveSheet()->mergeCells('B' . $horNum . ":" . 'B' . ($horNum + $num - 1));
                $objectPHPExcel->getActiveSheet()->mergeCells('L' . $horNum . ":" . 'L' . ($horNum + $num - 1));
            }
            $objectPHPExcel->getActiveSheet()->setCellValue('A' . $horNum, $val[0]['add_time']);
            //设置非数字
            $objectPHPExcel->getActiveSheet()->setCellValueExplicit('B' . $horNum, $val[0]['buy_order_number'], PHPExcel_Cell_DataType::TYPE_STRING);
            $objectPHPExcel->getActiveSheet()->getStyle('B')->getAlignment()->setWrapText(true);
            if (!empty($val)) {
                foreach ($val as $k => $v) {
                    $objectPHPExcel->getActiveSheet()->setCellValueExplicit('C' . ($horNum + $k), $v['order_sn'], PHPExcel_Cell_DataType::TYPE_STRING);
                    $objectPHPExcel->getActiveSheet()->setCellValue('D' . ($horNum + $k), $v['goods_name']);
                    $objectPHPExcel->getActiveSheet()->getStyle('D' . ($horNum + $k))->getAlignment()->setWrapText(true);
                    $objectPHPExcel->getActiveSheet()->setCellValue('E' . ($horNum + $k), $v['supplier_name']);
                    $objectPHPExcel->getActiveSheet()->setCellValue('F' . ($horNum + $k), $v['buy_number']);
                    $objectPHPExcel->getActiveSheet()->setCellValue('G' . ($horNum + $k), $v['oversea_price']);
                    $objectPHPExcel->getActiveSheet()->setCellValue('H' . ($horNum + $k), $v['consumption_tax']);
                    $objectPHPExcel->getActiveSheet()->setCellValue('I' . ($horNum + $k), $v['shipping_fee']);
                    $objectPHPExcel->getActiveSheet()->setCellValue('J' . ($horNum + $k), $v['use_coupon']);
                    $objectPHPExcel->getActiveSheet()->setCellValue('K' . ($horNum + $k), '=(G' . ($horNum + $k) . '+H' . ($horNum + $k) . '+I' . ($horNum + $k) . '-J' . ($horNum + $k) . ')');
                }
            }
            $objectPHPExcel->getActiveSheet()->setCellValue('L' . $horNum, '=SUM(K' . $horNum . ':K' . ($horNum + $k) . ')');
            $horNum += $num;
        }
        $objectPHPExcel->getActiveSheet()->setCellValue('A' . $horNum, '总计');
        $objectPHPExcel->getActiveSheet()->setCellValue('F' . $horNum, '=SUM(F3:F' . ($horNum - 1) . ')');
        $objectPHPExcel->getActiveSheet()->setCellValue('G' . $horNum, '=SUM(G3:G' . ($horNum - 1) . ')');
        $objectPHPExcel->getActiveSheet()->setCellValue('H' . $horNum, '=SUM(H3:H' . ($horNum - 1) . ')');
        $objectPHPExcel->getActiveSheet()->setCellValue('I' . $horNum, '=SUM(I3:I' . ($horNum - 1) . ')');
        $objectPHPExcel->getActiveSheet()->setCellValue('J' . $horNum, '=SUM(J3:J' . ($horNum - 1) . ')');
        $objectPHPExcel->getActiveSheet()->setCellValue('K' . $horNum, '=SUM(K3:K' . ($horNum - 1) . ')');
        $objectPHPExcel->getActiveSheet()->setCellValue('L' . $horNum, '=SUM(L3:L' . ($horNum - 1) . ')');
        //设置对齐方式
        $objectPHPExcel->getActiveSheet()->getStyle('A1:L' . $horNum)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objectPHPExcel->getActiveSheet()->getStyle('A1:L' . $horNum)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objectPHPExcel->getActiveSheet()->getStyle('D3:D' . $horNum)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        //设置字体
        $objectPHPExcel->getActiveSheet()->getStyle('A1:L' . $horNum)->getFont()->setSize(9);
        //设置宽/高度
        $objectPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
        $objectPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
        $objectPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
        $objectPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
        $objectPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
        $objectPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(8);
        $objectPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(8);
        $objectPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(8);
        $objectPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(8);
        $objectPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(8);
        $objectPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(10);
        $objectPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(10);
        $objectPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(30);
        $objectPHPExcel->getActiveSheet()->getRowDimension('2')->setRowHeight(30);
        //设置边框
        if (count($orderList) < 45) {
            for ($x = 0; $x < 12; $x++) {
                for ($y = 2; $y <= $horNum; $y++) {
                    $objectPHPExcel->getActiveSheet()->getStyle(self::$item[$x] . $y . ':' . self::$item[$x] . $y)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                    $objectPHPExcel->getActiveSheet()->getStyle(self::$item[$x] . $y . ':' . self::$item[$x] . $y)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                    $objectPHPExcel->getActiveSheet()->getStyle(self::$item[$x] . $y . ':' . self::$item[$x] . $y)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                    $objectPHPExcel->getActiveSheet()->getStyle(self::$item[$x] . $y . ':' . self::$item[$x] . $y)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                }
            }
        }

        $objectPHPExcel->getActiveSheet()->setTitle('采购订单表');

        //设置导出文件名  
        $xlsWriter = new PHPExcel_Writer_Excel5($objectPHPExcel);

        ob_end_clean();
        ob_start();
        header("Pragma: public");
        header("Expires: 0");
        header("Content-Type: text/html; charset=UTF-8");
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header("Content-Disposition:attachment;filename=采购订单表.xls");
        header("Content-Transfer-Encoding: binary");
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Pragma: no-cache");
        $xlsWriter->save("php://output");
    }

}
