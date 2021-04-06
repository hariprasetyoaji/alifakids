<?php 

/*function api_get_course(WP_REST_Request $request) {
	global $wpdb;
	global $current_user;

	$data = $request->get_params();

	$class_id = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}parents_students as PS
			LEFT JOIN {$wpdb->prefix}students as S
				ON  PS.student_id = S.student_id
		WHERE parent_id = '$current_user->ID'",ARRAY_A );

	$arr_class = [];
	foreach ($class_id as $value) {
		$arr_class[] = $value['class_id'];
	}

	$student = getParentStudents($current_user->ID);

	if (empty($data['id']) && empty($data['student'])) {

		$args = array(
			'post_type' => 'course',
			'post_status' => 'publish',
			'posts_per_page' => -1,
			'meta_query' => array(
			  array(
			      'key'     => 'class_id',
			      'value'   => $arr_class,
			      'compare' => 'IN'
			  )
			)
		);
		$query = new WP_Query( $args );

		$posts = $query->posts;

		$user_payment = checkUserPayment();

		$terms = array();
		foreach( $posts  as $post ) {
			$new_cats = wp_get_object_terms( $post->ID, 'lesson' );

			$attachment_id = get_term_meta($new_cats[0]->term_id,'image',true);

			$course_class = get_post_meta( $post->ID, 'class_id', true );
  			$new_cats[0]->name = html_entity_decode($new_cats[0]->name);

  			$new_cats[0]->thumb = (wp_get_attachment_url($attachment_id)) ? wp_get_attachment_url($attachment_id) : '';
  			$new_cats[0]->class_id = $course_class;

  			foreach ($student as $value) {
  				if ($value['class_id'] == $course_class) {
		  			$new_cats[0]->student_id = $value['student_id'];
	  				$new_cats[0]->student_name = $value['name'];
	  				if ($user_payment && in_array($value['student_id'], $user_payment)) {
	  					$new_cats[0]->payment = false;
	  				} else {
	  					$new_cats[0]->payment = true;

	  				}
  				}
  			}


			$terms = array_merge($terms, $new_cats);
		}

		$terms = array_unique($terms,SORT_REGULAR);

		$terms2 = [];
		foreach ($terms as $term) {
			$terms2[] = $term; 

		}

		if (!empty($terms2)) {
			$result['data'] = $terms2;
			return new WP_REST_Response($result, 200);
		} else {
			return new WP_Error( 'get_empty', 'No course found.', array( 'status' => 403 ) );
		}
	} else {

		$class_id = getStudentByID($data['student'])->class_id;
		
		$level = getStudentLessonCompleted($data['id'],$class_id,$data['student']);

		$post_args = array(
		    'post_type' => 'course',
		    'post_status' => 'publish',
		    'posts_per_page' => -1,
		    'meta_query' => array(
		          array(
		              "key"     => "class_id",
		              "value"   => $class_id
		          )
		    ),
		    'tax_query' => array(
		        array (
		            'taxonomy' => 'lesson',
		            'field' => 'term_id',
		            'terms' => $data['id']
		        )
		    ),
		    'meta_key' => 'level',
		    'meta_type' => 'NUMERIC',
		    'orderby' => 'meta_value_num',
		    'order' => 'ASC'
	  	);
	  	$post_query = new WP_Query( $post_args );

	  	if ( $post_query->have_posts() ) {
			foreach ($post_query->posts as $post) {
				$lesson_level = get_post_meta($post->ID, 'level', true);
				
				$completed = false;
				$post->level = $lesson_level;
				$post->completed = false;
				$post->status = 'locked';

				if ($level) {
					$post->completed = in_array($lesson_level, $level);
					$last_completed = max($level)+1;

					if($post->completed || $last_completed == $lesson_level ||  $lesson_level <= $last_completed ) {
						$post->status = 'available';
					}
				} else {
					if ($lesson_level == 1) {
						$post->status = 'available';
						$post->completed = false;
					}
				}

				$post->thumb = (get_the_post_thumbnail_url( $post->ID, 'full' )) ? get_the_post_thumbnail_url( $post->ID, 'full' ) : '';

				$result['data'][] = $post;
			}
			return new WP_REST_Response($result, 200);
		} else {
			return new WP_Error( 'get_empty', 'No posts found.', array( 'status' => 403 ) );
		}
	}

}*/

function api_get_course(WP_REST_Request $request) {
	global $wpdb;
	global $current_user;

	$data = $request->get_params();
	
	//exit('tes');

	$class_id = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}parents_students as PS
			LEFT JOIN {$wpdb->prefix}students as S
				ON  PS.student_id = S.student_id
		WHERE parent_id = '$current_user->ID'",ARRAY_A );

	$arr_class = [];
	foreach ($class_id as $value) {
		$arr_class[] = $value['class_id'];
	}

	$student = getParentStudents($current_user->ID);

	$user_payment = checkUserPayment();

	if( !empty($data['student']) && 
		 !empty($data['year']) && 
		 !empty($data['month']) && 
		 !empty($data['week']) && 
		 !empty($data['day']) 
	){

		$nama_bulan = array(
			 'Januari' => '1',
			 'Februari' => '2',
			 'Maret' => '3',
			 'April' => '4',
			 'Mei' => '5',
			 'Juni' => '6',
			 'Juli' => '7',
			 'Agustus' => '8',
			 'September' => '9',
			 'Oktober' => '10',
			 'November' => '11',
			 'Desember' => '12'
		);

		$month = $nama_bulan[$data['month']];
		//exit(var_dump($month));

	 	$reports = getUserCourseDayReport( 
 			$data['student'], 
 			$data['year'], 
 			$month, 
 			$data['week'], 
 			$data['day']
 		);
	 	if (!empty($reports)) {
	 		$result['completed'] = true;
	 	} else {
	 		$result['completed'] = false;
	 	}
	 	$completed = getUserCourseDayReport(
	 		$data['student'], 
	 		$data['year'], 
	 		$month, 
	 		$data['week']
	 	);
	 	$available = [];
	 	if (!empty($completed)) {
			foreach ($completed as $value) {
				$available[] = intval($value);
			}
			array_push($available, end($completed)+1);
		} else {
			$available = array(1);
		}

		if ($user_payment && in_array($data['student'], $user_payment)) {
			$result['payment'] = false;
		} else {
			$result['payment'] = true;
		}

		
		if (in_array($data['day'], $available)) {
			$result['available'] = true;
		} else {
			$result['available'] = true;
		}

		$class_id = getStudentByID($data['student'])->class_id;
		$post_args = array(
            'post_type' => 'course',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'meta_query' => array(
                  array(
                      "key"     => "class_id",
                      "value"   => $class_id
                  ),
                  array(
                      'key'     => 'year',
                      'value'   => $data['year']
                  ),
                  array(
                      'key'     => 'month',
                      'value'   => $month
                  ),
                  array(
                      'key'     => 'week',
                      'value'   => $data['week']
                  ), 
                  array(
                      'key'     => 'day',
                      'value'   => $data['day']
                  )
            ),
            'orderby' => 'title',
		    'order' => 'ASC'
      	);
      	$post_query = new WP_Query( $post_args );

      	if ( $post_query->have_posts() ) {
			foreach ($post_query->posts as $post) {
				$post->thumb = (get_the_post_thumbnail_url( $post->ID, 'full' )) ? get_the_post_thumbnail_url( $post->ID, 'full' ) : '';

				$result['data'][] = $post;
			}
			return new WP_REST_Response($result, 200);
		} else {
			return new WP_Error( 'get_empty', 'No posts found.', array( 'status' => 403 ) );
		}


	} else {
		return new WP_Error( 'get_empty', 'No posts found.', array( 'status' => 403 ) );
	}

}


function api_get_course_detail(WP_REST_Request $request) {
	global $current_user;
	global $wpdb;

	$data = $request->get_params();

	$post_args = array(
		'p'         => $data['id'],
		'post_type' => 'course',
		'post_status' => 'publish',
		'posts_per_page' => 1
	);
	$post_query = new WP_Query( $post_args );
	$post = $post_query->posts;

	$student = getParentStudents($current_user->ID)[0];
	$student_id = $student['student_id'];
	$post_id = $data['id'];
	//$reports = getUserCourseReport($data['id']);

	$reports = $wpdb->get_row(
		"SELECT * 
			FROM {$wpdb->prefix}course_report as cr
			WHERE student_id = '$student_id' AND course_id='$post_id'"
	,ARRAY_A );


	//return var_dump($reports);
	//$level = getStudentLessonCompleted($data['id'],$student['class_id'],$student['student_id']);


	$video_url = get_post_meta( $post[0]->ID, 'video_url', true );

	$post[0]->post_url = get_permalink($post[0]->ID);
	$post[0]->video_url = ($video_url) ? $video_url : '' ;
	//$post[0]->report = ($reports) ? $reports : 'false' ;

	$lesson_level = get_post_meta($post[0]->ID, 'level', true);

	//return $level.'-'.$lesson_level;
				
	$completed = false;
	$post[0]->level = $lesson_level;
	$post[0]->completed = ($reports) ? true : false ;
	$post[0]->status = ($reports) ? 'available' : 'locked' ;

	/*if ($level) {
		$post[0]->completed = in_array($lesson_level, $level);
		$last_completed = max($level)+1;

		if($post->completed || $last_completed == $lesson_level ||  $lesson_level <= $last_completed ) {
			$post[0]->status = 'available';
		}
	} else {
		if ($lesson_level == 1) {
			$post[0]->status = 'available';
			$post[0]->completed = false;
		}
	}*/

	$post[0]->thumb = ($post[0]->thumb) ? get_the_post_thumbnail_url( $post[0]->ID, 'full' ) : '';

	
	if (!empty($post)) {
		$result['data'] = $post;
		return new WP_REST_Response($result, 200);
	} else {
    	return new WP_Error( 'error_fetching_data', 'Error fetching data.', array( 'status' => 403 ) );
    }
}

function api_get_course_video(WP_REST_Request $request) {
	$data = $request->get_params();

	$video_url = get_post_meta( $data['id'], 'video_url', true );

	if ($video_url) {
		$result['data'][]['video_url'] = $video_url;
		return new WP_REST_Response($result, 200);
	} else {
		return new WP_Error( 'error_fetching_data', 'Error fetching data.', array( 'status' => 403 ) );
	}

}

function api_post_course_report(WP_REST_Request $request){
	global $current_user;
    global $wpdb;

    $data = $request->get_params();
    $file = $request->get_file_params();

	if ($_FILES['attachment']['name'] == "") {
		return new WP_Error( 'empty_image', 'Student report photo required.', array( 'status' => 403 ) );
	}

	$attachment = $_FILES['attachment'];
	$wordpress_upload_dir = wp_upload_dir();
    $new_file_path = $wordpress_upload_dir['path'] . '/' . $attachment['name'];
    $new_file_mime = mime_content_type( $attachment['tmp_name'] );

    if( $attachment['size'] > wp_max_upload_size() )
		return new WP_Error( 'file_to_large', 'File to large.', array( 'status' => 403 ) );
 
	if( !in_array( $new_file_mime, get_allowed_mime_types() ) )
		return new WP_Error( 'file_not_allowed', 'File type not allowed.', array( 'status' => 403 ) );

	$table_name = $wpdb->prefix . 'course_report'; 

	//$student = getParentStudents($current_user->ID)[0];
	
	$nama_bulan = array(
		 'Januari' => '1',
		 'Februari' => '2',
		 'Maret' => '3',
		 'April' => '4',
		 'Mei' => '5',
		 'Juni' => '6',
		 'Juli' => '7',
		 'Agustus' => '8',
		 'September' => '9',
		 'Oktober' => '10',
		 'November' => '11',
		 'Desember' => '12'
	);
	
	$month = $nama_bulan[$data['month']];

	$column = $wpdb->get_row("
			SELECT * FROM {$wpdb->prefix}course_report
			 WHERE  year='".$data['year']."' 
			 AND month='".$month."' 
			 AND week='".$data['week']."' 
			 AND day='".$data['day']."' 
			 AND student_id='".$data['student_id']."' 
 	");

	//if ($column) 
		//return new WP_Error( 'already_exists', 'Laproan sudah pernah dikirim.', array( 'status' => 403 ) );

	/*$wpdb->delete( $table_name, 
		array(
	        'course_id' => $data['id'],
	        'student_id' => $student['student_id']
		)
	);

	wp_delete_attachment( $post_id, false );*/

	$insert = $wpdb->insert($table_name,
		array(
	        'course_id' => null,
	        'year' => $data['year'],
	        'month' => $month,
	        'week' => $data['week'],
	        'day' => $data['day'],
	        'student_id' => $data['student_id'],
	        'point_1' => $data['point_1'],
	        'point_2' => $data['point_2'],
	        'point_3' => $data['point_3'],
	        'point_4' => $data['point_4'],
	        'point_5' => $data['point_5'],
	        'date' => current_time('Y-m-d H:i:s')
    	)
	);

	if($insert) {
    	$course_id = $wpdb->insert_id;

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
				array('attachment' => $upload_id ), 
				array('ID' => $course_id)
			);
			require_once( ABSPATH . 'wp-admin/includes/image.php' );
    	}

    	$result['code'] = 'post_success';
		$result['message'] = 'Data Updated';

		return new WP_REST_Response($result, 200);		
    } else {
    	return new WP_Error( 'error_updating_data', 'Error updating data.', array( 'status' => 403 ) );
    }
}

function api_get_course_report(WP_REST_Request $request){
	global $current_user;
    global $wpdb;

    $data = $request->get_params();

}