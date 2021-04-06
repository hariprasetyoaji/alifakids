<?php 


//Parent admin form
function edit_profile_function() {
	if ( isset($_REQUEST['nonce']) 
    	&& wp_verify_nonce($_REQUEST['nonce'], 'edit_profile')
    ) {
    	global $wpdb;
    	global $flash;
    	global $user_ID;

    	$id = $user_ID;

    	$user_info = get_userdata($id);
		$user_email = $user_info->user_email;
    	if ( email_exists($_REQUEST['user_email']) && $user_email != $_REQUEST['user_email']) {
    		
    		$flash->add('warning', 'Email telah terdaftar.');
    		wp_redirect( wp_get_referer() );
    		
    		die();
    	} 

    	$user_array = array (
	        'ID' 			=> $id,
	        'user_email'    =>  $_REQUEST['user_email'],
	        'first_name'    =>  $_REQUEST['first_name'],
	        'last_name'     =>  $_REQUEST['last_name']
	    );
	    $update = wp_update_user( $user_array );
	    if (!empty($_REQUEST['password'])) {
	    	wp_set_password( $_REQUEST['password'], $id );
	    }

    	if (current_user_can( 'parent' )) {

		    update_user_meta( $id, 'birth_place', $_REQUEST['birth_place']);
	    	update_user_meta( $id, 'birth_date', $_REQUEST['birth_date']);
	    	update_user_meta( $id, 'address', $_REQUEST['address']);
	    	update_user_meta( $id, 'religion', $_REQUEST['religion']);
	    	update_user_meta( $id, 'education', $_REQUEST['education']);
	    	update_user_meta( $id, 'occupation', $_REQUEST['occupation']);
	    	update_user_meta( $id, 'office_address', $_REQUEST['office_address']);
	    	update_user_meta( $id, 'social_media', $_REQUEST['social_media']);
	    	update_user_meta( $id, 'phone', $_REQUEST['phone']);

		   
	    } elseif (current_user_can( 'teacher' )) {
	    	update_user_meta( $id, 'birth_place', $_REQUEST['birth_place']);
	    	update_user_meta( $id, 'birth_date', $_REQUEST['birth_date']);
	    	update_user_meta( $id, 'address', $_REQUEST['address']);
	    	update_user_meta( $id, 'religion', $_REQUEST['religion']);
	    	update_user_meta( $id, 'education', $_REQUEST['education']);
	    	update_user_meta( $id, 'social_media', $_REQUEST['social_media']);
	    	update_user_meta( $id, 'phone', $_REQUEST['phone']);
	    }

	    if($update){ 
    		$flash->add('success', 'Profil Berhasil dirubah.');
    		wp_redirect( wp_get_referer() );
    		die();
	    } else {
    		$flash->add('warning', 'Gagal merubah profil.');
    		wp_redirect( wp_get_referer() );
    		die();
	    }

    }

}
add_action( 'admin_post_edit_profile', 'edit_profile_function');