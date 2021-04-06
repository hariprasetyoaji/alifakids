<?php

function getUserBranch() {
	global $wpdb;

	$user_id = get_current_user_id();

	if (current_user_can( 'teacher' )) {
		$column = $wpdb->get_results( $wpdb->prepare(
			"SELECT * FROM {$wpdb->prefix}usermeta um
					LEFT JOIN {$wpdb->prefix}branch b
						ON b.branch_id = um.meta_value
				WHERE um.user_id = '%s'
					AND um.meta_key = 'branch' ",
			$user_id
		));
	} elseif(current_user_can( 'parent' )) {
		$column = $wpdb->get_results( $wpdb->prepare(
			"SELECT * FROM {$wpdb->prefix}students s
					LEFT JOIN {$wpdb->prefix}parents_students ps
						ON s.student_id = ps.student_id
					LEFT JOIN {$wpdb->prefix}branch b
						ON s.branch_id = b.branch_id
				WHERE ps.parent_id = '%s' ",
			$user_id
		));
	}


	return $column;
}

function getBranchSelectOption() {
	global $wpdb;

	$sql = "SELECT * FROM {$wpdb->prefix}branch";

	$result = $wpdb->get_results( $sql ,ARRAY_A);

	return $result;
}

function getBranchName($id) {
	global $wpdb;

	$column = $wpdb->get_row( $wpdb->prepare(
		"SELECT * FROM {$wpdb->prefix}branch WHERE branch_id = '%s'",
		$id
	) );

	return $column->name;
}

class Branch_List extends WP_List_Table {

	public function __construct() {

		parent::__construct( [
			'singular' => __( 'Branch', 'alifakids' ),
			'plural'   => __( 'Branch', 'alifakids' ),
			'ajax'     => false
		] );

	}

	public function get_branch( $per_page = 20, $page_number = 1 ) {

		global $wpdb;

		$sql = "SELECT 	*
				FROM {$wpdb->prefix}branch 
		";

		if ( ! empty(  $_REQUEST['s'] ) ) {
			$name_search_key = !empty( $_REQUEST['name'] ) ? wp_unslash( trim( $_REQUEST['name'] ) ) : '';

			$search_queries = array(
				"{$wpdb->prefix}branch.name" => $name_search_key
			);


			$search_queries_i = 0;
			$search_queries_len = count($search_queries);

			// if all empty
			if ( array_filter($search_queries)  ) {

				foreach ($search_queries as $key => $value ) {

					if ($search_queries_i == 0) {
						//first
						$sql .= " WHERE ";
					}

					if ( ($search_queries_i == $search_queries_len - 1) OR ($search_queries_len == 1)  ) {
						//last or not only 1
						$sql .= $key." LIKE '" . esc_sql( "%".$value."%" ). "' ";
				    } else {
						$sql .= $key." LIKE '" . esc_sql( "%".$value."%" ). "' AND ";
				    }

			    	$search_queries_i++;
			    }
			    
			}

		}

		if ( ! empty( $_REQUEST['orderby'] ) ) {
			$sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
			$sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
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
			'cb'      => '<input type="checkbox" />',
			'name'    => __( 'Nama Cabang', 'alifakids' ),
			'address'    => __( 'Alamat', 'alifakids' ),
			'phone'    => __( 'Telepon', 'alifakids' ),
			'email'    => __( 'Email', 'alifakids' )
		];

		return $columns;
	}

	public function get_sortable_columns() {
		$sortable_columns = array(
			'name' => array( 'name', true ),
			'address' => array( 'address', true ),
			'phone' => array( 'phone', true ),
			'email' => array( 'email', true )
		);

		return $sortable_columns;
	}

	public function get_bulk_actions() {
		$actions = [
			'bulk-delete' => 'Delete'
		];

		return $actions;
	}

	public function no_items() {
		_e( 'No branch available.', 'alifakids' );
	}

	public function column_default( $item, $column_name ) {
		return $item[$column_name];
	}

	public function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['branch_id']
		);
	}

	public function column_name( $item ) {

		$delete_nonce = wp_create_nonce( 'alifakids_delete_branch' );

		$title = sprintf(
				'<a href="?page=branch_add&id=%s">%s</a>',
				$item['branch_id'],
				$item['name']
			);

		$actions = array(
            'edit' => sprintf(
				'<a href="?page=branch_add&id=%s">%s</a>',
				$item['branch_id'],
				__('Edit', 'alifakids')
			),

            'delete' => sprintf(
				'<a href="?page=%s&action=%s&branch=%s&_wpnonce=%s">%s</a>',
				$_REQUEST['page'],
				'delete',
				$item['branch_id'],
				$delete_nonce,
				__('Delete', 'alifakids')
          	),
        );

		return $title . $this->row_actions( $actions );
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
	        'name'      => ''
	    );

	    $item = shortcode_atts($default, $_REQUEST);

	    $classData = getClassSelectOption();
 
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
		    <input 
			    type="search" 
			    id="name-search-input" 
			    name="name" 
			    placeholder="Nama Cabang"
			    value="<?php echo $item['name']; ?>" 
		    />
	        <?php submit_button( $text, '', '', false, array( 'id' => 'search-submit' ) ); ?>
		</p>
        <?php
    }
}