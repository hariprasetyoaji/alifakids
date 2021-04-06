<div class="col-lg-6 col-xl-6 mb-4">
  <div class="card file photo">
    <?php 
      global $current_user;

      $user_roles = $current_user->roles;

    ?>
    <a href="<?php echo get_the_permalink(); ?>" class="card-header file-icon" style="background-image: url(<?php echo get_the_post_thumbnail_url() ?>);"></a>
    <div class="card-body file-info">
      <a class="file-title" href="<?php echo get_the_permalink(); ?>">
        <?php echo get_the_title( ); ?>
      </a>
      <span class="file-size"><?php echo get_the_category( $id )[0]->name ?></span>
      <br/>
      <span class="file-date"><?php echo get_the_date() ?></span>
    </div>
  </div>
</div>