<?php 
/* Template Name: Course */
global $flash;
get_header();
if (is_parent()) {
  $student = getStudentByID($_REQUEST['student_id']);
}
?>
<div class="container">
  <div class="row">
    <div class="col-md-12">
      <div class="page-title">
      	<?php the_breadcrumb() ?>
        <h3><?php echo get_the_title(); ?> - 
          <?php if (is_parent() && isset($_REQUEST['student_id'])): ?>
            <?php echo $student->name ?>
          <?php else: ?>
          <?php echo getClassName($_REQUEST['class']) ?>
          <?php endif ?>
        </h3>  
      </div>
      <div class="page-action text-right">
      <?php if ( is_parent() && isset($_REQUEST['y']) && isset($_REQUEST['month']) && isset($_REQUEST['week']) && isset($_REQUEST['d']) ): ?>
          <?php 
            $reports = getUserCourseDayReport($_REQUEST['student_id'], $_REQUEST['y'], $_REQUEST['month'], $_REQUEST['week'], $_REQUEST['d']);
          ?>
          <?php  if ( empty($reports) ) : ?>
            <div class="mail-actions">
              <button class="btn btn-primary" data-toggle="modal" data-target="#sendLesson">Kirim Laporan Pembelajaran</button>
            </div>
            <div class="modal fade" id="sendLesson" tabindex="-1" role="dialog" aria-labelledby="newPostLabel" aria-hidden="true">
              <div class="modal-dialog modal-lg" role="document">
                  <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Kirim Laporan Pembelajaran</h5>
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <i class="material-icons">close</i>
                          </button>
                      </div>
                      <div class="modal-body">
                        <div class="row">
                          <div class="col-lg-12">
                            <?php get_template_part('page-parts/course/form') ?>
                          </div>
                        </div>
                      </div>
                      <div class="modal-footer">
                        
                      </div>
                  </div>
              </div>
            </div>
          <?php else: ?>
             <div class="mail-actions">
                <button class="btn btn-secondary" disabled>Pembelajaran Dikirim</button>
              </div>
          <?php endif ?>
      <?php endif; ?>
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

            <?php if ( isset($_REQUEST['y']) && isset($_REQUEST['month']) && isset($_REQUEST['week']) && isset($_REQUEST['d']) ): ?>
            <?php 
              $paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
              $class = ( isset($_REQUEST['class']) ) ? $_REQUEST['class'] : '';
              $y = ( isset($_REQUEST['y']) ) ? $_REQUEST['y'] : '';
              $m = ( isset($_REQUEST['month']) ) ? $_REQUEST['month'] : '';
              $w = ( isset($_REQUEST['week']) ) ? $_REQUEST['week'] : '';
              $d = ( isset($_REQUEST['d']) ) ? $_REQUEST['d'] : '';

              $args = array(
                  'post_type' => 'course',
                  'post_status' => 'publish',
                  'posts_per_page' => -1,
                  'meta_query' => array(
                      array(
                          'key'     => 'class_id',
                          'value'   => $class
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
                  )
              );
              $query = new WP_Query( $args );

              $posts = $query->posts;

              $terms = array();
              foreach( $posts  as $post ) {

                  $new_cats = wp_get_object_terms( $post->ID, 'lesson' );
                  $terms = array_merge(
                      $terms,$new_cats
                  );
              }

              $terms = array_unique($terms,SORT_REGULAR);

              foreach ($terms as $term):
                set_query_var( 'term', $term );

                get_template_part('page-parts/course/content','term');

              endforeach; 
              if (is_parent()): 
                set_query_var( 'reports', $reports );
                get_template_part( 'page-parts/course/report' ); 
              else: 
                get_template_part( 'page-parts/course/report-teacher' ); 
              endif
            ?> 

            <?php elseif ( isset($_REQUEST['y']) && isset($_REQUEST['month']) && isset($_REQUEST['week']) ): ?>
              <?php get_template_part('page-parts/course/content','day') ?>
            <?php elseif ( isset($_REQUEST['y']) && isset($_REQUEST['month']) ): ?>
              <?php get_template_part('page-parts/course/content','week') ?>
            <?php elseif ( isset($_REQUEST['y']) ): ?>
              <?php get_template_part('page-parts/course/content','month') ?>
            <?php else: ?>
              <?php get_template_part('page-parts/course/content','year') ?>
            <?php endif ?>

          	 
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php get_footer(); ?>