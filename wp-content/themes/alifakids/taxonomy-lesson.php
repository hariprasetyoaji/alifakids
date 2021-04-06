<?php 
global $user_ID;
get_header();

$level = '';
// if (is_parent()) {
//   $students = getParentStudents($user_ID);

//   $term_id = get_queried_object()->term_id;
//   $class_id = $_REQUEST['class'];
//   $student_id = $_REQUEST['student_id'];

//   $level = getStudentLessonCompleted($term_id, $class_id, $student_id);
// } 
//var_dump($level);
set_query_var( 'level', $level );
?>
<div class="container">
  <div class="row">
    <div class="col-md-12">
      <div class="page-title">
      	<?php the_breadcrumb() ?>
        <h3>
          <?php echo get_queried_object()->name; ?> - <?php echo getClassName($_REQUEST['class']) ?>
        </h3>  
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-3">
    	<?php get_template_part('page-parts/learning/content','sidebar') ?>
    </div>
    <div class="col-md-9">
      <?php echo $flash->show(); ?>
      <div class="card card-transparent file-list recent-files">
        <div class="card-body">
          <div class="row">
          	 <?php 
          		$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
          		$class = ( isset($_REQUEST['class']) ) ? $_REQUEST['class'] : '';

              $y = ( isset($_REQUEST['y']) ) ? $_REQUEST['y'] : '';
              $m = ( isset($_REQUEST['m']) ) ? $_REQUEST['m'] : '';
              $w = ( isset($_REQUEST['w']) ) ? $_REQUEST['w'] : '';
              $d = ( isset($_REQUEST['d']) ) ? $_REQUEST['d'] : '';

      				$post_args = array(
                'post_type' => 'course',
                'post_status' => 'publish',
                'posts_per_page' => get_option( 'posts_per_page' ),
                'paged' => $paged,
                'meta_query' => array(
                      array(
                          "key"     => "class_id",
                          "value"   => $class
                      ),
                      array(
                          'key'     => 'year',
                          'value'   => $y
                      ),
                      array(
                          'key'     => 'month',
                          'value'   => $m
                      ),
                      array(
                          'key'     => 'week',
                          'value'   => $w
                      ), 
                      array(
                          'key'     => 'day',
                          'value'   => $d
                      )
                ),
                'tax_query' => array(
                    array (
                        'taxonomy' => 'lesson',
                        'field' => 'term_id',
                        'terms' => get_queried_object()->term_id
                    )
                ),
                'meta_key' => 'level',
                'meta_type' => 'NUMERIC',
                'orderby' => 'meta_value_num',
                'order' => 'ASC'
              );
	          	$post_query = new WP_Query( $post_args );
	          
	          	if ( $post_query->have_posts() ) : while ( $post_query->have_posts() ) : $post_query->the_post(); 
	        ?>

	        	<?php get_template_part('page-parts/course/content','page') ?>

	        <?php endwhile; ?> 
	        <nav aria-label="Page navigation" class="col-md-12 pagination-with-number">
				<?php 
					echo paginate_links( array(
					    'total'        	=> $post_query->max_num_pages,
					    'current'      	=> max( 1, get_query_var( 'paged' ) ),
					    'type' 			=> 'list'
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