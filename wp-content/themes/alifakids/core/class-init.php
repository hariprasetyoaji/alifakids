<?php 

function redirect($url = false) {
	if(headers_sent()) {
		$destination = ($url == false ? 'location.reload();' : 'window.location.href="' . $url . '";');
		echo die('<script>' . $destination . '</script>');
	} else {
		$destination = ($url == false ? $_SERVER['REQUEST_URI'] : $url);
		header('Location: ' . $destination);
		die();
	}    
}

add_action('init', 'ak_start_session');
function ak_start_session() {
    session_start();

    global $flash;
    $flash = new Flash_Messages();

}

function ak_admin_notices(){
    if ( isset($_GET['notice']) && $_GET['notice'] == 'success' ) {
    	echo sprintf('<div class="notice notice-success is-dismissible">
             <p>%s</p>
         </div>','Data berhasil disimpan.');
    } else if ( isset($_GET['notice']) && $_GET['notice'] == 'error' ) {
    	echo sprintf('<div class="notice notice-warning is-dismissible">
             <p>%s</p>
         </div>','Error saat menyimpan data.');
    } else if ( isset($_GET['notice']) && $_GET['notice'] == 'number_exists' ) {
    	echo sprintf('<div class="notice notice-warning is-dismissible">
             <p>%s</p>
         </div>','Nomor induk siswa sudah terdaftar.');
    }  else if ( isset($_GET['notice']) && $_GET['notice'] == 'delete_success' ) {
    	echo sprintf('<div class="notice notice-success is-dismissible">
             <p>%s</p>
         </div>','Data berhasil dihapus.');
    } 


    if (isset($_SESSION['notice'] )) {
    	echo sprintf('<div class="notice notice-warning is-dismissible">
             <p>%s</p>
         </div>',$_SESSION['notice']);
    	unset($_SESSION['notice']);
    }
}
add_action( 'admin_notices', 'ak_admin_notices' );

//Student admin form
function ak_form_student_function() {
	if ( isset($_REQUEST['nonce']) 
    	&& wp_verify_nonce($_REQUEST['nonce'], 'ak_form_student')
    ) {
		global $wpdb;

		$table_name = $wpdb->prefix . 'students'; 

    	$default = array(
	        'student_id' => 0,
	        'name'      => null,
	        'number'  => null,
	        'year'  => null,
	        'branch_id'  => null,
	        'class_id'  => null,
	        'gender'  => null,
	        'child_number'  => null,
	        'father_name'  => '',
        	'mother_name'  => '',
	        'birth_place'  => null,
	        'birth_date'  => null,
	        'blood_type'  => null,
	        'address'  => null,
	        'religion'  => null,
	        'hobby'  => null
    	);

        $item = shortcode_atts($default, $_REQUEST);     

        if ($item['student_id'] == 0) {
    		if ( checkStudentIDExists($item['student_id'], $item['number']) == false ) {
	            $result = $wpdb->insert( $table_name, $item );

	            $student_id = $wpdb->insert_id;
	            
	            //createStudentDailyReport($student_id);

	            if ($result) {
	                wp_redirect(admin_url('admin.php?page=students&notice=success'));
	            } else {
	                wp_redirect( admin_url('admin.php?page=students_add&notice=error') );
	            }
            } else {
		    	wp_redirect( admin_url('admin.php?page=students_add&notice=number_exists') );
		    }
        } else {

        	if ( checkStudentIDExists($item['student_id'], $item['number']) == false ) {

	            $result = $wpdb->update ( 
					$table_name, 
					$item, 
					array('student_id' => $item['student_id'])
				);

	            if ($result) {
					wp_redirect(admin_url('admin.php?page=students&notice=success'));
	            } else {
					wp_redirect( admin_url('admin.php?page=students_add&student_id='.$item['student_id'].'&notice=error') );
	            }
		    } else {
		    	wp_redirect( admin_url('admin.php?page=students_add&student_id='.$item['student_id'].'&notice=number_exists') );
		    }
        }

    }

}
add_action( 'admin_post_ak_form_student', 'ak_form_student_function');

//Parent admin form
function ak_form_parent_function() {
	if ( isset($_REQUEST['nonce']) 
    	&& wp_verify_nonce($_REQUEST['nonce'], 'ak_form_parent')
    ) {
    	global $wpdb;

    	if ($_REQUEST['user_id'] == 0) {
	    	if ( username_exists($_REQUEST['user_login']) ) {
	    		$_SESSION['notice'] = 'Username sudah digunakan.';
				wp_redirect(admin_url('admin.php?page=parents_add'));
	    		die();
	    	} 
			if ( email_exists($_REQUEST['user_email']) ) {
	    		$_SESSION['notice'] = 'Email sudah digunakan.';
				wp_redirect(admin_url('admin.php?page=parents_add'));
	    		die();
	    	} 

	    	$user_array = array (
		        'user_login'    =>  $_REQUEST['user_login'],
		        'user_email'    =>  $_REQUEST['user_email'],
		        'user_pass'     =>  $_REQUEST['pass1'],
		        'first_name'    =>  $_REQUEST['first_name'],
		        'last_name'     =>  $_REQUEST['last_name']
		    ) ;

		    $id = wp_insert_user( $user_array );

		    if($id){
		    	wp_update_user( array ('ID' => $id, 'role' => 'parent') );
		    	
		    	update_user_meta( $id, 'birth_place', $_REQUEST['birth_place']);
		    	update_user_meta( $id, 'birth_date', $_REQUEST['birth_date']);
		    	update_user_meta( $id, 'address', $_REQUEST['address']);
		    	update_user_meta( $id, 'religion', $_REQUEST['religion']);
		    	update_user_meta( $id, 'education', $_REQUEST['education']);
		    	update_user_meta( $id, 'occupation', $_REQUEST['occupation']);
		    	update_user_meta( $id, 'office_address', $_REQUEST['office_address']);
		    	update_user_meta( $id, 'social_media', $_REQUEST['social_media']);
		    	update_user_meta( $id, 'phone', $_REQUEST['phone']);

		    	if (!empty($_REQUEST['students'])) {
			    	foreach ($_REQUEST['students'] as $value) {
			    		$wpdb->insert( "{$wpdb->prefix}parents_students", 
			    			array( 
			    				'parent_id' => $id, 
			    				'student_id' => $value, 
			    			) 
			    		);
			    	}
		    	}

				wp_redirect(admin_url('admin.php?page=parents&notice=success'));
		    } else {
				wp_redirect( admin_url('admin.php?page=parents_add&notice=error') );
            }

        } else {
        	$id = $_REQUEST['user_id'];

        	$user_info = get_userdata($id);
  			$user_email = $user_info->user_email;
        	if ( email_exists($_REQUEST['user_email']) && $user_email != $_REQUEST['user_email']) {
	    		$_SESSION['notice'] = 'Email sudah digunakan.';
				wp_redirect(admin_url('admin.php?page=teacher_add&id='.$id));
	    		die();
	    	} 


        	$user_array = array (
		        'ID' 			=> $_REQUEST['user_id'],
		        'user_email'    =>  $_REQUEST['user_email'],
		        'first_name'    =>  $_REQUEST['first_name'],
		        'last_name'     =>  $_REQUEST['last_name']
		    );
		    $update = wp_update_user( $user_array );

		    if (!empty($_REQUEST['pass1'])) {
		    	wp_set_password( $_REQUEST['pass1'], $id );
		    }

		    update_user_meta( $id, 'birth_place', $_REQUEST['birth_place']);
	    	update_user_meta( $id, 'birth_date', $_REQUEST['birth_date']);
	    	update_user_meta( $id, 'address', $_REQUEST['address']);
	    	update_user_meta( $id, 'religion', $_REQUEST['religion']);
	    	update_user_meta( $id, 'education', $_REQUEST['education']);
	    	update_user_meta( $id, 'occupation', $_REQUEST['occupation']);
	    	update_user_meta( $id, 'office_address', $_REQUEST['office_address']);
	    	update_user_meta( $id, 'social_media', $_REQUEST['social_media']);
	    	update_user_meta( $id, 'phone', $_REQUEST['phone']);

	    	if (!empty($_REQUEST['students'])) {
	    		$wpdb->query("DELETE FROM {$wpdb->prefix}parents_students WHERE parent_id = '".$id."'");
	    		
	    		foreach ($_REQUEST['students'] as $value) {
		    		$wpdb->insert( "{$wpdb->prefix}parents_students", 
		    			array( 
		    				'parent_id' => $id, 
		    				'student_id' => $value, 
		    			) 
		    		);
		    	}
    		}

		    if($update){ 
	    		wp_redirect( admin_url('admin.php?page=parents_add&notice=success&id='.$id) );
		    } else {
	    		wp_redirect( admin_url('admin.php?page=parents_add&notice=error&id='.$id) );
		    }

        }
    }

}
add_action( 'admin_post_ak_form_parent', 'ak_form_parent_function');

//Parent admin form
function ak_form_teacher_function() {
	if ( isset($_REQUEST['nonce']) 
    	&& wp_verify_nonce($_REQUEST['nonce'], 'ak_form_teacher')
    ) {
    	global $wpdb;

    	if ($_REQUEST['user_id'] == 0) {
	    	if ( username_exists($_REQUEST['user_login']) ) {
	    		$_SESSION['notice'] = 'Username sudah digunakan.';
				wp_redirect(admin_url('admin.php?page=teacher_add'));
	    		die();
	    	} 
			if ( email_exists($_REQUEST['user_email']) ) {
	    		$_SESSION['notice'] = 'Email sudah digunakan.';
				wp_redirect(admin_url('admin.php?page=teacher_add'));
	    		die();
	    	} 

	    	$user_array = array (
		        'user_login'    =>  $_REQUEST['user_login'],
		        'user_email'    =>  $_REQUEST['user_email'],
		        'user_pass'     =>  $_REQUEST['pass1'],
		        'first_name'    =>  $_REQUEST['first_name'],
		        'last_name'     =>  $_REQUEST['last_name']
		    ) ;

		    $id = wp_insert_user( $user_array );

		    if($id){
		    	wp_update_user( array ('ID' => $id, 'role' => 'teacher') );

    			$user = new WP_User( $id );
		    	if ($_REQUEST['branch_coordinator'] == 1) {
    				$user->add_role( 'editor' );
		    	}
		    	$user->remove_role( 'subscriber' );
		    	
		    	update_user_meta( $id, 'branch', $_REQUEST['branch']);
		    	update_user_meta( $id, 'class', $_REQUEST['class']);
		    	update_user_meta( $id, 'birth_place', $_REQUEST['birth_place']);
		    	update_user_meta( $id, 'birth_date', $_REQUEST['birth_date']);
		    	update_user_meta( $id, 'address', $_REQUEST['address']);
		    	update_user_meta( $id, 'religion', $_REQUEST['religion']);
		    	update_user_meta( $id, 'education', $_REQUEST['education']);
		    	update_user_meta( $id, 'social_media', $_REQUEST['social_media']);
		    	update_user_meta( $id, 'phone', $_REQUEST['phone']);

				wp_redirect(admin_url('admin.php?page=teacher&notice=success'));
		    } else {
				wp_redirect( admin_url('admin.php?page=teacher_add&notice=error') );
            }

        } else {

        	$id = $_REQUEST['user_id'];

        	$user_info = get_userdata($id);
  			$user_email = $user_info->user_email;
        	if ( email_exists($_REQUEST['user_email']) && $user_email != $_REQUEST['user_email']) {
	    		$_SESSION['notice'] = 'Email sudah digunakan.';
				wp_redirect(admin_url('admin.php?page=teacher_add&id='.$id));
	    		die();
	    	} 

        	$user_array = array (
		        'ID' 			=> $_REQUEST['user_id'],
		        'user_email'    =>  $_REQUEST['user_email'],
		        'first_name'    =>  $_REQUEST['first_name'],
		        'last_name'     =>  $_REQUEST['last_name']
		    );
		    $update = wp_update_user( $user_array );

		    if (!empty($_REQUEST['pass1'])) {
		    	wp_set_password( $_REQUEST['pass1'], $id );
		    }

			$user = new WP_User( $id );
		    if (isset($_REQUEST['branch_coordinator'])) {
				$user->add_role( 'editor' );
	    	} else {
				$user->remove_role( 'editor' );
	    	}


		    update_user_meta( $id, 'branch', $_REQUEST['branch']);
		    update_user_meta( $id, 'class', $_REQUEST['class']);
		    update_user_meta( $id, 'birth_place', $_REQUEST['birth_place']);
	    	update_user_meta( $id, 'birth_date', $_REQUEST['birth_date']);
	    	update_user_meta( $id, 'address', $_REQUEST['address']);
	    	update_user_meta( $id, 'religion', $_REQUEST['religion']);
	    	update_user_meta( $id, 'education', $_REQUEST['education']);
	    	update_user_meta( $id, 'social_media', $_REQUEST['social_media']);
	    	update_user_meta( $id, 'phone', $_REQUEST['phone']);


		    if($update){ 
	    		wp_redirect( admin_url('admin.php?page=teacher_add&notice=success&id='.$id) );
		    } else {
	    		wp_redirect( admin_url('admin.php?page=teacher_add&notice=error&id='.$id) );
		    }

        }
    }

}
add_action( 'admin_post_ak_form_teacher', 'ak_form_teacher_function');

class Flash
{

    const FLASH_KEYS = 'flash_message_stored_keys';
    const SKIP_FLAG = 'skip_flash_clean_up';
    private $xRedirectBy = 'flash';
    private $statusCode = 302;
    private $redirectUrl;

    public static function init()
    {
        if (!defined('FLASH_INIT')) {
            // ensure session is started
            if (session_status() !== PHP_SESSION_ACTIVE) {
                session_start();
            }
            // clean up flash messages from session on script finishing point
            register_shutdown_function([Flash::class, 'cleanUpFlashMessages']);
            // define flash is initialized
            define('FLASH_INIT', true);
        }
    }

    /**
     * Its a callback for (@register_shutdown_function) which registered in constructor
     */
    public static function cleanUpFlashMessages()
    {
        if (!defined(self::SKIP_FLAG)) {

            if (isset($_SESSION[self::FLASH_KEYS])) {

                //clean flash messages by using stored keys
                foreach ($_SESSION[self::FLASH_KEYS] as $message_key) {
                    unset($_SESSION[$message_key]);
                }

                //then clean stored keys itself
                unset($_SESSION[self::FLASH_KEYS]);
            }
        }
    }

    /**
     * @param string $key flash message key in session storage
     * @param string $message message value
     *
     * @return Flash
     */
    public function message($key, $message)
    {
        $_SESSION[self::FLASH_KEYS][] = $key;
        $_SESSION[$key] = $message;

        //skip cleaning once for redirection
        if (!defined(self::SKIP_FLAG)) {
            define(self::SKIP_FLAG, true);
        }

        return $this;
    }

    /**
     * @param $url
     *
     * @return Flash
     */
    public function redirectLocation($url)
    {
        $this->redirectUrl = $url;
        return $this;
    }

    /**
     * @param $status
     *
     * @return Flash
     */
    public function withStatus($status = 302)
    {
        $this->statusCode = $status;
        return $this;
    }

    /**
     * @param $xRedirectBy
     *
     * @return Flash
     */
    public function redirectBy($xRedirectBy = 'flash')
    {
        $this->xRedirectBy = $xRedirectBy;
        return $this;
    }

    public function redirect()
    {
        if (!isset($this->redirectUrl)) {
            $this->redirectBack();
            return;
        }

        @header("X-Redirect-By: $this->xRedirectBy", true, $this->statusCode);
        @header("Location: $this->redirectUrl", true, $this->statusCode);
        exit();
    }

    public function redirectBack()
    {
        $this->redirectUrl = $_SERVER['HTTP_REFERER'];
        $this->redirect();
    }

}

 function export_excel_course_report_cb() {
	global $wpdb;

	//$file = 'course_report';
	//$title = sanitize_title(get_the_title($_REQUEST['id']) );
	$results = $wpdb->get_results("
			SELECT s.name,
					cr.point_1,
					cr.point_2,
					cr.point_3,
					cr.point_4,
					cr.point_5,
					cr.attachment,
					cr.date
			FROM {$wpdb->prefix}course_report cr 
				LEFT JOIN {$wpdb->prefix}students s
					ON cr.student_id = s.student_id
			WHERE cr.year = '".$_REQUEST['year']."'
				AND cr.month = '".$_REQUEST['month']."'
				AND cr.week = '".$_REQUEST['week']."'
				AND cr.day = '".$_REQUEST['day']."'
				AND s.class_id = '".$_REQUEST['class']."'
		",
	ARRAY_A );


	$csv_output = "Nama, 1. Pada bagian sesi mana Ananda menunjukkan antusias?, 2. Apa hal yang sudah berjalan baik pada sesi kali ini?, 3. Apa aksi perbaikan untuk membersamai Ananda di sesi berikutnya?, 4. Bagian mana yang sangat dikuasai Ananda?, 5. Bagian mana yang menantang atau silit bagi Ananda?, Foto, Waktu "."\n";

	if(count($results) > 0){
		foreach($results as $result){

			$result['name'] = trim($result['name']);
			$result['point_1'] = trim($result['point_1']);
			$result['point_2'] = trim($result['point_2']);
			$result['point_3'] = trim($result['point_3']);
			$result['point_4'] = trim($result['point_4']);
			$result['point_5'] = trim($result['point_5']);
			$result['attachment'] = wp_get_attachment_url( $result['attachment'] );
			$result['date'] = trim($result['date']);

			$result = array_values($result);
			$result = implode(", ", $result);
			$csv_output .= $result."\n";
		}
	}

	$filename = "Laporan-".getClassName($_REQUEST['class'])."-".$_REQUEST['year']."-".$_REQUEST['month']."-".$_REQUEST['week']."-".$_REQUEST['day']."_".date("Y-m-d_H-i",time());
	header("Content-type: application/vnd.ms-excel");
	header("Content-disposition: csv" . date("Y-m-d") . ".csv");
	header( "Content-disposition: filename=".$filename.".csv");
	print $csv_output;
	exit;

}
add_action('wp_ajax_export_excel_course_report','export_excel_course_report_cb');

function export_excel_daily_report_cb() {
	global $wpdb;

 	if ( empty( $_REQUEST['class'] ) ||
 			empty( $_REQUEST['branch'] ) ||
 			empty( $_REQUEST['date'] )
 		){
        
        $_SESSION['notice'] = 'Filter cabang dan kelas harus diisi.';
		wp_redirect(admin_url('admin.php?page=reports_daily'));
		exit();
    } 

    $date_join = "AND r.date = CURRENT_DATE";
	$date_search_key = !empty( $_REQUEST['date'] ) ? wp_unslash( trim( $_REQUEST['date'] ) ) : '';
	if (! empty( $date_search_key )) {
		$date_join = "AND r.date = '$date_search_key'";
	} 

    $sql = "SELECT s.student_id as student_id,
						s.name as name,
						s.number as number,
						s.branch_id as branch_id,
						b.name as branch_name,
						c.name as class_name,
						s.class_id  as class_id,
						r.status as status_index,
						MAX(CASE WHEN r.report_id IS NOT null THEN r.report_id ELSE NULL END) AS report_id,
						MAX(CASE WHEN r.status IS NOT null THEN r.status ELSE NULL END) AS status,
						MAX(CASE WHEN r.date IS NOT null THEN r.date ELSE NULL END) AS date 

				FROM {$wpdb->prefix}students s 
					LEFT OUTER JOIN {$wpdb->prefix}reports_daily r 
						ON s.student_id = r.student_id {$date_join}
					LEFT OUTER JOIN {$wpdb->prefix}reports_daily_score rs 
						ON r.report_id = rs.report_id 
					LEFT OUTER JOIN {$wpdb->prefix}parents_students ps 
						ON s.student_id = ps.student_id
					LEFT OUTER JOIN {$wpdb->prefix}branch b 
						ON b.branch_id = s.branch_id
					LEFT OUTER JOIN {$wpdb->prefix}class c 
						ON c.class_id = s.class_id
				WHERE 	s.class_id = '".$_REQUEST['class']."' AND
						s.branch_id = '".$_REQUEST['branch']."'
				GROUP BY s.student_id ORDER BY s.name
	";

	$reports_temp = $wpdb->get_results($sql, ARRAY_A);

	foreach ($reports_temp as $report) {
		if ($report['report_id']) {
			$report['score'] = getDailyReportScoreByID($report['report_id']);
		} else {
			$report['score'] = null;
		}

		$reports[] = $report;
	}

	// $points_result = $wpdb->get_results( "SELECT *
	// 			FROM {$wpdb->prefix}reports_daily_points", 
	// 	ARRAY_A 
	// );

	// $points = [];
	// foreach ($points_result as $point) {
	// 	$points[$point['points_dimension']][] = $point['name'];
	// }

	// echo "<pre>",print_r($points,1),"</pre>";
	// exit();


	$result = [];
	$csv_output = '';

	$title = $_REQUEST['date'];
	$filename = "Report-harian_".$title;

	header('Content-Type: text/csv; charset=utf-8'); 
	header('Content-Disposition: attachment; filename="'.$filename.'.csv";');

	$f = fopen('php://output', 'w'); 

	fputcsv( $f, array(
			'Nama Siswa', 
			'Kelas',
			'Cabang',
			'Status',
			'Amanah',
			'',
			'',
			'',
			'',
			'',
			'Loyal',
			'',
			'',
			'',
			'',
			'',
			'Inisiatif',
			'',
			'',
			'',
			'',
			'',
			'Fathonah',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'Adil',
			'',
			'',
			'',
			'',
			'',
			''
		)
	);

	fputcsv( $f, array(
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
		)
	);

	if(count($reports) > 0){
		foreach($reports as $report){

			switch ($report['status']) {
				case '1':
					$report['status'] = 'Telah Ditulis';
					break;
				case '2':
					$report['status'] = 'Telah Diterima Ortu';
					break;
				default:
					$report['status'] = 'Belum Ditulis';
					break;
			}

			$fout =  array(
				trim($report['name']), 
				trim($report['class_name']),
				trim($report['branch_name']),
				trim($report['status'])
			);

			if (!empty($report['score'])) {
				foreach ($report['score'] as $dimension_key => $scores) {
					foreach ($scores as $score) {
						array_push($fout, strip_tags($score));
					}

				}
			} 
			fputcsv( $f, $fout);
				
				

			// if (!empty($report['score'])) {
			// 	$dimension_i = 1;
			// 	foreach ($report['score'] as $dimension_key => $scores) {
			// 		$score_i = 1;
			// 		foreach ($scores as $score) {
			// 			if($score_i == 1 && $dimension_i == 1) {
			// 				fputcsv( $f, array(
			// 						trim($report['name']), 
			// 						trim($report['class_name']),
			// 						trim($report['branch_name']),
			// 						trim($report['status']),
			// 						$dimension_key,
			// 						strip_tags($score)
			// 					)
			// 				);
			// 			} elseif($score_i == 1) {
			// 				fputcsv( $f, array(
			// 						'', 
			// 						'',
			// 						'',
			// 						'',
			// 						$dimension_key,
			// 						strip_tags($score)
			// 					)
			// 				);
			// 			} else {
			// 				fputcsv( $f, array(
			// 						'', 
			// 						'',
			// 						'',
			// 						'',
			// 						'',
			// 						strip_tags($score)
			// 					)
			// 				);
			// 			}



			// 			$score_i++;
			// 		}

			// 		$dimension_i++;
			// 	}
			// } else {
			// 	fputcsv( $f, array(
			// 			trim($report['name']), 
			// 			trim($report['class_name']),
			// 			trim($report['branch_name']),
			// 			trim($report['status']),
			// 			'',
			// 			''
			// 		)
			// 	);
			// }

		}
	} 

 	fclose($f);
	exit;

}
add_action('wp_ajax_export_excel_daily_report','export_excel_daily_report_cb');

function export_excel_weekly_report_cb() {
	global $wpdb;

 	if ( empty( $_REQUEST['class'] ) ||
 			empty( $_REQUEST['branch'] ) ||
 			empty( $_REQUEST['date'] )
 		){
        
        $_SESSION['notice'] = 'Filter cabang dan kelas harus diisi.';
		wp_redirect(admin_url('admin.php?page=reports_weekly'));
		exit();
    } 

    $date_join = "AND r.date = ( CURRENT_DATE - INTERVAL((WEEKDAY( CURRENT_DATE )) ) DAY)";

	$date_search_key = !empty( $_REQUEST['date'] ) ? wp_unslash( trim( $_REQUEST['date'] ) ) : '';

	if (! empty( $date_search_key )) {
		$date_join = "AND r.date = ( '$date_search_key' - INTERVAL((WEEKDAY( '$date_search_key' )) ) DAY)";
	} 

    $sql = "SELECT s.student_id as student_id,
						s.name as name,
						s.number as number,
						s.branch_id as branch_id,
						b.name as branch_name,
						c.name as class_name,
						s.class_id  as class_id,
						r.status as status_index,
						MAX(CASE WHEN r.report_id IS NOT null THEN r.report_id ELSE NULL END) AS report_id,
						MAX(CASE WHEN r.status IS NOT null THEN r.status ELSE NULL END) AS status,
						MAX(CASE WHEN r.date IS NOT null THEN r.date ELSE NULL END) AS date 

				FROM {$wpdb->prefix}students s 
					LEFT OUTER JOIN {$wpdb->prefix}reports_weekly r 
						ON s.student_id = r.student_id {$date_join}
					LEFT OUTER JOIN {$wpdb->prefix}reports_weekly_score rs 
						ON r.report_id = rs.report_id 
					LEFT OUTER JOIN {$wpdb->prefix}parents_students ps 
						ON s.student_id = ps.student_id
					LEFT OUTER JOIN {$wpdb->prefix}branch b 
						ON b.branch_id = s.branch_id
					LEFT OUTER JOIN {$wpdb->prefix}class c 
						ON c.class_id = s.class_id
				WHERE 	s.class_id = '".$_REQUEST['class']."' AND
						s.branch_id = '".$_REQUEST['branch']."'
				GROUP BY s.student_id ORDER BY s.name
	";

	$reports_temp = $wpdb->get_results($sql, ARRAY_A);

	foreach ($reports_temp as $report) {
		if ($report['report_id']) {
			$report_score = $wpdb->get_results("SELECT rdp.points_id as points_id,
					rds.score_value as score_value,
					rdp.name as score_name
				FROM {$wpdb->prefix}reports_weekly_points rdp
					LEFT JOIN {$wpdb->prefix}reports_weekly_score rds
						ON rdp.points_id = rds.points_id
					WHERE rds.report_id = '".$report['report_id']."'", 'ARRAY_A' );

			$score_result = [];
			foreach ($report_score as $value) {
				$score_result[$value['points_id']] = $value['score_value'];
			}


			$report['score'] = $score_result;
		} else {
			$report['score'] = null;
		}

		$reports[] = $report;
	}

	// echo "<pre>",print_r($reports,1),"</pre>";
	// exit();


	$result = [];
	$csv_output = '';

	$title = $_REQUEST['date'];
	$filename = "Report-mingguan".$title;

	header('Content-Type: text/csv; charset=utf-8'); 
	header('Content-Disposition: attachment; filename="'.$filename.'.csv";');

	$f = fopen('php://output', 'w'); 

	// fputcsv( $f, array(
	// 		'Nama Siswa', 
	// 		'Kelas',
	// 		'Cabang',
	// 		'Status',
	// 		'Penilaian',
	// 		'Nilai'
	// 	)
	// );

	fputcsv( $f, array(
			'Nama Siswa', 
			'Kelas',
			'Cabang',
			'Status',
			'Agama dan Budi Pekerti',
			'Sastra dan Puisi',
			'Seni Rupa',
			'Logika Matematika',
			'Sejarah dan Geografi ',
			'Hasta Karya',
			'Persiapan Membaca',
			'Sains dan Lingkungan Hidup',
			'Pendidikan Jasmani',
			'Musik Klasik',
			'Keterampilan Hidup',
			'Persiapan Menulis',
			'Apa yang Sangat menonjol?',
			'Apa yang perlu dibantu',
		)
	);

	if(count($reports) > 0){
		foreach($reports as $report){

			switch ($report['status']) {
				case '1':
					$report['status'] = 'Telah Ditulis';
					break;
				case '2':
					$report['status'] = 'Telah Diterima Ortu';
					break;
				default:
					$report['status'] = 'Belum Ditulis';
					break;
			}

			if (!empty($report['score'])) {
				fputcsv( $f, array(
						trim($report['name']), 
						trim($report['class_name']),
						trim($report['branch_name']),
						trim($report['status']),
						strip_tags($report['score'][1]),
						strip_tags($report['score'][2]),
						strip_tags($report['score'][3]),
						strip_tags($report['score'][4]),
						strip_tags($report['score'][5]),
						strip_tags($report['score'][6]),
						strip_tags($report['score'][7]),
						strip_tags($report['score'][8]),
						strip_tags($report['score'][9]),
						strip_tags($report['score'][10]),
						strip_tags($report['score'][11]),
						strip_tags($report['score'][12]),
						strip_tags($report['score'][13]),
						strip_tags($report['score'][14])
					)
				);

				// $dimension_i = 1;
				// foreach ($report['score'] as $key => $value) {
				// 	fputcsv( $f, array(
				// 			trim($report['name']), 
				// 			trim($report['class_name']),
				// 			trim($report['branch_name']),
				// 			trim($report['status']),
				// 			$key,
				// 			strip_tags($value)
				// 		)
				// 	);
					// if( $dimension_i == 1) {
					// } else {
					// 	fputcsv( $f, array(
					// 			'', 
					// 			'',
					// 			'',
					// 			'',
					// 			$key,
					// 			strip_tags($value)
					// 		)
					// 	);
					// } 
					// $dimension_i++;
				// }
			} else {
				fputcsv( $f, array(
						trim($report['name']), 
						trim($report['class_name']),
						trim($report['branch_name']),
						trim($report['status']),
						'',
						'',
						'',
						'',
						'',
						'',
						'',
						'',
						'',
						'',
						'',
						'',
						'',
						''
					)
				);
			}

		}
	}

 	fclose($f);
	exit;

}
add_action('wp_ajax_export_excel_weekly_report','export_excel_weekly_report_cb');

function export_excel_monthly_report_cb() {
	global $wpdb;

	$student = getStudentByID($_REQUEST['student_id']);
	$class = getClassName($student->class_id);
	$branch = getbranchname($student->branch_id);

	$table = new ReportsMonthly();
	$temp_report = $table->getStudentMonthlyReport($_REQUEST['student_id'], $_REQUEST['date']);
	$report = [];

	foreach ($temp_report as $key => $value) {
	  $report[$value['points_key']][$value['points_dimension']][$value['points_id']] = $value['score'];
	}

	$temp_points = getDailyReportPoints();
	$points = [];
	foreach ($temp_points as $key => $value) {
	  $points[$value['points_key']][$value['points_dimension']][$value['points_id']] = $value['name'];
	}

	$title = $student->name.'-'.date_i18n("F Y", strtotime( $_REQUEST['date'] ) );
	$filename = "Report-bulanan-".$title;

	header('Content-Type: text/csv; charset=utf-8'); 
	header('Content-Disposition: attachment; filename="'.$filename.'.csv";');

	$f = fopen('php://output', 'w'); 

	fputcsv( $f, array(
			'Nama Siswa', 
			'Kelas',
			'Cabang',
			'Status',
			'Amanah',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'Loyal',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'Inisiatif',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'Fathonah',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'Adil',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			''
		)
	);

	fputcsv( $f, array(
			'', 
			'',
			'',
			'',
			'Integritas',
			'',
			'',
			'',
			'',
			'Tanggung Jawab',
			'',
			'',
			'',
			'',
			'',
			'Produktif',
			'',
			'',
			'',
			'',
			'',
			'Spiritual',
			'',
			'',
			'',
			'',
			'',
			'',
			'Tangguh',
			'',
			'',
			'',
			'',
			'Pengendalian Diri',
			'',
			'',
			'',
			'',
			'Mandiri',
			'',
			'',
			'',
			'',
			'',
			'Pengambil Resiko',
			'',
			'',
			'',
			'',
			'Berkolaborasi',
			'',
			'',
			'',
			'',
			'Intelijen',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'',
			'Komunikasi',
			'',
			'',
			'',
			'',
			'',
			'Kreasi',
			'',
			'',
			'',
			'',
			'Kesantunan',
			'',
			'',
			'',
			'',
			'Menghargai',
			'',
			'',
			'',
			'',
			'Berpikir Kritis',
			'',
			'',
			'',
			''
		)
	);

	$fout =  array(
		trim($student->name), 
		trim($class),
		trim($branch)
	);

	foreach ($points as $points_key => $dimensions) {

		foreach ($dimensions as $dimensions_key => $dimension) {
			$report_dimension = $dimensions_key;

          	$count_dimension = ( !empty($report[$points_key][$report_dimension]) ) ? array_sum($report[$points_key][$report_dimension]) : 0;

          	foreach ($dimension as $id => $name){
				if (isset($report[$points_key][$report_dimension][$id])) {
					$score = $report[$points_key][$report_dimension][$id];
					$score_percent = ($score / $count_dimension)*100;
				}  else {
					$score = 0;
					$score_percent = 0;
          		}

          		$score = $score.'('.$score_percent.'%)';
          		$name = strip_tags(trim($name)).' '.$score;

          		array_push($fout, $name);

          	} 
		}
	}

	fputcsv( $f, $fout);

	/*if (!empty($report['score'])) {
		foreach ($report['score'] as $dimension_key => $scores) {
			foreach ($scores as $score) {
				array_push($fout, strip_tags($score));
			}

		}
	} */

	// fputcsv( $f, array(
	// 		'Poin', 
	// 		'Dimensi',
	// 		'Penilaian',
	// 		'Nilai'
	// 	)
	// );

	/*foreach ($points as $points_key => $dimensions) {

		$p_i = 1;
		foreach ($dimensions as $dimensions_key => $dimension) {
			$report_dimension = $dimensions_key;

          	$count_dimension = ( !empty($report[$points_key][$report_dimension]) ) ? array_sum($report[$points_key][$report_dimension]) : 0;

			$d_i = 1;
          	foreach ($dimension as $id => $name){
				if (isset($report[$points_key][$report_dimension][$id])) {
					$score = $report[$points_key][$report_dimension][$id];
					$score_percent = ($score / $count_dimension)*100;
				}  else {
					$score = 0;
					$score_percent = 0;
          		}

          		$score = $score.'('.$score_percent.'%)';
          		$name = strip_tags(trim($name));

          		if ($p_i == 1 && $d_i == 1) {
					fputcsv( $f, array(
							$points_key,
							$dimensions_key,
							$name,
							$score
						)
					);
          		} elseif($p_i != 1 && $d_i == 1) {
          			fputcsv( $f, array(
							'',
							$dimensions_key,
							$name,
							$score
						)
					);
          		} else {
          			fputcsv( $f, array(
							'',
							'',
							$name,
							$score
						)
					);
          		}
          		$d_i++;
          	} 
			$p_i++;
		}
	}*/

 	fclose($f);
	exit;

}
add_action('wp_ajax_export_excel_monthly_report','export_excel_monthly_report_cb');


function export_excel_payment_cb(){

	global $wpdb;

 	if ( 	empty( $_REQUEST['branch'] ) || empty( $_REQUEST['class'] ) ||
 			empty( $_REQUEST['date'] )
 		){
        
        $_SESSION['notice'] = 'Filter cabang dan kelas harus diisi.';
		wp_redirect(admin_url('admin.php?page=payment'));
		exit();
    } 

	$date_join = "AND month(p.period) = month(CURRENT_DATE) AND year(p.period) = year(CURRENT_DATE)";
	$date_search_key = current_time('Y-m-01');
	if ( ! empty(  $_REQUEST['s'] ) ) {
		$date_search_key = !empty( $_REQUEST['date'] ) ? wp_unslash( trim( $_REQUEST['date'] ) ) : '';

		if (! empty( $date_search_key )) {
			$date_join = "AND month(p.period) = month('$date_search_key') AND year(p.period) = year('$date_search_key')";
		} 
	}

	$sql = "SELECT 	s.student_id as student_id,
					s.name as name,
					s.number as number,
					s.branch_id as branch_id,
					b.name as branch_name,
					c.name as class_name,
					s.class_id as class_id,
					MAX(CASE WHEN p.payment_id IS NOT null THEN p.payment_id ELSE NULL END) AS payment_id,
					MAX(CASE WHEN p.period  IS NOT null THEN p.period  ELSE DATE_FORMAT('$date_search_key' ,'%Y-%m-01')  END) AS period,
					MAX(CASE WHEN p.status IS NOT null THEN p.status ELSE 0 END) AS status,
					MAX(CASE WHEN p.image IS NOT null THEN p.status ELSE 0 END) AS image

			FROM {$wpdb->prefix}students s 
			LEFT OUTER JOIN {$wpdb->prefix}payment p 
					ON s.student_id = p.student_id {$date_join}
			LEFT OUTER JOIN {$wpdb->prefix}branch b 
					ON s.branch_id = b.branch_id
			LEFT OUTER JOIN {$wpdb->prefix}class c 
					ON s.class_id = c.class_id
			WHERE s.branch_id = '".$_REQUEST['branch']."' AND s.class_id = '".$_REQUEST['class']."'
			 GROUP BY s.student_id ORDER BY s.name
	";

	$results = $wpdb->get_results( $sql, ARRAY_A );

	foreach ($results as $result) {
		if ($result['payment_id']) {
			 $payment_result = $wpdb->get_row(
            	$wpdb->prepare( " 
	            		SELECT *
	            		FROM {$wpdb->prefix}payment 
	            		WHERE payment_id = %d 
        			", 
            		$result['payment_id'] 
            	), 
            	ARRAY_A
            );

			$result['payment'] = $payment_result;
		} else {
			$result['payment'] = null;
		}

		$results[] = $result;
	}

	// echo "<pre>",print_r($results,1),"</pre>";
	// exit();

	$title = date_i18n("F Y", strtotime( $_REQUEST['date'] ) );
	$filename = "Pembayaran_".$title;

	header('Content-Type: text/csv; charset=utf-8'); 
	header('Content-Disposition: attachment; filename="'.$filename.'.csv";');

	$f = fopen('php://output', 'w'); 

	fputcsv( $f, array(
			'Nama Siswa', 
			'Kelas',
			'Cabang',
			'Status Pembayaran',
			'Tanggal Pembayaran',
			'Jumlah Pembayaran',
			'Rekening Pengirim',
			'Bukti Pembayaran'
		)
	);

	foreach ($results as $result) {
		switch ($result['status']) {
			case '1':
				$result['status'] = 'Menunggu Konfirmasi';
				break;
			case '2':
				$result['status'] = 'Lunas';
				break;
			default:
				$result['status'] = 'Belum Dibayar';
				break;
		}

		if ($result['payment_id']) {
			fputcsv( $f, array(
					trim($result['name']),
					trim($result['class_name']),
					trim($result['branch_name']),
					trim($result['status']),
					trim($result['payment']['date']),
					trim($result['payment']['amount']),
					trim($result['payment']['sender']),
					wp_get_attachment_url( $result['payment']['image'] )
				)
			);
		} else {
			fputcsv( $f, array(
					trim($result['name']),
					trim($result['class_name']),
					trim($result['branch_name']),
					trim($result['status']),
					'',
					'',
					'',
					''
				)
			);
		}
	}

	fclose($f);
	exit;


}
add_action('wp_ajax_export_excel_payment','export_excel_payment_cb');

function ak_check_user_payment() {
    if (is_parent()) {
    	global $flash;
    	global $payment;
    	$checkPayment = checkUserPayment();
    	
    	if ($checkPayment) {
    		$link = site_url('payment');
	   		$flash->add('danger', "Ada pembayaran yang belum lunas, mohon segera melakukan pembayaran. <a href='$link'>Klik disini.</a>");
	   		$payment = $checkPayment;
    	} else {
    		$payment = false;
    	}

    }

}
add_action( 'init', 'ak_check_user_payment' );