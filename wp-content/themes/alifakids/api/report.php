<?php 

function api_get_report_daily(WP_REST_Request $request){
	global $current_user;
	global $wpdb;

	$data = $request->get_params();

	if (isset($data['m']) &&isset( $data['y'])) {
		$data_m = monthToIndex($data['m']);
		$data_y = $data['y'];

		$first_day = current_time("$data_y-$data_m-01");

		$last_day = date("Y-m-t", strtotime($first_day));

		//return $first_day." ".$last_day;
	} else {
		$first_day = current_time('Y-m-01');
		$last_day = current_time('Y-m-d');
	}

	$union_i = 0;
	$union_j = 0;


	$loop_date = $first_day;
	$union_sql = '';

	while (strtotime($loop_date) <= strtotime($last_day)) {
		if ( date('N', strtotime($loop_date)) <= 6 ) {
			if (($union_j == 0) && ($union_i == 0)) {
				$union_sql .= " select DATE_ADD('$first_day', INTERVAL '$union_i' DAY) as day ";
			} elseif(($union_j == 0) && ($union_i != 0)) {
				$union_sql .= " select DATE_ADD('$first_day', INTERVAL '+$union_i' DAY) day ";
			} else {
				$union_sql .= " union select DATE_ADD('$first_day', INTERVAL '+$union_i' DAY) day ";
			}
			$union_j++;
		}
		$loop_date = date ("Y-m-d", strtotime("+1 day", strtotime($loop_date)));
		$union_i++;
	}

	$students = getParentStudents($current_user->ID);
	$student_id = intval($students[0]['student_id']);

	$join_report_sql = " AND rd.student_id='$student_id'";
	if(isset($data['name'])) {
		$search_students = getStudentByName($data['name']);

		if ($search_students) {
			$join_report_sql = "AND rd.student_id='".$search_students['student_id']."'";
		}


	} 

	$items = $wpdb->get_results(
		"SELECT 
				rd.report_id as report_id,
				days.day as date,
				rd.status as status,
				s.student_id as s_student_id,
				rd.student_id as rd_student_id
				FROM ( $union_sql ) days
				  	LEFT JOIN {$wpdb->prefix}reports_daily rd
				  	 	ON days.day = rd.date $join_report_sql
			  	 	LEFT JOIN {$wpdb->prefix}students s 
			  	 		ON rd.student_id = s.student_id
			  	 	LEFT JOIN {$wpdb->prefix}parents_students ps 
						ON s.student_id = ps.student_id AND ps.parent_id = '$current_user->ID'
		"
		, ARRAY_A
	);

	uasort($items, "sortFunction");

	$items2 = [];
	$i= 1;
	foreach ($items as $item) {
		//$item['date_format'] = $item['date'];

		$date_formated = date_i18n("l, d F Y", strtotime( $item['date'] ) );
		$item['date'] = $date_formated;
		$items2[] = $item; 
		$i++;
	}


	if (!empty($items2)) {
		$result['message'] = 'Get success';
		$result['data'] = $items2;
		return new WP_REST_Response($result, 200);
	} else {
		return new WP_Error( 'get_empty', 'No report found.', array( 'status' => 403 ) );
	}

}

function api_get_report_weekly(WP_REST_Request $request){
	global $wpdb;
	global $current_user;

	$student = getParentStudents($current_user->ID);

	$student_id = $student[0]['student_id'];

	$data = $request->get_params();

	if (isset($data['m']) && isset($data['y'])) {
		$data_m = monthToIndex($data['m']);
		$data_y = $data['y'];

		$first_day = current_time("$data_y-$data_m-01");
		$last_day = date("Y-m-t", strtotime($first_day));

		//return $first_day." ".$last_day;
	} else {
		$first_day = current_time('Y-m-01');
		$last_day = current_time('Y-m-d');
	}

	$union_i = 0;
	$union_j = 0;


	$loop_date = $first_day;
	$union_sql = '';

	while (strtotime($loop_date) <= strtotime($last_day)) {
		if ( date('N', strtotime($loop_date)) == 1 ) {
			if (($union_j == 0) && ($union_i == 0)) {
				$union_sql .= " select DATE_ADD('$first_day', INTERVAL '$union_i' DAY) as day ";
			} elseif(($union_j == 0) && ($union_i != 0)) {
				$union_sql .= " select DATE_ADD('$first_day', INTERVAL '+$union_i' DAY) day ";
			} else {
				$union_sql .= " union select DATE_ADD('$first_day', INTERVAL '+$union_i' DAY) day ";
			}
			$union_j++;
		}
		$loop_date = date ("Y-m-d", strtotime("+1 day", strtotime($loop_date)));
		$union_i++;
	}

	$students = getParentStudents($current_user->ID);
	$student_id = intval($students[0]['student_id']);

	$join_report_sql = " AND rd.student_id='$student_id'";
	if(isset($data['name'])) {
		$search_students = getStudentByName($data['name']);

		if ($search_students) {
			$join_report_sql = "AND rd.student_id='".$search_students['student_id']."'";
		}


	} 


	$sql = "SELECT 
				report_id,
				days.day as date,
				status
				FROM ( $union_sql ) days
				  	LEFT JOIN {$wpdb->prefix}reports_weekly rd
				  	 	ON days.day = rd.date $join_report_sql	
			  	 	LEFT JOIN {$wpdb->prefix}students s 
			  	 		ON rd.student_id = s.student_id 
			  	 	LEFT JOIN {$wpdb->prefix}parents_students ps 
						ON s.student_id = ps.student_id AND ps.parent_id = '$current_user->ID' 
	";

	$items = $wpdb->get_results( $sql, 'ARRAY_A' );

	uasort($items, "sortFunction");

	$items2 = []; 
	foreach ($items as $item) {
		$week_formated = weekOfMonth($item['date']);
		$item['date'] = 'Minggu ke-'.$week_formated;

		$items2[] = $item; 
	}


	if (!empty($items2)) {
		$result['data'] = $items2;
		return new WP_REST_Response($result, 200);
	} else {
		return new WP_Error( 'get_empty', 'No report found.', array( 'status' => 403 ) );
	}
}

function api_get_report_monthly(WP_REST_Request $request){
	global $wpdb;
	global $current_user;
	$data = $request->get_params();

	$student = getParentStudents($current_user->ID);

	//$student_id = $student[0]['student_id'];

	if(isset($data['name'])) {
		$search_students = getStudentByName($data['name']);
		if ($search_students) {
			$student_id = $search_students['student_id'];
		}
	} 


	if (isset($data['m']) && isset($data['y'])) {
		$data_m = monthToIndex($data['m']);
		$data_y = $data['y'];

		$first_day = current_time("$data_y-$data_m-01");

		//return $first_day." ".$last_day;
	} else {
		$first_day = current_time('Y-m-01');
	}


	$items['report_id'] = $first_day;
	$items['date'] = "Bulan".date_i18n(" F Y", strtotime( $first_day ) );
	$items['status'] = "1";
	$items['student_id'] = $student_id;


	if (!empty($items)) {
		$result['data'][] = $items;
		return new WP_REST_Response($result, 200);
	} else {
		return new WP_Error( 'get_empty', 'No report found.', array( 'status' => 403 ) );
	}
}

function api_get_report_daily_detail(WP_REST_Request $request){
	global $wpdb;
	global $current_user;

	$data = $request->get_params();	

	$items = getApiDailyReportScoreByID($data['id']);

	if (!empty($items)) {
		$result = $items; 

		return new WP_REST_Response($result, 200);
	} else {
		return new WP_Error( 'get_empty', 'No report found.', array( 'status' => 403 ) );
	}

}

function api_get_report_weekly_detail(WP_REST_Request $request){
	global $wpdb;
	global $current_user;

	$data = $request->get_params();	


	$reports = getWeeklyReportPointsByID($data['id']); 

	$items = [];
	foreach ($reports as $key => $value) {
		$name = getWeeklyReportNameByID($key);
		$score = $value;
		array_push($items, array('name' => $name, 'score' => $score ));
	}

	if (!empty($items)) {
		$result['data'] = $items;
		return new WP_REST_Response($result, 200);
	} else {
		return new WP_Error( 'get_empty', 'No report found.', array( 'status' => 403 ) );
	}

}

function api_get_report_monthly_detail(WP_REST_Request $request){
	global $wpdb;
	global $current_user;

	$data = $request->get_params();	

	$date = $data['date'];

	$student = getParentStudents($current_user->ID);

	$student_id = $data['student_id'];


	$table = new ReportsMonthly();

	$temp_report = $table->getStudentMonthlyReport($student_id, $date);
	$report = [];

	foreach ($temp_report as $key => $value) {
	  $report[$value['points_key']][$value['points_dimension']][$value['points_id']] = $value['score'];
	}

	$temp_points = getDailyReportPoints();
	$points = [];
	foreach ($temp_points as $key => $value) {
	  $points[$value['points_key']][$value['points_dimension']][$value['points_id']] = $value['name'];
	}

	//return $report['amanah']['integritas'][2];

	$items = [];
	foreach ($points as $points_key => $dimensions) {
		foreach ($dimensions as $dimensions_key => $dimension) {
			$count_dimension = ( !empty($report[$points_key][$dimensions_key]) ) ? array_sum($report[$points_key][$dimensions_key]) : 0;

			foreach ($dimension as $id => $name){
				 if (isset($report[$points_key][$dimensions_key][$id])) {
	                $score = $report[$points_key][$dimensions_key][$id];
	                $score_percent = ($score / $count_dimension)*100;
	              }  else {
	                $score = "0";
	                $score_percent = "0";
	              }

				$items_temp['name'] =  $name;
				$items_temp['score'] =  $score;
				$items_temp['score_percent'] =  number_format($score_percent,2, '.', '');

				$items[$dimensions_key][] =  $items_temp;
				//$items[$points_key][$dimensions_key][$id]['score'] = $score;
			}
		}

	}

	if (!empty($items)) {
		$result = $items;
		return new WP_REST_Response($result, 200);
	} else {
		return new WP_Error( 'get_empty', 'No report found.', array( 'status' => 403 ) );
	}
}

function api_get_report_daily_confirm(WP_REST_Request $request){
	global $wpdb;

	$data = $request->get_params();	

	$update = $wpdb->update( 
		"{$wpdb->prefix}reports_daily", 
		array('status' => 2 ),
		array('report_id' => $data['id'] )
	);

	return $update;

	if ($update) {
		$result['message'] = 'Confirm success';
		return new WP_REST_Response($result, 200);
	} else {
		return new WP_Error( 'confirm_error', 'Confirm report error.', array( 'status' => 403 ) );
	}
}

function api_get_report_weekly_confirm(WP_REST_Request $request){
	global $wpdb;

	$data = $request->get_params();	
	
	$update = $wpdb->update( 
		"{$wpdb->prefix}reports_weekly", 
		array('status' => 2 ),
		array('report_id' => $data['id'] )
	);

	if ($update) {
		$result['message'] = 'Confirm success';
		return new WP_REST_Response($result, 200);
	} else {
		return new WP_Error( 'confirm_error', 'Confirm report error.', array( 'status' => 403 ) );
	}
}

function sortFunction( $a, $b ) {
    return strtotime($b['date']) - strtotime($a['date']);
}

function monthToIndex($month = ""){
	$months = [];

	$months['Januari'] = 1;
	$months['Februari'] = 2;
	$months['Maret'] = 3;
	$months['April'] = 4;
	$months['Mei'] = 5;
	$months['Juni'] = 6;
	$months['Juli'] = 7;
	$months['Agustus'] = 8;
	$months['September'] = 9;
	$months['Oktober'] = 10;
	$months['November'] = 11;
	$months['Desember'] = 12;

	return $months[$month];
}

function getApiDailyReportScoreByID($report_id) {
	global $wpdb;

	$sql = "SELECT * 
				FROM ak_reports_daily_points rdp
					LEFT JOIN ak_reports_daily_score rds
				    	ON rdp.points_id = rds.points_id";

	$sql .= " WHERE rds.report_id = '".$report_id."' ";

	$query = $wpdb->get_results( $sql, 'ARRAY_A' );

	$result = [];

	foreach($query as $value){
		$result[ $value['points_dimension'] ][]['name'] = $value['name'];
	}

	return $result;
}