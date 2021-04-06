<?php

function isTeacherCoordinator($user_id = null) {
	if($user_id == null) return false;

	$user_obj = get_userdata( $user_id );

    $id_user = $user_obj->ID;
	$user_roles = $user_obj->roles;

	if ( in_array( 'teacher', $user_roles, true ) && in_array( 'editor', $user_roles, true ) ) {
        return true;
    } else {
    	return false;
    }

}


class Teachers_List extends WP_Users_List_Table {

	public function __construct( $args = array() ) {
        parent::__construct(
            array(
                'singular' => 'teacher',
                'plural'   => 'teachers'            )
        );
 
        if ( $this->is_site_users ) {
            $this->site_id = isset( $_REQUEST['id'] ) ? intval( $_REQUEST['id'] ) : 0;
        }
    }

    public function get_parents( $per_page = 20, $page_number = 1 ) {

		global $wpdb;

		$sql = "SELECT 	u.ID as ID,
					    u.user_login AS user_login,
					    u.user_pass AS user_pass,
					    u.user_email AS user_email,
					    (select meta_value from {$wpdb->prefix}usermeta where user_id = u.id and meta_key = 'first_name' limit 1) as first_name,
						(select meta_value from {$wpdb->prefix}usermeta where user_id = u.id and meta_key = 'last_name' limit 1) as last_name,
						(select meta_value from {$wpdb->prefix}usermeta where user_id = u.id and meta_key = 'address' limit 1) as address,
						(select meta_value from {$wpdb->prefix}usermeta where user_id = u.id and meta_key = 'phone' limit 1) as phone,
						(select meta_value from {$wpdb->prefix}usermeta where user_id = u.id and meta_key = 'branch' limit 1) as branch,
						b.branch_id,
						b.name as branch_name

				FROM {$wpdb->prefix}users as u

		    	LEFT JOIN {$wpdb->prefix}branch AS b
			    	ON (select meta_value from {$wpdb->prefix}usermeta where user_id = u.id and meta_key = 'branch' limit 1) = b.branch_id

			    WHERE (select meta_value from {$wpdb->prefix}usermeta where user_id = u.id and meta_key = 'ak_capabilities' limit 1) LIKE '%teacher%' 
		";

		if ( ! empty(  $_REQUEST['s'] ) ) {
			$name_search_key = !empty( $_REQUEST['name'] ) ? wp_unslash( trim( $_REQUEST['name'] ) ) : '';
			$branch_search_key = !empty( $_REQUEST['branch'] ) ? wp_unslash( trim( $_REQUEST['branch'] ) ) : '';
			$coordinator_search_key = !empty( $_REQUEST['coordinator'] ) ? 'editor'  : '';

			$search_queries = array(
				"CONCAT( (select meta_value from {$wpdb->prefix}usermeta where user_id = u.id and meta_key = 'first_name' limit 1),' ', (select meta_value from {$wpdb->prefix}usermeta where user_id = u.id and meta_key = 'last_name' limit 1) )" => $name_search_key,
				"(select meta_value from {$wpdb->prefix}usermeta where user_id = u.id and meta_key = 'ak_capabilities' limit 1)" => $coordinator_search_key,
				"b.branch_id" => $branch_search_key 
			);


			$search_queries_i = 0;
			$search_queries_len = count($search_queries);

			// if all empty
			if ( array_filter($search_queries)  ) {
				$sql .= " AND ";
				foreach ($search_queries as $key => $value ) {

					if ( ($search_queries_i == $search_queries_len - 1) OR ($search_queries_len == 1)  ) {
						if ($key == 's.name') {
							$sql .= '('.$key." LIKE '" . esc_sql( "%".$value."%" ). "' OR ";
							$sql .= "s.number LIKE '" . esc_sql( "%".$value."%" ). "') ";
						} else if ($key == "b.branch_id" && $value != '') {
							$sql .= $key." = '" . esc_sql( $value ). "' ";
						} else {
							$sql .= $key." LIKE '" . esc_sql( "%".$value."%" ). "' ";
						}
						//last or not only 1
				    } else {
						if ($key == 's.name') {
							$sql .= '('.$key." LIKE '" . esc_sql( "%".$value."%" ). "' OR ";
							$sql .= "s.number LIKE '" . esc_sql( "%".$value."%" ). "') AND ";
						} else if ($key == "b.branch_id" && $value != '') {
							$sql .= $key." = '" . esc_sql( $value ). "' AND ";
						}else {
							$sql .= $key." LIKE '" . esc_sql( "%".$value."%" ). "' AND ";
						}
				    }

			    	$search_queries_i++;
			    }
			    
			}

		}

		if ( ! empty( $_REQUEST['orderby'] ) ) {
			switch ($_REQUEST['orderby']) {
				case 'name':
					$sql .= ' ORDER BY first_name';
					$sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
					$sql .= ', last_name';
					$sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
					break;
				
				default:
					$sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
					$sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
					break;
			}

		}

		return $sql;
	}

	public function record_count( $item = null ) {
		global $wpdb;

		$sql = $this->get_parents(-1);

		return count($wpdb->get_results( $sql, 'ARRAY_A' ));
	}

	public function display_rows() {
        // Query the post counts for this page
        if ( ! $this->is_site_users ) {
            $post_counts = count_many_users_posts( array_keys( $this->items ) );
        }
 
        foreach ( $this->items as $user_object ) {
            echo "\n\t" . $this->single_row( $user_object, '', '', '');
        }
    }

 	public function single_row( $user_object, $style = '', $role = '', $numposts = 0 ) {
		/*if ( ! ( $user_object instanceof WP_User ) ) {
	        $user_object = get_userdata( (int) $user_object );
	    }*/

	    $email = $user_object['user_email'];
	 
	    if ( $this->is_site_users ) {
	        $url = "site-users.php?id={$this->site_id}&amp;";
	    } else {
	        $url = 'users.php?';
	    }
	 
	    //$user_roles = $this->get_role_list( $user_object );
	 
	    // Set up the hover actions for this user
	    $actions     = array();
	    $checkbox    = '';
	    $super_admin = '';

	 
	    // Check if the user for this row is editable
	    if ( current_user_can( 'list_users' ) ) {
	        // Set up the user editing link
	        $edit_link = esc_url( '?page=teacher_add&id='.$user_object['ID'].'' );
	 		
	 		if(isTeacherCoordinator($user_object['ID'])){
	 			$coordinator_text = ' <small><i>(Koordinator)</i></small>';
	 		} else {
	 			$coordinator_text = '';
	 		}

	        if ( current_user_can( 'edit_user', $user_object['ID'] ) ) {
	        	if ( $user_object['first_name'] && $user_object['last_name'] ) {
                    $user_fullname = $user_object['first_name']." ".$user_object['last_name'];
                } elseif ( $user_object['first_name'] ) {
                    $user_fullname = $user_object['first_name'];
                } elseif ( $user_object['last_name'] ) {
                    $user_fullname = $user_object['last_name'];
                } else {
                    $user_fullname = sprintf(
                        '<span aria-hidden="true">&#8212;</span><span class="screen-reader-text">%s</span>',
                        _x( 'Unknown', 'name' )
                    );
                }

	            $edit            = "<strong><a href=\"{$edit_link}\">{$user_fullname}</a>{$super_admin}</strong>{$coordinator_text}<br />";
	            $actions['edit'] = '<a href="' . $edit_link . '">' . __( 'Edit' ) . '</a>';
	        } else {
	            $edit = "<strong>{$user_object['user_login']}{$super_admin}</strong><br />";
	        }
	 
	        if ( get_current_user_id() != $user_object['ID'] && current_user_can( 'delete_user', $user_object['ID'] ) ) {
	            $actions['delete'] = "<a class='submitdelete' href='" . wp_nonce_url( "users.php?action=delete&amp;user=".$user_object['ID']."", 'bulk-users' ) . "'>" . __( 'Delete' ) . '</a>';
	        }
	 
	        /**
	         * Filters the action links displayed under each user in the Users list table.
	         *
	         * @since 2.8.0
	         *
	         * @param string[] $actions     An array of action links to be displayed.
	         *                              Default 'Edit', 'Delete' for single site, and
	         *                              'Edit', 'Remove' for Multisite.
	         * @param WP_User  $user_object WP_User object for the currently listed user.
	         */
	        $actions = apply_filters( 'user_row_actions', $actions, $user_object );
	 
	        // Role classes.
	        //$role_classes = esc_attr( implode( ' ', array_keys( $user_roles ) ) );
	 
	        // Set up the checkbox ( because the user is editable, otherwise it's empty )
	        $checkbox = sprintf(
	            '<label class="screen-reader-text" for="user_%1$s">%2$s</label>' .
	            '<input type="checkbox" name="users[]" id="user_%1$s"  value="%1$s" />',
	            $user_object['ID'],
	            /* translators: %s: User login. */
	            sprintf( __( 'Select %s' ), $user_object['user_login'] )
	        );
	 
	    } else {
	        $edit = "<strong>{$user_object['user_login']}{$super_admin}</strong>";
	    }
	 
	    $avatar = get_avatar( $user_object['ID'], 32 );

	    $students_data = getParentStudents($user_object['ID']);
	 	$students_data_i = 0;
	    $students_data_len = count($students_data);
	    $students = '';

	    foreach ($students_data	 as $value) {
	    	if ( ($students_data_i == $students_data_len - 1) OR ($students_data_len == 1)  ) {
				//last or not only 1
	    		$students .= $value['name'].' ('.($value['number']).')';
		    } else {
	    		$students .= $value['name'].' ('.($value['number']).')<br>';
		    }


	    }

	 
	    $r = "<tr id='user-".$user_object['ID']."'>";
	 
	    list( $columns, $hidden, $sortable, $primary ) = $this->get_column_info();
	 
	    foreach ( $columns as $column_name => $column_display_name ) {
	        $classes = "$column_name column-$column_name";
	        if ( $primary === $column_name ) {
	            $classes .= ' has-row-actions column-primary';
	        }
	        if ( 'posts' === $column_name ) {
	            $classes .= ' num'; // Special case for that column
	        }
	 
	        if ( in_array( $column_name, $hidden ) ) {
	            $classes .= ' hidden';
	        }
	 
	        $data = 'data-colname="' . wp_strip_all_tags( $column_display_name ) . '"';
	 
	        $attributes = "class='$classes' $data";
	 
	        if ( 'cb' === $column_name ) {
	            $r .= "<th scope='row' class='check-column'>$checkbox</th>";
	        } else {
	            $r .= "<td $attributes>";
	            switch ( $column_name ) {
	                case 'fullname':
	                    $r .= "$avatar $edit";
	                    
	                    break;
	                case 'user_email':
	                    $r .= "<a href='" . esc_url( "mailto:$email" ) . "'>$email</a>";
	                    break;
                    case 'students':
	                    $r .= $students;
	                    break;
	                default:
	                    /**
	                     * Filters the display output of custom columns in the Users list table.
	                     *
	                     * @since 2.8.0
	                     *
	                     * @param string $output      Custom column output. Default empty.
	                     * @param string $column_name Column name.
	                     * @param int    $user_id     ID of the currently-listed user.
	                     */
	                    $r .= $user_object[$column_name];
	            }
	 
	            if ( $primary === $column_name ) {
	                $r .= $this->row_actions( $actions );
	            }
	            $r .= '</td>';
	        }
	    }
	    $r .= '</tr>';
	 
	    return $r;
	}

	function extra_tablenav( $which ) {

	}

	public function get_columns() {
		$columns = [
			'cb'      => '<input type="checkbox" />',
			'fullname'    => __( 'Nama', 'alifakids' ),
			'user_email'    => __( 'Email', 'alifakids' ),
			'branch_name'    => __( 'Cabang', 'alifakids' ),
			'phone'    => __( 'Telepon', 'alifakids' )
		];

		return $columns;
	}

	public function get_sortable_columns() {
	    $c = array(
	        'fullname' => array( 'name', true ),
	        'branch_name'    => array( 'branch', true ),
	        'user_email'    => array( 'user_email', true )
	    );
	 
	    return $c;
	}

	public function no_items() {
		_e( 'No Teachers available.', 'alifakids' );
	}

	public function column_default( $item, $column_name ) {
		return $item[$column_name];
	}

	public function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['ID']
		);
	}


	public function get_column_info() {
	    if ( isset( $this->_column_headers ) && is_array( $this->_column_headers ) ) {
	        $column_headers = array( array(), array(), array(), $this->get_primary_column_name() );
	        foreach ( $this->_column_headers as $key => $value ) {
	            $column_headers[ $key ] = $value;
	        }
	 
	        return $column_headers;
	    }

	    $columns = $this->get_columns();
	    $hidden = array();
	    $sortable = $this->get_sortable_columns();
	 
	    $primary               = $this->get_primary_column_name();
	    $this->_column_headers = array( $columns, $hidden, $sortable, $primary );
	 
	    return $this->_column_headers;
	}

	function get_default_primary_column_name() {
	    return 'fullname';
	}

	public function prepare_items() {
		global $wpdb;
		
		$_SERVER['REQUEST_URI'] = remove_query_arg( '_wp_http_referer', $_SERVER['REQUEST_URI'] );

		$columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
		$this->_column_headers = array($columns, $hidden, $sortable);

		$this->process_bulk_action();

		$per_page     = $this->get_items_per_page( 'parents_per_page', 20 );
		$current_page = $this->get_pagenum();
		$total_items  = $this->record_count();

		$this->set_pagination_args( [
			'total_items' => $total_items,
			'per_page'    => $per_page
		] );


		$sql = $this->get_parents( $per_page, $current_page );

		$sql .= " LIMIT $per_page";
		$sql .= ' OFFSET ' . ( $current_page - 1 ) * $per_page;

		$this->items = $wpdb->get_results( $sql, 'ARRAY_A' );
	}

	public function get_bulk_actions() {
		$actions = [
			'bulk-delete' => 'Delete'
		];

		return $actions;
	}

	public function process_bulk_action() {

		//Detect when a bulk action is being triggered...
		if ( 'delete' === $this->current_action() ) {

			// In our file that handles the request, verify the nonce.
			$nonce = esc_attr( $_REQUEST['_wpnonce'] );

			if ( ! wp_verify_nonce( $nonce, 'bulk-users' ) ) {
				die( 'Go get a life script kiddies' );
			}
			else {
				$this->delete_teacher( absint( $_GET['user'] ) );
			}

		}

		// If the delete bulk action is triggered
		elseif ( ( isset( $_GET['action'] ) && $_GET['action'] == 'bulk-delete' )
		     || ( isset( $_GET['action2'] ) && $_GET['action2'] == 'bulk-delete' )
		) {

			$delete_ids = esc_sql( $_GET['users'] );

			// loop over the array of record IDs and delete them
			foreach ( $delete_ids as $id ) {
				$this->delete_teacher( $id );
			}

			redirect(admin_url('admin.php?page=teacher&notice=delete_success'));
		}
	}

	public function delete_teacher( $id ) {
		wp_delete_user( $id );
	}

	public function search_box( $text, $input_id ) {
        if ( empty( $_REQUEST['s'] ) && ! $this->has_items() ) {
            return;
        }
 		
 		$default = array(
	        'name'=> '',
	        'coordinator'      => '',
	        'branch'      => ''
	    );

	    $item = shortcode_atts($default, $_REQUEST);

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
		    <select 
			    type="search" 
			    id="coordinator-search-input" 
			    name="coordinator"
		    >
	    		<option value="" <?php echo ($item['coordinator'] == '') ? 'selected' : '' ; ?> >Semua Guru</option>
	    		<option value="1" <?php echo ($item['coordinator'] == 1) ? 'selected' : '' ; ?> >Guru Koordinator</option>
			</select>
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
	        <?php submit_button( $text, '', '', false, array( 'id' => 'search-submit' ) ); ?>
		</p>
        <?php
    }
}