<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

class SMCP_Admin
{

    /* Returns class instance (singleton method) */
    private static $instance = null;
    /**
     * get_instance
     *
     * @return SMCP_Admin
     */
    public static function get_instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }


    // Add meta box with toggle switch
    public function add_meta_boxes_cb()
    {
        add_meta_box(
            'smart_post_controls',
            'Smart Controls',
            function ($post) {
                $type = get_post_type();
                $value = get_post_meta($post->ID, '_hide_featured_image', true);
                $checked = $value === '1' ? 'checked' : '';
?>
            <style>
                :root {
                    --toggle-width: 50px;
                }

                .smpc-input span {
                    display: inline-block;
                    width: calc(100% - var(--toggle-width) - 5px);
                    vertical-align: middle;
                }

                .toggle-switch {
                    position: relative;
                    display: inline-block;
                    width: var(--toggle-width);
                    height: 24px;
                    vertical-align: middle;
                }

                .toggle-switch input {
                    display: none;
                }

                .toggle-switch .slider {
                    position: absolute;
                    cursor: pointer;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    background-color: #ccc;
                    transition: .4s;
                    border-radius: 24px;
                }

                .toggle-switch .slider:before {
                    position: absolute;
                    content: "";
                    height: 18px;
                    width: 18px;
                    left: 3px;
                    bottom: 3px;
                    background-color: white;
                    transition: .4s;
                    border-radius: 50%;
                }

                .toggle-switch input:checked+.slider {
                    background-color: #2196F3;
                }

                .toggle-switch input:checked+.slider:before {
                    transform: translateX(26px);
                }
            </style>

            <div class="smpc-input">
                <span><strong>Hide Featured Image</strong></span>
                <div class="toggle-switch">
                    <input type="checkbox" id="hide_featured_image" name="hide_featured_image" value="1" <?php echo $checked; ?> />
                    <label class="slider" for="hide_featured_image"></label>
                </div>
                <!-- <p style="margin-top: 8px;">Show/hide featured image on this <?= $type; ?>.</p> -->
            </div>
<?php
            },
            ['post', 'page'],
            'side'
        );
    }

    // Save toggle value
    public function save_post_cb($post_id)
    {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
        if (isset($_POST['hide_featured_image'])) {
            update_post_meta($post_id, '_hide_featured_image', '1');
        } else {
            delete_post_meta($post_id, '_hide_featured_image');
        }
    }
}
