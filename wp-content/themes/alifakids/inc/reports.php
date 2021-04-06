<?php 

/**
 * Reports Class
 */
class Reports {
	public function get_daily_reports($per_page = 10, $page_number = 1, $user_id=null) {
		
		global $wpdb;
		global $user_ID;

		$by_user = '';
		if ($user_id) {
			$by_user = ' WHERE ps.parent_id = '.$user_id.' ';
		}

		$teacher_sql = '';
		if (is_teacher()) {

			if (current_user_can('editor')) {
				$teacher_sql = "LEFT JOIN {$wpdb->prefix}usermeta u1 
									ON u1.meta_key='branch' AND u1.user_id = '$user_ID' 
								WHERE u1.meta_value = s.branch_id ";
			} else{
				$t_class = get_user_meta( $user_ID, 'class', true );
				$t_class_Array = '(' . join(',', $t_class) . ')';

				$teacher_sql = "LEFT JOIN {$wpdb->prefix}usermeta u1 
									ON u1.meta_key='branch' AND u1.user_id = '$user_ID' 
								WHERE u1.meta_value = s.branch_id 
									AND s.class_id IN $t_class_Array";
			}
		}

		$date_join = "AND r.date = CURRENT_DATE";

		if ( ! empty(  $_REQUEST['search'] ) ) {
			$date_search_key = !empty( $_REQUEST['date'] ) ? wp_unslash( trim( $_REQUEST['date'] ) ) : '';

			if (! empty( $date_search_key )) {
				$date_join = "AND r.date = '$date_search_key'";
			} 
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
						ON s.student_id = r.student_id {$date_join}
					LEFT OUTER JOIN {$wpdb->prefix}reports_daily_score rs 
						ON r.report_id = rs.report_id 
					LEFT OUTER JOIN {$wpdb->prefix}parents_students ps 
						ON s.student_id = ps.student_id
					$teacher_sql
					$by_user
		";

		if ( ! empty(  $_REQUEST['search'] ) ) {
			$name_search_key = !empty( $_REQUEST['n'] ) ? wp_unslash( trim( $_REQUEST['n'] ) ) : '';
			
			$class_search_key = !empty( $_REQUEST['class'] ) ? wp_unslash( trim( $_REQUEST['class'] ) ) : '';
			$branch_search_key = !empty( $_REQUEST['branch'] ) ? wp_unslash( trim( $_REQUEST['branch'] ) ) : '';
			$status_search_key = (isset( $_REQUEST['status']) && is_numeric($_REQUEST["status"]) ) ? wp_unslash( trim( $_REQUEST['status'] ) ) : '';

			if ( $status_search_key == 0) {
				$status_search_index = 'NOT EXISTS(SELECT * FROM ak_reports_daily rd2 WHERE rd2.report_id = r.report_id )' ;
			} elseif ( $status_search_key >= 1) {
				$status_search_index = 'r.status' ;
			}

			$search_queries = array(
				"s.name" => $name_search_key,
				"s.class_id" => $class_search_key,
				"s.branch_id" => $branch_search_key
			);

			if ( isset( $_REQUEST['status']) && is_numeric($_REQUEST["status"]) ) {
				$search_queries[$status_search_index] = $status_search_key;
			}


			$search_queries_i = 0;
			$search_queries_len = count($search_queries);

			// if all empty
			if ( array_filter($search_queries) || $status_search_key == 0  ) {

				foreach ($search_queries as $key => $value ) {

					if ($search_queries_i == 0 ) {
						if ($user_id || !empty($teacher_sql)) {
							$sql .= " AND ";
						} else{
							$sql .= " WHERE ";
						}
					}

					if ( ($search_queries_i == $search_queries_len - 1) OR ($search_queries_len == 1)  ) {
						if ( ($key == "s.branch_id" && $value != '') || ($key == "r.status" && $value !== '') ) {
							$sql .= $key." = '" . esc_sql( $value ). "' ";
						} elseif($key == 'NOT EXISTS(SELECT * FROM ak_reports_daily rd2 WHERE rd2.report_id = r.report_id )'){
							$sql .= $key." ";
						} else {
							$sql .= $key." LIKE '" . esc_sql( "%".$value."%" ). "' ";
						}
				    } else {
						if (($key == "s.branch_id" && $value != '') || ($key == "r.status" && $value !== '') ) {
							$sql .= $key." = '" . esc_sql( $value ). "' AND ";
						} elseif($key == 'NOT EXISTS(SELECT * FROM ak_reports_daily rd2 WHERE rd2.report_id = r.report_id )'){
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

	public function record_count( $item = null ) {
		global $wpdb;

		$sql = $this->get_daily_reports();

		$sql .= " GROUP BY s.student_id ";
		$sql .= " ORDER BY s.name ASC ";

		return count($wpdb->get_results( $sql ));
	}

	public function prepare_items($per_page = 10, $page_number = 1,$user_id=null) {
		global $wpdb;

		$_SERVER['REQUEST_URI'] = remove_query_arg( '_wp_http_referer', $_SERVER['REQUEST_URI'] );

		$sql = $this->get_daily_reports($per_page , $page_number, $user_id );
		
		$sql .= " GROUP BY s.student_id ";
		$sql .= " ORDER BY s.name ASC ";
		$sql .= " LIMIT $per_page";
		$sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;


		$result = $wpdb->get_results( $sql, 'ARRAY_A' );

		return $this->items = $result;
	}

	public function search_box( $text, $input_id ) {
        // if ( empty( $_REQUEST['s'] ) && ! $this->has_items() ) {
        //     return;
        // }

        global $user_ID;
 		
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
				<input type="text" class="form-control datepicker" placeholder="Tanggal Report" value="<?php echo $item['date']; ?>" />
				<input name="n" type="text" class="form-control" value="<?php echo $item['n']; ?>" placeholder="Nama siswa" aria-label="Example text with button addon" aria-describedby="button-addon1"/>
				<?php if ( current_user_can('administrator') || current_user_can('editor') ): ?>
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
				<?php endif ?>
				<select class="form-control"
				    type="search" 
				    id="class-search-input" 
				    name="class"
			    >
			    	<option value="">Kelas</option>
			    	<?php if (is_teacher() && !current_user_can('editor')) : 
						$classes = get_user_meta( $user_ID, 'class', true );
			    	?>
				    	<?php foreach ($classData as $value): 
							if(in_array($value['class_id'], $classes)):
							?>
				    		<option 
				    			value="<?php echo $value['class_id'] ?>" 
				    			<?php echo ($item['class'] == $value['class_id']) ? 'selected' : '' ; ?> 
			    			>
			    				<?php echo $value['name'] ?>
		    				</option>
							<?php endif ?>
				    	<?php endforeach ?>
					<?php else: ?>
						<?php foreach ($classData as $value): ?>
				    		<option 
				    			value="<?php echo $value['class_id'] ?>" 
				    			<?php echo ($item['class'] == $value['class_id']) ? 'selected' : '' ; ?> 
			    			>
			    				<?php echo $value['name'] ?>
		    				</option>
				    	<?php endforeach ?>
					<?php endif ?>

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

        $url = add_query_arg($url_arg,admin_url('report-harian'));

	    $status_links = array(
	        "all"       => __("<a class='btn btn-secondary m-r-xxs' href='".add_query_arg($url_arg,site_url('report-harian/'))."'>Semua</a>",'alifakids'),
	        "empty" => __("<a class='btn btn-warning m-r-xxs' href='".add_query_arg($url_arg,site_url('report-harian/?status=0'))."'>Kosong</a>",'alifakids'),
	        "written" => __("<a class='btn btn-primary m-r-xxs' href='".add_query_arg($url_arg,site_url('report-harian/?status=1'))."'>Telah Ditulis</a>",'alifakids'),
	        "approved"   => __("<a class='btn btn-success m-r-xxs' href='".add_query_arg($url_arg,site_url('report-harian/?status=2'))."'>Diterima Ortu</a>",'alifakids')
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

function new_report_daily_function() {
	if ( isset($_REQUEST['nonce']) 
      && (wp_verify_nonce($_REQUEST['nonce'], 'new_report_daily') || wp_verify_nonce($_REQUEST['nonce'], 'new_report_daily_admin'))
    ){
	    global $user_ID;
		global $flash;
      	global $wpdb;

    	$table_reports_daily = $wpdb->prefix . 'reports_daily'; 
    	$table_reports_score = $wpdb->prefix . 'reports_daily_score'; 

	    $default = array(
		    'report_id' => '',
		    'student_id' => '',
		    'author_id' => '',
		    'points_id' => '',
		    'date' => '',
		    'amanah' => array(),
		    'loyal' => array(),
		    'inisiatif' => array(),
		    'fathonah' => array(),
		    'adil' => array()
		);

	    $item = shortcode_atts($default, $_REQUEST);    

	    if ( empty($_REQUEST['amanah']) 
		    	|| empty($_REQUEST['loyal']) 
		    	|| empty($_REQUEST['inisiatif']) 
		    	|| empty($_REQUEST['fathonah']) 
		    	|| empty($_REQUEST['adil']) 
	    	){
	    	$flash->add('warning', 'Report tidak boleh kosong.');
			wp_redirect( site_url('/report-harian/') );
			return;
	    }

		if ($_REQUEST['report_id'] == '') {

		    $report_args = array(
		        'date' => $item['date'],
		        'student_id' => $item['student_id'],
		        'author_id' => $user_ID,
		        'status' => '1'
		    );

		    $insert = $wpdb->insert( $table_reports_daily, $report_args );


		    if ($insert) {
		    	$report_id = $wpdb->insert_id;

		    	$scores = array_merge(
					$_REQUEST['amanah'],
					$_REQUEST['loyal'],
					$_REQUEST['inisiatif'],
					$_REQUEST['fathonah'],
					$_REQUEST['adil']
				);

				foreach ($scores as $score) {
		    		$wpdb->insert($table_reports_score, 
		    			array(
					        'report_id' => $report_id,
					        'points_id' => $score,
					        'score_value' => 1
		    			)
		    		);
				}

				if (wp_verify_nonce($_REQUEST['nonce'], 'new_report_daily_admin')) {
					redirect(admin_url('admin.php?page=reports_daily&notice=success'));
				} else {
					$flash->add('success', 'Report berhasil ditambah.');
					wp_redirect( site_url('/report-harian/') );

				}


		    } else {
		    	if (wp_verify_nonce($_REQUEST['nonce'], 'new_report_daily_admin')) {
					redirect(admin_url('admin.php?page=reports_daily&notice=error'));
				} else {
			    	$flash->add('warning', 'Gagal menambahkan report.');
					wp_redirect( site_url('/report-harian/') );
				}

		    }
		} else {
	    	$report_id = $_REQUEST['report_id'];

			$delete = $wpdb->delete(
				$table_reports_score,
				[ 'report_id' => $report_id ]
			);

			if ($delete) {
				$scores = array_merge(
					$_REQUEST['amanah'],
					$_REQUEST['loyal'],
					$_REQUEST['inisiatif'],
					$_REQUEST['fathonah'],
					$_REQUEST['adil']
				);

				foreach ($scores as $score) {
		    		$wpdb->insert($table_reports_score, 
		    			array(
					        'report_id' => $report_id,
					        'points_id' => $score,
					        'score_value' => 1
		    			)
		    		);
				}

				if (wp_verify_nonce($_REQUEST['nonce'], 'new_report_daily_admin')) {

					redirect(admin_url(  'admin.php?page=report_daily_add&id='.$_REQUEST['report_id'].'&student_id='.$_REQUEST['student_id'].'&date='.$_REQUEST['date'].'&notice=success' ) );
				} else {
					$flash->add('success', 'Report berhasil ubah.');
					wp_redirect( 
						add_query_arg( array(
								'id' => $report_id, 
								'student_id' => $_REQUEST['student_id'], 
								'date' => $_REQUEST['date'], 
							) , site_url('/report-harian/add')
						)
						
					);

				}

			}
		}
	    
	}

}
add_action( 'admin_post_new_report_daily', 'new_report_daily_function' );

function getDailyReportPointsByID($report_id , $key = null) {
	global $wpdb;

	$sql = "SELECT rdp.points_id as points_id
				FROM {$wpdb->prefix}reports_daily_points rdp
					LEFT JOIN {$wpdb->prefix}reports_daily_score rds
						ON rdp.points_id = rds.points_id";

	$sql .= " WHERE rds.report_id = '".$report_id."' ";

	if ($key) {
		$sql .= " AND rdp.points_key LIKE '%".$key."%' ";
	}

	$query = $wpdb->get_results( $sql, 'ARRAY_A' );

	$result = [];
	foreach ($query as $value) {
		$result[] = $value['points_id'];
	}

	return $result;
}


function getDailyReportScoreByID($report_id , $key= null) {
	global $wpdb;

	$sql = "SELECT * 
				FROM ak_reports_daily_points rdp
					LEFT JOIN ak_reports_daily_score rds
				    	ON rdp.points_id = rds.points_id";

	if ($key) {
		$sql .= " WHERE rds.report_id = '".$report_id."' ";
	} else {
		$sql .= " WHERE rds.report_id = '".$report_id."' AND rdp.points_key LIKE '%".$key."%'";
	}


	$query = $wpdb->get_results( $sql, 'ARRAY_A' );

	$result = [];

	foreach($query as $value){
		$result[ $value['points_dimension'] ][] = $value['name'];
	}

	return $result;
}


add_action("wp_ajax_ajax_get_report_detail", "ajax_get_report_detail_cb");
function ajax_get_report_detail_cb() {

	if ($_REQUEST['report_id'] && $_REQUEST['student_id'] && $_REQUEST['report_date']   ) {
		$amanah = getDailyReportScoreByID($_REQUEST['report_id'], 'amanah'); 
		$loyal = getDailyReportScoreByID($_REQUEST['report_id'], 'loyal'); 
		$inisiatif = getDailyReportScoreByID($_REQUEST['report_id'], 'inisiatif'); 
		$fathonah = getDailyReportScoreByID($_REQUEST['report_id'], 'fathonah'); 
		$adil = getDailyReportScoreByID($_REQUEST['report_id'], 'adil'); 

		$date = date_i18n("l, d F Y", strtotime( $_REQUEST['report_date'] ) );
		$student = getStudentByID( $_REQUEST['student_id'] );
	?>
	<div class="row mb-1">
		<div class="col-12 text-left" >
			<table class="table">
				<tbody>
					<tr>
						<td class="text-left min">Nama Siswa : </td>
						<td><?php echo $student->name. ' ('.$student->number.')' ?></td>
					</tr>
					<tr>
						<td class="text-left min">Tanggal Report : </td>
						<td><?php echo $date ?></td>
					</tr>
					<tr>
						<td class="text-left min">Status : </td>
						<td><?php echo printReportStatus($_REQUEST['report_status']) ?></td>
					</tr>
				</tbody>
			</table>
			
		</div>
	</div>
	<div class="table-responsive">
	    <table class="table table-striped table-bordered">
	      <thead class="thead-purple">
	        <tr>
	          <th scope="col" class="text-left min">Dimensi</th>
	          <th scope="col" class="text-left">Poin</th>
	        </tr>
	      </thead>
	      <tbody>
	      	<thead class="thead-pink">
		      	<tr>
		      		<th colspan="3" class="text-center">Amanah</th>
		      	</tr>
	      	</thead>
	      	<?php 
	      		$dimmensions = array(
                    'integritas' => 'Integritas',
                    'tanggungjawab' => 'Tanggung Jawab', 
                    'produktif' => 'Produktif'
                  );

	      		foreach ($dimmensions as $key => $dimmension): 
	      			$i=1; 	
	      			foreach ($amanah[ $key ] as $value) :
	      	?>
		        <tr>
		          <td class="text-left min">
		          	<?php if ($i == 1): ?>
		            	<strong><?php echo $dimmension ?></strong>
		          	<?php endif ?>
		          </td>
		          <td class="text-left"><?php echo $value ?></td>
		      	</tr>
	      	<?php 
	      			$i++;
		      		endforeach; 
		      	endforeach; 
	      	?>

	  
	      	<thead class="thead-pink">
		      	<tr>
		      		<th colspan="3" class="text-center">Loyal</th>
		      	</tr>
	      	</thead>
	      	<?php 
	      		$dimmensions = array(
                    'spiritual' => 'Spiritual',
                    'tangguh' => 'Tangguh',
                    'pengendaliandiri' => 'Pengendalian Diri'
                  );

	      		foreach ($dimmensions as $key => $dimmension): 
	      			$i=1; 	
	      			foreach ($loyal[ $key ] as $value) :
	      	?>
		        <tr>
		          <td class="text-left min">
		          	<?php if ($i == 1): ?>
		            	<strong><?php echo $dimmension ?></strong>
		          	<?php endif ?>
		          </td>
		          <td class="text-left"><?php echo $value ?></td>
		      	</tr>
	      	<?php 
	      			$i++;
		      		endforeach; 
		      	endforeach; 
	      	?>

	      	<thead class="thead-pink">
		      	<tr>
		      		<th colspan="3" class="text-center">Inisiatif</th>
		      	</tr>
	      	</thead>
	      	<?php 
	      		$dimmensions = array(
                    'mandiri' => 'Mandiri',
                    'pengambilresiko' => 'Pengambil Resiko',
                    'berkolaborasi' => 'Berkolaborasi'
                  );

	      		foreach ($dimmensions as $key => $dimmension): 
	      			$i=1; 	
	      			foreach ($inisiatif[ $key ] as $value) :
	      	?>
		        <tr>
		          <td class="text-left min">
		          	<?php if ($i == 1): ?>
		            	<strong><?php echo $dimmension ?></strong>
		          	<?php endif ?>
		          </td>
		          <td class="text-left"><?php echo $value ?></td>
		      	</tr>
	      	<?php 
	      			$i++;
		      		endforeach; 
		      	endforeach; 
	      	?>

	      	<thead class="thead-pink">
		      	<tr>
		      		<th colspan="3" class="text-center">Fathonah</th>
		      	</tr>
	      	</thead>
	      	<?php 
	      		$dimmensions = array(
                    'intelijen' => 'Intelijen',
                    'komunikasi' => 'Komunikasi',
                    'kreasi' => 'Kreasi'
                  );

	      		foreach ($dimmensions as $key => $dimmension): 
	      			$i=1; 	
	      			foreach ($fathonah[ $key ] as $value) :
	      	?>
		        <tr>
		          <td class="text-left min">
		          	<?php if ($i == 1): ?>
		            	<strong><?php echo $dimmension ?></strong>
		          	<?php endif ?>
		          </td>
		          <td class="text-left"><?php echo $value ?></td>
		      	</tr>
	      	<?php 
	      			$i++;
		      		endforeach; 
		      	endforeach; 
	      	?>

	      	<thead class="thead-pink">
		      	<tr>
		      		<th colspan="3" class="text-center">Adil</th>
		      	</tr>
	      	</thead>
	      	<?php 
	      		$dimmensions = array(
                  'kesantunan' => 'Kesantunan',
                  'menghargai' => 'Menghargai',
                  'berpikirkritis' => 'Berpikir Kritis'
                );

	      		foreach ($dimmensions as $key => $dimmension): 
	      			$i=1; 	
	      			foreach ($adil[ $key ] as $value) :
	      	?>
		        <tr>
		          <td class="text-left min">
		          	<?php if ($i == 1): ?>
		            	<strong><?php echo $dimmension ?></strong>
		          	<?php endif ?>
		          </td>
		          <td class="text-left"><?php echo $value ?></td>
		      	</tr>
	      	<?php 
	      			$i++;
		      		endforeach; 
		      	endforeach; 
	      	?>


	      </tbody>
	    </table>
	  </div>
	<?php
	} else {
		return false;
	}


	wp_die();
}

add_action("wp_ajax_ajax_confirm_daily_report", "ajax_confirm_daily_report_cb");
function ajax_confirm_daily_report_cb(){
	global $wpdb;

	$update = $wpdb->update( 
		"{$wpdb->prefix}reports_daily", 
		array('status' => 2 ),
		array('report_id' => $_REQUEST['report_id'] )
	);

	echo json_encode($update);

	wp_die();
}