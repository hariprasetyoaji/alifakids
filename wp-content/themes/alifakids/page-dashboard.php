<?php 
/* Template Name: Dashboard */
get_header();
?>
<div class="container">
  <?php get_template_part( '/page-parts/dashboard', 'top' ); ?>
  <div class="row">
    
    <div class="col-md-4">
      <?php get_template_part( '/page-parts/dashboard', 'left' ); ?>
    </div>
    <div class="col-md-8">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Learning</h5>
          <div class="story-list">
            <?php 
              $paged = ( get_query_var( 'page' ) ) ? get_query_var( 'page' ) : 1;
              
              $categories = getLearningCategoryByRole($current_user->roles);

              $post_args = array(
                'post_type' => 'post',
                'post_status' => 'publish',
                'posts_per_page' => 4,
                'category_name' => $categories
              );
              $post_query = new WP_Query( $post_args );
            
              if ( $post_query->have_posts() ) : while ( $post_query->have_posts() ) : $post_query->the_post() ;

              $author_id = get_the_author_meta();

          ?>
            <div class="story">
              <a href="<?php echo get_the_permalink(); ?>">
                <img src="<?php echo get_the_post_thumbnail_url()?>" alt=""/>
              </a>
              <div class="story-info">
                <a href="<?php echo get_the_permalink(); ?>">
                  <span class="story-author"><?php echo get_the_title() ?></span>
                </a>
                <span class="story-time"><?php echo get_the_author_fullname() ?></span>
              </div>
              <div class="story-right">
                <a href="<?php echo get_the_permalink(); ?>" class="btn btn-warning m-t-xs">Lihat</a>
              </div>
            </div>
          <?php endwhile; ?> 
          <?php endif; wp_reset_query();?>
            
            
          </div>
          <hr/>
          <div class="w-100 mt-3 text-center">
            <a href="<?php echo site_url('learning'); ?>" type="button" class="btn btn-default">Lihat Semua</a>
          </div>
        </div>
      </div>
      <?php if (is_parent()): ?>
          <?php get_template_part( '/page-parts/dashboard-report', 'parent' ); ?>
      <?php else: ?>
          <?php get_template_part( '/page-parts/dashboard-report', 'teacher' ); ?>
      <?php endif ?>

      
    </div>
  </div>
</div>
<?php get_footer(); ?>