<?php 
/* Template Name: Learning */
get_header();
global $payment;
global $flash;

//echo "<pre>",print_r($payment,1),"</pre>";
?>
<div class="container">
  <div class="row">
    <div class="col-md-12">
      <div class="page-title">
      	<?php the_breadcrumb() ?>
        <h3><?php echo get_the_title(); ?></h3>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-3">
    	<?php get_template_part('page-parts/learning/content','sidebar') ?>
    </div>
    <div class="col-md-9">
      <?php echo $flash->show() ?>
      <div class="card card-transparent file-list">
        <div class="card-body">
          <h5 class="card-title">PELAJARAN</h5>
          <div class="row">
            <div class="col-12">

              <?php 

              $classes = getClassSelectOption();

              $icons_url = array( 
                '1' => array(
                    'icon' => get_template_directory_uri().'/assets/images/class_toddler.png',
                    'bg' => get_template_directory_uri().'/assets/images/class-toddler-bg.png'
                  ), 
                '2' => array(
                    'icon' => get_template_directory_uri().'/assets/images/class_playgroup.png',
                    'bg' => get_template_directory_uri().'/assets/images/class-playgroup-bg.png'
                  ), 
                '3' => array(
                    'icon' => get_template_directory_uri().'/assets/images/class_tka.png',
                    'bg' => get_template_directory_uri().'/assets/images/class-tka-bg.png'
                  ), 
                '4' =>array(
                    'icon' =>  get_template_directory_uri().'/assets/images/class_tkb.png',
                    'bg' =>  get_template_directory_uri().'/assets/images/class-tkb-bg.png'
                  ),
                '5' => array(
                    'icon' => get_template_directory_uri().'/assets/images/class_toddler.png',
                    'bg' => get_template_directory_uri().'/assets/images/class-toddler-bg.png'
                  ), 
                '6' => array(
                    'icon' => get_template_directory_uri().'/assets/images/class_playgroup.png',
                    'bg' => get_template_directory_uri().'/assets/images/class-playgroup-bg.png'
                  ), 
                '7' => array(
                    'icon' => get_template_directory_uri().'/assets/images/class_tka.png',
                    'bg' => get_template_directory_uri().'/assets/images/class-tka-bg.png'
                  ), 
                '8' =>array(
                    'icon' =>  get_template_directory_uri().'/assets/images/class_tkb.png',
                    'bg' =>  get_template_directory_uri().'/assets/images/class-tkb-bg.png'
                  ),
                '9' =>array(
                    'icon' =>  get_template_directory_uri().'/assets/images/class_prasd.png',
                    'bg' =>  get_template_directory_uri().'/assets/images/class-prasd-bg.png'
                  )
                  /*, 
                'Modul Pra SD' => array(
                    'icon' => get_template_directory_uri().'/assets/images/class_prasd.png',
                    'bg' => get_template_directory_uri().'/assets/images/class-prasd-bg.png'
                  )*/
              );


              if (in_array( 'parent' , $current_user->roles) ) {
                //$class_ids = getStudentClassByParent($current_user->ID);
                $students = getParentStudents($current_user->ID);

                //if ($class_ids) {
                  foreach ($students as $key => $student) {
                    $class = $student['class_id'];
                    $class_name = getClassName($student['class_id']);
                    //if ( in_array( $class['class_id'], $class_ids) ) {
                    $url_args =  array(
                      'class' => $student['class_id'], 
                      'student_id' => $student['student_id']
                    );

                    if ($payment && in_array($student['student_id'], $payment)) {
                      $link = '#';
                      $disabled = 'disabled';
                    } else {
                      $link = add_query_arg( $url_args, site_url('/course') );
                      $disabled = '';
                    }
              ?>
                       <a class="card folder <?php echo $disabled ?>" href="<?php echo $link ?>">
                        <div class="card-body">
                            <div class="folder-bg" style="background-image: url('<?php echo $icons_url[$class]['bg'] ?>'); "></div>
                            <div class="folder-icon">
                                <img src='<?php echo $icons_url[$class]['icon'] ?>' />
                            </div>
                            <div class="folder-info">
                                <div class="folder-name"><?php echo $student['name'].' <small>('.$class_name.')</small>' ?></div>
                            </div>
                        </div>
                      </a>

              <?php
                    //}
                  }
                //}
              } else {
                foreach ($classes as $class) {
            ?>
                      <a class="card folder" href="<?php echo add_query_arg( 'class', $class['class_id'], site_url('/course') ) ?>">
                        <div class="card-body">
                            <div class="folder-bg" style="background-image: url('<?php echo $icons_url[$class['class_id']]['bg'] ?>'); "></div>
                            <div class="folder-icon">
                                <img src='<?php echo $icons_url[$class['class_id']]['icon'] ?>' />
                            </div>
                            <div class="folder-info">
                                <div class="folder-name"><?php echo $class['name'] ?></div>
                            </div>
                        </div>
                      </a>
              <?php
                }
              }?>
            </div>
          </div>
        </div>  
      </div>
      <div class="divider"></div>
      <div class="card card-transparent file-list recent-files">
        <h5 class="card-title">ARTIKEL</h5>
        <div class="card-body">
          <div class="row">

          	 <?php 
          		$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

          		$categories = getLearningCategoryByRole($current_user->roles);

      				$post_args = array(
      					'post_type' => 'post',
      					'post_status' => 'publish',
      					'posts_per_page' => get_option( 'posts_per_page' ),
      					'paged' => $paged,
      					'category_name' => $categories
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
					    'current'      	=> max( 1, get_query_var( 'paged' ) ),
					    'type' 			     => 'list'
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
<?php get_footer(); ?>