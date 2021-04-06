<?php 

function api_get_user(){
	global $current_user;
	global $wpdb;

	$item = [];

	$item = $wpdb->get_row(
	    	$wpdb->prepare( " 
	    			SELECT 	u.ID as ID,
						    u.user_login AS user_login,
						    u.user_email AS user_email,
						    (select meta_value from {$wpdb->prefix}usermeta where user_id = u.id and meta_key = 'first_name' limit 1) as first_name,
							(select meta_value from {$wpdb->prefix}usermeta where user_id = u.id and meta_key = 'last_name' limit 1) as last_name,
							(select meta_value from {$wpdb->prefix}usermeta where user_id = u.id and meta_key = 'address' limit 1) as address,
							(select meta_value from {$wpdb->prefix}usermeta where user_id = u.id and meta_key = 'phone' limit 1) as phone,
							(select meta_value from {$wpdb->prefix}usermeta where user_id = u.id and meta_key = 'birth_place' limit 1) as birth_place,
							(select meta_value from {$wpdb->prefix}usermeta where user_id = u.id and meta_key = 'birth_date' limit 1) as birth_date,
							(select meta_value from {$wpdb->prefix}usermeta where user_id = u.id and meta_key = 'religion' limit 1) as religion,
							(select meta_value from {$wpdb->prefix}usermeta where user_id = u.id and meta_key = 'education' limit 1) as education,
							(select meta_value from {$wpdb->prefix}usermeta where user_id = u.id and meta_key = 'social_media' limit 1) as social_media,
							(select meta_value from {$wpdb->prefix}usermeta where user_id = u.id and meta_key = 'occupation' limit 1) as occupation,
							(select meta_value from {$wpdb->prefix}usermeta where user_id = u.id and meta_key = 'office_address' limit 1) as office_address,
							(select meta_value from {$wpdb->prefix}usermeta where user_id = u.id and meta_key = 'mother_name' limit 1) as mother_name,
							(select meta_value from {$wpdb->prefix}usermeta where user_id = u.id and meta_key = 'father_name' limit 1) as father_name
					FROM {$wpdb->prefix}users as u
					WHERE (select meta_value from {$wpdb->prefix}usermeta where user_id = u.id and meta_key = 'ak_capabilities' limit 1) LIKE '%parent%'
					AND u.ID = '%d' 
				", 
	    		$current_user->ID
	    	), 
	    	ARRAY_A
    );


	if ($item) {
		$avatar = get_avatar_url($current_user->ID,'150');
		$item['avatar'] = ($avatar) ? $avatar : '' ;
		$result['data'][] = $item;
		//$result["data"] = array();
		//array_push($result["data"],$item);
		//return $result;
		return new WP_REST_Response($result, 200);
	} else {
		return new WP_Error( 'get_error', 'Error getting data.', array( 'status' => 403 ) );
	}

}

function api_update_user(WP_REST_Request $request){
	global $current_user;
	global $wpdb;

	$data = $request->get_params();
	$file = $request->get_file_params();

	$user_info = get_userdata($current_user->ID);
	$user_email = $user_info->user_email;

	if ( email_exists($data['email']) && $user_email != $data['email']) {
		return new WP_Error( 'email_exists', 'Email already exists.', array( 'status' => 403 ) );
	} 

	if (!empty($file)) {

		$avatar = $_FILES['avatar'];
		$wordpress_upload_dir = wp_upload_dir();
	    $new_file_path = $wordpress_upload_dir['path'] . '/' . $avatar['name'];
	    $new_file_mime = mime_content_type( $avatar['tmp_name'] );

	    if( $avatar['size'] > wp_max_upload_size() )
			return new WP_Error( 'file_to_large', 'File to large.', array( 'status' => 403 ) );
 
		if( !in_array( $new_file_mime, get_allowed_mime_types() ) )
			return new WP_Error( 'file_not_allowed', 'File type not allowed.', array( 'status' => 403 ) );

		
		if( move_uploaded_file( $avatar['tmp_name'], $new_file_path ) ) {

			$upload_id = wp_insert_attachment( array(
				'guid'           => $new_file_path, 
				'post_mime_type' => $new_file_mime,
				'post_title'     => preg_replace( '/\.[^.]+$/', '', $avatar['name'] ),
				'post_content'   => '',
				'post_status'    => 'inherit'
			), $new_file_path );

			wp_update_attachment_metadata( $upload_id, wp_generate_attachment_metadata( $upload_id, $new_file_path ) );

			update_user_meta( $current_user->ID, 'ak_user_avatar', $upload_id);
 
			require_once( ABSPATH . 'wp-admin/includes/image.php' );
    	}		
	}
	
	$user_array = array (
        'ID' 			=> $current_user->ID,
        'user_email'    =>  $data['email'],
        'first_name'    =>  $data['first_name'],
        'last_name'     =>  $data['last_name']
    );
    $update = wp_update_user( $user_array );

    update_user_meta( $current_user->ID, 'birth_place', $data['birth_place']);
	update_user_meta( $current_user->ID, 'birth_date', $data['birth_date']);
	update_user_meta( $current_user->ID, 'address', $data['address']);
	update_user_meta( $current_user->ID, 'religion', $data['religion']);
	update_user_meta( $current_user->ID, 'education', $data['education']);
	update_user_meta( $current_user->ID, 'occupation', $data['occupation']);
	update_user_meta( $current_user->ID, 'office_address', $data['office_address']);
	update_user_meta( $current_user->ID, 'social_media', $data['social_media']);
	update_user_meta( $current_user->ID, 'phone', $data['phone']);
	update_user_meta( $current_user->ID, 'mother_name', $data['mother_name']);
	update_user_meta( $current_user->ID, 'father_name', $data['father_name']);

	if ($update) {
		$result['code'] = 'update_success';
		$result['message'] = 'Data Updated';

		return new WP_REST_Response($result, 200);
	} else {
		return new WP_Error( 'error_updating_data', 'Error updating data.', array( 'status' => 403 ) );
	}
}

function api_update_user_avatar(WP_REST_Request $request){
	global $current_user;
	global $wpdb;

	$data = $request->get_params();
	$file = $request->get_file_params();

	if (!empty($file)) {
		$avatar = $_FILES['avatar'];
		$wordpress_upload_dir = wp_upload_dir();
	    $new_file_path = $wordpress_upload_dir['path'] . '/' . $avatar['name'];
	    $new_file_mime = mime_content_type( $avatar['tmp_name'] );

	    if( $avatar['size'] > wp_max_upload_size() )
			return new WP_Error( 'file_to_large', 'File to large.', array( 'status' => 403 ) );
 
		if( !in_array( $new_file_mime, get_allowed_mime_types() ) )
			return new WP_Error( 'file_not_allowed', 'File type not allowed.', array( 'status' => 403 ) );

		
		if( move_uploaded_file( $avatar['tmp_name'], $new_file_path ) ) {
			$upload_id = wp_insert_attachment( array(
				'guid'           => $new_file_path, 
				'post_mime_type' => $new_file_mime,
				'post_title'     => preg_replace( '/\.[^.]+$/', '', $avatar['name'] ),
				'post_content'   => '',
				'post_status'    => 'inherit'
			), $new_file_path );

			wp_update_attachment_metadata( $upload_id, wp_generate_attachment_metadata( $upload_id, $new_file_path ) );

			update_user_meta( $current_user->ID, 'ak_user_avatar', $upload_id);
 
			require_once( ABSPATH . 'wp-admin/includes/image.php' );
			$result['code'] = 'update_success';
			$result['message'] = 'Data Updated';
			$result['data'][]['user_profile'] = $avatar = get_avatar_url($current_user->ID,'150');;

			if ($upload_id) {
				return new WP_REST_Response($result, 200);
			}

		} else {
			return new WP_Error( 'error_updating_data', 'Error updating data.', array( 'status' => 403 ) );
		}	
	} else {
		return new WP_Error( 'error_updating_data', 'Error updating data.', array( 'status' => 403 ) );
	}
}

function api_password_user($data){
	global $current_user;

	wp_set_password( $data['password'], $current_user->ID );

	$result['code'] = 'update_success';
	$result['message'] = 'Password Changed';

	return new WP_REST_Response($result, 200);
}

function api_student_user($data) {
	global $current_user;
	global $wpdb;

	$item = $wpdb->get_results( $wpdb->prepare(
		"SELECT S.*,
				PS.parent_id,
				S.father_name,
				S.mother_name,
				(select name from {$wpdb->prefix}class where S.class_id = class_id  limit 1) as class_name,
				(select name from {$wpdb->prefix}branch where S.branch_id = branch_id  limit 1) as branch_name
			FROM {$wpdb->prefix}parents_students as PS
				LEFT JOIN {$wpdb->prefix}students as S
					ON  PS.student_id = S.student_id
			WHERE parent_id = '%s'",
		$current_user->ID
	),ARRAY_A );

	if ($item) {
		$result['data'] = $item;
		return new WP_REST_Response($result, 200);
	} else {
		return new WP_Error( 'get_error', 'Error getting data.', array( 'status' => 403 ) );
	}
}