<?php 

/**
 * 
 */
class Payment {
	
	public function get_payment_data($per_page = 20, $page_number = 1) {
		global $wpdb;
		global $user_ID;

		$teacher_sql_join = '';
		$teacher_sql_where = '';
		if (is_teacher()) {
			$teacher_sql_join = "LEFT JOIN {$wpdb->prefix}usermeta u1 
								ON u1.meta_key='branch' AND u1.user_id = '$user_ID' 
							LEFT JOIN {$wpdb->prefix}usermeta u2 
								ON u2.meta_key='class' AND u2.user_id = '$user_ID' ";
			$teacher_sql_where = " AND u1.meta_value = s.branch_id 
								AND u2.meta_value = s.class_id";
		}

		$date_join = "AND month(p.period) = month(CURRENT_DATE) AND year(p.period) = year(CURRENT_DATE)";
		$date_search_key = current_time('Y-m-01 00:00:00');
		if ( ! empty(  $_REQUEST['search'] ) ) {
			$date_search_key = !empty( $_REQUEST['date'] ) ? wp_unslash( trim( $_REQUEST['date'] ) ) : '';

			if (! empty( $date_search_key )) {
				$date_join = "AND month(p.period) = month('$date_search_key') AND year(p.period) = year('$date_search_key')";
			} 
		}

// 		$sql = "SELECT 	s.student_id as student_id,
// 						s.name as name,
// 						s.number as number,
// 						s.branch_id as branch_id,
// 						s.class_id as class_id,
// 						u.user_login as user_login,
// 						u.user_registered as user_registered,
// 						CURRENT_TIMESTAMP,
// 						MAX(CASE WHEN p.payment_id IS NOT null THEN p.payment_id ELSE NULL END) AS payment_id,
// 						MAX(CASE WHEN p.period  IS NOT null THEN p.period  ELSE DATE_FORMAT('$date_search_key' ,'%Y-%m-01')  END) AS period,
// 						MAX(CASE WHEN p.status IS NOT null THEN p.status ELSE NULL END) AS status

// 				FROM {$wpdb->prefix}students s 
// 				LEFT OUTER JOIN {$wpdb->prefix}payment p 
// 						ON s.student_id = p.student_id {$date_join}
// 				LEFT JOIN {$wpdb->prefix}parents_students ps
// 						ON ps.student_id = s.student_id
// 				LEFT JOIN {$wpdb->prefix}users u
// 						ON u.ID = ps.parent_id
// 				$teacher_sql_join
// 				WHERE month(u.user_registered) <= month('$date_search_key') and year(u.user_registered) <= year('$date_search_key')
// 				$teacher_sql_where
// 		";
		
			$sql = "SELECT 	s.student_id as student_id,
						s.name as name,
						s.number as number,
						s.branch_id as branch_id,
						s.class_id as class_id,
						u.user_login as user_login,
						u.user_registered as user_registered,
						CURRENT_TIMESTAMP,
						MAX(CASE WHEN p.payment_id IS NOT null THEN p.payment_id ELSE NULL END) AS payment_id,
						MAX(CASE WHEN p.period  IS NOT null THEN p.period  ELSE DATE_FORMAT('$date_search_key' ,'%Y-%m-01')  END) AS period,
						MAX(CASE WHEN p.status IS NOT null THEN p.status ELSE NULL END) AS status

				FROM {$wpdb->prefix}students s 
				LEFT OUTER JOIN {$wpdb->prefix}payment p 
						ON s.student_id = p.student_id {$date_join}
				LEFT JOIN {$wpdb->prefix}parents_students ps
						ON ps.student_id = s.student_id
				LEFT JOIN {$wpdb->prefix}users u
						ON u.ID = ps.parent_id
				$teacher_sql_join
	    	 
				$teacher_sql_where
		";
		

		if ( ! empty(  $_REQUEST['search'] ) ) {
			$name_search_key = !empty( $_REQUEST['n'] ) ? wp_unslash( trim( $_REQUEST['n'] ) ) : '';
			
			$class_search_key = !empty( $_REQUEST['class'] ) ? wp_unslash( trim( $_REQUEST['class'] ) ) : '';
			$branch_search_key = !empty( $_REQUEST['branch'] ) ? wp_unslash( trim( $_REQUEST['branch'] ) ) : '';
			$status_search_key = (isset( $_REQUEST['status']) && is_numeric($_REQUEST["status"]) ) ? wp_unslash( trim( $_REQUEST['status'] ) ) : '';
			

			if ( $status_search_key == 0) {
				$status_search_index = 'NOT EXISTS(SELECT * FROM ak_payment p2 WHERE p2.payment_id = p.payment_id )' ;
			} elseif ( $status_search_key >= 1) {
				$status_search_index = 'p.status' ;
			}

			$search_queries = array(
				"s.name" => $name_search_key,
				"s.class_id" => $class_search_key,
				"s.branch_id" => $branch_search_key,
				$status_search_index => $status_search_key

			);


			$search_queries_i = 0;
			$search_queries_len = count($search_queries);
			


			// if all empty
			if ( array_filter($search_queries, 'is_numeric')  ) {

				foreach ($search_queries as $key => $value ) {

					if ($search_queries_i == 0 ) {
						if (!empty($teacher_sql)) {
							$sql .= " AND ";
						} else{
							$sql .= " AND ";
						}
					}

					if ( ($search_queries_i == $search_queries_len - 1) OR ($search_queries_len == 1)  ) {
						if ( ($key == "s.branch_id" && $value != '') 
							|| ($key == "p.status" && $value !== '') ) {
							$sql .= $key." = '" . esc_sql( $value ). "' ";
						} elseif($key == 'NOT EXISTS(SELECT * FROM ak_payment p2 WHERE p2.payment_id = p.payment_id )'){
							$sql .= $key." ";
						} else {
							$sql .= $key." LIKE '" . esc_sql( "%".$value."%" ). "' ";
						}
				    } else {
						if (($key == "s.branch_id" && $value != '') 
							|| ($key == "p.status" && $value !== '') ) {
							$sql .= $key." = '" . esc_sql( $value ). "' AND ";
						} elseif($key == 'NOT EXISTS(SELECT * FROM ak_payment p2 WHERE p2.payment_id = p.payment_id )'){
							$sql .= $key." AND ";
						} else {
							$sql .= $key." LIKE '" . esc_sql( "%".$value."%" ). "' AND ";
						}
				    }

			    	$search_queries_i++;
			    }
			    
			}

		} /*else {
			$sql .= " WHERE r.DATE LIKE CURRENT_DATE ";
		}*/

		if ( ! empty( $_REQUEST['orderby'] ) ) {
			$sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
			$sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
		} 

		return $sql;
	}

	public function record_count()
	{
		global $wpdb;

		$sql = $this->get_payment_data();

		$sql .= " GROUP BY s.student_id ";
		$sql .= " ORDER BY s.name ASC ";

		return count($wpdb->get_results( $sql ));
	}

	public function prepare_items($per_page = 10, $page_number = 1) {
		global $wpdb;

		$_SERVER['REQUEST_URI'] = remove_query_arg( '_wp_http_referer', $_SERVER['REQUEST_URI'] );

		$sql = $this->get_payment_data($per_page , $page_number );
		
		$sql .= " GROUP BY s.student_id ";
		$sql .= " ORDER BY s.name ASC ";
		$sql .= " LIMIT $per_page";
		$sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;


		$result = $wpdb->get_results( $sql, 'ARRAY_A' );

		return $this->items = $result;
	}

	public function search_box( $text, $input_id ) {
 		$default = array(
	        'n'      => '',
	        'date'      => '',
	        'class'  => null,
	        'branch'  => null
	    );

	    $item = shortcode_atts($default, $_REQUEST);

	    $classData = getClassSelectOption();
	    $branchData = getBranchSelectOption();
 
        if ( ! empty( $_REQUEST['orderby'] ) ) {
            echo '<input type="hidden" name="orderby" value="' . esc_attr( $_REQUEST['orderby'] ) . '" />';
        }
        if ( ! empty( $_REQUEST['order'] ) ) {
            echo '<input type="hidden" name="order" value="' . esc_attr( $_REQUEST['order'] ) . '" />';
        }
        if ( ! empty( $_REQUEST['post_mime_type'] ) ) {
            echo '<input type="hidden" name="post_mime_type" value="' . esc_attr( $_REQUEST['post_mime_type'] ) . '" />';
        }
        if ( ! empty( $_REQUEST['detached'] ) ) {
            echo '<input type="hidden" name="detached" value="' . esc_attr( $_REQUEST['detached'] ) . '" />';
        }
        if ( ! empty( $_REQUEST['status'] ) ) {
            echo '<input type="hidden" name="status" value="' . esc_attr( $_REQUEST['status'] ) . '" />';
        }
        ?>
			<input type="hidden" name="search" value="TRUE">	
			<input type="hidden" name="date" id="actualDate" value="<?php echo $item['date']; ?>">

			<div class="input-group">
				<input type="text" class="form-control datepicker" placeholder="Bulan / Tahun" value="<?php echo $item['date']; ?>" />
				<input name="n" type="text" class="form-control" value="<?php echo $item['n']; ?>" placeholder="Nama siswa" aria-label="Example text with button addon" aria-describedby="button-addon1"/>
				<select  class="form-control"
				    type="search" 
				    id="branch-search-input" 
				    name="branch"
			    >
			    	<option value="">Cabang</option>
			    	<?php foreach ($branchData as $value): ?>
			    		<option 
			    			value="<?php echo $value['branch_id'] ?>" 
			    			<?php echo ($item['branch'] == $value['branch_id']) ? 'selected' : '' ; ?> 
		    			>
		    				<?php echo $value['name'] ?>
	    				</option>
			    	<?php endforeach ?>
				</select>
				<select class="form-control"
				    type="search" 
				    id="class-search-input" 
				    name="class"
			    >
			    	<option value="">Kelas</option>
			    	<?php foreach ($classData as $value): ?>
			    		<option 
			    			value="<?php echo $value['class_id'] ?>" 
			    			<?php echo ($item['class'] == $value['class_id']) ? 'selected' : '' ; ?> 
		    			>
		    				<?php echo $value['name'] ?>
	    				</option>
			    	<?php endforeach ?>
				</select>
				<div class="input-group-append">
	              <button type="submit" class="btn btn-secondary" type="button" id="button-addon1">Cari</button>
	            </div>	
			</div>
        <?php
    }

	public function get_views() { 
		$url_arg = array();

		if ( ! empty( $_REQUEST['search'] ) ) {
            $url_arg['search']=$_REQUEST['search'];
        }
	 	if ( ! empty( $_REQUEST['orderby'] ) ) {
            $url_arg['orderby']=$_REQUEST['orderby'];
        }
        if ( ! empty( $_REQUEST['order'] ) ) {
            $url_arg['order']=$_REQUEST['order'];
        }
        if ( ! empty( $_REQUEST['name'] ) ) {
            $url_arg['name']=$_REQUEST['n'];
        }
        if ( ! empty( $_REQUEST['date'] ) ) {
            $url_arg['date']=$_REQUEST['date'];
        }
        if ( ! empty( $_REQUEST['class'] ) ) {
            $url_arg['class']=$_REQUEST['class'];
        }
        if ( ! empty( $_REQUEST['branch'] ) ) {
            $url_arg['branch']=$_REQUEST['branch'];
        }

        $url = add_query_arg($url_arg,admin_url('pay'));

	    $status_links = array(
	        "all"       => __("<a class='btn btn-secondary m-r-xxs' href='".add_query_arg($url_arg,site_url('payment/'))."'>Semua</a>",'alifakids'),
	        "unpaid" => __("<a class='btn btn-danger m-r-xxs' href='".add_query_arg($url_arg,site_url('payment/?status=0'))."'>Belum Dibayar</a>",'alifakids'),
	        "paid" => __("<a class='btn btn-warning m-r-xxs' href='".add_query_arg($url_arg,site_url('payment/?status=1'))."'>Menunggu Konfirmasi</a>",'alifakids'),
	        "done"   => __("<a class='btn btn-success m-r-xxs' href='".add_query_arg($url_arg,site_url('payment/?status=2'))."'>Lunas</a>",'alifakids')
	    );
	    return $status_links;
	}

	public function views() {
	    $views = $this->get_views();
	 
	    foreach ( $views as $class => $view ) {
	    	echo $view;
	    }
	}
}

/**
 * Payment_Parent Class
 */
class Payment_Parent {
	public function get_payment_data($student_id, $per_page = 10, $page_number = 1) {
		global $wpdb;
		global $user_ID;

		$udata = get_userdata( $user_ID );

		$now = strtotime(current_time('Y-m-d'));
		$registered = strtotime(date("Y-m-d",strtotime($udata->user_registered)));

		$union_sql = '';
		$month_interval = -(( $page_number - 1 ) * $per_page); 
		
		$union_i = 1;

		while ($union_i <= $per_page) {

			$union_date = strtotime( date("Y-m-d",strtotime("$month_interval month")));
			//exit($union_date.'-'.$registered);

			if ( ($union_date >= $registered) || ($union_date == $registered) ) {
				if ($union_i == 1) {
					$union_sql .= " select DATE_ADD(CURDATE(), INTERVAL '$month_interval' MONTH) as month ";
				} else {
					$union_sql .= " union select DATE_ADD(CURDATE(), INTERVAL '$month_interval' MONTH) month ";
				}
			}
			$month_interval--;
			$union_i++;
		}

		if ($now >= $registered) {
			$sql = "SELECT 
						payment_id,
						months.month as period,
						$student_id as student_id,
						status
						FROM ( $union_sql ) months
						  	LEFT JOIN {$wpdb->prefix}payment p
						  	 	ON MONTH(months.month) = MONTH(p.period) 
						  	 	AND YEAR(months.month) = YEAR(p.period) 
						  	 	AND p.student_id = '$student_id'
			";
		} else {
			return false;
		}

		return $sql;
	}

	public function record_count()
	{
		global $wpdb;
		global $user_ID;

		$udata = get_userdata( $user_ID );
		$registered = strtotime($udata->user_registered);
		$now = strtotime(current_time('Y-m-d'));

		$year_registered = date('Y', $registered);
		$year_now = date('Y', $now);

		$month_registered = date('m', $registered);
		$month_now = date('m', $now);

		return (($year_now - $year_registered) * 12) + ($month_now - $month_registered)+1;
	}

	public function prepare_items($student_id, $per_page = 10, $page_number = 1) {
		global $wpdb;

		$_SERVER['REQUEST_URI'] = remove_query_arg( '_wp_http_referer', $_SERVER['REQUEST_URI'] );

		$sql = $this->get_payment_data($student_id, $per_page , $page_number );

		if ($sql) {
			$sql .= " GROUP BY months.month ";
			$sql .= " ORDER BY months.month DESC ";


			$result = $wpdb->get_results( $sql, 'ARRAY_A' );
		} else {
			$result = [];
		}
		

		return $this->items = $result;
	}
	
}

function printPaymentStatus($status = null) {
	$result = '';

	if ($status == 1) {
		$result .= '<span class="badge badge-warning">Menunggu Konfirmasi</span>';
	} else if($status == 2) {
		$result .= '<span class="badge badge-success">Lunas</span>';
	} else {
		$result .= '<span class="badge badge-danger">Belum Dibayar</span>';
	}

	return $result;
}

function getPaymentDetailByID($payment_id) {
	global $wpdb;

	$sql = "SELECT 	p.payment_id as payment_id,
						p.student_id as student_id,
						p.period as period,
						p.date as date,
						p.amount as amount,
						p.sender as sender,
						p.transfer_to as transfer_to,
						p.image as image,
						p.status as status,
						s.name as name,
						s.number as number,
						s.branch_id as branch_id,
						s.class_id as class_id

				FROM {$wpdb->prefix}payment p 
					LEFT JOIN {$wpdb->prefix}students s 
						ON s.student_id = p.student_id 
					WHERE p.payment_id = '$payment_id' ";

	$query = $wpdb->get_row( $sql, 'ARRAY_A' );

	return $query;
}


function rupiahformat($price){
	
	$result = "Rp " . number_format($price,0,',','.');
	return $result;
 
}


add_action("wp_ajax_ajax_get_payment_detail", "ajax_get_payment_detail_cb");
function ajax_get_payment_detail_cb() {
	if ($_REQUEST['payment_id']  ) {
		$item = getPaymentDetailByID($_REQUEST['payment_id']); 

		$period = date_i18n("F Y", strtotime( $item['period'] ) );
		$date = date_i18n("l, d F Y", strtotime( $item['date'] ) );
	?>
	<div class="row mb-1">
		<div class="col-12 text-left" >
			<table class="table">
				<tbody>
					<tr>
						<td class="text-left min">Nama siswa : </td>
						<td><?php echo $item['name']. ' ('.$item['number'].')' ?></td>
					</tr>
					<tr>
						<td class="text-left min">Bulan / Tahun : </td>
						<td><?php echo $period ?></td>
					</tr>
					<tr>
						<td class="text-left min">Status : </td>
						<td class="payment-status" data-payment_id="<?php echo $item['payment_id'] ?>"><?php echo printPaymentStatus($item['status']) ?></td>
					</tr>
					<tr>
						<td class="text-left min">Jumlah pembayaran : </td>
						<td><?php echo rupiahformat($item['amount']) ?></td>
					</tr>
					<tr>
						<td class="text-left min">Bank & Nama Rekening Pengirim : </td>
						<td><?php echo $item['sender'] ?></td>
					</tr>
					<tr>
						<td class="text-left min">Di Transfer ke : </td>
						<td><?php echo $item['transfer_to'] ?></td>
					</tr>
					<tr>
						<td class="text-left min">Bukti Pembayaran : </td>
						<td>
							<?php if($item['image']):?>
								<img src="<?php echo wp_get_attachment_url( $item['image'] ); ?>" class="img-fluid">
							<?php else: ?>
								<span>Tidak Ada</span>
							<?php endif ?>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="col-12">
			<?php if ($item['status'] == 1 && current_user_can( 'manage_options' ) ): ?>
				<button data-payment_id="<?php echo $item['payment_id'] ?>" type="button" class="btn btn-block btn-primary confirmPayment">
					<div style="display: none;" class="spinner-border text-secondary text-center" role="status" id="confirmLoading">
		                  <span class="sr-only">Loading...</span>
		              </div>
					Terima Pembayaran
				</button>
			<?php endif ?>
		</div>
	</div>
	<?php
	} else {
		return false;
	}

	wp_die();
}

add_action("wp_ajax_ajax_confirm_payment", "ajax_confirm_payment_cb");
function ajax_confirm_payment_cb() {
	global $wpdb;

	if( !$_REQUEST['payment_id'] || !is_admin() ) return false;

	$table_name = $wpdb->prefix . 'payment'; 

	$update = $wpdb->update( $table_name, 
		array('status' =>  '2' ),
		array('payment_id' => $_REQUEST['payment_id'] )
	);

	if ($update) {
		echo json_encode(true);
	} else {
		echo json_encode(false);
	}

	wp_die();

}



function new_payment_confirmation() {

	if ( isset($_REQUEST['nonce']) 
      && wp_verify_nonce($_REQUEST['nonce'], 'new_payment_confirmation')
    ){
	    global $user_ID;
	    global $wpdb;
	    global $flash;
		
	    $table_name = $wpdb->prefix . 'payment'; 

	    $default = array(
	        'student_id' => '',
	        'period' => '',
	        'date' => '',
	        'amount' => '',
	        'sender' => '',
	        'transfer_to' => '',
	        'status' => 1
	    );

	    $item = shortcode_atts($default, $_REQUEST);     
	    
	    $insert = $wpdb->insert($table_name,$item);

	    if($insert) {
	    	$payment_id = $wpdb->insert_id;

	    	if ($_FILES['image']['size'] != 0) {
	    		$attachment = $_FILES['image'];
				$wordpress_upload_dir = wp_upload_dir();
			    $new_file_path = $wordpress_upload_dir['path'] . '/' . $attachment['name'];
			    $new_file_mime = mime_content_type( $attachment['tmp_name'] );

			    if( $attachment['size'] > wp_max_upload_size() )
					die( 'It is too large than expected.' );
			 
				if( !in_array( $new_file_mime, get_allowed_mime_types() ) )
					die( 'WordPress doesn\'t allow this type of uploads.' );
		    	
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

	  		$flash->add('success', 'Konfirmasi pembayaran telah dikirim.');

			wp_redirect( site_url('/payment/'));
	    } else {
	    	$flash->add('warning', 'Konfirmasi pembayaran gagal dikirim.');

			wp_redirect( site_url('/payment/form?period='.$_REQUEST['period'].'&student_id='.$_REQUEST['student_id'] ));
	    }
  	}
}
add_action( 'admin_post_new_payment_confirmation', 'new_payment_confirmation' );

function checkUserPayment(){
	global $wpdb;
	global $user_ID;

	$students = getParentStudents($user_ID);

	$udata = get_userdata( $user_ID );
	$registered = strtotime(date("Y-m-d",strtotime($udata->user_registered)));

	$union_sql = '';
	$now = strtotime(current_time('Y-m-d'));

	$year_registered = date('Y', $registered);
	$year_now = date('Y', $now);

	$month_registered = date('m', $registered);
	$month_now = date('m', $now);

	$diff = (($year_now - $year_registered) * 12) + ($month_now - $month_registered)+1;

	$result = [];

	if ($now >= $registered) {

		foreach ($students as $student) {
			$month_interval = 0; 
			$union_sql = '';
			$union_i = 1;
			$student_id = $student['student_id'];

			while ($union_i <= $diff) {

				$union_date = strtotime( date("Y-m-d",strtotime("$month_interval month")));

				if ( ($union_date >= $registered) || ($union_date == $registered) ) {
					if ($union_i == 1) {
						$union_sql .= " select DATE_ADD(CURDATE(), INTERVAL '$month_interval' MONTH) as month ";
					} else {
						$union_sql .= " union select DATE_ADD(CURDATE(), INTERVAL '$month_interval' MONTH) month ";
					}
				}
				$month_interval--;
				$union_i++;
			}

			$sql = "SELECT 
							payment_id,
							months.month as period,
							$student_id as student_id,
							status
							FROM ( $union_sql ) months
							  	LEFT JOIN {$wpdb->prefix}payment p
							  	 	ON MONTH(months.month) = MONTH(p.period) 
							  	 	AND YEAR(months.month) = YEAR(p.period) 
							  	 	AND p.student_id = '$student_id'

					  	 	GROUP BY months.month 
							ORDER BY months.month DESC 
				";

			$payments[$student_id] = $wpdb->get_results( $sql, ARRAY_A );
		}


		foreach ($payments as $p_key => $payment) {
			$lunas = true;
			foreach ($payment as $value) {
				if ($value['status'] != 2) {
					$now_m = date("Y-m",strtotime(current_time('Y-m-d')));
					$period_m = date("Y-m",strtotime($value['period']));

					$now_d = current_time('Y-m-d');
					$last_date = current_time('Y-m-10');
					

					if ( ($now_m != $period_m) || ($now_d > $last_date) ) {
						//$result[$p_key][] = $value['period'];
						$lunas = false;
					}
				}
			}
			if (!$lunas) {
				$result[] = $p_key;
			}
		}
	}

	if ( !empty($result) ) {
		return $result;
	} else {
		return false;
	}
}