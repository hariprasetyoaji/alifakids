<?php 
  $link = '#';
  $url_args = array(
    /*'class' => $_REQUEST['class'], 
    'year' => $_REQUEST['y'], 
    'month' => $_REQUEST['m'], 
    'week' => $_REQUEST['w'], 
    'day' => $_REQUEST['d']*/
  );

  if (is_parent()) {
    $url_args['student_id'] = $_REQUEST['student_id'];
  }

  $link = add_query_arg( $url_args,get_the_permalink() );

  $lesson_level = get_post_meta(get_the_ID(), 'level', true);
  //var_dump($level);

  /*if (is_parent()) {
    $completed = false;
    //var_dump( $level );
    if ($level) {
      $completed = in_array($lesson_level, $level);
      $last_completed = max($level)+1;

      if($completed || $last_completed == $lesson_level ||  $lesson_level <= $last_completed ) {
       $link = get_the_permalink().'?student_id='.$_REQUEST['student_id'];
      }
    } else {
      if ($lesson_level == 1) {
       $link = get_the_permalink().'?student_id='.$_REQUEST['student_id'];
        $completed = false;
      }
    }
  } else {
    }*/
?>
<div class="col-lg-6 col-xl-6 mb-4 ">
  <div class="card file photo <?php //echo ( !is_parent() || ($completed || $last_completed == $lesson_level || $lesson_level == 1 ) ||  $lesson_level <= $last_completed ) ? '' : 'disabled' ; ?>">
    <?php 
    if (has_post_thumbnail()): ?>
      <a href="<?php echo $link; ?>" class="card-header file-icon" style="background-image: url('<?php echo get_the_post_thumbnail_url() ?>');"></a>
    <?php else: ?>
       <a href="<?php echo $link; ?>" class="card-header file-icon" style="background-image: url(<?php echo get_template_directory_uri().'/assets/images/calistung.jpg' ?> );"></a>
    <?php endif ?>
    <div class="card-body file-info">
      <a class="file-title" href="<?php echo $link; ?>">
        <?php echo get_the_title(); ?>
      </a>
      <span class="file-size">Level : <?php echo $lesson_level ?></span>
      <br/>
      <span class="file-date">
        <?php /*if (is_parent()): ?>
        <?php if ($completed ): ?>
          <span class="badge badge-pill badge-success">Selesai</span>
        <?php else: ?>
          <span class="badge badge-pill badge-warning">Belum Selesai</span>
        <?php endif ?>
        <?php endif*/ ?>
      </span>
    </div>
  </div>
</div>