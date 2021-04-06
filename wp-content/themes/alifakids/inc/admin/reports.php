<?php 

function printReportStatus($status = null) {
	$result = '';

	if ($status == 1) {
		$result .= '<span class="badge badge-primary">Telah Ditulis</span>';
	} else if($status == 2) {
		$result .= '<span class="badge badge-success">Diterima Ortu</span>';
	} else {
		$result .= '<span class="badge badge-secondary">Belum Ditulis</span>';
	}

	return $result;
}

function isWeekend() {
    return (date('N') >= 6);
}

/*function createStudentDailyReport($student_id) {
	if ( isWeekend() == FALSE ) {
		global $wpdb;

		$wpdb->query("INSERT INTO {$wpdb->prefix}reports_daily ( student_id,date,status )
	    				SELECT * FROM (SELECT '".$student_id."',CURRENT_DATE,0) as tmp
	                    WHERE not exists 
	                    ( SELECT student_id,date from {$wpdb->prefix}reports_daily 
	                    	 where student_id='".$student_id."'
	                    			AND date = CURRENT_DATE) LIMIT 1"
		);
	}
}

if ( ! wp_next_scheduled( 'create_report_daily_cron_action' ) ) {
	wp_schedule_event( time(), 'daily', 'create_report_daily_cron_action' );
} 
function create_report_daily_run() {
	if ( isWeekend() == FALSE ) {
		global $wpdb;

		$wpdb->query("INSERT INTO {$wpdb->prefix}reports_daily ( student_id,date,status )
	    				SELECT student_id, CURRENT_DATE,0
	    				FROM {$wpdb->prefix}students as t1
	                    WHERE not exists 
	                    ( SELECT 1 from {$wpdb->prefix}reports_daily as t2
	                    	 where t2.student_id = t1.student_id 
	                    			AND t2.date = CURRENT_DATE)"
		);
	}
}
add_action( 'create_report_daily_cron_action', 'create_report_daily_run' );

function alifakids_add_weekly( $schedules ) {
    $schedules['weekly'] = array(
        'interval' => 604800,
        'display' => __('Weekly')
    );
    return $schedules;
}
add_filter('cron_schedules', 'alifakids_add_weekly');

if ( ! wp_next_scheduled( 'create_report_weekly_cron_action' ) ) {
	wp_schedule_event( time(), 'weekly', 'create_report_weekly_cron_action' );
} 
function create_report_weekly_run() {
	global $wpdb;

	$wpdb->query("INSERT INTO {$wpdb->prefix}reports_weekly ( student_id,week,year,status )
    				SELECT student_id, WEEK(NOW()), YEAR(NOW()) ,0
    				FROM {$wpdb->prefix}students as t1
                    WHERE not exists 
                    ( SELECT 1 from {$wpdb->prefix}reports_weekly as t2
                    	 where t2.student_id = t1.student_id 
                    			AND t2.week = WEEK(NOW()) 
                    			AND t2.year = YEAR(NOW()) )"
	);
}
add_action( 'create_report_weekly_cron_action', 'create_report_weekly_run' );*/

function getDailyReportPoints($key = null, $dimensions = null) {
	global $wpdb;

	$sql = "SELECT * FROM {$wpdb->prefix}reports_daily_points";

	if (!is_null($key) && is_null($dimensions)) {
		$sql .= " WHERE points_key LIKE '%".$key."%'";
	} elseif (is_null($key) && !is_null($dimensions)) {
		$sql .= " WHERE points_dimension LIKE '%".$dimensions."%'";
	} elseif (!is_null($key) && !is_null($dimensions)) {
		$sql .= " WHERE points_key LIKE '%".$key."%'";
		$sql .= " AND points_dimension LIKE '".$dimensions."'";
	}

	$result = $wpdb->get_results( $sql, 'ARRAY_A' );
	return $result;
}

function getWeeklyReportPoints() {
	global $wpdb;

	$sql = "SELECT * FROM {$wpdb->prefix}reports_weekly_points";

	$result = $wpdb->get_results( $sql, 'ARRAY_A' );
	return $result;
}

class Report_Daily_List extends WP_List_Table {

	public function __construct() {

		parent::__construct( [
			'singular' => __( 'Report Daily', 'alifakids' ),
			'plural'   => __( 'Report Daily', 'alifakids' ),
			'ajax'     => false
		] );

	}

	public function get_daily_report( $per_page = 20, $page_number = 1 ) {

		global $wpdb;

		$date_join = "AND r.date = CURRENT_DATE";

		if ( ! empty(  $_REQUEST['s'] ) ) {
			$date_search_key = !empty( $_REQUEST['date'] ) ? wp_unslash( trim( $_REQUEST['date'] ) ) : '';

			if (! empty( $date_search_key )) {
				$date_join = "AND r.date = '$date_search_key'";
			} 
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
		";

		if ( !empty(  $_REQUEST['s'] ) ) {
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

					if ($search_queries_i == 0) {
						//first
						$sql .= " WHERE ";
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

		}

		$sql .= " GROUP BY s.student_id ";

		if ( ! empty( $_REQUEST['orderby'] ) ) {
			$sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
			$sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
		} else {
			$sql .= " ORDER BY s.name ASC ";

		}

		return $sql;
	}

	public function record_count($item = null)	{
		global $wpdb;

		$sql = $this->get_daily_report(-1);

		return count($wpdb->get_results( $sql, 'ARRAY_A' ));
	}

	protected function get_views() { 
		$url_arg = array();

	 	if ( ! empty( $_REQUEST['orderby'] ) ) {
            $url_arg['orderby']=$_REQUEST['orderby'];
        }
        if ( ! empty( $_REQUEST['order'] ) ) {
            $url_arg['order']=$_REQUEST['order'];
        }
        if ( ! empty( $_REQUEST['name'] ) ) {
            $url_arg['name']=$_REQUEST['name'];
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
        $url_arg['s']=TRUE;

        $url = add_query_arg($url_arg,admin_url('admin.php?page=reports_daily'));

	    $status_links = array(
	        "all"       => __("<a href='".add_query_arg($url_arg,admin_url('admin.php?page=reports_daily'))."'>Semua</a>",'my-plugin-slug'),
	        "empty" => __("<a href='".add_query_arg($url_arg,admin_url('admin.php?page=reports_daily&status=0'))."'>Belum Ditulis</a>",'my-plugin-slug'),
	        "written" => __("<a href='".add_query_arg($url_arg,admin_url('admin.php?page=reports_daily&status=1'))."'>Telah Ditulis</a>",'my-plugin-slug'),
	        "approved"   => __("<a href='".add_query_arg($url_arg,admin_url('admin.php?page=reports_daily&status=2'))."'>Diterima Ortu</a>",'my-plugin-slug')
	    );
	    return $status_links;
	}

	public function get_columns() {
		$columns = [
			'name'    => __( 'Nama Siswa', 'alifakids' ),
			'report_date'    => __( 'Tanggal', 'alifakids' ),
			'branch_name'    => __( 'Cabang', 'alifakids' ),
			'class_name'    => __( 'Kelas', 'alifakids' ),
			'status'    => __( 'Status', 'alifakids' ),
			'action'    => ''
		];

		return $columns;
	}

	public function get_sortable_columns() {
		$sortable_columns = array(
			'name' => array( 'name', true ),
			'report_date' => array( 'date', true ),
			'status' => array( 'address', true )
		);

		return $sortable_columns;
	}


	public function no_items() {
		_e( 'No report available.', 'alifakids' );
	}

	public function column_default( $item, $column_name ) {
		return $item[$column_name];
	}

	public function column_report_date( $item ) {
		$date = date_i18n("l, d F Y", strtotime( $item['date'] ) );

		return $date;
	}
	public function column_status( $item ) {
		return printReportStatus($item['status']);
	}

	public function column_action( $item ) {
		$button = '';

		$date = (isset($_REQUEST['date'])) ? $_REQUEST['date'] : current_time('Y-m-d');

		$button_link = esc_url( '?page=report_daily_add&id='.$item['report_id'].'&student_id='.$item['student_id'].'&date='.$date.'' );
	
		if ($item['status'] == 2) {
			$button .= "<a class='button action' href='#' disabled>Tambah</a>";
		} elseif($item['status'] == 1) {
			$button .= "<a class='button action' href=\"{$button_link}\">Ubah</a>";
		}else {
			$button .= "<a class='button action' href=\"{$button_link}\">Tambah</a>";
		}


		return $button;
	}

	protected function bulk_actions($which = '') {
		$url_arg = array();

        if ( ! empty( $_REQUEST['class'] ) ) {
            $url_arg['class']=$_REQUEST['class'];
        }
        if ( ! empty( $_REQUEST['branch'] ) ) {
            $url_arg['branch']=$_REQUEST['branch'];
        }
        if ( ! empty( $_REQUEST['date'] ) ) {
            $url_arg['date']=$_REQUEST['date'];
        }

        $url = add_query_arg($url_arg,admin_url('admin-ajax.php?action=export_excel_daily_report'));

	    echo "<a class='button-primary' href='$url'>Export to Excel</a>";
	}

	 public function get_bulk_actions() {

        return array(
            'delete' => __( 'Delete', 'your-textdomain' ),
            'save'   => __( 'Save', 'your-textdomain' ),
        );

    }

	public function prepare_items() {
		global $wpdb;
		$_SERVER['REQUEST_URI'] = remove_query_arg( '_wp_http_referer', $_SERVER['REQUEST_URI'] );

		$columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
		$this->_column_headers = array($columns, $hidden, $sortable);

		$this->process_bulk_action();

		$per_page     = $this->get_items_per_page( 'branch_per_page', 20 );
		$current_page = $this->get_pagenum();
		$total_items  = $this->record_count();

		$this->set_pagination_args( [
			'total_items' => $total_items,
			'per_page'    => $per_page
		] );



		$sql = $this->get_daily_report( $per_page, $current_page );

		$sql .= " LIMIT $per_page";
		$sql .= ' OFFSET ' . ( $current_page - 1 ) * $per_page;

		$this->items = $wpdb->get_results( $sql, 'ARRAY_A' );
	}

	public function process_bulk_action() {
		if ( 'delete' === $this->current_action() ) {

			$nonce = esc_attr( $_REQUEST['_wpnonce'] );

			if ( ! wp_verify_nonce( $nonce, 'alifakids_delete_branch' ) ) {
				die();
			} else {
				$this->delete_branch( absint( $_GET['branch'] ) );
			}

		} 

		elseif ( ( isset( $_GET['action'] ) && $_GET['action'] == 'bulk-delete' )
		     || ( isset( $_GET['action2'] ) && $_GET['action2'] == 'bulk-delete' )
		) {

			$delete_ids = esc_sql( $_GET['bulk-delete'] );

			foreach ( $delete_ids as $id ) {
				$this->delete_branch( $id );

			}
		}
	}

	public function search_box( $text, $input_id ) {
        if ( empty( $_REQUEST['s'] ) && ! $this->has_items() ) {
            return;
        }
 		
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
        ?>
		<p class="search-box">
			<input type="hidden" name="s" value="TRUE">	
			<input type="hidden" name="date" id="actualDate" value="<?php echo $item['date']; ?>">
		    <input 
            	id="date" 
            	type="text"
				placeholder="Tanggal"
			    value="<?php echo $item['date']; ?>" 
        	/>
		    <input 
			    type="search" 
			    id="name-search-input" 
			    name="n" 
			    placeholder="Nama"
			    value="<?php echo $item['n']; ?>" 
		    />
		    <select 
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
			<select 
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
	        <?php submit_button( $text, '', '', false, array( 'id' => 'search-submit' ) ); ?>
		</p>
        <?php
    }
}

class Report_Weekly_List extends WP_List_Table {

	public function __construct() {

		parent::__construct( [
			'singular' => __( 'Report Weekly', 'alifakids' ),
			'plural'   => __( 'Report Weekly', 'alifakids' ),
			'ajax'     => false
		] );

	}

	public function get_branch( $per_page = 20, $page_number = 1 ) {

		global $wpdb;

		$cur_date = current_time('Y-m-d');
		$date_join = "AND r.date = ( '$cur_date' - INTERVAL((WEEKDAY( '$cur_date' )) ) DAY)";

		if ( ! empty(  $_REQUEST['s'] ) ) {
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
						b.name  as branch_name,
						c.name  as class_name,
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
			";

		if ( ! empty(  $_REQUEST['s'] ) ) {
			$name_search_key = !empty( $_REQUEST['n'] ) ? wp_unslash( trim( $_REQUEST['n'] ) ) : '';
			
			$class_search_key = !empty( $_REQUEST['class'] ) ? wp_unslash( trim( $_REQUEST['class'] ) ) : '';
			$branch_search_key = !empty( $_REQUEST['branch'] ) ? wp_unslash( trim( $_REQUEST['branch'] ) ) : '';
			$status_search_key = (isset( $_REQUEST['status']) && is_numeric($_REQUEST["status"]) ) ? wp_unslash( trim( $_REQUEST['status'] ) ) : '';


			$search_queries = array(
				"s.name" => $name_search_key,
				"s.class_id" => $class_search_key,
				"s.branch_id" => $branch_search_key
			);

			if ( isset( $_REQUEST['status']) && is_numeric($_REQUEST["status"]) ) {
				if ( $status_search_key == 0) {
					$search_queries['NOT EXISTS(SELECT * FROM ak_reports_weekly rd2 WHERE rd2.report_id = r.report_id )'] = '';
				} elseif ( $status_search_key >= 1) {
					$search_queries['r.status'] = $status_search_key;
				}


			}

			$search_queries_i = 0;
			$search_queries_len = count($search_queries);
			
			// if all empty
			if ( array_filter($search_queries) || $status_search_key == 0  ) {

				foreach ($search_queries as $key => $value ) {

					if ($search_queries_i == 0) {
						//first
						$sql .= " WHERE ";
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

		} 

		$sql .= " GROUP BY s.student_id ";

		if ( ! empty( $_REQUEST['orderby'] ) ) {
			$sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
			$sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
		} else {
			$sql .= " ORDER BY s.name ASC ";

		}



		return $sql;
	}

	public function record_count( $item = null ) {
		global $wpdb;

		$sql = $this->get_branch(-1);

		return count($wpdb->get_results( $sql, 'ARRAY_A' ));
	}

	public function get_columns() {
		$columns = [
			'name'    => __( 'Nama Siswa', 'alifakids' ),
			'report_week'    => __( 'Minggu ke', 'alifakids' ),
			'branch_name'    => __( 'Cabang', 'alifakids' ),
			'class_name'    => __( 'Kelas', 'alifakids' ),
			'status'    => __( 'Status', 'alifakids' ),
			'action'    => ''
		];

		return $columns;
	}

	public function get_sortable_columns() {
		$sortable_columns = array(
			'name' => array( 'name', true ),
			'report_week' => array( 'week', true ),
			'status' => array( 'address', true )
		);

		return $sortable_columns;
	}


	public function no_items() {
		_e( 'No report available.', 'alifakids' );
	}

	public function column_default( $item, $column_name ) {
		return $item[$column_name];
	}

	public function column_report_week( $item ) {
		if (!empty($item['date'])) {
			$date = $item['date'];
		} else {
			if (!empty($_GET['date'])) {
				$date = $_GET['date'];
			} else {
				$date = current_time( 'Y-m-d' );
			}
		}

			return 'Minggu ke-'.weekOfMonth($date).', Bulan '.date_i18n("F Y", strtotime( $date ));
	}
	public function column_status( $item ) {
		return printReportStatus($item['status']);
	}

	public function column_action( $item ) {
		$button = '';

		$date = (isset($_REQUEST['date'])) ? $_REQUEST['date'] : date('Y-m-d', strtotime( 'monday this week' )) ;

		$button_link = esc_url( '?page=report_weekly_add&id='.$item['report_id'].'&student_id='.$item['student_id'].'&date='.$date.'' );
	
		if ($item['status'] == 2) {
			$button .= "<a class='button action' href='#' disabled>Tambah</a>";
		} elseif($item['status'] == 1) {
			$button .= "<a class='button action' href=\"{$button_link}\">Ubah</a>";
		}else {
			$button .= "<a class='button action' href=\"{$button_link}\">Tambah</a>";
		}


		return $button;
	}


	public function delete_branch( $id ) {
		global $wpdb;

		$wpdb->delete(
			"{$wpdb->prefix}branch",
			[ 'branch_id' => $id ],
			[ '%d' ]
		);
	}

	public function prepare_items() {
		global $wpdb;
		$_SERVER['REQUEST_URI'] = remove_query_arg( '_wp_http_referer', $_SERVER['REQUEST_URI'] );

		$columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
		$this->_column_headers = array($columns, $hidden, $sortable);

		$this->process_bulk_action();

		$per_page     = $this->get_items_per_page( 'branch_per_page', 20 );
		$current_page = $this->get_pagenum();
		$total_items  = $this->record_count();

		$this->set_pagination_args( [
			'total_items' => $total_items,
			'per_page'    => $per_page
		] );


		$sql = $this->get_branch( $per_page, $current_page );


		$sql .= " LIMIT $per_page";
		$sql .= ' OFFSET ' . ( $current_page - 1 ) * $per_page;


		$this->items = $wpdb->get_results( $sql, 'ARRAY_A' );
	}

	public function process_bulk_action() {
		if ( 'delete' === $this->current_action() ) {

			$nonce = esc_attr( $_REQUEST['_wpnonce'] );

			if ( ! wp_verify_nonce( $nonce, 'alifakids_delete_branch' ) ) {
				die();
			} else {
				$this->delete_branch( absint( $_GET['branch'] ) );
			}

		} 

		elseif ( ( isset( $_GET['action'] ) && $_GET['action'] == 'bulk-delete' )
		     || ( isset( $_GET['action2'] ) && $_GET['action2'] == 'bulk-delete' )
		) {

			$delete_ids = esc_sql( $_GET['bulk-delete'] );

			foreach ( $delete_ids as $id ) {
				$this->delete_branch( $id );

			}
		}
	}

	public function search_box( $text, $input_id ) {
        if ( empty( $_REQUEST['s'] ) && ! $this->has_items() ) {
            return;
        }
 		
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
		<p class="search-box">
			<input type="hidden" name="s" value="TRUE">	
			<input type="hidden" name="date" id="actualDate" value="<?php echo $item['date']; ?>">
		    <input 
		    	class="week-picker"
            	type="text"
				placeholder="Tanggal"
			    value="<?php echo $item['date']; ?>" 
        	/>
		    <input 
			    type="search" 
			    id="name-search-input" 
			    name="n" 
			    placeholder="Nama"
			    value="<?php echo $item['n']; ?>" 
		    />
		    <select 
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
			<select 
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
	        <?php submit_button( $text, '', '', false, array( 'id' => 'search-submit' ) ); ?>
		</p>
        <?php
    }

	protected function get_views() { 
		$url_arg = array();

	 	if ( ! empty( $_REQUEST['orderby'] ) ) {
            $url_arg['orderby']=$_REQUEST['orderby'];
        }
        if ( ! empty( $_REQUEST['order'] ) ) {
            $url_arg['order']=$_REQUEST['order'];
        }
        if ( ! empty( $_REQUEST['name'] ) ) {
            $url_arg['name']=$_REQUEST['name'];
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
        $url_arg['s']=TRUE;

        $url = add_query_arg($url_arg,admin_url('admin.php?page=reports_weekly'));

	    $status_links = array(
	        "all"       => __("<a href='".add_query_arg($url_arg,admin_url('admin.php?page=reports_weekly'))."'>Semua</a>",'my-plugin-slug'),
	        "empty" => __("<a href='".add_query_arg($url_arg,admin_url('admin.php?page=reports_weekly&status=0'))."'>Belum Ditulis</a>",'my-plugin-slug'),
	        "written" => __("<a href='".add_query_arg($url_arg,admin_url('admin.php?page=reports_weekly&status=1'))."'>Telah Ditulis</a>",'my-plugin-slug'),
	        "approved"   => __("<a href='".add_query_arg($url_arg,admin_url('admin.php?page=reports_weekly&status=2'))."'>Diterima Ortu</a>",'my-plugin-slug')
	    );
	    return $status_links;
	}

	protected function bulk_actions($which = '') {
		$url_arg = array();

        if ( ! empty( $_REQUEST['class'] ) ) {
            $url_arg['class']=$_REQUEST['class'];
        }
        if ( ! empty( $_REQUEST['branch'] ) ) {
            $url_arg['branch']=$_REQUEST['branch'];
        }
        if ( ! empty( $_REQUEST['date'] ) ) {
            $url_arg['date']=$_REQUEST['date'];
        }

        $url = add_query_arg($url_arg,admin_url('admin-ajax.php?action=export_excel_weekly_report'));

	    echo "<a class='button-primary' href='$url'>Export to Excel</a>";
	}
}

class Report_Monthly_List extends WP_List_Table {

	public function __construct() {

		parent::__construct( [
			'singular' => __( 'Report Weekly', 'alifakids' ),
			'plural'   => __( 'Report Weekly', 'alifakids' ),
			'ajax'     => false
		] );

	}

	public function get_branch( $per_page = 20, $page_number = 1 ) {
		global $wpdb;

		$sql = "SELECT s.student_id as student_id,
						s.name as name,
						s.number as number,
						s.branch_id as branch_id,
						s.class_id  as class_id,
						b.name  as branch_name,
						c.name  as class_name

				FROM {$wpdb->prefix}students s 
					LEFT OUTER JOIN {$wpdb->prefix}parents_students ps 
						ON s.student_id = ps.student_id
					LEFT OUTER JOIN {$wpdb->prefix}branch b 
						ON b.branch_id = s.branch_id
					LEFT OUTER JOIN {$wpdb->prefix}class c 
						ON c.class_id = s.class_id
			";

		if ( ! empty(  $_REQUEST['s'] ) ) {
			$name_search_key = !empty( $_REQUEST['n'] ) ? wp_unslash( trim( $_REQUEST['n'] ) ) : '';
			
			$class_search_key = !empty( $_REQUEST['class'] ) ? wp_unslash( trim( $_REQUEST['class'] ) ) : '';
			$branch_search_key = !empty( $_REQUEST['branch'] ) ? wp_unslash( trim( $_REQUEST['branch'] ) ) : '';
			$status_search_key = (isset( $_REQUEST['status']) && is_numeric($_REQUEST["status"]) ) ? wp_unslash( trim( $_REQUEST['status'] ) ) : '';


			$search_queries = array(
				"s.name" => $name_search_key,
				"s.class_id" => $class_search_key,
				"s.branch_id" => $branch_search_key
			);

			if ( isset( $_REQUEST['status']) && is_numeric($_REQUEST["status"]) ) {
				if ( $status_search_key == 0) {
					$search_queries['NOT EXISTS(SELECT * FROM ak_reports_weekly rd2 WHERE rd2.report_id = r.report_id )'] = '';
				} elseif ( $status_search_key >= 1) {
					$search_queries['r.status'] = $status_search_key;
				}


			}

			$search_queries_i = 0;
			$search_queries_len = count($search_queries);
			
			// if all empty
			if ( array_filter($search_queries) || $status_search_key == 0  ) {

				foreach ($search_queries as $key => $value ) {

					if ($search_queries_i == 0) {
						//first
						$sql .= " WHERE ";
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

		} 

		$sql .= " GROUP BY s.student_id ";

		if ( ! empty( $_REQUEST['orderby'] ) ) {
			$sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
			$sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
		} else {
			$sql .= " ORDER BY s.name ASC ";

		}


		$sql .= " LIMIT $per_page";
		$sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;


		$result = $wpdb->get_results( $sql, 'ARRAY_A' );

		return $result;
	}

	public function record_count( $item = null ) {
		global $wpdb;

		$sql = " SELECT COUNT(*) 
			FROM {$wpdb->prefix}branch
		";

		return $wpdb->get_var( $sql );
	}

	public function get_columns() {
		$columns = [
			'name'    => __( 'Nama Siswa', 'alifakids' ),
			'report_month'    => __( 'Bulan', 'alifakids' ),
			'branch_name'    => __( 'Cabang', 'alifakids' ),
			'class_name'    => __( 'Kelas', 'alifakids' ),
			'action'    => ''
		];

		return $columns;
	}

	public function get_sortable_columns() {
		$sortable_columns = array(
			'name' => array( 'name', true )
		);

		return $sortable_columns;
	}


	public function no_items() {
		_e( 'No report available.', 'alifakids' );
	}

	public function column_default( $item, $column_name ) {
		return $item[$column_name];
	}

	public function column_report_month( $item ) {
		if (!empty($item['date'])) {
			$date = $item['date'];
		} else {
			if (!empty($_GET['date'])) {
				$date = $_GET['date'];
			} else {
				$date = current_time( 'Y-m-d' );
			}
		}

		return 'Bulan '.date_i18n("F Y", strtotime( $date ));
	}
	public function column_status( $item ) {
		return printReportStatus($item['status']);
	}

	public function column_action( $item ) {
		$button = '';

		$date = (isset($_REQUEST['date'])) ? $_REQUEST['date'] : current_time('Y-m-01') ;

		$button_link = esc_url( '?page=report_monthly_detail&student_id='.$item['student_id'].'&date='.$date.'' );
	
		$button .= "<a class='button action' href=\"{$button_link}\">Lihat</a>";


		return $button;
	}

	public function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['report_id']
		);
	}

	public function delete_branch( $id ) {
		global $wpdb;

		$wpdb->delete(
			"{$wpdb->prefix}branch",
			[ 'branch_id' => $id ],
			[ '%d' ]
		);
	}

	public function prepare_items() {
		$_SERVER['REQUEST_URI'] = remove_query_arg( '_wp_http_referer', $_SERVER['REQUEST_URI'] );

		$columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
		$this->_column_headers = array($columns, $hidden, $sortable);

		$this->process_bulk_action();

		$per_page     = $this->get_items_per_page( 'branch_per_page', 20 );
		$current_page = $this->get_pagenum();
		$total_items  = $this->record_count();

		$this->set_pagination_args( [
			'total_items' => $total_items,
			'per_page'    => $per_page
		] );


		$this->items = $this->get_branch( $per_page, $current_page );
	}

	public function process_bulk_action() {
		if ( 'delete' === $this->current_action() ) {

			$nonce = esc_attr( $_REQUEST['_wpnonce'] );

			if ( ! wp_verify_nonce( $nonce, 'alifakids_delete_branch' ) ) {
				die();
			} else {
				$this->delete_branch( absint( $_GET['branch'] ) );
			}

		} 

		elseif ( ( isset( $_GET['action'] ) && $_GET['action'] == 'bulk-delete' )
		     || ( isset( $_GET['action2'] ) && $_GET['action2'] == 'bulk-delete' )
		) {

			$delete_ids = esc_sql( $_GET['bulk-delete'] );

			foreach ( $delete_ids as $id ) {
				$this->delete_branch( $id );

			}
		}
	}

	public function search_box( $text, $input_id ) {
        if ( empty( $_REQUEST['s'] ) && ! $this->has_items() ) {
            return;
        }
 		
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
		<p class="search-box">
			<input type="hidden" name="s" value="TRUE">	
			<input type="hidden" name="date" id="actualDate" value="<?php echo $item['date']; ?>">
		    <input 
		    	class="datepicker"
            	type="text"
				placeholder="Tanggal"
			    value="<?php echo $item['date']; ?>" 
        	/>
		    <input 
			    type="search" 
			    id="name-search-input" 
			    name="n" 
			    placeholder="Nama"
			    value="<?php echo $item['n']; ?>" 
		    />
		    <select 
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
			<select 
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
	        <?php submit_button( $text, '', '', false, array( 'id' => 'search-submit' ) ); ?>
		</p>
        <?php
    }

	protected function get_views() { 
		$url_arg = array();

	 	if ( ! empty( $_REQUEST['orderby'] ) ) {
            $url_arg['orderby']=$_REQUEST['orderby'];
        }
        if ( ! empty( $_REQUEST['order'] ) ) {
            $url_arg['order']=$_REQUEST['order'];
        }
        if ( ! empty( $_REQUEST['name'] ) ) {
            $url_arg['name']=$_REQUEST['name'];
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
        $url_arg['s']=TRUE;

        $url = add_query_arg($url_arg,admin_url('admin.php?page=reports_weekly'));

	    $status_links = array(
	        "all"       => __("<a href='".add_query_arg($url_arg,admin_url('admin.php?page=reports_monthly'))."'>Semua</a>",'my-plugin-slug')
	    );
	    return $status_links;
	}
}