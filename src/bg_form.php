<?php
/*
Plugin Name: BG Remover
Plugin URI: 
Description: A simple plugin that allows users to upload an image and removes its background using the Picsart API.
Version: 1.6
Author: Your Name
Author URI: 
*/

require_once 'picsart_bg.php';

function bg_form() {
    ?>
    <form action="" method="post" enctype="multipart/form-data">
        <input type="file" name="image" accept="image/jpeg, image/png, image/gif" id="image">
        <input type="submit" value="Upload" id="submit" disabled>
    </form>
    <div style="width: 512px; height: 512px;">
        <img id="preview" style="display: none;">
        <?php
        if (isset($_FILES['image'])) {
            $image = file_get_contents($_FILES['image']['tmp_name']);
            $processed_image = pa_bg($image, 'binary');
            if (is_wp_error($processed_image)) {
                echo 'Error: ' . $processed_image->get_error_message();
            } else {
                echo '<img src="' . $processed_image . '" />';
            }
        }
        ?>
       
    </div>
    <script>
        document.querySelector('#image').addEventListener('change', function() {
            document.querySelector('#submit').disabled = !this.files.length;
            let reader = new FileReader();
            reader.onload = function(e) {
                document.querySelector('#preview').src = e.target.result;
                document.querySelector('#preview').style.display = 'block';
            };
            reader.readAsDataURL(this.files[0]);
        });


    </script>
    <?php
}

add_shortcode('bg_form', 'bg_form');
