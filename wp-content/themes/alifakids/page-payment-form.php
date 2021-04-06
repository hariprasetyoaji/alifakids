<?php 
/* Template Name: Payment Form */
get_header();
global $user_ID;
global $current_user;
$student = getStudentByID($_REQUEST['student_id']);
?>
<div class="container">
  <div class="row">
    <div class="col-md-12">
      <div class="page-title">
        <?php the_breadcrumb() ?> 
        <h3><?php echo get_the_title(); ?> - <?php echo date_i18n("F Y", strtotime( $_REQUEST['period'] ) ); ?></h3>
      </div>
      <?php echo $flash->show(); ?>
    </div>
  </div>

  <div class="row">
    <div class="col-lg-4">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title"><?php echo $student->name ?></h5>
          <ul class="list-unstyled profile-about-list">
            <li>
              <i class="material-icons">school</i>
              <span>Kelas : <?php echo getClassName($student->class_id) ?></span>
            </li>
            <li>
              <i class="material-icons">my_location</i>
              <span>Cabang : <?php echo getBranchName($student->branch_id) ?></span>
            </li>
            <li>
              <i class="material-icons">person</i>
              <span>
                Nama Orang Tua : <?php echo $current_user->first_name.' '.$current_user->last_name ?>
              </span>
            </li>
          </ul>
        </div>
      </div>
    </div>
    <?php if(is_parent()):
      get_template_part( 'page-parts/payment/form' );
    endif;  ?>
    </div>
</div>
<?php get_footer(); ?>