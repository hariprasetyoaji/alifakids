<?php 
get_header();

/*$reports = '';
if (is_parent()) {
  $reports = getUserCourseReport(get_the_ID(), $_REQUEST['student_id']);
  set_query_var( 'reports', $reports );
}*/
?>
<div class="container">
  <div class="row">
    <div class="col-md-12">
      <div class="page-title">
      	<?php the_breadcrumb() ?>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-3">
    	<?php get_template_part('page-parts/learning/content','sidebar') ?>
    </div>
    <div class="col-md-9">
      <?php echo $flash->show() ?>
      <div class="card card-transparent file-list recent-files">
        <div class="card-body">
          <div class="row">
            <div class="col-md-12">
        		<?php
        			/* Start the Loop */
        			while ( have_posts() ) :
        			the_post();
        		?>

        		<?php get_template_part( 'page-parts/course/content' ); ?>

        		<?php endwhile; ?>  
            </div>
          </div>
        </div>
      </div>

      <?php /*if (is_parent()): ?>
        <?php get_template_part( 'page-parts/course/report' ); ?>
      <?php else: ?>
        <?php get_template_part( 'page-parts/course/report-teacher' ); ?>
      <?php endif*/ ?>

    </div>
  </div>
</div>
</div>


<?php get_footer(); ?>