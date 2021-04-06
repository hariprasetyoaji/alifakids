<?php
add_action( 'rest_api_init', function () {

	/*User API*/
	register_rest_route( 'alifakids/v1', '/user/me', array(
		'methods' => 'GET',
		'callback' => 'api_get_user',
	));

	register_rest_route( 'alifakids/v1', '/user/update', array(
		'methods' => 'POST',
		'callback' => 'api_update_user',
	));

	register_rest_route( 'alifakids/v1', '/user/avatar', array(
		'methods' => 'POST',
		'callback' => 'api_update_user_avatar',
	));


	register_rest_route( 'alifakids/v1', '/user/password', array(
		'methods' => 'POST',
		'callback' => 'api_password_user',
	));

	register_rest_route( 'alifakids/v1', '/user/student', array(
		'methods' => 'GET',
		'callback' => 'api_student_user',
	));

	/*Learning API*/
	register_rest_route( 'alifakids/v1', '/learning', array(
			'methods' => 'GET',
			'callback' => 'api_learning_parenting',
	));


	register_rest_route( 'alifakids/v1', '/learning/(?P<id>\d+)', array(
		'methods' => 'GET',
		'callback' => 'api_learning_parenting',
		'args' => [
	        'id'
	    ],
	));

	register_rest_route( 'alifakids/v1', '/learning(?:/(?P<limit>\d+))', array(
		'methods' => 'POST',
		'callback' => 'api_learning_parenting',
		'args' => [
	        'limit'
	    ],
	));

	/*Course API*/
	register_rest_route( 'alifakids/v1', '/course', array(
			'methods' => 'GET',
			'callback' => 'api_get_course',
	));


	register_rest_route( 'alifakids/v1', '/course/(?P<id>\d+)/(?P<student>\d+)', array(
			'methods' => 'GET',
			'callback' => 'api_get_course',
			'args' => [
		        'id',
		        'student'
		    ]
	));

	register_rest_route( 'alifakids/v1', '/course/detail/(?P<id>\d+)', array(
			'methods' => 'GET',
			'callback' => 'api_get_course_detail',
			'args' => [
		        'id'
		    ]
	));


	register_rest_route( 'alifakids/v1', '/course/video/(?P<id>\d+)', array(
			'methods' => 'GET',
			'callback' => 'api_get_course_video',
			'args' => [
		        'id'
		    ]
	));

	register_rest_route( 'alifakids/v1', '/course/report/', array(
		'methods' => 'POST',
		'callback' => 'api_post_course_report',
	));

	register_rest_route( 'alifakids/v1', '/course/report/', array(
		'methods' => 'GET',
		'callback' => 'api_get_course_report',
	));

	/*Report API*/
	register_rest_route( 'alifakids/v1', '/report/Harian', array(
		'methods' => 'GET',
		'callback' => 'api_get_report_daily',
	));

	register_rest_route( 'alifakids/v1', '/report/Harian/detail/(?P<id>\d+)', array(
		'methods' => 'GET',
		'callback' => 'api_get_report_daily_detail',
		'args' => [
	        'id'
	    ]
	));

	register_rest_route( 'alifakids/v1', '/report/Harian/confirm', array(
		'methods' => 'POST',
		'callback' => 'api_get_report_daily_confirm',
	));

	register_rest_route( 'alifakids/v1', '/report/Mingguan', array(
		'methods' => 'GET',
		'callback' => 'api_get_report_weekly',
	));

	register_rest_route( 'alifakids/v1', '/report/Mingguan/detail/(?P<id>\d+)', array(
		'methods' => 'GET',
		'callback' => 'api_get_report_weekly_detail',
		'args' => [
	        'id'
	    ]
	));

	register_rest_route( 'alifakids/v1', '/report/Mingguan/confirm', array(
		'methods' => 'POST',
		'callback' => 'api_get_report_weekly_confirm',
	));

	register_rest_route( 'alifakids/v1', '/report/Bulanan', array(
		'methods' => 'GET',
		'callback' => 'api_get_report_monthly',
	));

	register_rest_route( 'alifakids/v1', '/report/Bulanan/detail', array(
		'methods' => 'GET',
		'callback' => 'api_get_report_monthly_detail',
		'args' => [
	        'date'
	    ]
	));

	/*API Payment*/
	register_rest_route( 'alifakids/v1', '/payment/tagihan/(?P<id>\d+)', array(
		'methods' => 'GET',
		'callback' => 'api_get_payment_tagihan',
		'args' => [
	        'id'
	    ]
	));

	register_rest_route( 'alifakids/v1', '/payment/history/(?P<id>\d+)', array(
		'methods' => 'GET',
		'callback' => 'api_get_payment_history',
		'args' => [
	        'id'
	    ]
	));

	register_rest_route( 'alifakids/v1', '/payment/history/detail/(?P<id>\d+)', array(
		'methods' => 'GET',
		'callback' => 'api_get_payment_history_detail',
	));

	register_rest_route( 'alifakids/v1', '/payment/tagihan/', array(
		'methods' => 'POST',
		'callback' => 'api_post_payment_tagihan',
	));

	register_rest_route( 'alifakids/v1', '/slider', array(
		'methods' => 'GET',
		'callback' => 'api_get_slider',
	));
});

function api_get_slider($data) {
	global $wpdb;

	$post_id = 338;
    $wp1s_option = get_post_meta($post_id,'wp1s_option',true);

    $item = [];

    if(!empty($wp1s_option['slides'])){
        foreach ($wp1s_option['slides'] as $slide) {
        	$image_url= esc_attr($slide['slide_image_url']);
        	
			array_push($item, array( 
				'title' =>  $slide['slide_title'], 
				'image_url' =>  $image_url
			));
        }
	 }

	$result['data'] = $item;
	return new WP_REST_Response($result, 200);
}