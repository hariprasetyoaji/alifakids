<?php 
$post_id = get_the_ID();
$term = wp_get_post_terms($post_id,'lesson');
 ?>
<div class="card">
  <div class="card-body">
    <div class="mail-container">
        <div class="mail-header">
            <div class="mail-title">
              <h3>
                <?php echo get_the_title() ?>
                <small>
                  - <?php echo $term[0]->name ?> (Level :<?php echo get_post_meta( $post_id, 'level', true ); ?>)
                </small>
                
              </h3>
            </div>
            <?php 
              if ( current_user_can( 'administrator' ) ) {
            ?>
              <div class="mail-actions">
                  <a href="<?php echo add_query_arg(array('id' => $post_id, ),site_url('/courses/post')); ?>" class="btn btn-secondary">Edit</a>
                  <button class="btn btn-danger" data-toggle="modal" data-target="#deleteLesson">Delete</button>
              </div>
            <?php 
              } /*elseif(is_parent()) {
                if ( empty($reports) ) {
            ?>
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
            <?php 
                } else {
                  ?>
                    <div class="mail-actions">
                      <button class="btn btn-secondary" disabled>Pembelajaran Dikirim</button>
                    </div>
                  <?php
                }
            }*/
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
<div class="modal fade" id="deleteLesson" tabindex="-1" role="dialog" aria-labelledby="deleteLesson" aria-hidden="true">
<form  method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
<input name='action' type="hidden" value='delete_lesson'>
<input type="hidden" name="nonce" value="<?php echo wp_create_nonce('delete_lesson')?>"/>
<input id="deleteLesson" type="hidden" name="id" value="<?php echo $post_id ?>">
<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Hapus Pelajaran</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <i class="material-icons">close</i>
            </button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-lg-12 text-center">
              <p>Apakah anda yakin ingin menghapus pelajaran ini?</p>
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