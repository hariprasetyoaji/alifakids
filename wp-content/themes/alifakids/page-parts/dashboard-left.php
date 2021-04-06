<?php if ( is_page_template('page-profile.php')): ?>
  <div class="card">
    <div class="card-body " id="my-avatar">
     <h5 class="card-title">Foto Profil</h5>
        <?php echo do_shortcode( '[avatar_upload]' ); ?>
    </div>  
  </div>

<?php endif; ?>
<?php 
if (current_user_can( 'parent' ) || current_user_can( 'teacher' )) {
$branches = getUserBranch();
?>
<?php foreach ($branches as $branch): ?>
  
<div class="card">
  <div class="card-body">
    <h5 class="card-title"><?php echo $branch->name ?></h5>
    <ul class="list-unstyled profile-about-list">
      <li>
        <i class="material-icons">local_phone</i>
        <span><?php echo $branch->phone ?></span>
      </li>
      <li>
        <i class="material-icons">home</i>
        <span><?php echo $branch->address ?></span>
      </li>
      <li>
        <i class="material-icons">mail_outline</i>
        <span><?php echo $branch->email ?></span>
      </li>
    </ul>
  </div>
</div>
<?php endforeach ?>
<?php } ?>