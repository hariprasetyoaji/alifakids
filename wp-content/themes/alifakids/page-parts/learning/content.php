<?php 
$post_id = get_the_ID();
 ?>
<div class="card">
  <div class="card-body">
    <div class="mail-container">
        <div class="mail-header">
            <div class="mail-title">
              <h3>
                <?php echo get_the_title() ?>
                
              </h3>
            </div>
            <?php 
              global $current_user;

              $user_roles = $current_user->roles;
              if ( in_array( 'administrator', $user_roles, true )) {
            ?>
             <div class="mail-actions">
                  <a href="<?php echo add_query_arg(array('id' => $post_id, ),site_url('/learning/post')); ?>" class="btn btn-secondary">Edit</a>
                  <button class="btn btn-danger" data-toggle="modal" data-target="#deletePosts">Delete</button>
              </div>
            <?php 
              } 
            ?>
        </div>
        <div class="mail-info">
            <?php  
              $author_id = get_the_author_meta();

            ?>
            <div class="mail-author">
                <img src="<?php echo get_avatar_url($author_id, '50') ?>" alt="">
                <div class="mail-author-info">
                    <span class="mail-author-name"><?php echo get_the_author_fullname() ?></span>
                    <span class="mail-author-address"><?php echo  get_the_author_meta('email') ?></span>
                </div>
            </div>
            <div class="mail-other-info">
                <span><?php echo get_the_date(); ?></span>
            </div>
        </div>
        <div class="divider"></div>
        <div class="mail-text">
          <?php if ( '' !== get_the_post_thumbnail() ) : ?>
            <div class="post-thumbnail">
              <?php the_post_thumbnail(); ?>
            </div><!-- .post-thumbnail -->
          <?php endif; ?>
          
          <?php the_content();  ?>
        </div>
    </div>
  </div>
</div>
<div class="modal fade" id="deletePosts" tabindex="-1" role="dialog" aria-labelledby="deleteLesson" aria-hidden="true">
<form  method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
<input name='action' type="hidden" value='delete_posts'>
<input type="hidden" name="nonce" value="<?php echo wp_create_nonce('delete_posts')?>"/>
<input id="deleteLesson" type="hidden" name="id" value="<?php echo $post_id ?>">
<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Hapus Artikel</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <i class="material-icons">close</i>
            </button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-lg-12 text-center">
              <p>Apakah anda yakin ingin menghapus artikel ini?</p>
            </div>
          </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-danger" >Delete</button>
        </div>
    </div>
</form>
</div>