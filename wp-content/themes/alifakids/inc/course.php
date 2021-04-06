<?php

function create_course_posttype() {
	register_post_type( 'course',
		array(
	  		'labels' => array(
		   		'name' => __( 'Pelajaran' ),
		   		'singular_name' => __( 'Pelajaran' )
		  	),
		  	'supports' => array(
		   		'thumbnail',
		   		'editor',
		   		'title'
		  	),
		  	'public' => true,
		  	'show_ui' => true,
		  	'menu_icon' => 'dashicons-editor-ol',
		  	'menu_position' => 6,
		  	'has_archive' => false,
		  	'query_var' => true,
		  	'capability_type' => 'post',
            'hierarchical' => true,
            'publicly_queryable'  => true,
		  	'rewrite' => array('slug' => 'course','with_front' => false)
	 	)
	);

	$labels = array(
        'name' => _x( 'Mata Pelajaran', 'alifakids' ),
        'singular_name' =>_x( 'Mata Pelajaran', 'alifakids' ),
        'search_items' =>  __( 'Search Pelajaran' ),
        'all_items' => __( 'Semua Pelajaran' ),
        'parent_item' => null,
        'parent_item_colon' => null,
        'edit_item' => __( 'Edit Pelajaran' ),
        'update_item' => __( 'Update Pelajaran' ),
        'add_new_item' => __( 'Add New Pelajaran' ),
        'new_item_name' => __( 'New Pelajaran' )
        );
    register_taxonomy(  
    'lesson',  
    'course',  
        array(  
            'hierarchical' => false,  
            'label' => 'News Types',  
            'query_var' => true,  
            'label' => __('Mata Pelajaran'),
            'labels' => $labels,
            'hierarchical' => true,
            'show_ui' => true,
            'rewrite' => true  
        )  
    );  
}
add_action( 'init', 'create_course_posttype' );

add_action( 'pre_get_posts', 'course_sort_custom_column_query' );
function course_sort_custom_column_query( $query )
{
    $post_type = $query->get('post_type');

    if ( $post_type == 'course') {
    	
    	
    	$orderby = $query->get( 'orderby' );

    	if (!empty($orderby)) {
		    if ( 'class' == $orderby ) {
		        $meta_query = array(
		            array(
		                'key'=>'class_id'
		            )
		        );

		        $query->set( 'meta_query', $meta_query );
		        $query->set( 'orderby', 'meta_value' );
		        $query->set( 'order', $query->get( 'order' ) );
		    } elseif ( 'lesson' == $orderby ) {
		    	$taxonomies = array();

	            foreach (get_terms('lesson', array('order' => $query->get( 'order' ))) as $tax ) {
	                $taxonomies[] = $tax->name;
	            }


		        $tax_query = array(
		            array(
		                'taxonomy' => 'lesson',
				        'field' => 'name',
				        'terms' => $taxonomies
		            )
		        );

		        $query->set( 'tax_query', $tax_query );
		        $query->set( 'orderby', 'taxonomy' );
		        $query->set( 'order', $query->get( 'order' ) );
		    } else {
	    		/*$query->set('orderby', 'date');
	      		$query->set('order', 'DESC');*/

		    }
    	} 	
	}
}

// add_filter('page_row_actions','my_action_row', 10, 99);
// function my_action_row($actions, $post){
//     //check for your post type
//     if ($post->post_type =="course"){
//         unset( $actions['inline hide-if-no-js'] );
//         //$actions['inline'] = '';
//         $link = esc_url( admin_url('admin.php?page=course-report&id='.$post->ID));
//         $actions['detail'] = '<a href="'.$link.'">Laporan</a>';
//     }
//     return $actions;
// }

add_filter( 'manage_edit-course_columns', 'course_custom_post_column');
function course_custom_post_column( $column ) {
	unset($column['date']);
	// unset($column['title']);

    // $column['titles'] = 'Judul';
    $column['lesson'] = 'Mata Pelajaran';
    $column['class'] = 'Kelas';
    $column['year'] = 'Tahun';
    $column['month'] = 'Bulan';
    $column['week'] = 'Minggu';
    $column['day'] = 'Hari';

    return $column;
}

add_action( 'admin_head-edit.php', 'custom_css_js_so_14257172' );
function custom_css_js_so_14257172() 
{
    // Apply only in the correct CPT, otherwise it would print in Pages/Posts
    global $current_screen;
    if( 'course' != $current_screen->post_type)
        return;
    ?>
        <style>
            select[name="m"] { display:none }
            th#year{ width: 60px}
            th#month{ width: 70px}
            th#week{ width: 60px}
            th#day{ width: 60px}

        </style>
    <?php
}

add_action( 'manage_course_posts_custom_column', 'location_tax_column_info', 10, 2);
function location_tax_column_info( $column_name, $post_id ) {

    $taxonomy = $column_name;
    $post_type = get_post_type($post_id);
    $terms = get_the_terms($post_id, $taxonomy);

    // if ($column_name == 'titles') {
    	// echo "string";
    // }
	if ($column_name == 'lesson') {
	    if (!empty($terms) ) {
	        foreach ( $terms as $term )
	        $post_terms[] ="<a href='edit.php?post_type={$post_type}&{$taxonomy}={$term->slug}'> " .esc_html(sanitize_term_field('name', $term->name, $term->term_id, $taxonomy, 'edit')) . "</a>";
	        echo join('', $post_terms );
	    }
	} elseif ($column_name == 'class') {
		$class_id = get_post_meta( $post_id, 'class_id', true );

		if (!empty($class_id)) {
			echo "<a href='edit.php?post_type={$post_type}&{$column_name}={$class_id}'> " .esc_html(getClassName($class_id)) . "</a>";
			echo "<br>( Level : ";
			echo get_post_meta( $post_id, 'level', true );
			echo " )";
		}
	} elseif ($column_name == 'year') {
		$year = get_post_meta( $post_id, 'year', true );

		if (!empty($year)) {
			echo $year;
		}
	} elseif ($column_name == 'month') {
		$month = get_post_meta( $post_id, 'month', true );

		if (!empty($month)) {
			 $nama_bulan = array(
            	'1' => 'Januari',
            	'2' => 'Februari',
            	'3' => 'Maret',
            	'4' => 'April',
            	'5' => 'Mei',
            	'6' => 'Juni',
            	'7' => 'Juli',
            	'8' => 'Agustus',
            	'9' => 'September',
            	'10' => 'Oktober',
            	'11' => 'November',
            	'12' => 'Desember'
         	);
			echo $nama_bulan[$month];
		}
	} elseif ($column_name == 'week') {
		$week = get_post_meta( $post_id, 'week', true );

		if (!empty($week)) {
			echo $week;
		}
	} elseif ($column_name == 'day') {
		$day = get_post_meta( $post_id, 'day', true );

		if (!empty($day)) {
			$nama_hari = array(
            	'1' => 'Senin',
            	'2' => 'Selasa',
            	'3' => 'Rabu',
            	'4' => 'Kamis',
            	'5' => 'Jumat',
            	'6' => 'Sabtu',
            	'7' => 'Minggu'
         	);
			echo $nama_hari[$day];
		}
	}
}

add_filter( 'manage_edit-course_sortable_columns', 'course_sortable_custom_column' );
function course_sortable_custom_column( $columns ) {
    $columns['lesson'] = 'lesson';
    $columns['class'] = 'class';

    return $columns;
}

function course_meta_boxes() {
	add_meta_box(
	    'course_video', // $id
	    'Video Url', // $title
	    'post_course_video_meta_cb', // $callback
	    'course', // $screen
	    'normal', // $context
	    'high' // $priority
	);

    add_meta_box(
        'course_video', // $id
        'Video Url', // $title
        'post_course_video_meta_cb', // $callback
        'post', // $screen
        'normal', // $context
        'high' // $priority
    );

	add_meta_box(
	    'course_meta', // $id
	    'Pelajaran', // $title
	    'post_course_meta_cb', // $callback
	    'course', // $screen
	    'normal', // $context
	    'high' // $priority
	);
}
add_action( 'add_meta_boxes', 'course_meta_boxes' );

function post_course_video_meta_cb( $post ) {
    $values = get_post_custom( $post->ID );
    $text = isset( $values['my_meta_box_text'] ) ? esc_attr( $values['my_meta_box_text'][0] ) : '';
    wp_nonce_field( 'course_meta_box_nonce', 'meta_box_nonce' );

    $item['video_url'] = '';

    if ( null !== get_post_meta( $post->ID, 'video_url', true ) ) {
    	$item['video_url'] = esc_url( get_post_meta( $post->ID, 'video_url', true ) );
    }

    ?>
    <p class="post-attributes-label-wrapper category-add">
    	<label class="post-attributes-label" for="class_id">Video Url</label><br>
	 	<input type="text" name="video_url" value="<?php echo $item['video_url'] ?>">
    </p>

<?php
}

function post_course_meta_cb( $post ) {
    $values = get_post_custom( $post->ID );
    $text = isset( $values['my_meta_box_text'] ) ? esc_attr( $values['my_meta_box_text'][0] ) : '';
    wp_nonce_field( 'course_meta_box_nonce', 'meta_box_nonce' );

    $classData = getClassSelectOption();

    $item['class_id'] = '';
    $item['level'] = '';

    if ( null !== get_post_meta( $post->ID, 'class_id', true ) ) {
    	$item['class_id'] = get_post_meta( $post->ID, 'class_id', true );
    } 
    if ( null !== get_post_meta( $post->ID, 'level', true ) ) {
    	$item['level'] = get_post_meta( $post->ID, 'level', true );
    }

    if ( null !== get_post_meta( $post->ID, 'year', true ) ) {
    	$item['year'] = get_post_meta( $post->ID, 'year', true );
    }

    if ( null !== get_post_meta( $post->ID, 'month', true ) ) {
    	$item['month'] = get_post_meta( $post->ID, 'month', true );
    }

    if ( null !== get_post_meta( $post->ID, 'week', true ) ) {
    	$item['week'] = get_post_meta( $post->ID, 'week', true );
    }

    if ( null !== get_post_meta( $post->ID, 'day', true ) ) {
    	$item['day'] = get_post_meta( $post->ID, 'day', true );
    }

    ?>
    <p class="post-attributes-label-wrapper">
    	<label class="post-attributes-label" for="class_id">Kelas</label>
    </p>
    <select name="class_id" id="class_id">
    	<?php foreach ($classData as $value): ?>
    		<option 
    			value="<?php echo $value['class_id'] ?>" 
    			<?php echo ($item['class_id'] == $value['class_id']) ? 'selected' : '' ; ?> 
			><?php echo $value['name'] ?></option>
    	<?php endforeach ?>
    </select>

    <p class="post-attributes-label-wrapper">
    	<label class="post-attributes-label" for="class_id">Tahun / Bulan / Minggu / Hari</label>
    </p>
    <select name="year" id="year">
    	<?php $year = 2020; while ($year <= 2030): ?>
    		<option 
    			value="<?php echo $year ?>" 
    			<?php echo ($item['year'] == $year) ? 'selected' : '' ; ?> 
			><?php echo $year ?></option>
    	<?php $year++; endwhile; ?>
    </select>
    <select name="month" id="month">
    	<?php 
    		 $nama_bulan = array(
            	'1' => 'Januari',
            	'2' => 'Februari',
            	'3' => 'Maret',
            	'4' => 'April',
            	'5' => 'Mei',
            	'6' => 'Juni',
            	'7' => 'Juli',
            	'8' => 'Agustus',
            	'9' => 'September',
            	'10' => 'Oktober',
            	'11' => 'November',
            	'12' => 'Desember'
         	);
    		foreach ($nama_bulan as $key => $value) :
    	?>
    		<option 
    			value="<?php echo $key ?>" 
    			<?php echo ($item['month'] == $key) ? 'selected' : '' ; ?> 
			><?php echo $value ?></option>
    	<?php endforeach; ?>
    </select>
    <select name="week" id="week">
    	<?php $week = 1; while ($week <= 4): ?>
    		<option 
    			value="<?php echo $week ?>" 
    			<?php echo ($item['week'] == $week) ? 'selected' : '' ; ?> 
			><?php echo 'Minggu ke-'.$week ?></option>
    	<?php $week++; endwhile; ?>
    </select>
    <select name="day" id="day">
    	<?php 
    		 $nama_hari = array(
            	'1' => 'Senin',
            	'2' => 'Selasa',
            	'3' => 'Rabu',
            	'4' => 'Kamis',
            	'5' => 'Jumat',
            	'6' => 'Sabtu',
            	'7' => 'Minggu'
         	);
    		foreach ($nama_hari as $key => $value) :
    	?>
    		<option 
    			value="<?php echo $key ?>" 
    			<?php echo ($item['day'] == $key) ? 'selected' : '' ; ?> 
			><?php echo $value ?></option>
    	<?php endforeach; ?>
    </select>

    <p class="post-attributes-label-wrapper">
    	<label class="post-attributes-label" for="class_id">Level</label>
    </p>
    <select name="level" id="level">
    	<?php $level = 1; while ($level <= 100): ?>
    		<option 
    			value="<?php echo $level ?>" 
    			<?php echo ($item['level'] == $level) ? 'selected' : '' ; ?> 
			><?php echo $level ?></option>
    	<?php $level++; endwhile; ?>
    </select>
<?php
}

function save_post_course_meta( $post_id ) {   
    // verify nonce

    if ( !isset( $_POST['meta_box_nonce'] ) || !wp_verify_nonce( $_POST['meta_box_nonce'], 'course_meta_box_nonce' ) ) {
        return $post_id; 
    }
    // check autosave
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return $post_id;
    }
    update_post_meta( $post_id, 'video_url',  $_POST['video_url'] );
    
    // check permissions
    if ( 'course' === $_POST['post_type'] ) {
        if ( !current_user_can( 'edit_page', $post_id ) ) {
            return $post_id;
        } elseif ( !current_user_can( 'edit_post', $post_id ) ) {
            return $post_id;
        }  

        //exit(var_dump($_POST));

        //update_post_meta( $post_id, 'video_url',  $_POST['video_url'] );

        update_post_meta( $post_id, 'year',  $_POST['year'] );
        update_post_meta( $post_id, 'month',  $_POST['month'] );
        update_post_meta( $post_id, 'week',  $_POST['week'] );
        update_post_meta( $post_id, 'day',  $_POST['day'] );
	
	    global $wpdb;

	    /*$terms = get_the_terms($post_id, 'lesson');

	    foreach ($terms as $term) {
		    $levels = $wpdb->get_col($wpdb->prepare("
		    	SELECT meta_value
					FROM ak_postmeta pm
					left join ak_term_relationships tr
						ON pm.post_id = tr.object_id
					left join ak_posts p
						ON pm.post_id = p.ID
					WHERE tr.term_taxonomy_id = %d 
					AND (select meta_value from ak_postmeta where post_id = pm.post_id and meta_key = 'class_id') = %d
					AND (select meta_value from ak_postmeta where post_id = pm.post_id and meta_key = 'level') IS NOT null
					AND meta_key='level' AND p.post_status = 'publish'
		    	", $term->term_id ,$_POST['class_id'] ) 

			);
	    }*/


    	// echo "<pre>",print_r($_POST['level'],1),"</pre>";
    	// echo "<pre>",print_r($levels,1),"</pre>";
    	// exit();


	    /*if( in_array($_POST['level'], $levels)) {
	    	add_action( 'admin_notices', 'course_post_error' );
	    	return $post_id;
	    }*/

	    $old_class = get_post_meta( $post_id, 'class_id', true );
	    $new_class = $_POST['class_id'];
	    
	    $old_level = get_post_meta( $post_id, 'level', true );
	    $new_level = $_POST['level'];


	    if ($old_class == $new_class ||  $old_level == $new_level) {
	    	if( in_array($_POST['level'], $levels)) {
		    	add_action( 'admin_notices', 'course_post_error' );
		    	return $post_id;
		    }
	    }

	    if ( $new_class && $new_class !== $old_class ) {
	        update_post_meta( $post_id, 'class_id', $new_class );
	    } elseif ( '' === $new_class && $old_class ) {
	        delete_post_meta( $post_id, 'class_id', $old_class );
	    }


	    if ( $new_level && $new_level !== $old_level ) {
	        update_post_meta( $post_id, 'level', $new_level );
	    } elseif ( '' === $new_level && $old_level ) {
	        delete_post_meta( $post_id, 'level', $old_level );
	    }

        

    }
}
add_action( 'save_post', 'save_post_course_meta' ); 

function course_post_error() {
	?>
    <div class="notice notice-error">
        <p><?php _e( 'Level sudah ada di pelajaran dan kelas ini.', 'alifakids' ); ?></p>
    </div>
    <?php
}

function lesson_add_image( $term ) {
	
	?>
	<div class="form-field">
		<label for="taxImage"><?php _e( 'Gambar Utama', 'alifakids' ); ?></label>

	    <input id="upload_image" type="hidden" size="36" name="upload_image" value="" />
	    <img id="feaured_image" size="36" style="display: block;" />
		<input id="upload_image_button" type="button" class="button action" value="Upload Image" />
	</div>
<?php
}
add_action( 'lesson_add_form_fields', 'lesson_add_image', 10, 2 );

function lesson_edit_image( $term ) {
	?>
	<tr class="form-field term-slug-wrap">
		<th scope="row">
			<label for="slug">Gambar Utama</label>
		</th>
		<td>
			<input id="upload_image" type="hidden" size="36" name="upload_image" value="" />
	    	<img id="feaured_image" size="36" style="display: block;" />
			<input id="upload_image_button" type="button" class="button action" value="Upload Image" />
		</td>
	</tr>
<?php
}
add_action( 'lesson_edit_form_fields', 'lesson_edit_image', 10, 2 );

add_action( "lesson_add_form", "lesson_custom_script");
add_action( "lesson_edit_form", "lesson_custom_script");

function lesson_custom_script( $taxonomy ) { 
    ?>
    <style>
    	.term-description-wrap{display:none;}
    	.term-parent-wrap{display:none;}
    </style>
    <script>
    	jQuery(document).ready( function( $ ) {

		    $('#upload_image_button').click(function() {

		        formfield = $('#upload_image').attr('name');
		        tb_show( '', 'media-upload.php?type=image&amp;TB_iframe=true' );
		        window.send_to_editor = function(html) {
		        	var class_string    = jQuery( html ).attr( 'class' );
		            var image_url       = jQuery( html ).attr( 'src' );
		            var classes         = class_string.split( /\s+/ );
		            var image_id        = 0;

		            for ( var i = 0; i < classes.length; i++ ) {
		                var source = classes[i].match(/wp-image-([0-9]+)/);
		                if ( source && source.length > 1 ) {
		                    image_id = parseInt( source[1] );
		                }
		            }

		           $( '#feaured_image' ).attr('src', image_url);
		           $( '#upload_image' ).attr('value', image_id);
		           tb_remove();
		        }

		        return false;
		    });

		    $('#submit').click(function(event) {
		    	event.preventDefault();
		    	$( '#feaured_image' ).attr('src', '');
		        $( '#upload_image' ).attr('value', '');

		    });

		});
    </script>
    <?php
}

add_action ( 'edited_lesson', 'save_extra_lesson_fileds');
add_action ( 'create_lesson', 'save_extra_lesson_fileds');
function save_extra_lesson_fileds( $term_id ) {
    if ( isset( $_POST['upload_image'] ) ) {
        $t_id = $term_id;

        update_term_meta( $t_id, 'image', $_POST['upload_image'], false );
    }
}

function getStudentClassByParent($parent_id) {
	global $wpdb;

	$column = $wpdb->get_row( $wpdb->prepare(
		"SELECT GROUP_CONCAT(class_id) as class_id 
			FROM {$wpdb->prefix}parents_students as PS
			LEFT JOIN {$wpdb->prefix}students as S
				ON  PS.student_id = S.student_id
			WHERE parent_id = '%s'
			GROUP BY parent_id",
		$parent_id
	),ARRAY_A );

	return explode(',', $column['class_id']);;

}

function getClassNameByID($class_id) {
	global $wpdb;

	$column = $wpdb->get_row( $wpdb->prepare(
		"SELECT name 
			FROM {$wpdb->prefix}class as c
			WHERE class_id = '%d'",
		$class_id
	),ARRAY_A );

	return $column['name'];
}

function getUserCourseReport($post_id, $student_id = null){
	global $user_ID;
	global $wpdb;

	if ($student_id) {
		$column = $wpdb->get_results(
			"SELECT * 
				FROM {$wpdb->prefix}course_report as cr
				WHERE student_id='$student_id' AND course_id='$post_id'"
		,ARRAY_A );
	} else {
		$students = getParentStudents($user_ID);

		$students_id = array();
		foreach ($students as $students) {
			array_push($students_id, $students['student_id']);
		}

		$students_id = implode(",",$students_id);

		$column = $wpdb->get_results(
			"SELECT * 
				FROM {$wpdb->prefix}course_report as cr
				WHERE student_id IN ($students_id) AND course_id='$post_id'"
		,ARRAY_A );
	}



	if ($column) {
		return $column;
	} else {
		return false;
	}
}

function getUserCourseDayReport($student_id = null, $year, $month, $week, $day = null){
	global $user_ID;
	global $wpdb;

	if ($student_id) {
		if ($day) {
			$column = $wpdb->get_results(
				"SELECT * 
					FROM {$wpdb->prefix}course_report as cr
					WHERE student_id='$student_id' 
						AND year='$year'
						AND month='$month'
						AND week='$week'
						AND day='$day'
				",ARRAY_A 
			);
		} else {
			$column=[];
			$days = $wpdb->get_results(
				"SELECT * 
					FROM {$wpdb->prefix}course_report as cr
					WHERE student_id='$student_id' 
						AND year='$year'
						AND month='$month'
						AND week='$week'
				",ARRAY_A 
			);

			foreach ($days as $key => $value) {
				$column[] = $value['day'];
			}
		}
	} else {
		$students = getParentStudents($user_ID);

		$students_id = array();
		foreach ($students as $students) {
			array_push($students_id, $students['student_id']);
		}

		$students_id = implode(",",$students_id);

		$column = $wpdb->get_results(
			"SELECT * 
				FROM {$wpdb->prefix}course_report as cr
				WHERE student_id IN ($students_id) AND course_id='$post_id'"
		,ARRAY_A );
	}



	if ($column) {
		return $column;
	} else {
		return false;
	}
}

function getUserCourseDayReportTeacher($year, $month, $week, $day){
	global $user_ID;
	global $wpdb;

	$join_sql = '';
	if (is_teacher()) {
		$join_sql = "LEFT JOIN {$wpdb->prefix}usermeta u1 
							ON u1.meta_key='branch' AND u1.user_id = '$user_ID' 
						WHERE u1.meta_value = s.branch_id 
						AND cr.year='$year'
						AND cr.month='$month'
						AND cr.week='$week'
						AND cr.day='$day' ";
	} else {
		$join_sql = "WHERE cr.year='$year'
						AND cr.month='$month'
						AND cr.week='$week'
						AND cr.day='$day'";
	}

	$column = $wpdb->get_results(
		"SELECT s.student_id as student_id,
				s.name as name,
				s.number as number,
                s.branch_id as branch_id,
				s.class_id  as class_id,
				cr.date as date,
				cr.ID as ID,
				cr.course_id as course_id
			FROM {$wpdb->prefix}course_report as cr
				LEFT JOIN {$wpdb->prefix}students s 
					ON s.student_id = cr.student_id
				$join_sql"
	,ARRAY_A );

	if ($column) {
		return $column;
	} else {
		return false;
	}
}

function getUserCourseReportTeacher($post_id){
	global $user_ID;
	global $wpdb;

	$join_sql = '';
	if (is_teacher()) {
		$join_sql = "LEFT JOIN {$wpdb->prefix}usermeta u1 
							ON u1.meta_key='branch' AND u1.user_id = '$user_ID' 
						WHERE u1.meta_value = s.branch_id AND course_id='$post_id'";
	} else {
		$join_sql = "WHERE course_id='$post_id'";
	}

	$column = $wpdb->get_results(
		"SELECT s.student_id as student_id,
				s.name as name,
				s.number as number,
				s.branch_id as branch_id,
				s.class_id  as class_id,
				cr.ID as ID,
				cr.course_id as course_id
			FROM {$wpdb->prefix}course_report as cr
				LEFT JOIN {$wpdb->prefix}students s 
					ON s.student_id = cr.student_id
				$join_sql"
	,ARRAY_A );

	if ($column) {
		return $column;
	} else {
		return false;
	}
}

function getStudentLessonCompleted($term_id, $class_id, $student_id){
	global $user_ID;
	global $wpdb;

	$column = $wpdb->get_results(
		"SELECT meta_value as level FROM ak_postmeta as pm 
			LEFT JOIN ak_term_relationships as tr 
		    	ON pm.post_id = tr.object_id
		    RIGHT JOIN ak_course_report as cr
		    	ON cr.course_id = pm.post_id
		    WHERE pm.meta_key = 'level' 
		    AND tr.term_taxonomy_id = $term_id 
		    AND (SELECT meta_value FROM ak_postmeta WHERE pm.post_id = post_id AND meta_key = 'class_id' LIMIT 1) = $class_id
		    AND cr.student_id = $student_id"
	,ARRAY_A );

	if ($column) {
		$levels = array();
		foreach ($column as $level) {
			array_push($levels, $level['level']);
		}

		return $levels;
	} else {
		return false;
	}

}

function new_lesson_report_function() {
    global $user_ID;
    global $wpdb;
    global $flash;

		
	if ( $_FILES['attachment']['name'] == "" || 
         $_REQUEST['point_1'] == "" ||
         $_REQUEST['point_2'] == "" ||
         $_REQUEST['point_3'] == "" ||
         $_REQUEST['point_4'] == "" ||
         $_REQUEST['point_5'] == ""
     ) {
		$flash->add('danger', 'Konten & Foto pembelajaran adinda harus diisi.');

		wp_redirect( wp_get_referer() );
		die();
	} else {

    	$attachment = $_FILES['attachment'];
		$wordpress_upload_dir = wp_upload_dir();
	    $new_file_path = $wordpress_upload_dir['path'] . '/' . $attachment['name'];
	    $new_file_mime = mime_content_type( $attachment['tmp_name'] );

	}



    if( $attachment['size'] > wp_max_upload_size() )
		die( 'It is too large than expected.' );
 
	if( !in_array( $new_file_mime, get_allowed_mime_types() ) )
		die( 'WordPress doesn\'t allow this type of uploads.' );


    $table_name = $wpdb->prefix . 'course_report'; 

    $default = array(
        'course_id' => null,
        'year' => '',
        'month' => '',
        'week' => '',
        'day' => '',
        'student_id' => '',
        'point_1' => '',
        'point_2' => '',
        'point_3' => '',
        'point_4' => '',
        'point_5' => '',
        'date' => current_time('Y-m-d H:i:s')
    );

    $item = shortcode_atts($default, $_REQUEST);     
    
    $insert = $wpdb->insert($table_name,$item);

    if($insert) {
    	$course_id = $wpdb->insert_id;

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
				array('attachment' => $upload_id ), 
				array('ID' => $course_id)
			);

 
			require_once( ABSPATH . 'wp-admin/includes/image.php' );

    	}		
    }
    $args = array(
    	'class' => $_REQUEST['class'], 
    	'student_id' => $_REQUEST['student_id'], 
    	'y' => $_REQUEST['year'], 
    	'month' => $_REQUEST['month'], 
    	'week' => $_REQUEST['week'], 
    	'd' => $_REQUEST['day'], 
    );
  	wp_redirect( add_query_arg( $args, site_url('courses/') ) );

}
add_action( 'admin_post_new_lesson_report', 'new_lesson_report_function' );


add_action("wp_ajax_ajax_get_select_next_level", "ajax_get_select_next_level_cb");
function ajax_get_select_next_level_cb() {
	global $wpdb;

	$lesson_id = $_REQUEST['lesson_id'];
	$class_id = $_REQUEST['class_id'];

	$columns = $wpdb->get_results(
			"SELECT 
				(SELECT meta_value FROM ak_postmeta WHERE post_id = p.ID AND meta_key = 'level' LIMIT 1) as level
				FROM ak_posts p 
					LEFT JOIN ak_term_relationships tr 
				    	ON p.ID = tr.object_id
				    WHERE p.post_status = 'publish'
				    	AND tr.term_taxonomy_id = '$lesson_id'
				        AND (SELECT meta_value FROM ak_postmeta WHERE post_id = p.ID AND meta_key = 'class_id' LIMIT 1) = '$class_id'
				        GROUP BY p.ID
				        ORDER BY (SELECT meta_value FROM ak_postmeta WHERE post_id = p.ID AND meta_key = 'level' LIMIT 1) ASC"
	,ARRAY_A );

	if ($columns) {

		$level = array();
		foreach ($columns as $column) {
			$level[] = $column['level'];
		}

		$level_all = range(1,20);
		$result = array_diff($level_all, $level);


		echo json_encode(min($result));

	} else {
		echo json_encode(1);
	}


	wp_die();

}

add_action("wp_ajax_ajax_get_course_report_detail", "ajax_get_course_report_detail_cb");
function ajax_get_course_report_detail_cb() {
	global $wpdb;

	$student_id = $_REQUEST['student_id'];
	$report_id = $_REQUEST['report_id'];

	$report = $wpdb->get_row( $wpdb->prepare(
		"SELECT s.student_id as student_id,
				s.name as name,
				s.number as number,
				s.branch_id as branch_id,
				s.class_id  as class_id,
				cr.ID as ID,
				cr.course_id as course_id,
                cr.content as content,
                cr.point_1 as point_1,
                cr.point_2 as point_2,
                cr.point_3 as point_3,
                cr.point_4 as point_4,
                cr.point_5 as point_5,
                cr.date as date,
				cr.attachment as attachment,
				c.name as class_name,
				b.name as branch_name

			FROM {$wpdb->prefix}course_report as cr
				LEFT JOIN {$wpdb->prefix}students as s
					ON cr.student_id = s.student_id
				LEFT JOIN {$wpdb->prefix}branch as b
					ON b.branch_id = s.branch_id
				LEFT JOIN {$wpdb->prefix}class as c
					ON c.class_id = s.class_id
			WHERE cr.student_id = '%s' AND cr.ID = '%s'" ,
		$student_id,$report_id
	));
	if ($report) {
	?>
	<div class="row mb-1">
		<div class="col-12 text-left" >
			<table class="table">
				<tbody>
					<tr>
						<td class="text-left min"><strong>Nama Siswa : </strong></td>
						<td><?php echo $report->name. ' ('.$report->number.')' ?></td>
					</tr>
					<tr>
						<td class="text-left min"><strong>Cabang : </strong></td>
						<td><?php echo $report->branch_name ?></td>
					</tr>
					<tr>
						<td class="text-left min"><strong>Kelas : </strong></td>
						<td><?php echo $report->class_name ?></td>
					</tr>
                    <tr>
                        <td class="text-left min"><strong>Tanggal Laporan : </strong></td>
                        <td><?php echo date_i18n("l, d F Y H:i:s", strtotime( $report->date ) ); ?></td>
                    </tr>
					<tr>
						<td class="text-left" colspan="2">
							<strong>1. Pada bagian sesi mana Ananda menunjukkan antusias?</strong>
							<p><?php echo $report->point_1 ?></p>
						</td>
					</tr>
                    <tr>
                        <td class="text-left" colspan="2">
                            <strong>2. Apa hal yang sudah berjalan baik pada sesi kali ini?</strong>
                            <p><?php echo $report->point_2 ?></p>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-left" colspan="2">
                            <strong>3. Apa aksi perbaikan untuk membersamai Ananda di sesi berikutnya?</strong>
                            <p><?php echo $report->point_3 ?></p>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-left" colspan="2">
                            <strong>4. Bagian mana yang sangat dikuasai Ananda?</strong>
                            <p><?php echo $report->point_4 ?></p>
                        </td>
                    </tr>
                     <tr>
                        <td class="text-left" colspan="2">
                            <strong>5. Bagian mana yang menantang atau silit bagi Ananda?</strong>
                            <p><?php echo $report->point_5 ?></p>
                        </td>
                    </tr>
					<tr>
						<td class="text-left" colspan="2">
							<img class="img-fluid" src="<?php echo wp_get_attachment_url( $report->attachment, 'thumbnail'); ?>" />
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	<?php
	} else {
		return false;
	}
	wp_die();
}



add_action( 'restrict_manage_posts', function(){
    global $wpdb, $table_prefix;

    $post_type = (isset($_GET['post_type'])) ? $_GET['post_type'] : 'post';

    if ($post_type == 'course'){
    	$classData = getClassSelectOption();

        $values = array();
        $query_years = $wpdb->get_results("SELECT pm.meta_value as year 
                from ".$table_prefix."posts as p
                LEFT JOIN ".$table_prefix."postmeta as pm
                    ON pm.post_id = p.ID               
                where pm.meta_key = 'year'
                AND p.post_type='".$post_type."'
                group by pm.meta_value
                order by pm.meta_value");

        foreach ($query_years as &$data){
            $values['year'][$data->year] = $data->year;
        }

        $query_month = $wpdb->get_results("SELECT pm.meta_value as month 
                from ".$table_prefix."posts as p
                LEFT JOIN ".$table_prefix."postmeta as pm
                    ON pm.post_id = p.ID               
                where pm.meta_key = 'month'
                AND p.post_type='".$post_type."'
                group by pm.meta_value
                order by pm.meta_value");
        foreach ($query_month as &$data){
            $values['month'][$data->month] = $data->month;
        }

        ?>
        <select name="class">
	    	<option value="">Kelas</option>

	    	<?php 
	    		$current_class = isset($_GET['class'])? $_GET['class'] : '';
	    		foreach ($classData as $value): 
    		?>
	    		<option 
	    			value="<?php echo $value['class_id'] ?>" 
	    			<?php echo ($current_class == $value['class_id']) ? 'selected' : '' ; ?> 
    			>
    				<?php echo $value['name'] ?>
				</option>
	    	<?php endforeach ?>
		</select>

		<?php 
		$current_vl = isset($_GET['lesson'])? $_GET['lesson'] : '';
		$args_lesson = array(
	        'show_option_all'    => 'Pelajaran',
	        'show_option_none'   => '',
	        'orderby'            => 'ID',
	        'order'              => 'ASC',
	        'show_count'         => 0,
	        'hide_empty'         => 0,
	        'child_of'           => 0,
	        'echo'               => 1,
	        'selected'           => $current_vl,
	        'hierarchical'       => 1,
	        'name'               => 'lesson',
	        'id'                 => '',
	        'class'              => 'postform',
	        'depth'              => 1,
	        'tab_index'          => 0,
	        'taxonomy'           => 'lesson',
	        'hide_if_empty'      => false,
	             ); ?>
		<?php wp_dropdown_categories( $args_lesson ); ?>
        <select name="year">
			<option value="">Tahun</option>
                <?php 
                $current_vy = isset($_GET['year'])? $_GET['year'] : '';
                foreach ($values['year'] as $label => $value) {
                    printf(
                        '<option value="%s"%s>%s</option>',
                        $value,
                        $value == $current_vy? ' selected="selected"':'',
                        $label
                    );
                }
                ?>
        </select>
        <select name="month">
			<option value="">Bulan</option>
                <?php 
                $nama_bulan = array(
                	'1' => 'Januari',
                	'2' => 'Februari',
                	'3' => 'Maret',
                	'4' => 'April',
                	'5' => 'Mei',
                	'6' => 'Juni',
                	'7' => 'Juli',
                	'8' => 'Agustus',
                	'9' => 'September',
                	'10' => 'Oktober',
                	'11' => 'November',
                	'12' => 'Desember'
                 );
                $current_vm = isset($_GET['month'])? $_GET['month'] : '';
                foreach ($values['month'] as $label => $value) {
                	$label = $nama_bulan[$label];
                    printf(
                        '<option value="%s"%s>%s</option>',
                        $value,
                        $value == $current_vm? ' selected="selected"':'',
                        $label
                    );
                }
                ?>
        </select>
        <select name="week">
			<option value="">Minggu</option>
            <?php 
            	$current_vw = isset($_GET['week'])? $_GET['week'] : '';
            ?>
            <option value="1" <?php echo ($current_vw == 1) ? 'selected="selected"' : '' ; ?>>1</option>
            <option value="2" <?php echo ($current_vw == 2) ? 'selected="selected"' : '' ; ?>>2</option>
            <option value="3" <?php echo ($current_vw == 3) ? 'selected="selected"' : '' ; ?>>3</option>
            <option value="4" <?php echo ($current_vw == 4) ? 'selected="selected"' : '' ; ?>>4</option>
        </select>
        <select name="day">
			<option value="">Hari</option>
            <?php 
            	$current_vd = isset($_GET['day'])? $_GET['day'] : '';
            ?>
            <option value="1" <?php echo ($current_vd == 1) ? 'selected="selected"' : '' ; ?>>Senin</option>
            <option value="2" <?php echo ($current_vd == 2) ? 'selected="selected"' : '' ; ?>>Selasa</option>
            <option value="3" <?php echo ($current_vd == 3) ? 'selected="selected"' : '' ; ?>>Rabu</option>
            <option value="4" <?php echo ($current_vd == 4) ? 'selected="selected"' : '' ; ?>>Kamis</option>
            <option value="5" <?php echo ($current_vd == 4) ? 'selected="selected"' : '' ; ?>>Jumat</option>
            <option value="6" <?php echo ($current_vd == 4) ? 'selected="selected"' : '' ; ?>>Sabtu</option>
            <option value="7" <?php echo ($current_vd == 4) ? 'selected="selected"' : '' ; ?>>Minggu</option>
        </select>
        <?php
    }
});



add_filter( 'parse_query', 'prefix_parse_filter' );
function  prefix_parse_filter($query) {
   global $pagenow;
   $current_page = isset( $_GET['post_type'] ) ? $_GET['post_type'] : '';

   $s_class = isset( $_GET['class'] ) ? $_GET['class'] : '';
   $s_lesson = isset( $_GET['lesson'] ) ? $_GET['lesson'] : '';
   $s_year = isset( $_GET['year'] ) ? $_GET['year'] : '';
   $s_month = isset( $_GET['month'] ) ? $_GET['month'] : '';
   $s_week = isset( $_GET['week'] ) ? $_GET['week'] : '';
   $s_day = isset( $_GET['day'] ) ? $_GET['day'] : '';

   if ( is_admin() &&  $pagenow=='edit.php' &&
     'course' == $current_page ) {


	   	if (!empty($s_class)) {
	   		$query->query_vars['meta_key'] = 'class_id';
        	$query->query_vars['meta_value'] = $s_class;
    		$query->query_vars['meta_compare'] = '=';
	   	}

	   	if (!empty($s_year)) {
	   		$query->query_vars['meta_key'] = 'year';
        	$query->query_vars['meta_value'] = $s_year;
    		$query->query_vars['meta_compare'] = '=';
	   	}

   		if (!empty($s_month)) {
	   		$query->query_vars['meta_key'] = 'month';
        	$query->query_vars['meta_value'] = $s_month;
    		$query->query_vars['meta_compare'] = '=';
	   	}

   		if (!empty($s_week)) {
	   		$query->query_vars['meta_key'] = 'week';
        	$query->query_vars['meta_value'] = $s_week;
    		$query->query_vars['meta_compare'] = '=';
	   	}

   		if (!empty($s_day)) {
	   		$query->query_vars['meta_key'] = 'day';
        	$query->query_vars['meta_value'] = $s_day;
    		$query->query_vars['meta_compare'] = '=';
	   	}
        
        if (!empty($s_lesson)) {
       		if ( $term = get_term_by( 'id', $s_lesson, 'lesson' ) ) {
                $query->query_vars['lesson'] = $term->slug;
            }
        }
  }

  return $query;
}


add_action( 'manage_posts_extra_tablenav', 'admin_order_list_top_bar_button', 20, 1 );
function admin_order_list_top_bar_button( $which ) {
    global $typenow;

    if ( 'course' === $typenow  ) {
        $class = (isset($_REQUEST['class'])) ? $_REQUEST['class'] : '';
        $lesson = (isset($_REQUEST['lesson'])) ? $_REQUEST['lesson'] : '';
        $year = (isset($_REQUEST['year'])) ? $_REQUEST['year'] : '';
        $month = (isset($_REQUEST['month'])) ? $_REQUEST['month'] : '';
        $week = (isset($_REQUEST['week'])) ? $_REQUEST['week'] : '';
        $day = (isset($_REQUEST['day'])) ? $_REQUEST['day'] : '';

        $url_args = array(
            'page' => 'course-report', 
            'class' => $class, 
            'lesson' => $lesson, 
            'year' => $year, 
            'month' => $month, 
            'week' => $week, 
            'day' => $day
        );
        ?>
        <div class="alignleft actions custom">
            <a href="<?php echo add_query_arg( $url_args, admin_url('admin.php') ); ?>" class="button-primary">
                Laporan
            </a>
        </div>
        <?php
    }
}