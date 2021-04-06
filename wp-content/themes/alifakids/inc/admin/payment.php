<?php 

/**
 * payment Admin Table
 */
class Payment_List extends WP_List_Table
{
	
	public function __construct() {

		parent::__construct( [
			'singular' => __( 'Pembayaran', 'alifakids' ),
			'plural'   => __( 'Pembayaran', 'alifakids' ),
			'ajax'     => false
		] );

	}

	public function get_payment_data($per_page = 10, $page_number = 1) {
		global $wpdb;

		$date_join = "AND month(p.period) = month(CURRENT_DATE) AND year(p.period) = year(CURRENT_DATE)";
		$date_search_key = current_time('Y-m-01 00:00:00');
		if ( ! empty(  $_REQUEST['s'] ) ) {
			$date_search_key = !empty( $_REQUEST['date'] ) ? wp_unslash( trim( $_REQUEST['date'] ) ) : '';

			if (! empty( $date_search_key )) {
				$date_join = "AND month(p.period) = month('$date_search_key') AND year(p.period) = year('$date_search_key')";
			} 
		}

// 		$sql = "SELECT 	s.student_id as student_id,
// 						s.name as name,
// 						s.number as number,
// 						s.branch_id as branch_id,
// 						b.name as branch_name,
// 						c.name as class_name,
// 						s.class_id as class_id,
// 						MAX(CASE WHEN p.payment_id IS NOT null THEN p.payment_id ELSE NULL END) AS payment_id,
// 						MAX(CASE WHEN p.period  IS NOT null THEN p.period  ELSE DATE_FORMAT('$date_search_key' ,'%Y-%m-01')  END) AS period,
// 						MAX(CASE WHEN p.status IS NOT null THEN p.status ELSE 0 END) AS status,
// 						MAX(CASE WHEN p.image IS NOT null THEN p.status ELSE 0 END) AS image

// 				FROM {$wpdb->prefix}students s 
// 				LEFT OUTER JOIN {$wpdb->prefix}payment p 
// 						ON s.student_id = p.student_id {$date_join}
// 				LEFT OUTER JOIN {$wpdb->prefix}branch b 
// 						ON s.branch_id = b.branch_id
// 				LEFT OUTER JOIN {$wpdb->prefix}class c 
// 						ON s.class_id = c.class_id
// 				LEFT JOIN {$wpdb->prefix}parents_students ps
// 						ON ps.student_id = s.student_id
// 				LEFT JOIN {$wpdb->prefix}users u
// 						ON u.ID = ps.parent_id
// 				WHERE month(u.user_registered) <= month('$date_search_key') and year(u.user_registered) <= year('$date_search_key')
// 		";
		
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
				LEFT JOIN {$wpdb->prefix}parents_students ps
						ON ps.student_id = s.student_id
				LEFT JOIN {$wpdb->prefix}users u
						ON u.ID = ps.parent_id
				
		";
		


		
	

		if ( ! empty(  $_REQUEST['s'] ) ) {
		    
			$name_search_key = !empty( $_REQUEST['name'] ) ? wp_unslash( trim( $_REQUEST['name'] ) ) : '';
			
			$class_search_key = !empty( $_REQUEST['class'] ) ? wp_unslash( trim( $_REQUEST['class'] ) ) : '';
			$branch_search_key = !empty( $_REQUEST['branch'] ) ? wp_unslash( trim( $_REQUEST['branch'] ) ) : '';
			$status_search_key = (isset( $_REQUEST['status']) && is_numeric($_REQUEST["status"]) ) ? wp_unslash( trim( $_REQUEST['status'] ) ) : '';
			


			$search_queries = array(
				"s.name" => $name_search_key,
				"s.class_id" => $class_search_key,
				"s.branch_id" => $branch_search_key

			);
			
// 			$search_queries = array(
// 				"{$wpdb->prefix}students.name" => $name_search_key,
// 				"{$wpdb->prefix}students.class_id" => $class_search_key ,
// 				"{$wpdb->prefix}branch.branch_id" => $branch_search_key 
// 			);
			
			
			

			if ( is_numeric($_REQUEST["status"]) && $status_search_key == 0) {
				$status_search_index = 'NOT EXISTS(SELECT * FROM ak_payment p2 WHERE p2.payment_id = p.payment_id )';
				$status_search_key = 1;
			} elseif (is_numeric($_REQUEST["status"]) &&  $status_search_key >= 1) {
				$status_search_index = 'p.status' ;
			}

			if (is_numeric($_REQUEST["status"]) && isset($_REQUEST["status"])) {
				$search_queries[$status_search_index] = $status_search_key;
			}

			
			$search_queries_i = 0;
			$search_queries_len = count($search_queries);
			

			// if all empty
			if ( array_filter($search_queries)  ) {

				foreach ($search_queries as $key => $value ) {

					if ($search_queries_i == 0) {
						//first
						$sql .= " AND ";
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

		$sql = $this->get_payment_data(-1);

		return count($wpdb->get_results( $sql, 'ARRAY_A' ));
	}

	public function get_columns() {
		$columns = [
			'cb'      => '<input type="checkbox" />',
			'name'    => __( 'Nama', 'alifakids' ),
			'branch_name'    => __( 'Cabang', 'alifakids' ),
			'class_name'    => __( 'Kelas', 'alifakids' ),
			'period'    => __( 'Bulan', 'alifakids' ),
			'status'    => __( 'Status', 'alifakids' )
		];

		return $columns;
	}

	public function get_sortable_columns() {
		$sortable_columns = array(
			'name' => array( 'name', true ),
			'branch_name' => array( 'branch_name', true ),
			'class_name' => array( 'class_name', true ),
			'status' => array( 'status', true )
		);

		return $sortable_columns;
	}

	public function no_items() {
		_e( 'No payment available.', 'alifakids' );
	}

	public function column_default( $item, $column_name ) {
		return $item[$column_name];
	}

	public function column_name( $item ) {

		$delete_nonce = wp_create_nonce( 'alifakids_delete_payment' );
		$confirm_nonce = wp_create_nonce( 'alifakids_confirm_payment' );

		$title = sprintf(
				'<a href="?page=payment_detail&payment_id=%s&student_id=%s&period=%s">%s</a>',
				$item['payment_id'],
				$item['student_id'],
				$item['period'],
				$item['name']
			);

		$actions['edit'] = sprintf(
				'<a href="?page=payment_detail&payment_id=%s&student_id=%s&period=%s">%s</a>',
				$item['payment_id'],
				$item['student_id'],
				$item['period'],
				__('Edit', 'alifakids')
			);

		if($item['payment_id']) {
			if ($item['status'] == 1) {
				$actions['confirm'] = sprintf(
					'<a href="?page=%s&action=%s&student_id=%s&period=%s&_wpnonce=%s">%s</a>',
					$_REQUEST['page'],
					'confirm',
					$item['student_id'],
					$item['period'],
					$confirm_nonce,
					__('Konfirmasi', 'alifakids')
	          	);
			} elseif($item['status'] == 2){
				$actions['unconfirm'] = sprintf(
					'<a href="?page=%s&action=%s&student_id=%s&period=%s&_wpnonce=%s">%s</a>',
					$_REQUEST['page'],
					'unconfirm',
					$item['student_id'],
					$item['period'],
					$confirm_nonce,
					__('Batal Konfirmasi', 'alifakids')
	          	);
			}

          	$actions['delete'] =sprintf(
				'<a href="?page=%s&action=%s&student_id=%s&period=%s&_wpnonce=%s">%s</a>',
				$_REQUEST['page'],
				'delete',
				$item['student_id'],
				$item['period'],
				$delete_nonce,
				__('Delete', 'alifakids')
          	);
		}

		return $title . $this->row_actions( $actions );
	}

	public function column_period( $item ) {
		return date_i18n("F Y", strtotime( $item['period'] ) );
	}

	public function column_status( $item ) {
		$result = '';

		if ($item['status'] == 1) {
			$result .= '<span class="badge badge-primary">Menunggu Konfirmasi</span>';
		} else if($item['status'] == 2) {
			$result .= '<span class="badge badge-success">Lunas</span>';
		} else {
			$result .= '<span class="badge badge-secondary">Belum Dibayar</span>';
		}

		return $result;

	}

	public function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['student_id']
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

		$per_page     = $this->get_items_per_page( 'payment_per_page', 20 );
		$current_page = $this->get_pagenum();
		$total_items  = $this->record_count();

		$this->set_pagination_args( [
			'total_items' => $total_items,
			'per_page'    => $per_page
		] );


		$sql = $this->get_payment_data( $per_page, $current_page );

		$sql .= " LIMIT $per_page";
		$sql .= ' OFFSET ' . ( $current_page - 1 ) * $per_page;

		$this->items = $wpdb->get_results( $sql, 'ARRAY_A' );
	}

	public function process_bulk_action() {

		//Detect when a bulk action is being triggered...
		if ( 'confirm' === $this->current_action() ) {

			// In our file that handles the request, verify the nonce.
			$nonce = esc_attr( $_REQUEST['_wpnonce'] );

			if ( ! wp_verify_nonce( $nonce, 'alifakids_confirm_payment' ) ) {
				die( 'Go get a life script kiddies' );
			}
			else {
				$this->confirm_payment( absint( $_GET['student_id'] ), $_GET['period'],2  );
			}

		} elseif ( 'unconfirm' === $this->current_action() ) {

			// In our file that handles the request, verify the nonce.
			$nonce = esc_attr( $_REQUEST['_wpnonce'] );

			if ( ! wp_verify_nonce( $nonce, 'alifakids_confirm_payment' ) ) {
				die( 'Go get a life script kiddies' );
			}
			else {
				$this->confirm_payment( absint( $_GET['student_id'] ), $_GET['period'],1  );
			}

		} elseif ( 'delete' === $this->current_action() ) {

			// In our file that handles the request, verify the nonce.
			$nonce = esc_attr( $_REQUEST['_wpnonce'] );

			if ( ! wp_verify_nonce( $nonce, 'alifakids_delete_payment' ) ) {
				die( 'Go get a life script kiddies' );
			}
			else {
				$this->delete_payment( absint( $_GET['student_id'] ), $_GET['period'] );
			}

		}
	}

	function confirm_payment($student_id, $period, $status) {
		global $wpdb;

		$wpdb->update(
			"{$wpdb->prefix}payment",
			array('status' => $status ),
			array(
				'student_id' => $student_id, 
				'period' => $period, 
			)
		);
	}

	function delete_payment($student_id, $period) {
		global $wpdb;

		$wpdb->delete(
			"{$wpdb->prefix}payment",
			array(
				'student_id' => $student_id, 
				'period' => $period, 
			)
		);
	}

	public function search_box( $text, $input_id ) {
        if ( empty( $_REQUEST['s'] ) && ! $this->has_items() ) {
            return;
        }
 		
 		$default = array(
	        'name'      => '',
	        'date'      => '',
	        'class'  => null,
	        'branch'  => null,
	        'status'  => null
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
			    name="name" 
			    placeholder="Nama"
			    value="<?php echo $item['name']; ?>" 
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
			<select 
			    type="search" 
			    id="class-search-input" 
			    name="status"
		    >
		    	<option value="">Status</option>
		    	<option value="0" <?php echo (is_numeric($item['status']) && $item['status']==0)? 'selected' : '' ?>>Belum Dibayar</option>
		    	<option value="1" <?php echo (is_numeric($item['status']) && $item['status']==1)? 'selected' : '' ?>>Menunggu Konfirmasi</option>
		    	<option value="2" <?php echo (is_numeric($item['status']) && $item['status']==2)? 'selected' : '' ?>>Lunas</option>
			</select>
	        <?php submit_button( $text, '', '', false, array( 'id' => 'search-submit' ) ); ?>
		</p>
        <?php
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

        $url = add_query_arg($url_arg,admin_url('admin-ajax.php?action=export_excel_payment'));

	    echo "<a class='button-primary' href='$url'>Export to Excel</a>";
	}

}