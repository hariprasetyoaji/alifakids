<?php 

/**
 * Class Course_Report_List
 */
class Course_Report_List extends WP_List_Table {
	
	public function __construct() {

		parent::__construct( [
			'singular' => __( 'Branch', 'alifakids' ),
			'plural'   => __( 'Branch', 'alifakids' ),
			'ajax'     => false
		] );
	}

	public function get_course_report( $per_page = 20, $page_number = 1 ) {

		global $wpdb;

		$sql = "SELECT 	p.post_title,
						p.ID

				FROM {$wpdb->prefix}posts as p
					LEFT JOIN {$wpdb->prefix}postmeta as pm
						ON p.ID = pm.post_id
					WHERE p.post_type = 'course' AND p.post_status = 'publish'
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

		$sql .= 'GROUP BY p.ID';

		return $sql;
	}

	public function record_count( $item = null ) {
		global $wpdb;

		$sql = $this->get_course_report(-1);

		return count($wpdb->get_results($sql));
	}

	public function get_columns() {
		$columns = [
			'post_title'    => __( 'Pelajaran', 'alifakids' )
		];

		return $columns;
	}

	public function get_sortable_columns() {
		$sortable_columns = array(
			'post_title' => array( 'post_title', true )
		);

		return $sortable_columns;
	}

	public function no_items() {
		_e( 'No branch available.', 'alifakids' );
	}

	public function column_default( $item, $column_name ) {
		return $item[$column_name];
	}
	public function column_name( $item ) {

		$title = sprintf(
				'<a href="?page=course_detail&id=%s">%s</a>',
				$item['student_id'],
				$item['name']
			);

		$actions = array(
            'view' => sprintf(
				'<a href="?page=course_detail&id=%s">%s</a>',
				$item['student_id'],
				__('View', 'alifakids')
			)
        );

		return $title . $this->row_actions( $actions );
	}

	public function prepare_items() {
		global $wpdb;
		$_SERVER['REQUEST_URI'] = remove_query_arg( '_wp_http_referer', $_SERVER['REQUEST_URI'] );

		$columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
		$this->_column_headers = array($columns, $hidden, $sortable);

		$this->process_bulk_action();

		$per_page     = $this->get_items_per_page( 'course_per_page', 20 );
		$current_page = $this->get_pagenum();
		$total_items  = $this->record_count();

		$this->set_pagination_args( [
			'total_items' => $total_items,
			'per_page'    => $per_page
		] );

		$sql = $this->get_course_report( $per_page, $current_page );

		$sql .= " LIMIT $per_page";
		$sql .= ' OFFSET ' . ( $current_page - 1 ) * $per_page;


		$this->items = $wpdb->get_results( $sql, 'ARRAY_A' );
	}

	public function search_box( $text, $input_id ) {
        if ( empty( $_REQUEST['s'] ) && ! $this->has_items() ) {
            return;
        }
 		
 		$default = array(
	        'n'      => '',
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