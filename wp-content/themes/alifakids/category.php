<?php 
get_header();
$currCat = get_category(get_query_var('cat'));
$cat_name = $currCat->name;
$cat_id   = get_cat_ID( $cat_name );
?>
<div class="container">
  <div class="row">
    <div class="col-md-12">
      <div class="page-title">
      	<?php the_breadcrumb() ?>
        <h3><?php echo $currCat->name; ?></h3>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-3">
    	<?php get_template_part('page-parts/learning/content','sidebar') ?>
    </div>
    <div class="col-md-9">
      <div class="card card-transparent file-list recent-files">
        <div class="card-body">
          <div class="row">
          	<?php 
	          	$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

      				$post_args = array(
      					'post_type' => 'post',
      					'post_status' => 'publish',
      					'posts_per_page' => get_option( 'posts_per_page' ),
      					'paged' => $paged,
      					'cat' => $cat_id
      				);
	          	$post_query = new WP_Query( $post_args );
	          
	          	if ( $post_query->have_posts() ) : while ( $post_query->have_posts() ) : $post_query->the_post() 
	        ?>

	        	<?php get_template_part('page-parts/learning/content','page') ?>

	        <?php endwhile; ?> 
	        	<nav aria-label="Page navigation" class="col-md-12 pagination-with-number">
				<?php 
					echo paginate_links( array(
					    'total'        	=> $post_query->max_num_pages,
					    'current'      	=> max( 1, get_query_var( 'page' ) ),
					    'type' 			   => 'list'
					  ));
				?>
				</nav>
         	<?php else : ?>
            <?php get_template_part('page-parts/learning/content','none') ?>
         	<?php endif; wp_reset_query();?>
          
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php get_footer(); ?>