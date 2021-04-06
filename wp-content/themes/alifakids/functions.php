<?php 
define('theme_version', '1.0');

function alifakids_setup()
{
	/*Theme Support*/
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );

	add_theme_support(
		'html5',
		array(
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'script',
			'style',
		)
	);
	add_theme_support(
		'post-formats',
		array(
			'video',
			'gallery'
		)
	);

	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'wp-block-styles' );

	/*Register Menu*/
	register_nav_menus(
		array(
			'primary'    => __( 'Primary Menu', 'alifakids' )
		)
	);

}
add_action( 'after_setup_theme', 'alifakids_setup' );

function alifakids_scripts()
{

	/*Fonts*/
	wp_enqueue_style( 'alifakids-fonts', 'https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700,800,900&display=swap', array(), NULL );
	wp_enqueue_style( 'alifakids-material-icons', 'https://fonts.googleapis.com/icon?family=Material+Icons', array(), NULL );
	wp_enqueue_style( 'alifakids-fa-icons', get_theme_file_uri( '/assets/plugins/font-awesome/css/all.min.css' ), array(), theme_version );
	
	/*Plugins*/
	wp_enqueue_style( 'alifakids-bs4', get_theme_file_uri( '/assets/plugins/bootstrap/css/bootstrap.min.css' ), array(), theme_version );
	wp_enqueue_style( 'alifakids-admin-select2', get_theme_file_uri( '/assets/plugins/select2/css/select2.min.css' ), array(), theme_version );
    wp_enqueue_script( 'alifakids-admin-js-select2', get_theme_file_uri( '/assets/plugins/select2/js/select2.min.js' ), array( 'jquery' ), theme_version, false );
    wp_enqueue_style( 'alifakids-admin-multiselect', get_theme_file_uri( '/assets/plugins/multiselect/css/multi-select.css' ), array(), theme_version );
    wp_enqueue_script( 'alifakids-admin-js-multiselect', get_theme_file_uri( '/assets/plugins/multiselect/js/jquery.multi-select.js' ), array( 'jquery' ), theme_version, false );
    
    wp_enqueue_script( 'alifakids-js-validate', get_theme_file_uri( '/assets/plugins/validate/jquery.validate.min.js' ), array( 'jquery' ), theme_version, false );
	//wp_enqueue_style( 'alifakids-bs-datepicker', get_theme_file_uri( '/assets/plugins/bootstrap-datepicker/bootstrap-datepicker3.min.css' ), array(), theme_version );
	
	wp_register_style( 'alifakids-admin-jquery-ui', 'https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css' );
    wp_enqueue_style( 'alifakids-admin-jquery-ui' ); 
	wp_enqueue_script( 'alifakids-admin-jquery-ui-loc', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/i18n/jquery-ui-i18n.min.js', array( 'jquery' ), theme_version, false );
	wp_enqueue_script( 'jquery-ui-core');
    wp_enqueue_script( 'jquery-ui-datepicker');

	/*Theme Styles*/
	wp_enqueue_style( 'alifakids-lime', get_theme_file_uri( '/assets/css/lime.min.css' ), array(), theme_version );
	wp_enqueue_style( 'alifakids-style', get_stylesheet_uri(), array(), theme_version );

	wp_enqueue_style( 'dashicons' );

	/*Update Jquery*/
	wp_deregister_script( 'jquery' );
    wp_register_script( 'jquery', "https://code.jquery.com/jquery-3.1.0.min.js", array(), '3.1.0' );

	/*Ajax*/
	wp_enqueue_script( 'ajax-script', get_template_directory_uri() . '/assets/js/ajax.js', array('jquery') );
	wp_localize_script( 'ajax-script', 'ajax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));  

	/*Scripts*/
	wp_enqueue_script( 'alifakids-js-popper', get_theme_file_uri( '/assets/plugins/bootstrap/popper.min.js' ), array( 'jquery' ), theme_version, true );
	wp_enqueue_script( 'alifakids-js-bs4', get_theme_file_uri( 'assets/plugins/bootstrap/js/bootstrap.min.js' ), array( 'jquery' ), theme_version, true );
	wp_enqueue_script( 'alifakids-js-slimscroll', get_theme_file_uri( '/assets/plugins/jquery-slimscroll/jquery.slimscroll.min.js' ), array( 'jquery' ), theme_version, true );
	wp_enqueue_script( 'alifakids-js-chart', get_theme_file_uri( '/assets/plugins/chartjs/chart.min.js' ), array( 'jquery' ), theme_version, true );
	wp_enqueue_script( 'alifakids-js-apexcharts', get_theme_file_uri( '/assets/plugins/apexcharts/dist/apexcharts.min.js' ), array( 'jquery' ), theme_version, true );
	wp_enqueue_script( 'alifakids-js-lime', get_theme_file_uri( '/assets/js/lime.min.js' ), array( 'jquery' ), theme_version, true );
	//wp_enqueue_script( 'alifakids-js-datepicker', get_theme_file_uri( '/assets/plugins/bootstrap-datepicker/bootstrap-datepicker.min.js' ), array( 'jquery' ), theme_version, true );
	//wp_enqueue_script( 'alifakids-js-datepicker-id', get_theme_file_uri( '/assets/plugins/bootstrap-datepicker/bootstrap-datepicker.id.js' ), array( 'jquery' ), theme_version, true );
	wp_enqueue_media();

	if(is_page_template('page-profile.php') ){
       	wp_enqueue_script( 'user-profile' );
        wp_enqueue_script( 'password-strength-meter' );
        //wp_enqueue_style('forms');
    }

    wp_localize_script( 'wp-api', 'wpApiSettings', array(
	    'root' => esc_url_raw( rest_url() ),
	    'nonce' => wp_create_nonce( 'wp_rest' )
	));
}
add_action( 'wp_enqueue_scripts', 'alifakids_scripts');


function alifakids_admin_scripts($hook){
    wp_register_style( 'alifakids-admin-css', get_theme_file_uri( '/assets/admin/style.css'), array(), theme_version );
    wp_enqueue_style( 'alifakids-admin-css' );   

    wp_register_style( 'alifakids-admin-jquery-ui', 'https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css' );
    wp_enqueue_style( 'alifakids-admin-jquery-ui' );  

    wp_enqueue_script( 'alifakids-admin-jquery-ui-loc', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/i18n/jquery-ui-i18n.min.js', array( 'jquery' ), theme_version, false );
    wp_enqueue_script( 'jquery-ui-core');
    wp_enqueue_script( 'jquery-ui-datepicker');

    
    wp_enqueue_style( 'alifakids-admin-multiselect', get_theme_file_uri( '/assets/plugins/multiselect/css/multi-select.css' ), array(), theme_version );
    wp_enqueue_script( 'alifakids-admin-js-multiselect', get_theme_file_uri( '/assets/plugins/multiselect/js/jquery.multi-select.js' ), array( 'jquery' ), theme_version, false );

    wp_localize_script( 'alifakids-admin-ajax', 'ajax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));  
    wp_enqueue_script( 'alifakids-admin-ajax' );      

     wp_enqueue_script('media-upload');
    wp_enqueue_style('thickbox');
    wp_enqueue_script('thickbox');
    wp_enqueue_script('postbox');
    wp_enqueue_script('common');
    wp_enqueue_script('wp-lists');

    wp_enqueue_style( 'alifakids-admin-select2', get_theme_file_uri( '/assets/plugins/select2/css/select2.min.css' ), array(), theme_version );
    wp_enqueue_script( 'alifakids-admin-js-select2', get_theme_file_uri( '/assets/plugins/select2/js/select2.min.js' ), array( 'jquery' ), theme_version, false );

    if( 'orang-tua_page_parents_add' == $hook || 'guru_page_teacher_add' == $hook ) {
       	wp_enqueue_script( 'user-profile' );
        wp_enqueue_script( 'password-strength-meter' );

    }


    if( 'admin_page_report_monthly_detail' == $hook ) {
       	wp_enqueue_style( 'alifakids-bs4', get_theme_file_uri( '/assets/plugins/bootstrap/css/bootstrap.min.css' ), array(), theme_version );
       wp_enqueue_style( 'alifakids-lime', get_theme_file_uri( '/assets/css/lime.min.css' ), array(), theme_version );
       wp_enqueue_script( 'alifakids-js-bs4', get_theme_file_uri( 'assets/plugins/bootstrap/js/bootstrap.min.js' ), array( 'jquery' ), theme_version, true );

    }

     wp_localize_script( 'wp-api', 'wpApiSettings', array(
	    'root' => esc_url_raw( rest_url() ),
	    'nonce' => wp_create_nonce( 'wp_rest' )
	));
}
add_action('admin_enqueue_scripts', 'alifakids_admin_scripts' );


/* Includes */
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
if ( ! class_exists( 'WP_Users_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-users-list-table.php' );
}
if ( ! class_exists( 'WP_User_Query' ) ) {
	require_once( ABSPATH . 'wp-includes/class-wp-user-query.php' );
}

//  Class
require get_template_directory() . '/core/class-loader.php';
require get_template_directory() . '/core/class-init.php';
//require get_template_directory() . '/assets/vendor/autoload.php';

/* Admin Menu */
function alifakids_admin_menu() {
	add_submenu_page(
	    null,
	    __( 'Laporan Pembelajaran', 'alifakids' ),
	    __( 'Laporan Pembelajaran', 'alifakids' ),
	    'manage_options',
	    'course-report',
	    'course_report_page_handler'
	);

	add_menu_page(
		__( 'Siswa', 'alifakids' ),
		__( 'Siswa', 'alifakids' ),
		'manage_options',
		'students',
		'students_page_handler',
		'dashicons-groups',
		26
	);

	add_submenu_page( 
		'students', 
		__( 'Siswa', 'alifakids' ),
		__( 'Semua Siswa', 'alifakids' ),
		'manage_options',
		'students', 
		'students_page_handler' 
	);

	add_submenu_page( 
		'students', 
		__( 'Tambah Siswa Baru', 'alifakids' ),
		__( 'Tambah Baru', 'alifakids' ),
		'manage_options',
		'students_add', 
		'students_new_page_handler' 
	);

	add_menu_page(
		__( 'Cabang', 'alifakids' ),
		__( 'Cabang', 'alifakids' ),
		'manage_options',
		'branch',
		'branch_page_handler',
		'dashicons-admin-multisite',
		30
	);

	add_submenu_page( 
		'branch', 
		__( 'Cabang', 'alifakids' ),
		__( 'Semua Cabang', 'alifakids' ),
		'manage_options',
		'branch', 
		'branch_page_handler' 
	);

	add_submenu_page( 
		'branch', 
		__( 'Tambah Cabang Baru', 'alifakids' ),
		__( 'Tambah Baru', 'alifakids' ),
		'manage_options',
		'branch_add', 
		'branch_new_page_handler' 
	);

	add_menu_page(
		__( 'Orang Tua', 'alifakids' ),
		__( 'Orang Tua', 'alifakids' ),
		'manage_options',
		'parents',
		'parents_page_handler',
		'dashicons-businesswoman',
		28
	);

	add_submenu_page( 
		'parents', 
		__( 'Orang Tua', 'alifakids' ),
		__( 'Semua Orang Tua', 'alifakids' ),
		'manage_options',
		'parents', 
		'parents_page_handler' 
	);

	add_submenu_page( 
		'parents', 
		__( 'Tambah Orang Tua Baru', 'alifakids' ),
		__( 'Tambah Baru', 'alifakids' ),
		'manage_options',
		'parents_add', 
		'parents_new_page_handler' 
	);

	add_menu_page(
		__( 'Guru', 'alifakids' ),
		__( 'Guru', 'alifakids' ),
		'manage_options',
		'teacher',
		'teachers_page_handler',
		'dashicons-businessman',
		27
	);

	add_submenu_page( 
		'teacher', 
		__( 'Guru', 'alifakids' ),
		__( 'Semua Guru', 'alifakids' ),
		'manage_options',
		'teacher', 
		'teachers_page_handler' 
	);

	add_submenu_page( 
		'teacher', 
		__( 'Tambah Guru Baru', 'alifakids' ),
		__( 'Tambah Baru', 'alifakids' ),
		'manage_options',
		'teacher_add', 
		'teachers_new_page_handler' 
	);

	add_menu_page(
		__( 'Semua Report', 'alifakids' ),
		__( 'Report', 'alifakids' ),
		'manage_options',
		'reports_daily',
		'report_daily_page_handler',
		'dashicons-book-alt',
		25
	);

	add_submenu_page( 
		'reports_daily', 
		__( 'Report Harian', 'alifakids' ),
		__( 'Report Harian', 'alifakids' ),
		'manage_options',
		'reports_daily', 
		'report_daily_page_handler' 
	);

	add_submenu_page( 
		null, 
		__( 'Report Harian', 'alifakids' ),
		__( 'Tambah Report Harian', 'alifakids' ),
		'manage_options',
		'report_daily_add', 
		'report_daily_new_page_handler' 
	);

	add_submenu_page( 
		'reports_daily', 
		__( 'Report Minguan', 'alifakids' ),
		__( 'Report Minguan', 'alifakids' ),
		'manage_options',
		'reports_weekly', 
		'report_weekly_page_handler' 
	);

	add_submenu_page( 
		null, 
		__( 'Report Harian', 'alifakids' ),
		__( 'Tambah Report Harian', 'alifakids' ),
		'manage_options',
		'report_weekly_add', 
		'report_weekly_new_page_handler' 
	);

	add_submenu_page( 
		'reports_daily', 
		__( 'Report Bulanan', 'alifakids' ),
		__( 'Report Bulanan', 'alifakids' ),
		'manage_options',
		'reports_monthly', 
		'reports_monthly_page_handler' 
	);

	add_submenu_page( 
		null, 
		__( 'Report Harian', 'alifakids' ),
		__( 'Detail Report Bulanan', 'alifakids' ),
		'manage_options',
		'report_monthly_detail', 
		'report_monthly_detail_page_handler' 
	);

	add_menu_page(
		__( 'Semua Pembayaran', 'alifakids' ),
		__( 'Pembayaran', 'alifakids' ),
		'manage_options',
		'payment',
		'payment_page_handler',
		'dashicons-book-alt',
		30
	);

	add_submenu_page( 
		null, 
		__( 'Detail Pembayaran', 'alifakids' ),
		__( 'Detail Pembayaran', 'alifakids' ),
		'manage_options',
		'payment_detail', 
		'payment_detail_page_handler' 
	);

}
add_action( 'admin_menu', 'alifakids_admin_menu' );

function hide_admin_bar(){ return false; }
add_filter( 'show_admin_bar', 'hide_admin_bar' );


function my_avatar_filter() {
  // Remove from show_user_profile hook
  remove_action('show_user_profile', array('wp_user_avatar', 'wpua_action_show_user_profile'));
  remove_action('show_user_profile', array('wp_user_avatar', 'wpua_media_upload_scripts'));

  // Remove from edit_user_profile hook
  remove_action('edit_user_profile', array('wp_user_avatar', 'wpua_action_show_user_profile'));
  remove_action('edit_user_profile', array('wp_user_avatar', 'wpua_media_upload_scripts'));

  // Add to edit_user_avatar hook
  add_action('edit_user_avatar', array('wp_user_avatar', 'wpua_action_show_user_profile'));
  add_action('edit_user_avatar', array('wp_user_avatar', 'wpua_media_upload_scripts'));
}

// Loads only outside of administration panel
if(!is_admin()) {
  add_action('init','my_avatar_filter');
}

remove_action('wpua_before_avatar', 'wpua_do_before_avatar');
remove_action('wpua_after_avatar', 'wpua_do_after_avatar');

add_filter( 'wp_nav_menu_items', 'ak_loginout_menu_link', 10, 2 );
function ak_loginout_menu_link( $items, $args ) {

   if ($args->menu_id  == 'primary') {
      if (is_user_logged_in()) {
         $items .= '<li><a href="'. wp_logout_url() .'"><i class="material-icons">exit_to_app</i>'. __("Log Out") .'</a></li>';
	   }
	}
   return $items;
}

/*function wpabsolute_block_users_backend() {
	if ( is_admin() && ! current_user_can( 'administrator' ) && ! wp_doing_ajax() ) {
		wp_redirect( home_url() );
		exit;
	}
}
add_action( 'init', 'wpabsolute_block_users_backend' );*/

remove_filter( 'rest_authentication_errors', 'v_forcelogin_rest_access', 99 );

 function mod_jwt_auth_token_before_dispatch($data, $user) {
 	
 	if (in_array('parent', $user->roles,true)) {
	    do_action('wp_login', $user->data->ID, $user);
	    $data['user_profile'] = get_avatar_url($user->data->ID,'150');
	    return $data;
 	} else {
		return new WP_Error( 'jwt_auth_failed', 'Invalid Credentials.', array( 'status' => 403 ) );
 	}

 }
 add_filter('jwt_auth_token_before_dispatch','mod_jwt_auth_token_before_dispatch',10,2);

 function test_jwt_auth_expire() {
  return time() + (DAY_IN_SECONDS * 180);
  }
add_action('jwt_auth_expire', 'test_jwt_auth_expire');
