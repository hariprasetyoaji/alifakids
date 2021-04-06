<?php 
  $term = get_query_var('term'); 
  //echo "<pre>".print_r($term)."</pre>"; 
  if (is_parent()) {
    $url_args = array(
      'class' => $_REQUEST['class'], 
      'student_id' => $_REQUEST['student_id'],
      'y' => $_REQUEST['y'], 
      'm' => $_REQUEST['month'], 
      'w' => $_REQUEST['week'], 
      'd' => $_REQUEST['d']
    ); 
  } else {
    $url_args = array(
      'class' => $_REQUEST['class'],
      'y' => $_REQUEST['y'], 
      'm' => $_REQUEST['month'], 
      'w' => $_REQUEST['week'], 
      'd' => $_REQUEST['d']
    ); 
  }
  $link = add_query_arg($url_args,get_term_link($term)); 
  $attachment_id = get_term_meta($term->term_id,'image',true);
  $attachment_url = wp_get_attachment_url($attachment_id);
?>
<div class="col-lg-6 col-xl-6 mb-4">
  <div class="card file photo">
    <?php 
      global $current_user;

      $user_roles = $current_user->roles;

     if ($attachment_url): ?>
      <a href="<?php echo $link ?>" class="card-header file-icon" style="background-image: url(<?php echo $attachment_url ?>);"></a>
    <?php else: ?>
       <a href="<?php echo $link ?> " class="card-header file-icon" style="background-image: url(<?php echo get_template_directory_uri().'/assets/images/calistung.jpg' ?> );"></a>
    <?php endif ?>
    <div class="card-body file-info">
      <a class="file-title" href="<?php echo $link ?>">
        <?php echo $term->name; ?>
      </a>
      <span class="file-size"><?php echo getClassNameByID(get_post_meta(get_the_ID(), 'class_id', true)) ?></span>
      <br/>
      <span class="file-date"><?php //echo get_the_date() ?></span>
    </div>
  </div>
</div>
