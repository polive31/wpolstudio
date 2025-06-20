<?php

// Exit if accessed directly.

if (!defined('ABSPATH')) {
    exit;
}
class CWM_Settings
{


    /* Returns class instance (singleton method) */
    private static $instance = null;
    /**
     * get_instance
     *
     * @return CWM_Settings
     */
    public static function get_instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }


    /**
     * create_option_page
     *
     * @return void
     */
    public function create_option_page()
    {
        add_options_page(
            'Widget Visibility Settings',
            'Tiny Widget Manager',
            'manage_options',
            'cwm-settings',
            'CWM_Settings::cwm_render_settings_page'
        );
    }

    /**
     * populage_option_page
     *
     * @return void
     */
    public function populate_option_page()
    {
        register_setting('cwm_settings_group', 'cwm_disable_block_editor', [
            'sanitize_callback' => 'sanitize_text_field'
        ]);

        add_settings_section(
            'cwm_main_section',
            'General Settings',
            null,
            'cwm-settings'
        );

        add_settings_field(
            'cwm_disable_block_editor',
            'Disable Block Widgets Editor',
            'CWM_Settings::cwm_render_block_editor_field',
            'cwm-settings',
            'cwm_main_section'
        );
    }

    /**
     * cwm_render_block_editor_field
     *
     * @return void
     */
    public static function cwm_render_block_editor_field()
    {
        $checked = get_option('cwm_disable_block_editor') ? 'checked' : '';
        echo '<input type="checkbox" name="cwm_disable_block_editor" value="1" ' . esc_attr($checked) . '> Disable block-based widget editor (use classic)';
    }

    /**
     * cwm_render_settings_page
     *
     * @return void
     */
    public static function cwm_render_settings_page()
    {
?>
        <div class="wrap">
            <h1>Widget Visibility Settings</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('cwm_settings_group');
                do_settings_sections('cwm-settings');
                submit_button();
                ?>
            </form>
        </div>
<?php
    }
}
