<?php 

/**
 * ReportsWeekly Class
 */
class ReportsWeekly {
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

		$date_join = "AND r.date = ( CURRENT_DATE - INTERVAL((WEEKDAY( CURRENT_DATE )) ) DAY)";

		if ( ! empty(  $_REQUEST['search'] ) ) {
			$date_search_key = !empty( $_REQUEST['date'] ) ? wp_unslash( trim( $_REQUEST['date'] ) ) : '';

			if (! empty( $date_search_key )) {
				$date_join = "AND r.date = ( '$date_search_key' - INTERVAL((WEEKDAY( '$date_search_key' )) ) DAY)";
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
					LEFT OUTER JOIN {$wpdb->prefix}reports_weekly r 
						ON s.student_id = r.student_id {$date_join}
					LEFT OUTER JOIN {$wpdb->prefix}reports_weekly_score rs 
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
				$status_search_index = 'NOT EXISTS(SELECT * FROM ak_reports_weekly rd2 WHERE rd2.report_id = r.report_id )' ;
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
						} elseif($key == 'NOT EXISTS(SELECT * FROM ak_reports_weekly rd2 WHERE rd2.report_id = r.report_id )'){
							$sql .= $key." ";
						} else {
							$sql .= $key." LIKE '" . esc_sql( "%".$value."%" ). "' ";
						}
				    } else {
						if (($key == "s.branch_id" && $value != '') || ($key == "r.status" && $value !== '') ) {
							$sql .= $key." = '" . esc_sql( $value ). "' AND ";
						} elseif($key == 'NOT EXISTS(SELECT * FROM ak_reports_weekly rd2 WHERE rd2.report_id = r.report_id )'){
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
				<input type="text" class="form-control week-picker" placeholder="Tanggal Report" value="<?php echo $item['date']; ?>" />
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

function new_report_weekly_function() {
	if ( isset($_REQUEST['nonce']) 
      && ( wp_verify_nonce($_REQUEST['nonce'], 'new_report_weekly') || wp_verify_nonce($_REQUEST['nonce'], 'new_report_weekly_admin') )
    ){
	    global $user_ID;
		global $flash;
      	global $wpdb;

    	$table_reports_weekly = $wpdb->prefix . 'reports_weekly'; 
    	$table_reports_score = $wpdb->prefix . 'reports_weekly_score'; 

		if ($_REQUEST['report_id'] == '') {

		    $report_args = array(
		        'date' => $_REQUEST['date'],
		        'student_id' => $_REQUEST['student_id'],
		        'author_id' => $user_ID,
		        'status' => '1'
		    );

		    $insert = $wpdb->insert( $table_reports_weekly, $report_args );


		    if ($insert) {
		    	$report_id = $wpdb->insert_id;

		    	for ($i=1; $i <= 14 ; $i++) { 
		    		$wpdb->insert($table_reports_score, 
		    			array(
					        'report_id' => $report_id,
					        'points_id' => $i,
					        'score_value' => $_REQUEST['points_'.$i]
		    			)
		    		);
		    	}

		    	if (wp_verify_nonce($_REQUEST['nonce'], 'new_report_weekly_admin')) {
					redirect(admin_url('admin.php?page=reports_weekly&notice=success'));
				} else {
					$flash->add('success', 'Report berhasil ditambah.');
					wp_redirect( site_url('/report-mingguan/') );

				}

		    } else {

				if (wp_verify_nonce($_REQUEST['nonce'], 'new_report_weekly_admin')) {
					redirect(admin_url('admin.php?page=reports_weekly&notice=error'));
				} else {
					$flash->add('warning', 'Gagal menambahkan report.');
					wp_redirect( site_url('/report-mingguan/') );
				}
		    }
		} else {
	    	$report_id = $_REQUEST['report_id'];

			$delete = $wpdb->delete(
				$table_reports_score,
				[ 'report_id' => $report_id ]
			);

			if ($delete) {
				for ($i=1; $i <= 14 ; $i++) { 
		    		$wpdb->insert($table_reports_score, 
		    			array(
					        'report_id' => $report_id,
					        'points_id' => $i,
					        'score_value' => $_REQUEST['points_'.$i]
		    			)
		    		);
		    	}

		    	if (wp_verify_nonce($_REQUEST['nonce'], 'new_report_weekly_admin')) {
					redirect(admin_url(  'admin.php?page=report_weekly_add&id='.$_REQUEST['report_id'].'&student_id='.$_REQUEST['student_id'].'&date='.$_REQUEST['date'].'&notice=success' ) );
				} else {

					$flash->add('success', 'Report berhasil ubah.');
					wp_redirect( 
						add_query_arg( array(
								'id' => $report_id, 
								'student_id' => $_REQUEST['student_id'], 
								'date' => $_REQUEST['date'], 
							) , site_url('/report-mingguan/add')
						)
						
					);
				}
			}
		}
	    
	}

}
add_action( 'admin_post_new_report_weekly', 'new_report_weekly_function' );

function getWeeklyReportPointsByID($report_id) {
	global $wpdb;

	$sql = "SELECT rdp.points_id as points_id,
					rds.score_value as score_value
				FROM {$wpdb->prefix}reports_weekly_points rdp
					LEFT JOIN {$wpdb->prefix}reports_weekly_score rds
						ON rdp.points_id = rds.points_id";

	$sql .= " WHERE rds.report_id = '".$report_id."' ";

	$query = $wpdb->get_results( $sql, 'ARRAY_A' );

	$result = [];
	foreach ($query as $value) {
		$result[$value['points_id']] = $value['score_value'];
	}

	return $result;
}

function getWeeklyReportNameByID($key) {
	global $wpdb;

	$sql = "SELECT *
				FROM {$wpdb->prefix}reports_weekly_points
			WHERE points_id = '$key'";

	$result = $wpdb->get_row( $sql, 'ARRAY_A' );

	return $result['name'];
}

// function weekOfMonth($date) {
//     list($y, $m, $d) = explode('-', date('Y-m-d', strtotime($date)));

//     $w = 1;

//     for ($i = 1; $i <= $d; ++$i) {
//         if ($i > 1 && date('w', strtotime("$y-$m-$i")) == 0) {
//             ++$w;
//         }
//     }
//     return $w;
// }

function weekOfMonth($date,$rollover='monday')
{
	 $cut        = substr($date, 0, 8);
    $daylen     = 86400;
    $timestamp  = strtotime($date);
    $first      = strtotime($cut . "01");   
    $elapsed    = (($timestamp - $first) / $daylen)+1;
    $i          = 1;
    $weeks      = 0;
    for($i==1; $i<=$elapsed; $i++)
    {
        $dayfind        = $cut . (strlen($i) < 2 ? '0' . $i : $i);
        $daytimestamp   = strtotime($dayfind);
        $day            = strtolower(date("l", $daytimestamp));
        if($day == strtolower($rollover))
        {
            $weeks++;  
        }
    } 
    if($weeks==0)
    {
        $weeks++; 
    }
    return $weeks;  
}


add_action("wp_ajax_ajax_get_report_weekly_detail", "ajax_get_report_weekly_detail_cb");
function ajax_get_report_weekly_detail_cb() {

	if ($_REQUEST['report_id'] && $_REQUEST['student_id'] && $_REQUEST['report_date']   ) {
		$points = getWeeklyReportPointsByID($_REQUEST['report_id']); 

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
						<td>
							Minggu Ke <?php echo weekOfMonth($_REQUEST['report_date']) ?>, 
                      		Bulan <?php echo date_i18n("F", strtotime( $_REQUEST['report_date'] ) ); ?> 
						</td>
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
	          <th scope="col" class="text-left min">Indikator</th>
	          <th scope="col" class="text-left">Penilaian</th>
	        </tr>
	      </thead>
	      <tbody>
	      	<?php foreach ($points as $key => $value): ?>
	      		<tr>
	      			<td class="text-left min"><?php echo getWeeklyReportNameByID($key) ?></td>
	      			<td><?php echo $value ?></td>
	      		</tr>
	      	<?php endforeach ?>
	      </tbody>
	    </table>
	  </div>
	<?php
	} else {
		return false;
	}


	wp_die();
}

add_action("wp_ajax_ajax_confirm_weekly_report", "ajax_confirm_weekly_report_cb");
function ajax_confirm_weekly_report_cb(){
	global $wpdb;

	$update = $wpdb->update( 
		"{$wpdb->prefix}reports_weekly", 
		array('status' => 2 ),
		array('report_id' => $_REQUEST['report_id'] )
	);

	echo json_encode($update);

	wp_die();
}