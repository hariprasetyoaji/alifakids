<?php 
global $flash;
global $current_user; 
?>

<div class="row">
    <div class="col-xl-12">
      <?php echo $flash->show(); ?>
      <div class="profile-cover"></div>
      <div class="profile-header">
        <div class="profile-img">
          <img src="<?php echo get_avatar_url(get_current_user_id(),'150') ?>"/>
        </div>
        <div class="profile-name">
          <h3><?php echo $current_user->first_name.' '. $current_user->last_name ?></h3>
          <h5>
            <?php 

            $user_roles = $current_user->roles;

              if ( in_array( 'parent', $user_roles, true ) ) {
                  echo "Orang Tua";
              } else if ( in_array( 'teacher', $user_roles, true ) ) {
                  echo "Guru";     
              } else if ( in_array( 'administrator', $user_roles, true ) ) {
                  echo "Admin";     
              }
            ?>
              
          </h5>
        </div>
        <div class="profile-header-menu">
          <a href="<?php echo site_url('profile'); ?>" class="btn btn-warning m-t-xs">Edit Profil</a>
        </div>
      </div>
    </div>
  </div>