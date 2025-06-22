<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

class CWM_Settings
{
    private static $instance = null;

    public static function get_instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

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

    public function populate_option_page()
    {
        register_setting('cwm_settings_group', 'cwm_disable_block_editor', [
            'sanitize_callback' => 'sanitize_text_field'
        ]);

        register_setting('cwm_settings_group', 'cwm_color_theme', [
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

        add_settings_field(
            'cwm_color_theme',
            'Color Theme',
            'CWM_Settings::cwm_render_color_theme_field',
            'cwm-settings',
            'cwm_main_section'
        );
    }

    public static function cwm_render_block_editor_field()
    {
        $checked = get_option('cwm_disable_block_editor') ? 'checked' : '';
        echo '<input type="checkbox" name="cwm_disable_block_editor" value="1" ' . esc_attr($checked) . '> Disable block-based widget editor (use classic)';
    }

    public static function cwm_render_color_theme_field()
    {
        $value = get_option('cwm_color_theme', 'blue');
        $options = [
            'blue'   => 'Blue',
            'gray'   => 'Gray',
            'orange' => 'Orange',
            'lime'   => 'Lime',
        ];

        echo '<select name="cwm_color_theme">';
        foreach ($options as $key => $label) {
            printf(
                '<option value="%s"%s>%s</option>',
                esc_attr($key),
                selected($value, $key, false),
                esc_html($label)
            );
        }
        echo '</select>';
    }

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
