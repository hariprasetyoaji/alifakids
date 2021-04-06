<?php 
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

function export_excel_test_cb() {
	global $wpdb;

 // 	if ( empty( $_REQUEST['class'] ) ||
 // 			empty( $_REQUEST['branch'] ) ||
 // 			empty( $_REQUEST['date'] )
 // 		){
        
 //        $_SESSION['notice'] = 'Filter cabang dan kelas harus diisi.';
	// 	wp_redirect(admin_url('admin.php?page=reports_daily'));
	// 	exit();
 //    } 

 //    $date_join = "AND r.date = CURRENT_DATE";
	// $date_search_key = !empty( $_REQUEST['date'] ) ? wp_unslash( trim( $_REQUEST['date'] ) ) : '';
	// if (! empty( $date_search_key )) {
	// 	$date_join = "AND r.date = '$date_search_key'";
	// } 

 //    $sql = "SELECT s.student_id as student_id,
	// 					s.name as name,
	// 					s.number as number,
	// 					s.branch_id as branch_id,
	// 					b.name as branch_name,
	// 					c.name as class_name,
	// 					s.class_id  as class_id,
	// 					r.status as status_index,
	// 					MAX(CASE WHEN r.report_id IS NOT null THEN r.report_id ELSE NULL END) AS report_id,
	// 					MAX(CASE WHEN r.status IS NOT null THEN r.status ELSE NULL END) AS status,
	// 					MAX(CASE WHEN r.date IS NOT null THEN r.date ELSE NULL END) AS date 

	// 			FROM {$wpdb->prefix}students s 
	// 				LEFT OUTER JOIN {$wpdb->prefix}reports_daily r 
	// 					ON s.student_id = r.student_id {$date_join}
	// 				LEFT OUTER JOIN {$wpdb->prefix}reports_daily_score rs 
	// 					ON r.report_id = rs.report_id 
	// 				LEFT OUTER JOIN {$wpdb->prefix}parents_students ps 
	// 					ON s.student_id = ps.student_id
	// 				LEFT OUTER JOIN {$wpdb->prefix}branch b 
	// 					ON b.branch_id = s.branch_id
	// 				LEFT OUTER JOIN {$wpdb->prefix}class c 
	// 					ON c.class_id = s.class_id
	// 			WHERE 	s.class_id = '".$_REQUEST['class']."' AND
	// 					s.branch_id = '".$_REQUEST['branch']."'
	// 			GROUP BY s.student_id ORDER BY s.name
	// ";

	// $reports_temp = $wpdb->get_results($sql, ARRAY_A);

	// foreach ($reports_temp as $report) {
	// 	if ($report['report_id']) {
	// 		$report['score'] = getDailyReportScoreByID($report['report_id']);
	// 	} else {
	// 		$report['score'] = null;
	// 	}

	// 	$reports[] = $report;
	// }

	$title = 'test';
	$filename = "Report-harian_".$title;


	
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"');

	$spreadsheet = new Spreadsheet();

	$spreadsheet->getProperties()
    ->setTitle($filename);

    $columnNames = array(
			'', 
			'',
			'',
			'',
			'Integritas',
			'',
			'Tanggung Jawab',
			'',
			'Produktif',
			'',
			'Spiritual',
			'',
			'Tangguh',
			'',
			'Pengendalian Diri',
			'',
			'Mandiri',
			'',
			'Pengambil Resiko',
			'',
			'Berkolaborasi',
			'',
			'Intelijen',
			'',
			'',
			'',
			'Komunikasi',
			'',
			'Kreasi',
			'',
			'Kesantunan',
			'',
			'Menghargai',
			'',
			'Berpikir Kritis',
			''
		);

    $spreadsheet->setActiveSheetIndex(0)->fromArray($columnNames, null, 'A1');

    $mergeCol1_i = 'E'; 
    foreach (range('E','Z') as $col) {
    	$mergeCol1_next = 'F';
    	$spreadsheet->getActiveSheet()->mergeCells($mergeCol1_i.'1:'.$mergeCol1_next.'1');
    	$mergeCol1_i++;
    	$mergeCol1_i++;
    }
    /*$spreadsheet->getActiveSheet()
    	->mergeCells('E1:F1')
    	->mergeCells('G1:H1')
    	->mergeCells('G1:H1')
    	->mergeCells('G1:H1')
    	->mergeCells('G1:H1')*/
	/*$spreadsheet->setActiveSheetIndex(0)
	    ->setCellValue('A1', 'This')
	    ->setCellValue('A2', 'is')
	    ->setCellValue('A3', 'only')
	    ->setCellValue('A4', 'an')
	    ->setCellValue('A5', 'example');*/

   /* $columnLetter = 'A';
    foreach ($columnNames as $columnName) {
        $spreadsheet->getActiveSheet()->setCellValue($columnLetter.'1', $columnName);
        $columnLetter++;
    }*/

	//Adding data to the excel sheet

	/*$spreadsheet->getActiveSheet()
	    ->setCellValue('B1', "You")
	    ->setCellValue('B2', "can")
	    ->setCellValue('B3', "download")
	    ->setCellValue('B4', "this")
	    ->setCellValue('B5', "library")
	    ->setCellValue('B6', "on")
	    ->setCellValue('B7', "https://php-download.com/package/phpoffice/phpspreadsheet");


	$spreadsheet->getActiveSheet()
	    ->setCellValue('C1', 1)
	    ->setCellValue('C2', 0.5)
	    ->setCellValue('C3', 0.25)
	    ->setCellValue('C4', 0.125)
	    ->setCellValue('C5', 0.0625);

	$spreadsheet->getActiveSheet()
	    ->setCellValue('C6', '=SUM(C1:C5)');
	$spreadsheet->getActiveSheet()
	    ->getStyle("C6")->getFont()
	    ->setBold(true);*/


	//$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
	$writer = new PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
	$writer->save('php://output');
	exit();

}
add_action('wp_ajax_export_excel_test','export_excel_test_cb');

