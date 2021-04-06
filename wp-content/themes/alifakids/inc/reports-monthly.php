<?php 

/**
 * ReportsMonthly Class
 */
class ReportsMonthly {
	public function getStudentMonthlyReport($student_id, $date = null) {
		global $wpdb;

		if ($date) {
			$date = $date;
		} else {
			$date = current_time( 'Y-m-01');
		}

		$sql = "SELECT 	rds.points_id,
						count(rds.points_id) as score,
						rdp.points_key,
						rdp.points_dimension

						FROM {$wpdb->prefix}reports_daily rd	
							INNER JOIN {$wpdb->prefix}reports_daily_score rds
						    	ON rd.report_id = rds.report_id
					    	INNER JOIN {$wpdb->prefix}reports_daily_points rdp
						    	ON rdp.points_id = rds.points_id
						WHERE student_id='$student_id' 
							AND month(date)=month('$date') 
						    AND year(date)=year('$date')
						    GROUP BY rds.points_id
						    HAVING COUNT(rds.score_value) > 0 
		"; 

		$result = $wpdb->get_results( $sql, 'ARRAY_A' );

		return $result; 

	}

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
						s.class_id  as class_id

				FROM {$wpdb->prefix}students s 
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
	        "all"       => __("<a class='btn btn-secondary m-r-xxs' href='".add_query_arg($url_arg,site_url('report-bulanan/'))."'>Semua</a>",'alifakids')
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
