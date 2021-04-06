<?php 
get_header();
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
		<?php
			/* Start the Loop */
			while ( have_posts() ) :
			the_post();
		?>

		<?php get_template_part( 'page-parts/learning/content' ); ?>

		<?php endwhile; ?>
    </div>
  </div>
</div>
</div>


<?php get_footer(); ?>