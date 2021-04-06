<?php

/*Remove Default Role*/
function alifakids_users_role()
{
	remove_role( 'subscriber' );
	remove_role( 'contributor' );
	remove_role( 'author' );

	add_role('teacher',
		__('Guru'),
		array(
			'read'			=> true,
			'create_posts'	=> true,
			'edit_posts'	=> true,
			'publish_posts' => true
		)
   	);

    add_role('editor',
        __('Editor'),
        array(
            'read'          => true,
            'create_posts'  => true,
            'edit_posts'    => true,
            'publish_posts' => true
        )
    );

   	add_role('parent',
		__('Orang Tua'),
		array(
			'read'			=> true
		)
   	);

}
add_action( 'after_setup_theme', 'alifakids_users_role' );

function ak_delete_user( $user_id ) {
    global $wpdb;
    $user_obj = get_userdata( $user_id );

    $id_user = $user_obj->ID;
	$user_roles = $user_obj->roles;

	if ( in_array( 'parent', $user_roles, true ) ) {
    	$q = $wpdb->query("DELETE FROM {$wpdb->prefix}parents_students WHERE parent_id = '".$id_user."'");
        if ($_REQUEST['action'] != 'bulk-delete') {
            add_action("deleted_user", function(){
                wp_redirect( admin_url('admin.php?page=parents&notice=delete_success') );
                exit;
            });
        }
	} else if ( in_array( 'teacher', $user_roles, true ) ) {
        if ($_REQUEST['action'] != 'bulk-delete') {
            add_action("deleted_user", function(){
                wp_redirect( admin_url('admin.php?page=teacher&notice=delete_success') );
                exit;
            });
        } 
    }
}
add_action( 'delete_user', 'ak_delete_user' );


function hide_all_parents_teachers( $u_query ) {
	$current_user = wp_get_current_user();
    global $wpdb;
    $u_query->query_where = str_replace(
        'WHERE 1=1', 
        "WHERE 1=1 AND {$wpdb->users}.ID IN (
            SELECT {$wpdb->usermeta}.user_id FROM $wpdb->usermeta 
                WHERE {$wpdb->usermeta}.meta_key = '{$wpdb->prefix}capabilities'
                AND {$wpdb->usermeta}.meta_value NOT LIKE '%parent%' AND {$wpdb->usermeta}.meta_value NOT LIKE '%teacher%')", 
        $u_query->query_where
    );
}
add_action('pre_user_query','hide_all_parents_teachers');

function hide_change_role_dropdown($all_roles) {
    global $pagenow;

    if( $pagenow == 'users.php' || $pagenow == 'user-new.php' || $pagenow == 'user-edit.php') {
        unset($all_roles['teacher']);
        unset($all_roles['parent']);
    }

    return $all_roles;
}
add_filter('editable_roles','hide_change_role_dropdown');

function is_parent() {
    global $current_user;

    if (in_array( 'parent' , $current_user->roles)) 
        return true;
    else
        return false;
}

function is_teacher() {
    global $current_user;

    if (in_array( 'teacher' , $current_user->roles)) 
        return true;
    else
        return false;
}