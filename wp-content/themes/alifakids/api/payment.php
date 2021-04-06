<?php

function api_get_payment_tagihan(WP_REST_Request $request) {
	global $wpdb;
	global $current_user;

	$data = $request->get_params();

	$udata = get_userdata( $current_user->ID );

	$student_id = getStudentByID($data['id'])->student_id;

	$registered = date("Y-m-01",strtotime($udata->user_registered));

	$union_sql = '';
	

	$first_day = $registered;
	$last_day = current_time('Y-m-d');

	$loop_date = $first_day;
	$union_i = 0;

	while (strtotime($loop_date) <= strtotime($last_day)) {

		if ($union_i == 0) {
			$union_sql .= " select DATE_ADD('$first_day', INTERVAL '$union_i' MONTH) as month ";
		} else {
			$union_sql .= " union select DATE_ADD('$first_day', INTERVAL '+$union_i' MONTH) month ";
		}

		$loop_date = date ("Y-m-d", strtotime("+1 MONTH", strtotime($loop_date)));
		$union_i++;
	}


	$sql = "SELECT 
				payment_id,
				months.month as period,
				$student_id as student_id,
				date,
				status
				FROM ( $union_sql ) months
				  	LEFT JOIN {$wpdb->prefix}payment p
				  	 	ON MONTH(months.month) = MONTH(p.period) 
				  	 	AND YEAR(months.month) = YEAR(p.period) 
				  	 	AND p.student_id = '$student_id'
	  	 		WHERE payment_id IS NULL OR status = '1'
		  	 	ORDER BY months.month desc
	";
		

	$items = $wpdb->get_results($sql, ARRAY_A);

	$items2 = [];
	$i= 1;
	foreach ($items as $item) {
		$date_formated = date_i18n("F Y", strtotime( $item['period'] ) );
		$item['bulan'] = $date_formated;
		$items2[] = $item; 
	}

	if (!empty($items2)) {
		$result['message'] = 'Get success';
		$result['data'] = $items2;
		return new WP_REST_Response($result, 200);
	} else {
		$result['message'] = 'Get success';
		$result['data'] = [];
		return new WP_REST_Response($result, 200);
		//return new WP_Error( 'get_empty', 'No data found.', array( 'status' => 403 ) );
	}
} 

function api_get_payment_history(WP_REST_Request $request) {
	global $wpdb;
	global $current_user;

	$data = $request->get_params();

	$student_id = getStudentByID($data['id'])->student_id;

	$sql = "SELECT 
				payment_id,
				period as period,
				student_id as student_id,
				date,
				status
				FROM  {$wpdb->prefix}payment p
	  	 		WHERE p.student_id = '$student_id' AND status=2
		  	 	ORDER BY period desc
	";
	


	$items = $wpdb->get_results($sql, ARRAY_A);

	$items2 = [];
	$i= 1;
	foreach ($items as $item) {
		$date_formated = date_i18n("F Y", strtotime( $item['period'] ) );
		$item['bulan'] = $date_formated;
		$items2[] = $item; 
	}

	if (!empty($items2)) {
		$result['message'] = 'Get success';
		$result['data'] = $items2;
		return new WP_REST_Response($result, 200);
	} else {
		$result['message'] = 'Get success';
		$result['data'] = [];
		return new WP_REST_Response($result, 200);
		//return new WP_Error( 'get_empty', 'No data found.', array( 'status' => 403 ) );
	}

} 

function api_get_payment_history_detail(WP_REST_Request $request) {
	$data = $request->get_params();	

	$item = getPaymentDetailByID($data['id']);
	$student = getStudentByID($item['student_id']);
	if (!empty($item)) {
		$result['message'] = 'Get success';
		$date_formated = date_i18n("F Y", strtotime( $item['period'] ) );
		$item['bulan'] = $date_formated;
		$item['student_name'] = $student->name;
		$item['image'] = ($item['image']) ? wp_get_attachment_url( $item['image'] ) : '';
		$result['data'][] = $item;
		return new WP_REST_Response($result, 200);
	} else {
		return new WP_Error( 'get_empty', 'No data found.', array( 'status' => 403 ) );
	}
}

function api_post_payment_tagihan(WP_REST_Request $request) {
	global $current_user;
    global $wpdb;

	$data = $request->get_params();
    $file = $request->get_file_params();

    $attachment = $_FILES['image'];

	$table_name = $wpdb->prefix . 'payment';   

	$select = $wpdb->get_row( $wpdb->prepare("
			SELECT * FROM {$wpdb->prefix}payment p WHERE student_id = '%s' AND period='%s'
		", $data['student_id'], $data['id']
	), ARRAY_A );

	//return $select;


	if (empty($select)) {
		$insert = $wpdb->insert($table_name, array(
				'student_id' => $data['student_id'],
				'period' => $data['id'],
				'date' => $data['date'],
				'amount' => $data['amount'],
				'sender' => $data['sender'],
				'transfer_to' => 'BCA',
				'status' => 1
			)
		);
	} else {
		$insert = $wpdb->update($table_name, array(
				'date' => $data['date'],
				'amount' => $data['amount'],
				'sender' => $data['sender'],
				'transfer_to' => 'BCA',
				'status' => 1
			), array(
				'student_id' => $data['student_id'], 
				'period' => $data['id']
			)
		);
	}


	if($insert) {
    	$payment_id = $wpdb->insert_id;

    	if (isset($_FILES['image'])) {
    		$attachment = $_FILES['image'];
			$wordpress_upload_dir = wp_upload_dir();
		    $new_file_path = $wordpress_upload_dir['path'] . '/' . $attachment['name'];
		    $new_file_mime = mime_content_type( $attachment['tmp_name'] );

		    if( $attachment['size'] > wp_max_upload_size() )
				return new WP_Error( 'file_to_large', 'File to large.', array( 'status' => 403 ) );
		 
			if( !in_array( $new_file_mime, get_allowed_mime_types() ) )
				return new WP_Error( 'file_not_allowed', 'File type not allowed.', array( 'status' => 403 ) );
	    	
	    	if( move_uploaded_file( $attachment['tmp_name'], $new_file_path ) ) {
				$upload_id = wp_insert_attachment( array(
					'guid'           => $new_file_path, 
					'post_mime_type' => $new_file_mime,
					'post_title'     => preg_replace( '/\.[^.]+$/', '', $attachment['name'] ),
					'post_content'   => '',
					'post_status'    => 'inherit'
				), $new_file_path );

				wp_update_attachment_metadata( $upload_id, wp_generate_attachment_metadata( $upload_id, $new_file_path ) );

				$wpdb->update ( 
					$table_name, 
					array('image' => $upload_id ), 
					array('payment_id' => $payment_id)
				);

	 
				require_once( ABSPATH . 'wp-admin/includes/image.php' );
	    	}		
    	}
		
		$result['code'] = 'post_success';
		$result['message'] = 'Data Updated';

		return new WP_REST_Response($result, 200);	
    } else {
    	return new WP_Error( 'error_updating_data', 'Error updating data.', array( 'status' => 403 ) );
    }
}