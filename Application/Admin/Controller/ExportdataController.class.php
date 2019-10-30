<?php
namespace Admin\Controller;
use Think\Controller;
class ExportdataController extends HeadsController {
	public function export_driveronline(){  //导出司机在岗时长

            $starttimeDate = I("start_time");
            if(empty($starttimeDate)){
                $starttime = 0;
            }else{
                $starttime = strtotime($starttimeDate);
            }
            $endtime = I("end_time");
            if(empty($endtime)){
                $endtime = NOW_TIME;
            }else{
                $endtime = $endtime." 23:59:59";
                
                $endtime = strtotime($endtime);
            }
            $where['reviewed'] = "Y";
            
            $address_id = I("address_id");
            if(!empty($address_id)){
                $where['s_driver.address_id'] = $address_id;
            }
            //查询数据
            $sql = <<<SQL
                select s_driver.id d_id,s_driver.card,s_driver.name,s_driver.phone,
                    
                    SEC_TO_TIME(
                        ifnull((select ($endtime-$starttime) from d_online_record where d_online_record.d_id=s_driver.id and d_online_record.sdate <= {$starttime}  and d_online_record.edate >= {$endtime}),0)
                            +
                        ifnull((select SUM(d_online_record.line_time) from d_online_record where d_online_record.d_id=s_driver.id and d_online_record.sdate between {$starttime} and {$endtime} and d_online_record.edate between {$starttime} and {$endtime}),0)
                            +
                        ifnull((select ({$endtime}-d_online_record.sdate) from d_online_record where d_online_record.d_id=s_driver.id and d_online_record.sdate between {$starttime} and {$endtime} and d_online_record.edate > {$endtime}),0)
                            +
                        ifnull((select (d_online_record.edate-{$starttime}) from d_online_record where d_online_record.d_id=s_driver.id and d_online_record.edate between {$starttime} and {$endtime} and d_online_record.sdate < {$starttime}),0)
                                ) line_time 
                        from s_driver 
                        %WHERE% 
                        order by line_time desc 
SQL;
        $user = M()->where($where)->query($sql,TRUE);
  
            //引用phpexcel
        import('Library/PHPExcel/PHPExcel', APP_PATH);
        $filename_xia='./Public/exceltmpl/print_tmpl_online.xls';
        require_once APP_PATH . 'Library/PHPExcel/PHPExcel.php';
        $inputFileType_xia = \PHPExcel_IOFactory::identify($filename_xia);
        $objReader_xia = \PHPExcel_IOFactory::createReader($inputFileType_xia);
        $objPHPExcel_xia = $objReader_xia->load($filename_xia);
        $objWorksheet_xia = $objPHPExcel_xia->setActiveSheetIndex(0);
        $contentStart = 2;
        
        foreach($user as $key => $val){
            
            $objPHPExcel_xia->setActiveSheetIndex(0)
                            ->setCellValue('A'.$contentStart, $val['name'])
                            ->setCellValueExplicit('B'.$contentStart, $val['card'],\PHPExcel_Cell_DataType::TYPE_STRING)  //不转义数字的输出
                            ->setCellValueExplicit('C'.$contentStart, $val['phone'],\PHPExcel_Cell_DataType::TYPE_STRING)
                            ->setCellValueExplicit('D'.$contentStart,$val['line_time'],\PHPExcel_Cell_DataType::TYPE_STRING)
//                            ->setCellValue('E'.$contentStart, $val['type_name'])
//                            ->setCellValue('F'.$contentStart,"")
//                    ->setCellValue('G'.$contentStart,"TRUE")
//                    ->setCellValue('H'.$contentStart,"Style")
//                    ->setCellValue('I'.$contentStart, $val['attribute'])
//                     ->setCellValue('J'.$contentStart,"")
//                    ->setCellValue('K'.$contentStart,"")
//                    ->setCellValue('L'.$contentStart,"")
//                    ->setCellValue('M'.$contentStart,"")
//                    ->setCellValue('N'.$contentStart,$val['sku'])
//                    ->setCellValue('O'.$contentStart,"")
//                    ->setCellValue('P'.$contentStart,"")
//                    ->setCellValue('Q'.$contentStart,"")
//                    ->setCellValue('R'.$contentStart,"deny")
//                    ->setCellValue('S'.$contentStart,"manual")
//                    ->setCellValueExplicit('T'.$contentStart, $val['price'],\PHPExcel_Cell_DataType::TYPE_STRING)
//                    ->setCellValueExplicit('U'.$contentStart, $val['navprice'],\PHPExcel_Cell_DataType::TYPE_STRING)
//                    ->setCellValue('V'.$contentStart,"TRUE")
//                    ->setCellValue('W'.$contentStart,"TRUE")
//                    ->setCellValue('X'.$contentStart,"")
//                    ->setCellValue('Y'.$contentStart,"http://46.101.90.127".$val['picture'])
//                    ->setCellValue('Z'.$contentStart,"")
//                    ->setCellValue('AA'.$contentStart,"FALSE")
                            ;
            $contentStart++;
        }
        $objWriter_xia = \PHPExcel_IOFactory::createWriter($objPHPExcel_xia, 'Excel5');  //引用 Excel5  是 .xls文件   Excel2007 是 .xlsx文件
	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="print_'.$starttimeDate.'.xls"');
	header('Cache-Control: max-age=0');
        
        ob_clean();  
        flush();  

	$objWriter_xia->save('php://output');
        
	}
        public function export_withdraw(){  //导出提现申请
            $adminid = I("id");  //管理员的ID
//            if(empty($adminid)){
//                $msg['error_code'] = -402;
//                $msg['message'] = "管理员ID为空";
//                $this->ajaxReturn($msg);
//                exit();
//            }
//            $token = I("token");
//            if($token!=S("admin_token{$adminid}")){
//                $msg['error_code'] = -101;
//                $msg['message'] = "token错误";
//                $this->ajaxReturn($msg);
//                exit();
//            }
            $state = I("state");
            if(!empty($state)){
                $where['d_withdraw.state'] = $state;
                
            }else{
                $state = "all";
            }
            $address_id = I("address_id");
            if(!empty($address_id)){
                $where['s_driver.address_id'] = $address_id;
            }
            //查询数据
            $sql = <<<SQL
                select s_driver.card,s_driver.name,d_withdraw.id withdrawid,d_withdraw.d_id,d_withdraw.banknum,d_withdraw.banktype,d_withdraw.pename,d_withdraw.bankphone,d_withdraw.bankaddress,d_withdraw.amount,d_withdraw.date,d_withdraw.state,(case d_withdraw.state 
        WHEN 'Y' THEN '已打款' 
        WHEN 'N' THEN '打款失败' 
        WHEN 'O' THEN '待打款' 
        end) state_c  
                        from d_withdraw 
                        left join s_driver on s_driver.id = d_withdraw.d_id 
                        %WHERE% 
                        order by d_withdraw.id desc 
SQL;
        $user = M()->where($where)->query($sql,TRUE);
            //引用phpexcel
        import('Library/PHPExcel/PHPExcel', APP_PATH);
        $filename_xia='./Public/exceltmpl/print_tmpl_withdraw.xls';
        require_once APP_PATH . 'Library/PHPExcel/PHPExcel.php';
        $inputFileType_xia = \PHPExcel_IOFactory::identify($filename_xia);
        $objReader_xia = \PHPExcel_IOFactory::createReader($inputFileType_xia);
        $objPHPExcel_xia = $objReader_xia->load($filename_xia);
        $objWorksheet_xia = $objPHPExcel_xia->setActiveSheetIndex(0);
        $contentStart = 2;
        
        foreach($user as $key => $val){
            
            $objPHPExcel_xia->setActiveSheetIndex(0)
                            ->setCellValue('A'.$contentStart, $val['card'])
                            ->setCellValueExplicit('B'.$contentStart, $val['name'],\PHPExcel_Cell_DataType::TYPE_STRING)  //不转义数字的输出
                            ->setCellValueExplicit('C'.$contentStart, $val['banknum'],\PHPExcel_Cell_DataType::TYPE_STRING)
                            ->setCellValueExplicit('D'.$contentStart,$val['pename'],\PHPExcel_Cell_DataType::TYPE_STRING)
                            ->setCellValue('E'.$contentStart, $val['banktype'])
                            ->setCellValueExplicit('F'.$contentStart, $val['bankaddress'],\PHPExcel_Cell_DataType::TYPE_STRING)  //不转义数字的输出
                            ->setCellValueExplicit('G'.$contentStart, $val['amount'],\PHPExcel_Cell_DataType::TYPE_STRING)  //不转义数字的输出
                            ->setCellValueExplicit('H'.$contentStart, number_format(($val['amount']*0.994), 2, '.', ''),\PHPExcel_Cell_DataType::TYPE_STRING)  //不转义数字的输出
                    ->setCellValueExplicit('I'.$contentStart, $val['date'],\PHPExcel_Cell_DataType::TYPE_STRING)  //不转义数字的输出
                    ->setCellValue('J'.$contentStart,$val['state_c'])
//                    ->setCellValue('I'.$contentStart, $val['attribute'])
//                     ->setCellValue('J'.$contentStart,"")
//                    ->setCellValue('K'.$contentStart,"")
//                    ->setCellValue('L'.$contentStart,"")
//                    ->setCellValue('M'.$contentStart,"")
//                    ->setCellValue('N'.$contentStart,$val['sku'])
//                    ->setCellValue('O'.$contentStart,"")
//                    ->setCellValue('P'.$contentStart,"")
//                    ->setCellValue('Q'.$contentStart,"")
//                    ->setCellValue('R'.$contentStart,"deny")
//                    ->setCellValue('S'.$contentStart,"manual")
//                    ->setCellValueExplicit('T'.$contentStart, $val['price'],\PHPExcel_Cell_DataType::TYPE_STRING)
//                    ->setCellValueExplicit('U'.$contentStart, $val['navprice'],\PHPExcel_Cell_DataType::TYPE_STRING)
//                    ->setCellValue('V'.$contentStart,"TRUE")
//                    ->setCellValue('W'.$contentStart,"TRUE")
//                    ->setCellValue('X'.$contentStart,"")
//                    ->setCellValue('Y'.$contentStart,"http://46.101.90.127".$val['picture'])
//                    ->setCellValue('Z'.$contentStart,"")
//                    ->setCellValue('AA'.$contentStart,"FALSE")
                            ;
            $contentStart++;
        }
        $objWriter_xia = \PHPExcel_IOFactory::createWriter($objPHPExcel_xia, 'Excel5');  //引用 Excel5  是 .xls文件   Excel2007 是 .xlsx文件
	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="print_'.$state.'.xls"');
	header('Cache-Control: max-age=0');
        
        ob_clean();  
        flush();  

	$objWriter_xia->save('php://output');
        
	}
        public function orderexporde() { //订单列表导出。
//            $adminid = I("id");  //管理员的ID
//            if(empty($adminid)){
//                $msg['error_code'] = -402;
//                $msg['message'] = "管理员ID为空";
//                $this->ajaxReturn($msg);
//                exit();
//            }
//            $token = I("token");
//            if($token!=S("admin_token{$adminid}")){
//                $msg['error_code'] = -101;
//                $msg['message'] = "token错误";
//                $this->ajaxReturn($msg);
//                exit();
//            }
            $address_id = I("address_id");
            if(!empty($address_id)){
                $where['s_driver.address_id'] = $address_id;
            }
            
            $starttimeDate = I("start_time");
            if(empty($starttimeDate)){
                $starttime = "1970-01-01 00:00:00";
            }else{
                $starttime = $starttimeDate." 00:00:00";
            }
            
            $endtime = I("end_time");
            if(empty($endtime)){
                $endtime = date("Y-m-d H:i:s",NOW_TIME);
            }else{
                $endtime = $endtime." 23:59:59";
            }
            $where['b_order.sdate'] = array("between","$starttime,$endtime");
            $state = I("state");
            if(!empty($state)){
                $where['b_order.state'] = $state;
            }
            $phone = I("phone");
            if(!empty($phone)){
                $where['CONCAT(s_user.phone,s_driver.phone)'] = array("like","%{$phone}%");
            }
            //查询数据
            $sql = <<<SQL
                select CONCAT(s_user.phone,"|",s_driver.phone),b_order.order_num,s_driver.name,s_driver.phone p_phone,s_user.phone u_phone,b_order.mileage_fee,b_order.duration_fee,b_order.start_fee,b_order.early_peak,b_order.late_peak,b_order.edge_town,b_order.out_town,b_order.night_driving_first,b_order.night_driving_second,b_order.bad_weather,b_order.other,b_order.amount,b_order.sdate,b_order.edate,b_order.duration,b_order.saddress,b_order.eaddress,b_order.distance,(case b_order.state 
        WHEN 'on' THEN '等待接驾' 
        WHEN 'active' THEN '行程中' 
        WHEN 'wait_pay' THEN '待支付' 
        WHEN 'end' THEN '已结束' 
        WHEN 'cannal' THEN '已取消' 
        end) state_c,b_order.source,if(b_order.warning='Y','出城','城内') warning,
            (case b_order_funding.method 
        WHEN null THEN '未支付' 
        WHEN 'nopublic' THEN '公众号支付' 
        WHEN 'offline' THEN '线下支付' 
        end) method,ifnull(b_order_funding.coupon_fee,0) coupon_fee,b_cancel_order_reason.text cannettext 
                        from b_order 
                        left join s_driver on s_driver.id = b_order.d_id 
                        left join s_user on s_user.id = b_order.u_id 
                        left join b_order_funding on b_order_funding.order_num = b_order.order_num 
                        left join b_cancel_order_reason on b_order.cancel_id = b_cancel_order_reason.id 
                        %WHERE% 
                        order by b_order.id desc 
SQL;
            $user = M()->where($where)->query($sql,TRUE);
                //引用phpexcel
            
            import('Library/PHPExcel/PHPExcel', APP_PATH);            
            require_once APP_PATH . 'Library/PHPExcel/PHPExcel.php';
            $filename_xia='./Public/exceltmpl/print_tmpl_order.xls';
            $inputFileType_xia = \PHPExcel_IOFactory::identify($filename_xia);
            $objReader_xia = \PHPExcel_IOFactory::createReader($inputFileType_xia);
            $objPHPExcel_xia = $objReader_xia->load($filename_xia);
            $objWorksheet_xia = $objPHPExcel_xia->setActiveSheetIndex(0);
            $contentStart = 2;

            foreach($user as $key => $val){

                $objPHPExcel_xia->setActiveSheetIndex(0)
                                ->setCellValueExplicit('A'.$contentStart, $val['order_num'],\PHPExcel_Cell_DataType::TYPE_STRING)  //不转义数字的输出
                                ->setCellValueExplicit('B'.$contentStart, $val['name'],\PHPExcel_Cell_DataType::TYPE_STRING)  //不转义数字的输出
                                ->setCellValueExplicit('C'.$contentStart, $val['p_phone'],\PHPExcel_Cell_DataType::TYPE_STRING)
                                ->setCellValueExplicit('D'.$contentStart,$val['u_phone'],\PHPExcel_Cell_DataType::TYPE_STRING)
                                ->setCellValue('E'.$contentStart, $val['mileage_fee'])
                                ->setCellValueExplicit('F'.$contentStart, $val['duration_fee'],\PHPExcel_Cell_DataType::TYPE_STRING)  //不转义数字的输出
                                ->setCellValueExplicit('G'.$contentStart, $val['start_fee'],\PHPExcel_Cell_DataType::TYPE_STRING)  //不转义数字的输出
                        ->setCellValueExplicit('H'.$contentStart, $val['early_peak'],\PHPExcel_Cell_DataType::TYPE_STRING)  //不转义数字的输出
                        ->setCellValue('I'.$contentStart, $val['late_peak'])
                         ->setCellValue('J'.$contentStart,$val['edge_town'])
                        ->setCellValue('K'.$contentStart,$val['out_town'])
                        ->setCellValue('L'.$contentStart,$val['night_driving_first'])
                        ->setCellValue('M'.$contentStart,$val['night_driving_second'])
                        ->setCellValue('N'.$contentStart,$val['bad_weather'])
                        ->setCellValue('O'.$contentStart,$val['other'])
                        ->setCellValue('P'.$contentStart,$val['amount'])
                        ->setCellValue('Q'.$contentStart,$val['sdate'])
                        ->setCellValue('R'.$contentStart,$val['edate'])
                        ->setCellValue('S'.$contentStart,$val['duration'])
                        ->setCellValueExplicit('T'.$contentStart, $val['saddress'],\PHPExcel_Cell_DataType::TYPE_STRING)
                        ->setCellValueExplicit('U'.$contentStart, $val['eaddress'],\PHPExcel_Cell_DataType::TYPE_STRING)
                        ->setCellValue('V'.$contentStart,$val['distance'])
                        ->setCellValue('W'.$contentStart,$val['state_c'])
                        ->setCellValue('X'.$contentStart,$val['source'])
                        ->setCellValue('Y'.$contentStart,$val['warning'])
                        ->setCellValue('Z'.$contentStart,$val['method'])
                        ->setCellValue('AA'.$contentStart,$val['coupon_fee'])
                        ->setCellValue('AB'.$contentStart,$val['cannettext'])
                                ;
                $contentStart++;
            }
            $objWriter_xia = \PHPExcel_IOFactory::createWriter($objPHPExcel_xia, 'Excel5');  //引用 Excel5  是 .xls文件   Excel2007 是 .xlsx文件
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="print_order'.$state.'.xls"');
            header('Cache-Control: max-age=0');

            ob_clean();  
            flush();  

            $objWriter_xia->save('php://output');
        }
        public function driverlistexporde() { //司机列表导出。
//            $adminid = I("id");  //管理员的ID
//            if(empty($adminid)){
//                $msg['error_code'] = -402;
//                $msg['message'] = "管理员ID为空";
//                $this->ajaxReturn($msg);
//                exit();
//            }
//            $token = I("token");
//            if($token!=S("admin_token{$adminid}")){
//                $msg['error_code'] = -101;
//                $msg['message'] = "token错误";
//                $this->ajaxReturn($msg);
//                exit();
//            }
            $address_id = I("address_id");
            if(!empty($address_id)){
                $where['s_driver.address_id'] = $address_id;
            }
            
            $state = I("state");
            if(!empty($state)){
                $where['s_driver.state'] = $state;
            }
            $reviewed = I("reviewed");
            if(!empty($reviewed)){
                $where['s_driver.reviewed'] = $reviewed;
            }

            $sql=<<<SQL
            select s_driver.id d_id,s_driver.card,s_driver.name,s_driver.sdate,s_driver.maturity_date,s_driver.phone,s_driver.invite_card,s_driver.idcard,s_driver.amount,s_driver.all_amount,s_driver.urgent_phone,s_driver.lits,s_driver.service_fee_now,
        (CASE s_driver.reviewed 
        WHEN 'Y' THEN '审核通过' 
        WHEN 'O' THEN '审核中' 
        WHEN 'N' THEN '审核未通过' 
        END)  reviewed_cn,(CASE s_driver.state 
        WHEN 'off' THEN '关闭' 
        WHEN 'stand' THEN '待接单' 
        WHEN 'running' THEN '行程中' 
        
        END)  state_cn,
                    (CASE s_driver.available 
        WHEN 'Y' THEN '可用' 
        WHEN 'snap_prohibited' THEN '临时封禁' 
        WHEN 'lasting_prohibited' THEN '永久分封禁' 
        END)  available,
            s_car.car_type,s_car.carcolor,s_car.carnum,
                ifnull((select COUNT(b_order.id) from b_order where d_id=s_driver.id),0) ordercount,
                ifnull((select FORMAT(AVG(point),1) from d_point where d_id=s_driver.id),5) point,
                ifnull((select COUNT(s_complaint.id) from s_complaint where d_id=s_driver.id),0) complaint 
                        from s_driver 
                        left join s_car on s_driver.id = s_car.d_id 
                        %WHERE% 
SQL;
            $user = M('')->where($where)->query($sql,true);
                //引用phpexcel
            import('Library/PHPExcel/PHPExcel', APP_PATH);
            $filename_xia='./Public/exceltmpl/print_tmpl_driver.xls';
            require_once APP_PATH . 'Library/PHPExcel/PHPExcel.php';
            $inputFileType_xia = \PHPExcel_IOFactory::identify($filename_xia);
            $objReader_xia = \PHPExcel_IOFactory::createReader($inputFileType_xia);
            $objPHPExcel_xia = $objReader_xia->load($filename_xia);
            $objWorksheet_xia = $objPHPExcel_xia->setActiveSheetIndex(0);
            $contentStart = 2;

            foreach($user as $key => $val){

                $objPHPExcel_xia->setActiveSheetIndex(0)
                                ->setCellValueExplicit('A'.$contentStart, $val['card'],\PHPExcel_Cell_DataType::TYPE_STRING)  //不转义数字的输出
                                ->setCellValueExplicit('B'.$contentStart, $val['name'],\PHPExcel_Cell_DataType::TYPE_STRING)  //不转义数字的输出
                                ->setCellValueExplicit('C'.$contentStart, $val['phone'],\PHPExcel_Cell_DataType::TYPE_STRING)
                                ->setCellValueExplicit('D'.$contentStart,$val['sdate'],\PHPExcel_Cell_DataType::TYPE_STRING)
                                ->setCellValue('E'.$contentStart, $val['maturity_date'])
                                ->setCellValueExplicit('F'.$contentStart, $val['invite_card'],\PHPExcel_Cell_DataType::TYPE_STRING)  //不转义数字的输出
                                ->setCellValueExplicit('G'.$contentStart, $val['idcard'],\PHPExcel_Cell_DataType::TYPE_STRING)  //不转义数字的输出
                        ->setCellValueExplicit('H'.$contentStart, $val['amount'],\PHPExcel_Cell_DataType::TYPE_STRING)  //不转义数字的输出
                        ->setCellValue('I'.$contentStart, $val['all_amount'])
                         ->setCellValue('J'.$contentStart,$val['urgent_phone'])
                        ->setCellValue('K'.$contentStart,$val['lits'])
                        ->setCellValue('L'.$contentStart,$val['ordercount'])
                        ->setCellValue('M'.$contentStart,$val['service_fee_now'])
                        ->setCellValue('N'.$contentStart,$val['state_cn'])
                        ->setCellValue('O'.$contentStart,$val['available'])
                        ->setCellValue('P'.$contentStart,$val['car_type'])
                        ->setCellValue('Q'.$contentStart,$val['carcolor'])
                        ->setCellValue('R'.$contentStart,$val['carnum'])
                        ->setCellValue('S'.$contentStart,$val['point'])
                        ->setCellValue('T'.$contentStart,$val['complaint'])
                                ;
                $contentStart++;
            }
            $objWriter_xia = \PHPExcel_IOFactory::createWriter($objPHPExcel_xia, 'Excel5');  //引用 Excel5  是 .xls文件   Excel2007 是 .xlsx文件
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="print_driver'.$state.'.xls"');
            header('Cache-Control: max-age=0');

            ob_clean();  
            flush();  

            $objWriter_xia->save('php://output');
        }
       
}