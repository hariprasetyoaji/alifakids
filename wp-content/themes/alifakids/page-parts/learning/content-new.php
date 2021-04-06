<?php 
    
     $default = array(
        'ID' => '',
        'post_title' => '',
        'post_content' => '',
        'post_category' => ''
    );

    $item = $default;
    if (isset($_REQUEST['id'])) {
        $item = $wpdb->get_row(
            $wpdb->prepare( "  
                    SELECT p.ID as ID,
                            p.post_title as post_title,
                            p.post_content as post_content,
                            tr.term_taxonomy_id as post_category
                    FROM {$wpdb->prefix}posts as p 
                        INNER JOIN ak_term_relationships tr
                            ON p.ID = tr.object_id
                    WHERE p.ID = '%d' AND p.post_type = 'post'
                 ", 
                $_REQUEST['id'] 
            ), 
            ARRAY_A
        );

        $item['attachment_id'] = get_post_thumbnail_id($_REQUEST['id']);

        if (!$item) {
            $item = $default;
            $notice = __('Item not found', 'alifakids');
        }
    }

?>

<h5 class="card-title">Tambah Artikel Baru</h5>
<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" enctype="multipart/form-data">
    <input name='action' type="hidden" value='new_post'>
    <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('new_post')?>"/>
    <input type="hidden" name="ID" value="<?php echo $item['ID'] ?>"/>

    <div class="form-group">
        <label for="title">Judul</label>
        <input type="text" name="post_title" class="form-control" id="title" aria-describedby="titleHelp" placeholder="Judul" value="<?php echo $item['post_title'] ?>" required>
    </div>

    <div class="form-group">
        <label for="class">Kategori</label>
        <select name="post_category" class="form-control" id="class" required>
            <option value="">Pilih Kategori</option>
            <?php 
                $values = array(
                  'orderby' => 'name', 
                  'order' => 'ASC',
                  'echo' => 1,
                  'hide_empty' => false
                 );
              $categories = get_categories($values); 
              foreach ($categories as $category) {
                if ( strtolower( $category->cat_name) != 'uncategorized'){ 
            ?>
                <option value="<?php echo $category->term_id ?>" <?php echo ($item['post_category'] == $category->term_id) ? 'selected' : '' ; ?>>
                    <?php echo $category->cat_name; ?>
                </option>
            <?php 
                }
            }
         ?>
        </select>
        <small class="form-text text-muted">Pilih kategori untuk artikel ini.</small>
    </div>

    <div class="form-group">
        <?php 

        $argswp = array(
            'textarea_rows' => 10,
            'teeny'         => false,
            'quicktags'     => false,
            'wpautop'       => true,
            'media_buttons' => true,
        );
        wp_editor( $item['post_content'], 'post_content', $argswp ); ?> 
            
    </div>
    <div class="form-group">
        <label for="class">Gambar Utama</label><br>
        <img style="display: block; margin-bottom: 8px;" id="frontend-image" class="img-fluid" /> 
        <button id="frontend-button" type="button" value="upload" class="btn btn-secondary">Tambah Gambar Utama</button>
        <small class="form-text text-muted">Pilih gambar utama untuk artikel ini.</small>

        <input id="attachment-id" type="hidden" name="attachment_id" value="<?php echo $item['attachment_id'] ?>" />
    </div>
    <button type="submit" class="btn btn-primary">Submit</button>
</form>

<script type="text/javascript">
    var attachment_id = $('#attachment-id').val();
    if (attachment_id != '') {
        $('#frontend-image').attr('src', '<?php echo get_the_post_thumbnail_url($_REQUEST['id']); ?>');
    }
</script>