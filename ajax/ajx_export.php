<?php
$exp_data = data::decode(data::$get->data);
if (!$exp_data) die();
if (!member::$current->id) {
    create_error('Please Login');
}
$fun = 'export_'.$exp_data['operation'];
if (function_exists($fun)) $fun($exp_data);
else create_error('Function Not Found '.$exp_data['operation']);

function export_report_client($exp_data) {
    if (!allowed('see_client_report') && !allowed('can_send_bills')) {
        redirect_to_index();
    }
    data::$get->id = $exp_data['user_id'];
    try {
        $df = new DateTime($exp_data['date_from']);
        $dt = new DateTime($exp_data['date_to']);
    } catch (Exception $exception) {die($exception->getMessage());}
    data::$get->df_y = $df->format('Y');
    data::$get->df_m = $df->format('m');
    data::$get->df_d = $df->format('d');
    data::$get->dt_y = $dt->format('Y');
    data::$get->dt_m = $dt->format('m');
    data::$get->dt_d = $dt->format('d');
    data::$get->is_export = true;
    require './ajax/ajx_report_client.php';
    // info .. bills
    $info = data::$get->export_data['info'];
    $bills = data::$get->export_data['bills'];
    $writer = new XLSXWriter();
    //$writer->setRightToLeft(true);


    $fname = 'export_'.time().'_'.$exp_data['user_id'].'.xlsx';
    $file_name = './downloads/'.$fname;
    $sheet1 = 'كشف حساب';
    $header = ['@','@','@','@','@','@','@','@'];
    $writer->writeSheetHeader($sheet1, $header, $col_options = ['suppress_row'=>true,'widths'=>[20,20,20,20,20,30,20,10]] );
    //$writer->writeSheetHeader($sheet1, $header, $col_options = ['suppress_row'=>true] );
    //$writer->writeSheetHeader($sheet1, $header, $col_options = ['widths'=>[14,23,15,15,33,20,11]] );
    $row = [(new style())->add_html('  كشف حساب <!-- INFO_username --> من تاريخ <!-- INFO_date_from|nlongdate --> إلى تاريخ <!-- INFO_date_to|nlongdate -->')->fill($info).''];
    $format = array('font'=>'Calibri','font-size'=>11,'font-style'=>'bold', 'fill'=>'#eee', 'halign'=>'center' , 'valign'=>'center','height'=>30);
    //'font-style'=>'bold',
    $writer->writeSheetRow($sheet1, $row,$format);
    $writer->markMergedCell($sheet1, $start_row=0, $start_col=0, $end_row=0, $end_col=7);
    // headers
    $row = ['الحالة','التاريخ','الوقت','الرصيد','السعر','البيان','النوع','الرقم'];
    $format = array('font'=>'Calibri','font-size'=>11,'font-style'=>'bold', 'fill'=>'#eee', 'halign'=>'center' , 'valign'=>'center','height'=>30,'border'=>'left,right,top,bottom');
    $writer->writeSheetRow($sheet1, $row,$format);
    $row = ['','','',$info['pre_balance'],'رصيد سابق','','',''];
    $format = array('font'=>'Calibri','font-size'=>11, 'halign'=>'right' , 'valign'=>'center','height'=>30,'border'=>'left,right,top,bottom');
    $writer->writeSheetRow($sheet1, $row,$format);
    $writer->markMergedCell($sheet1, $start_row=2, $start_col=4, $end_row=2, $end_col=6);
    // rows
    $format = array('font'=>'Calibri','font-size'=>11, 'halign'=>'right' , 'valign'=>'center','height'=>30,'border'=>'left,right,top,bottom','wrap_text'=>true);
    $i = 3;
    foreach ($bills as $bill) {
        $row = [$bill['info']['stat_html_mini'],
            (new style())->add_html('<!-- INFO_send_date|nlongdate -->')->fill($bill['info']).'',
            (new style())->add_html('<!-- INFO_send_date|nshorttime12 -->')->fill($bill['info']).'',
            $bill['info']['balance'],$bill['info']['price'],
            str_replace('<br>',"\r\n",(string) $bill['info']['infos']),
            $bill['info']['type_html'],$bill['id']
        ];
        //var_dump($row);
       // die();
        $writer->writeSheetRow($sheet1, $row,$format);
        $i++;
    }
    $row = ['','','',$info['after_balance'],'الرصيد الاجمالي','','',''];
    $format = array('font'=>'Calibri','font-size'=>11, 'halign'=>'right' , 'valign'=>'center','height'=>30,'border'=>'left,right,top,bottom');
    $writer->writeSheetRow($sheet1, $row,$format);
    $writer->markMergedCell($sheet1, $i, $start_col=4, $i, $end_col=6);
    $writer->writeToFile($file_name);
    $in['file_name'] = 'Report.xlsx' ;
    $in['location'] = $fname;
    $in['user_id'] = member::$current->id;
    $in['m_date'] = time();
    $in['file_format'] = phrases::$file_format['xlsx'];
    $id = db::$db->insert('files',$in);
    if (!$id) create_error('Adding file to DB Error !');
    download($id);
}
