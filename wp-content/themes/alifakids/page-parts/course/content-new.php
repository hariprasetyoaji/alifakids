<?php 
    
    $default = array(
        'ID' => '',
        'post_title' => '',
        'post_content' => '',
        'video_url' => '',
        'class_id' => '',
        'level' => '',
        'lesson_id' => '',
        'attachment_id' => ''
    );

    $item = $default;
    if (isset($_REQUEST['id'])) {
        $item = $wpdb->get_row(
            $wpdb->prepare( "  
                    SELECT p.ID as ID,
                            p.post_title as post_title,
                            p.post_content as post_content,
                            (SELECT meta_value from {$wpdb->prefix}postmeta WHERE post_id = p.ID AND meta_key = 'class_id' LIMIT 1) as class_id,
                            (SELECT meta_value from {$wpdb->prefix}postmeta WHERE post_id = p.ID AND meta_key = 'level' LIMIT 1) as level,
                            (SELECT meta_value from {$wpdb->prefix}postmeta WHERE post_id = p.ID AND meta_key = 'video_url' LIMIT 1) as video_url,
                            tr.term_taxonomy_id as lesson_id
                    FROM {$wpdb->prefix}posts as p 
                        INNER JOIN ak_term_relationships tr
                            ON p.ID = tr.object_id
                    WHERE p.ID = '%d' AND p.post_type = 'course'
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

<h5 class="card-title">Tambah Pelajaran Baru</h5>
<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" enctype="multipart/form-data">
    <input name='action' type="hidden" value='new_course_post'>
    <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('new_course_post')?>"/>
    <input type="hidden" name="ID" value="<?php echo $item['ID'] ?>"/>
    <input type="hidden" id="actualLevel" name="level" value="<?php echo $item['level'] ?>"/>

    <div class="form-group">
        <label for="title">Judul</label>
        <input type="text" name="post_title" class="form-control" id="title" aria-describedby="titleHelp" placeholder="Judul" value="<?php echo $item['post_title'] ?>" required>
    </div>
    <div class="form-group">
        <label for="class">Kelas</label>
        <?php $classes = getClassSelectOption(); ?>
        <select name="class_id" class="form-control" id="class_id" required>
            <option value="">Pilih Kelas</option>
            option
            <?php foreach ($classes as $value): ?>
                <option 
                    value="<?php echo $value['class_id'] ?>" 
                    <?php echo ($item['class_id'] == $value['class_id']) ? 'selected' : '' ; ?> 
                 ><?php echo $value['name'] ?></option>
            <?php endforeach ?>
        </select>
        <small class="form-text text-muted">Pilih kelas untuk pelajaran ini.</small>
    </div>
    <div class="form-group">
        <label for="class">Mata Pelajaran</label>
        <?php 
            $lessons = get_terms( array(
                    'taxonomy' => 'lesson', 
                    'hide_empty' => false 
                )
            ); 
        ?>
        <select name="lesson_id" class="form-control" id="lesson_id" required>
            <option value="">Pilih Pelajaran</option>
            <?php foreach ($lessons as $lesson): ?>
                <option 
                    value="<?php echo $lesson->term_id ?>" 
                    <?php echo ($item['lesson_id'] == $lesson->term_id) ? 'selected' : '' ; ?> 
                 ><?php echo $lesson->name ?></option>
            <?php endforeach ?>
        </select>
        <small class="form-text text-muted">Pilih mata pelajaran untuk pelajaran ini.</small>
    </div>
    <div class="form-group" id="level-group" style="display: none;">
        <label for="class">Level</label>
        <input type="text"  class="form-control" id="level" placeholder="Level" value="<?php echo $item['level'] ?>" readonly>
        <small class="form-text text-muted">Level otomatis memilih level selanjutnya dari pelajaran ini.</small>
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
        <label for="title">Video Url</label>
        <input type="text" name="video_url" class="form-control" id="title" aria-describedby="titleHelp" placeholder="Url Video" value="<?php echo $item['video_url'] ?>" required>
    </div>
    <div class="form-group">
        <label for="class">Gambar Utama</label><br>
        <img style="display: block; margin-bottom: 8px;" id="frontend-image" class="img-fluid" /> 
        <button id="frontend-button" type="button" value="upload" class="btn btn-secondary">Tambah Gambar Utama</button>
        <small class="form-text text-muted">Pilih gambar utama untuk pelajaran ini.</small>

        <input id="attachment-id" type="hidden" name="attachment_id" value="<?php echo  $item['attachment_id'] ?>" />
    </div>
    <button type="submit" class="btn btn-primary">Submit</button>
</form>
<?php 
    if ( isset($_REQUEST['id']) ) {
      $attc_url = get_the_post_thumbnail_url($_REQUEST['id']); 
    } else {
      $attc_url = '';
    }
?>
<script>
    $(document).ready(function() {
        var lesson_id = $('#lesson_id').val();
        var class_id = $('#class_id').val();
        var attachment_id = $('#attachment-id').val();

        if (lesson_id != '' && class_id != '') {
            $('#level-group').show('slow/400/fast');
        }

        if (attachment_id != '') {
            $('#frontend-image').attr('src', '<?php echo $attc_url ?>');
        }

        $('#lesson_id').change(function(e) {
            e.preventDefault();

            var lesson_id = $(this).val();
            var class_id = $('#class_id').val();

            if (lesson_id == '' || class_id == '') return;
            
            $.ajax({
                url: ajaxurl,
                dataType : 'json',
                data: {
                    action: "ajax_get_select_next_level", 
                    lesson_id : lesson_id,
                    class_id : class_id
                }, success: function(res) {
                    $('#level').val(res);
                    $('#actualLevel').val(res);
                    $('#level-group').show('slow/400/fast');
                }
            });
        });

        $('#class_id').change(function(e) {
            e.preventDefault();
            $('#lesson_id').val("");
            $('#level').val("");
            $('#level-group').hide('slow/400/fast');
        });
    });
</script>