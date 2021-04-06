<?php 

function getLearningCategoryByRole($role){
	if ( in_array( 'parent', $role, true ) ) {
       	return 'parent';
	} else if ( in_array( 'teacher', $role, true ) || in_array( 'administrator', $role, true )) {
       	return 'teacher,parent';
    }
    return;
}

function getCategoryIconBySlug($slug)
{
	if ($slug == 'parent') {
		$icon = 'supervised_user_circle';
	} else if ($slug == 'teacher'){
		$icon = 'school';
	} else {
		$icon = 'school';
	}

	return $icon;
}

function getTagColorBySlug($slug){
	if ($slug == 'lesson') {
		$color = 'primary';
	} else if ($slug == 'home-parenting'){
		$color = 'warning';
	} else {
		$color = 'secondary';
	}

	return $color;
}

function get_the_author_fullname() {
  $fname = get_the_author_meta('first_name');
  $lname = get_the_author_meta('last_name');
  $full_name = '';

  if( empty($fname)){
      $full_name = $lname;
  } elseif( empty( $lname )){
      $full_name = $fname;
  } else {
      //both first name and last name are present
      $full_name = "{$fname} {$lname}";
  }

  return $full_name;
}

function new_course_post_function() {
	if ( isset($_REQUEST['nonce']) 
      && wp_verify_nonce($_REQUEST['nonce'], 'new_course_post')
    ){
    global $flash;

    $default = array(
        'ID' => '',
        'post_title' => '',
        'class_id' => '',
        'lesson_id' => '',
        'level' => '',
        'post_content' => '',
        'video_url' => '',
        'attachment_id' => ''
    );
    $item = shortcode_atts($default, $_REQUEST);     

    if ($_REQUEST['ID'] == '') {
      global $user_ID;

      $new_post = array(
          'post_title' => $item['post_title'],
          'post_content' => $item['post_content'],
          'post_status' => 'publish',
          'post_date' => current_time('Y-m-d H:i:s'),
          'post_author' => $user_ID,
          'post_type' => 'course'
      );

      $post_id = wp_insert_post($new_post);

      update_post_meta( $post_id, 'class_id', $item['class_id'] );
      update_post_meta( $post_id, 'level', $item['level'] );
      update_post_meta( $post_id, 'video_url', $item['video_url'] );

      wp_set_post_terms( $post_id, array($item['lesson_id']), 'lesson', true );

    	set_post_thumbnail( $post_id, $item['attachment_id'] ); 

      $term = wp_get_post_terms( $post_id,'lesson' );


      $flash->add('success', 'Sukses menambah pelajaran baru.');

    	wp_redirect( site_url('/lesson/'.$term[0]->slug.'/?class='.$item['class_id'].'&notice=sucess') );
    } else {

      $post_id = $item['ID'];

      $args = array(
          'ID' => $item['ID'],
          'post_title' => $item['post_title'],
          'post_content' => $item['post_content']
      );
      $update = wp_update_post($args);

      update_post_meta( $post_id, 'class_id', $item['class_id'] );
      update_post_meta( $post_id, 'level', $item['level'] );
      update_post_meta( $post_id, 'video_url', $item['video_url'] );


      wp_set_post_terms( $post_id, array($item['lesson_id']), 'lesson', true );

      set_post_thumbnail( $post_id, $item['attachment_id'] ); 

      if ($update) {
        $flash->add('success', 'Sukses merubah pelajaran.');
        wp_redirect( site_url('/courses/post/?id='.$_REQUEST['ID']) );
      }

    }
  }

}
add_action( 'admin_post_new_course_post', 'new_course_post_function' );

function new_post_function() {

  if ( isset($_REQUEST['nonce']) 
      && wp_verify_nonce($_REQUEST['nonce'], 'new_post')
    ){

      global $flash;

      $default = array(
          'ID' => '',
          'post_title' => '',
          'post_content' => '',
          'post_category' => '',
          'attachment_id' => ''
      );
      $item = shortcode_atts($default, $_REQUEST);     

      if ($_REQUEST['ID'] == '') {
        
        $new_post = array(
            'post_title' => $item['post_title'],
            'post_content' => $item['post_content'],
            'post_status' => 'publish',
            'post_date' => current_time('Y-m-d H:i:s'),
            'post_author' => $user_ID,
            'post_type' => 'post'
        );

        $post_id = wp_insert_post($new_post);

        global $flash;

        if ($post_id) {
          wp_set_post_terms( $post_id, array($item['post_category']), 'category', true );

          set_post_thumbnail( $post_id, $item['attachment_id'] ); 
          $flash->add('success', 'Sukses menambah atikel baru.');
          wp_redirect( site_url('/learning/') );
        } else {
          $flash->add('danger', 'Gagal menambah atikel baru.');
          wp_redirect( site_url('/learning/post/?id='.$post_id) );
        }
      } else {
        $post_id = $item['ID'];

        $args = array(
            'ID' => $item['ID'],
            'post_title' => $item['post_title'],
            'post_content' => $item['post_content']
        );
        $update = wp_update_post($args);

        if ($update) {
          wp_set_post_terms( $post_id, array($item['post_category']), 'category', true );

          set_post_thumbnail( $post_id, $item['attachment_id'] ); 

          $flash->add('success', 'Sukses merubah atikel.');
          wp_redirect( site_url('/learning/post/?id='.$post_id) );
        } else {
          $flash->add('danger', 'Gagal merubah artikel.');
          wp_redirect( site_url('/learning/post/?id='.$post_id) );
        }

      }
    }

}
add_action( 'admin_post_new_post', 'new_post_function' );

function delete_lesson_function() {
  $post_id = $_REQUEST['id'];

  if ( isset($_REQUEST['nonce']) 
      && wp_verify_nonce($_REQUEST['nonce'], 'delete_lesson')
    ){
      global $flash;
      global $wpdb;

      $term = wp_get_post_terms( $post_id,'lesson' );
      $class = get_post_meta( $post_id, 'class_id', true );

      $wpdb->delete(
        "{$wpdb->prefix}course_report",
        [ 'course_id' => $post_id ],
        [ '%d' ]
      );

      $delete = wp_delete_post( $post_id, true );

      if ($delete) {
        $flash->add('success', 'Pelajaran berhasil dihapus.');
        wp_redirect( site_url('/lesson/'.$term[0]->slug ).'/?class='.$class);
      } else {
        $flash->add('warning', 'Gagal menghapus pelajaran.');
        wp_redirect( wp_get_referer() );
      }


  }
}
add_action( 'admin_post_delete_lesson', 'delete_lesson_function' );

function delete_posts_function() {
  $post_id = $_REQUEST['id'];

  if ( isset($_REQUEST['nonce']) 
      && wp_verify_nonce($_REQUEST['nonce'], 'delete_posts')
    ){
      global $flash;
      global $wpdb;

      $delete = wp_delete_post( $post_id, true );

      if ($delete) {
        $flash->add('success', 'Artikel berhasil dihapus.');
        wp_redirect( site_url('/learning/' ));
      } else {
        $flash->add('warning', 'Gagal menghapus Artikel.');
        wp_redirect( wp_get_referer() );
      }


  }
}
add_action( 'admin_post_delete_posts', 'delete_posts_function' );