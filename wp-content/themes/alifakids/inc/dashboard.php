<?php 

/**
 * Dashboard Parent
 */
class DashboardReports {
	
	public function get_daily_reports() {
		
		global $wpdb;

		$by_user = '';
		if ( is_parent() ) {
			$by_user = ' WHERE ps.parent_id = '.get_current_user_id().' ';
		}

		$sql = "SELECT s.student_id as student_id,
						s.name as name,
						s.number as number,
						s.branch_id as branch_id,
						s.class_id  as class_id,
						r.status as status_index,
						MAX(CASE WHEN r.report_id IS NOT null THEN r.report_id ELSE NULL END) AS report_id,
						MAX(CASE WHEN r.status IS NOT null THEN r.status ELSE NULL END) AS status,
						MAX(CASE WHEN r.date IS NOT null THEN r.date ELSE NULL END) AS date 

				FROM {$wpdb->prefix}students s 
					LEFT OUTER JOIN {$wpdb->prefix}reports_daily r 
						ON s.student_id = r.student_id AND r.date = CURRENT_DATE
					LEFT OUTER JOIN {$wpdb->prefix}reports_daily_score rs 
						ON r.report_id = rs.report_id 
					LEFT OUTER JOIN {$wpdb->prefix}parents_students ps 
						ON s.student_id = ps.student_id
					$by_user
				GROUP BY s.student_id
				ORDER BY s.name ASC 
		";

		return $wpdb->get_results( $sql, 'ARRAY_A' );
	}

	public function get_weekly_reports() {
		global $wpdb;

		$by_user = '';
		if (is_parent()) {
			$by_user = ' WHERE ps.parent_id = '.get_current_user_id().' ';
		}


		$sql = "SELECT s.student_id as student_id,
						s.name as name,
						s.number as number,
						s.branch_id as branch_id,
						s.class_id  as class_id,
						r.status as status_index,
						MAX(CASE WHEN r.report_id IS NOT null THEN r.report_id ELSE NULL END) AS report_id,
						MAX(CASE WHEN r.status IS NOT null THEN r.status ELSE NULL END) AS status,
						MAX(CASE WHEN r.date IS NOT null THEN r.date ELSE NULL END) AS date 

				FROM {$wpdb->prefix}students s 
					LEFT OUTER JOIN {$wpdb->prefix}reports_weekly r 
						ON s.student_id = r.student_id AND r.date = ( CURRENT_DATE - INTERVAL((WEEKDAY( CURRENT_DATE )) ) DAY)
					LEFT OUTER JOIN {$wpdb->prefix}reports_weekly_score rs 
						ON r.report_id = rs.report_id 
					LEFT OUTER JOIN {$wpdb->prefix}parents_students ps 
						ON s.student_id = ps.student_id
					$by_user
		";

		return $wpdb->get_results( $sql, 'ARRAY_A' );
	}

	public function get_completed_daily_report() {
		global $wpdb;
		global $user_ID;

		$result = [];

		if (is_teacher()) {
			$result['completed'] = $wpdb->get_var(" SELECT COUNT(*) 
						FROM {$wpdb->prefix}reports_daily r 
							LEFT JOIN {$wpdb->prefix}students s
								ON s.student_id = r.student_id
							 LEFT JOIN {$wpdb->prefix}usermeta u
								ON u.meta_key='branch' AND u.user_id = '$user_ID'
							 LEFT JOIN {$wpdb->prefix}usermeta u2
								ON u2.meta_key='class' AND u2.user_id = '$user_ID'
						WHERE r.date = CURRENT_DATE 
							AND status >= 0
	                        AND u.meta_value = s.branch_id
	                        AND u2.meta_value = s.class_id
	                        GROUP BY s.student_id
					");

			$result['all'] = $wpdb->get_var(" SELECT COUNT(*) 
						FROM {$wpdb->prefix}students s 
							LEFT JOIN {$wpdb->prefix}usermeta u
								ON u.meta_key='branch' AND u.user_id = '$user_ID'
							LEFT JOIN {$wpdb->prefix}usermeta u2
								ON u2.meta_key='class' AND u2.user_id = '$user_ID'
							WHERE u.meta_value = s.branch_id AND u2.meta_value = s.class_id
	                        	GROUP BY s.student_id
					");
		} else {
			$result['completed'] = $wpdb->get_var(" SELECT COUNT(*) 
						FROM {$wpdb->prefix}reports_daily r 
						WHERE r.date = CURRENT_DATE 
							AND status >= 0
					");

			$result['all'] = $wpdb->get_var(" SELECT COUNT(*) 
						FROM {$wpdb->prefix}students
					");
		}

		if ($result['completed']) {
			$result['completed'] = $result['completed'];
		} else {
			$result['completed'] = 0;
		}

		if ($result['all']) {
			$result['all'] = $result['all'];
		} else {
			$result['all'] = 0;
		}

		return $result;
	}

	public function get_completed_weekly_report() {
		global $wpdb;
		global $user_ID;

		$result = [];

		if (is_teacher()) {

			$result['completed'] = $wpdb->get_var("SELECT COUNT(*) 
						FROM {$wpdb->prefix}reports_weekly r 
							LEFT JOIN {$wpdb->prefix}students s
								ON s.student_id = r.student_id
							LEFT JOIN {$wpdb->prefix}usermeta u
								ON u.meta_key='branch' AND u.user_id = '$user_ID'
							LEFT JOIN {$wpdb->prefix}usermeta u2
								ON u2.meta_key='class' AND u2.user_id = '$user_ID'
						WHERE r.date = ( CURRENT_DATE - INTERVAL((WEEKDAY( CURRENT_DATE )) ) DAY) 
							AND status >= 0
	                        AND u.meta_value = s.branch_id
	                        AND u2.meta_value = s.class_id
	                        GROUP BY s.student_id
					");

			$result['all'] = $wpdb->get_var(" SELECT COUNT(*) 
						FROM {$wpdb->prefix}students s 
							LEFT JOIN {$wpdb->prefix}usermeta u
								ON u.meta_key='branch' AND u.user_id = '$user_ID'
							LEFT JOIN {$wpdb->prefix}usermeta u2
								ON u2.meta_key='class' AND u2.user_id = '$user_ID'
							WHERE u.meta_value = s.branch_id AND u2.meta_value = s.class_id
	                        	GROUP BY s.student_id
					");
		} else {
			$result['completed'] = $wpdb->get_var(" SELECT COUNT(*) 
						FROM {$wpdb->prefix}reports_daily r 
						WHERE r.date = ( CURRENT_DATE - INTERVAL((WEEKDAY( CURRENT_DATE )) ) DAY) 
							AND status >= 0
					");

			$result['all'] = $wpdb->get_var(" SELECT COUNT(*) 
						FROM {$wpdb->prefix}students
					");
		}

		if ($result['completed']) {
			$result['completed'] = $result['completed'];
		} else {
			$result['completed'] = 0;
		}

		if ($result['all']) {
			$result['all'] = $result['all'];
		} else {
			$result['all'] = 0;
		}

		return $result;
	}

	public function prepare_items() {
		global $wpdb;

		$_SERVER['REQUEST_URI'] = remove_query_arg( '_wp_http_referer', $_SERVER['REQUEST_URI'] );

		$result = [];

		if (is_parent()) {
			$daily_result = $this->get_daily_reports();
			$weekly_result = $this->get_weekly_reports();

			$result['daily'] = $daily_result;
			$result['weekly'] = $weekly_result;
		}


		return $this->items = $result;
	}

}