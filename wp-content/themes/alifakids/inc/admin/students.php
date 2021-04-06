<?php

function getAcademicYear()
{
	$years = range( 2015,date("Y") );
	krsort($years);
	return $years; 
}

function getStudentNumber($id)
{
	global $wpdb;

	$column = $wpdb->get_row( $wpdb->prepare(
		"SELECT * FROM {$wpdb->prefix}students WHERE student_id = '%s'",
		$id
	) );

	return $column->number;
}

function getStudentByID($id)
{
	global $wpdb;

	$column = $wpdb->get_row( $wpdb->prepare(
		"SELECT * FROM {$wpdb->prefix}students 
				WHERE student_id = '%s'",
		$id
	) );

	return $column;
}

function getStudentByName($name)
{
	global $wpdb;

	$column = $wpdb->get_row( $wpdb->prepare(
				"SELECT * FROM {$wpdb->prefix}students as S
				WHERE name = '%s'",
				$name
		),ARRAY_A );

	return $column;
}


function checkStudentIDExists($id, $number)
{
	global $wpdb;

	$column = $wpdb->get_results( $wpdb->prepare(
		"SELECT * FROM {$wpdb->prefix}students WHERE number = '%s' AND student_id != '%s'",
		$number,
		$id
	) );

	if ( ! empty( $column ) ) {
		return true;
	}

	return false;
}

function ajax_select_students_callback(){
    // we will pass post IDs and titles to this array
	$return = array();
 
	global $wpdb;

	$sql = "SELECT 	student_id,
					name,
					number
				FROM {$wpdb->prefix}students
	";

	if ( ! empty(  $_REQUEST['q'] ) ) {
		$sql .= " WHERE name LIKE '" . esc_sql( "%".$_REQUEST['q']."%" ). "' ";
		$sql .= " OR number LIKE '" . esc_sql( "%".$_REQUEST['q']."%" ). "' ";
	}


	// you can use WP_Query, query_posts() or get_posts() here - it doesn't matter
	$result = $wpdb->get_results( $sql, 'ARRAY_A' );
	
	if( !empty($result) ) {
		foreach ($result as $value) {
			$return[] = array( $value['student_id'], $value['name'].' ('.$value['number'].')');
		}
	}
	echo json_encode( $return );
	wp_die();
}
add_action( 'wp_ajax_select_students', 'ajax_select_students_callback' );

class Students_List extends WP_List_Table {

	public function __construct() {

		parent::__construct( [
			'singular' => __( 'Siswa', 'alifakids' ),
			'plural'   => __( 'Siswa', 'alifakids' ),
			'ajax'     => false
		] );

	}

	public function get_students( $per_page = 20, $page_number = 1 ) {

		global $wpdb;

		$sql = "SELECT 	{$wpdb->prefix}students.student_id as student_id,
						{$wpdb->prefix}students.name as name,
						{$wpdb->prefix}students.number as number,
						{$wpdb->prefix}students.year as year,
						{$wpdb->prefix}students.gender as gender,
						{$wpdb->prefix}students.child_number as child_number,
						{$wpdb->prefix}students.birth_place as birth_place,
						{$wpdb->prefix}students.birth_date as birth_date,
						{$wpdb->prefix}students.blood_type as blood_type,
						{$wpdb->prefix}students.address as address,
						{$wpdb->prefix}students.religion as religion,
						{$wpdb->prefix}students.hobby as hobby,
						{$wpdb->prefix}branch.branch_id as branch_id,
						{$wpdb->prefix}branch.name as branch_name,
						{$wpdb->prefix}class.name as class_name

				FROM {$wpdb->prefix}students 
			    LEFT JOIN {$wpdb->prefix}branch
			    	ON {$wpdb->prefix}students.branch_id = {$wpdb->prefix}branch.branch_id
		    	LEFT JOIN {$wpdb->prefix}class
			    	ON {$wpdb->prefix}students.class_id = {$wpdb->prefix}class.class_id
		";

		if ( ! empty(  $_REQUEST['s'] ) ) {
			$name_search_key = !empty( $_REQUEST['name'] ) ? wp_unslash( trim( $_REQUEST['name'] ) ) : '';
			$id_search_key = !empty( $_REQUEST['number'] ) ? wp_unslash( trim( $_REQUEST['number'] ) ) : '';
			$class_search_key = !empty( $_REQUEST['class_id'] ) ? wp_unslash( trim( $_REQUEST['class_id'] ) ) : '';
			$branch_search_key = !empty( $_REQUEST['branch_id'] ) ? wp_unslash( trim( $_REQUEST['branch_id'] ) ) : '';

			$search_queries = array(
				"{$wpdb->prefix}students.name" => $name_search_key,
				"{$wpdb->prefix}students.number" => $id_search_key,
				"{$wpdb->prefix}students.class_id" => $class_search_key ,
				"{$wpdb->prefix}branch.branch_id" => $branch_search_key 
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
						if ($key == "{$wpdb->prefix}branch.branch_id" && $value != '') {
							$sql .= $key." = '" . esc_sql( $value ). "' ";
						} else {
							$sql .= $key." LIKE '" . esc_sql( "%".$value."%" ). "' ";
						}
				    } else {
				    	if ($key == "{$wpdb->prefix}branch.branch_id" && $value != '') {
							$sql .= $key." = '" . esc_sql( $value ). "' AND ";
						} else {
							$sql .= $key." LIKE '" . esc_sql( "%".$value."%" ). "' AND ";
						}
				    }

			    	$search_queries_i++;
			    }
			    
			}

		}

		if ( ! empty( $_REQUEST['orderby'] ) ) {
			$sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
			$sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
		}

		return $sql;
	}

	public function record_count( $item = null ) {
		global $wpdb;

		$sql = $this->get_students(-1);

		return count($wpdb->get_results( $sql, 'ARRAY_A' ));
	}

	public function get_columns() {
		$columns = [
			'cb'      => '<input type="checkbox" />',
			'name'    => __( 'Nama', 'alifakids' ),
			'number'    => __( 'Nomor Induk', 'alifakids' ),
			'branch_name'    => __( 'Cabang', 'alifakids' ),
			'class_name'    => __( 'Kelas', 'alifakids' ),
			'year' => __( 'Tahun Ajaran', 'alifakids' )
		];

		return $columns;
	}

	public function get_sortable_columns() {
		$sortable_columns = array(
			'name' => array( 'name', true ),
			'number' => array( 'student_id', true ),
			'branch_name' => array( 'branch_name', true ),
			'class_name' => array( 'class_name', true )
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
		_e( 'No Students available.', 'alifakids' );
	}

	public function column_default( $item, $column_name ) {
		return $item[$column_name];
	}

	public function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['student_id']
		);
	}

	public function column_name( $item ) {

		$delete_nonce = wp_create_nonce( 'alifakids_delete_students' );

		$title = sprintf(
				'<a href="?page=students_add&student_id=%s">%s</a>',
				$item['student_id'],
				$item['name']
			);

		$actions = array(
            'edit' => sprintf(
				'<a href="?page=students_add&student_id=%s">%s</a>',
				$item['student_id'],
				__('Edit', 'alifakids')
			),

            'delete' => sprintf(
				'<a href="?page=%s&action=%s&students=%s&_wpnonce=%s">%s</a>',
				$_REQUEST['page'],
				'delete',
				$item['student_id'],
				$delete_nonce,
				__('Delete', 'alifakids')
          	),
        );

		return $title . $this->row_actions( $actions );
	}

	public function delete_students( $id ) {
		global $wpdb;

		$wpdb->delete(
			"{$wpdb->prefix}students",
			[ 'student_id' => $id ],
			[ '%d' ]
		);

		$wpdb->delete(
			"{$wpdb->prefix}parents_students",
			[ 'student_id' => $id ],
			[ '%d' ]
		);

		$wpdb->delete(
			"{$wpdb->prefix}reports_daily",
			[ 'student_id' => $id ],
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

		$per_page     = $this->get_items_per_page( 'students_per_page', 20 );
		$current_page = $this->get_pagenum();
		$total_items  = $this->record_count();

		$this->set_pagination_args( [
			'total_items' => $total_items,
			'per_page'    => $per_page
		] );


		$sql = $this->get_students( $per_page, $current_page );

		$sql .= " LIMIT $per_page";
		$sql .= ' OFFSET ' . ( $current_page - 1 ) * $per_page;

		$this->items = $wpdb->get_results( $sql, 'ARRAY_A' );
	}

	public function process_bulk_action() {

		//Detect when a bulk action is being triggered...
		if ( 'delete' === $this->current_action() ) {

			// In our file that handles the request, verify the nonce.
			$nonce = esc_attr( $_REQUEST['_wpnonce'] );

			if ( ! wp_verify_nonce( $nonce, 'alifakids_delete_students' ) ) {
				die( 'Go get a life script kiddies' );
			}
			else {
				$this->delete_students( absint( $_GET['students'] ) );
			}

		}

		// If the delete bulk action is triggered
		elseif ( ( isset( $_GET['action'] ) && $_GET['action'] == 'bulk-delete' )
		     || ( isset( $_GET['action2'] ) && $_GET['action2'] == 'bulk-delete' )
		) {

			$delete_ids = esc_sql( $_GET['bulk-delete'] );

			// loop over the array of record IDs and delete them
			foreach ( $delete_ids as $id ) {
				$this->delete_students( $id );

			}
		}
	}

	public function search_box( $text, $input_id ) {
        if ( empty( $_REQUEST['s'] ) && ! $this->has_items() ) {
            return;
        }
 		
 		$default = array(
	        'number'=> '',
	        'name'      => '',
	        'class_id'  => null,
	        'branch_id'  => null
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
		    <input 
			    type="search" 
			    id="name-search-input" 
			    name="name" 
			    placeholder="Nama"
			    value="<?php echo $item['name']; ?>" 
		    />
			<input 
			    type="search" 
			    id="id-search-input" 
			    name="number" 
			    placeholder="Nomor Induk"
			    value="<?php echo $item['number']; ?>" 
		    />
		    <select 
			    type="search" 
			    id="branch-search-input" 
			    name="branch_id"
		    >
	    		<option value="">Cabang</option>
		    	<?php foreach ($branchData as $value): ?>
		    		<option 
		    			value="<?php echo $value['branch_id'] ?>" 
		    			<?php echo ($item['branch_id'] == $value['branch_id']) ? 'selected' : '' ; ?> 
	    			>
	    				<?php echo $value['name'] ?>
    				</option>
		    	<?php endforeach ?>
			</select>
			<select 
			    type="search" 
			    id="class-search-input" 
			    name="class_id"
		    >
		    	<option value="">Kelas</option>
		    	<?php foreach ($classData as $value): ?>
		    		<option 
		    			value="<?php echo $value['class_id'] ?>" 
		    			<?php echo ($item['class_id'] == $value['class_id']) ? 'selected' : '' ; ?> 
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