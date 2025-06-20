<?php

// Exit if accessed directly.

if (!defined('ABSPATH')) {
    exit;
}

class CWM_Hooks
{
    /* Returns class instance (singleton method) */
    private static $instance = null;
    /**
     * get_instance
     *
     * @return CWM_Hooks
     */
    public static function get_instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    const PLUGIN_VERSION = '1.0';

    private static $PLUGIN_URI = null;
    private static $PLUGIN_PATH = null;
    private $sections;
    private $options;
    private $args;
    private $disable = false;
    private $mobile;
    // private $tablet;
    private $loggedin;
    private $user;
    private $post_types;
    private $pages;
    private $taxonomies;

    /* private constructor ensures that the class can only be */
    /* created using the get_instance static function */
    private function __construct()
    {
        self::$PLUGIN_PATH = trailingslashit(plugin_dir_path(dirname(__FILE__)));
        self::$PLUGIN_URI = trailingslashit(plugin_dir_url(dirname(__FILE__)));
        $this->sections = [
            'pages',
            'posts',
            'archives',
            'roles',
            'devices',
        ];
        // Get posts types

        $this->options = [];
        if (is_admin()) {
            $this->pages = $this->_get_all_pages();
            $this->post_types = $this->_get_post_types();
            $this->taxonomies = $this->_get_archives_and_taxonomies();
            $this->hydrate_options();
        }
        $this->args = [];
        $this->disable = false;
        $this->mobile = wp_is_mobile();
        // $this->tablet = false;
        $this->loggedin = is_user_logged_in();
        $this->user = wp_get_current_user();
    }

    /**
     * Enqueue scripts and styles for the admin area.
     *
     * @param string $hook The current admin page.
     */

    public function enqueue_scripts($hook)
    {
        if ($hook !== 'widgets.php') return;

        // Enqueue vendor scripts and styles
        wp_enqueue_script('selectize-scripts', self::$PLUGIN_URI . 'vendor/node_modules/@selectize/selectize/dist/js/selectize.min.js', ['jquery'], self::PLUGIN_VERSION, true);
        wp_enqueue_style('selectize-styles', self::$PLUGIN_URI . 'vendor/node_modules/@selectize/selectize/dist/css/selectize.default.css', [], self::PLUGIN_VERSION);

        // Enqueue custom scripts and styles
        wp_enqueue_script('cwm-admin-scripts', self::$PLUGIN_URI . 'assets/js/cwm-scripts.min.js', ['jquery', 'selectize-scripts'], self::PLUGIN_VERSION, true);
        wp_enqueue_style('cwm-admin-styles', self::$PLUGIN_URI . 'assets/css/cwm-styles.min.css', [], self::PLUGIN_VERSION);
    }

    /**
     * hydrate_args
     *
     * @param  mixed $params
     * @return void
     */
    public function hydrate_args($params)
    {
        $widget_id = $params[0]['widget_id'];
        $this->args[$widget_id] = $params[0];
        return $params;
    }


    /**
     * hydrate_options
     *
     * @return void
     */
    public function hydrate_options()
    {
        $full_options = [
            'pages' => [
                'label' => 'on checked Pages',
                'items' => $this->pages,
            ],
            'posts' => [
                'label' => 'on checked Posts',
                'items' => $this->post_types,
            ],
            'archives' => [
                'label' => 'on checked Taxonomies',
                'items' => $this->taxonomies,
            ],
            'roles' => [
                'label' => 'for checked User Roles',
                'items' => [
                    'logged_out'    => 'Logged-out',
                    'logged_in'     => 'Logged-in',
                    'administrator' => 'Admin',
                    'editor'        => 'Editor',
                    'subscriber'    => 'Subscriber',
                ],
            ],
            'devices' => [
                'label' => 'on checked Devices',
                'items' => [
                    'desktop' => 'Computer',
                    // 'tablet' => 'Tablette',
                    'mobile' => 'Mobile',
                ],
            ],
        ];

        // Hydrate options
        foreach ($this->sections as $section) {
            $this->options[$section] = $full_options[$section];
        }
    }

    /**
     * add_visibility_controls
     *
     * @param  mixed $widget
     * @param  mixed $return
     * @param  mixed $instance
     * @return void
     */
    public function add_visibility_controls($widget, $return, $instance)
    {
        echo '<div class="cwm-widget-controls" data-widget-id="' . esc_attr($widget->id) . '">';
        echo '<div class="cwm-tabs">';
        echo '<ul class="cwm-tab-nav">';

        // Display tabs for each section
        foreach ($this->options as $section => $data) {
            $mode = $instance['cwm_visibility_' . $section . '_mode'] ?? 'hide';
            $has_items =  !empty($instance['cwm_visibility_' . $section . '_items']);
            $has_settings = $has_items || ($mode === 'show');

            $classes = $has_settings ? 'has-settings setting-' . $mode : '';
            $classes .= ($section === 'pages') ? ' active' : '';
            echo '<li class="' . esc_attr($classes) . '" data-tab="' . esc_attr($section) . '">' . esc_attr(ucfirst($section)) . '</li>';
        }
        echo '</ul>';

        // Display content for each section
        foreach ($this->options as $section => $data) {
            $mode_val = $instance['cwm_visibility_' . $section . '_mode'] ?? 'hide';
            $items_val = (array) ($instance['cwm_visibility_' . $section . '_items'] ?? []);
            $this->render_tab($section, $widget, $mode_val, $items_val, $data);
        }

        // Display widget class input
        $class = $instance['cwm_custom_classes'] ?? '';
        echo '<label>CSS classes :</label><br />';
        echo '<input class="cwm-widget-classes" type="text" name="widget-' . esc_attr($widget->id_base) . '[' . esc_attr($widget->number) . '][cwm_custom_classes]" value="' . esc_attr($class) . '" />';

        echo '</div></div>';
    }


    /**
     * render_tab
     *
     * @param  mixed $section
     * @param  mixed $widget
     * @param  mixed $mode_val
     * @param  mixed $items_val
     * @param  mixed $data
     * @return void
     */
    private function render_tab($section, $widget, $mode_val, $items_val, $data)
    {
        echo '<div class="cwm-tab-content" data-tab="' . esc_attr($section) . '">';
        // echo '<label>' . ucfirst($section) . ' :</label><br />';
        echo '<select name="widget-' . esc_attr($widget->id_base) . '[' . esc_attr($widget->number) . '][cwm_visibility_' . esc_attr($section) . '_mode]" class="cwm-mode">';
        echo '<option value="hide"' . selected($mode_val, 'hide', false) . '>Hide ' . esc_html($data['label']) . '</option>';
        echo '<option value="show"' . selected($mode_val, 'show', false) . '>Show ' . esc_html($data['label']) . '</option>';
        echo '</select><br />';
        echo '<select multiple name="widget-' . esc_attr($widget->id_base) . '[' . esc_attr($widget->number) . '][cwm_visibility_' . esc_attr($section) . '_items][]" class="cwm-selectize">';
        foreach ($data['items'] as $value => $label) {
            $selected = in_array($value, $items_val) ? 'selected' : '';
            echo '<option value="' . esc_attr($value) . '" ' . esc_attr($selected) . '>' . esc_html($label) . '</option>';
        }
        echo '</select>';
        echo '</div>';
    }

    /**
     * get_all_pages
     *
     * @return void
     */
    private function _get_all_pages()
    {
        $pages = get_pages();
        $output = [];
        // Add frontpage
        $output['frontpage'] = 'Frontpage';
        $output['search'] = 'Search Page';
        $output['archives'] = 'Archive Page';
        $output['404'] = '404 Page';
        // Add other pages
        foreach ($pages as $page) {
            $output[$page->ID] = $page->post_title;
        }
        return $output;
    }

    /**
     * get_post_types
     *
     * @return array
     */
    private function _get_post_types()
    {
        $args = [
            'public'   => true,
            '_builtin' => false,
        ];
        $custom_types = get_post_types($args, 'names');
        $types = array_merge(['page', 'post'], $custom_types);
        $output = [];
        foreach ($types as $type) {
            $post_type_obj = get_post_type_object($type);
            if ($post_type_obj)
                $output[$type] = $post_type_obj->labels->singular_name;
        }
        return $output;
    }

    /**
     * get_archives_and_taxonomies
     *
     * @return array
     */
    private function _get_archives_and_taxonomies()
    {

        $archives = [];
        foreach ($this->post_types as $post_type => $label) {
            $archives[$post_type] = 'Archive ' . ucfirst($label);
        }
        $archives['author'] = 'Archive Author';


        $taxes = get_taxonomies([], 'names');
        foreach ($taxes as $tax) {
            $tax_obj = get_taxonomy($tax);
            if ($tax_obj) {
                $archives[$tax] = 'Taxonomy ' . $tax_obj->labels->name;
            }
        }
        return $archives;
    }

    /**
     * save_widget_controls
     *
     * @param  mixed $instance
     * @param  mixed $new_instance
     * @param  mixed $old_instance
     * @param  mixed $widget
     * @return void
     */
    public function save_widget_controls($instance, $new_instance, $old_instance, $widget)
    {
        foreach ($this->options as $section => $data) {
            $instance['cwm_visibility_' . $section . '_mode'] = $new_instance['cwm_visibility_' . $section . '_mode'] ?? 'hide';
            $instance['cwm_visibility_' . $section . '_items'] = $new_instance['cwm_visibility_' . $section . '_items'] ?? [];

            // Cleanup classes input
            $classes = trim(preg_replace('/\s+/', ' ',  $new_instance['cwm_custom_classes'] ?? ''));
            $classes = esc_attr($classes);
            $classes = sanitize_html_class($classes);
            $instance['cwm_custom_classes'] = $classes;
        }
        return $instance;
    }


    /* ----------------------------------------------------------------------------------------------------------------*/
    /*                                                 PUBLIC CALLBACKS
    /* ----------------------------------------------------------------------------------------------------------------*/


    /**
     * maybe_display_notice_on_block_widget_page
     *
     * @return void
     */
    public function maybe_display_notice_on_block_widget_page()
    {
        $screen = get_current_screen();
        if (
            $screen && $screen->id === 'widgets' &&
            !get_option('cwm_disable_block_editor')
        ) {
            wp_enqueue_script(
                'cwm-widget-notice',
                self::$PLUGIN_URI . 'assets/js/widget-notice.js',
                ['wp-data', 'wp-url'], // Required for wp.data.dispatch & wp.url
                '1.0',
                true
            );
        }
    }

    /**
     * maybe_display_block_editor_notice
     *
     * @return void
     */
    public function maybe_display_block_editor_notice()
    {
        // // Only show on widgets-related pages or plugin settings page
        // $screen = get_current_screen();
        // if (!in_array($screen->id, ['widgets', 'customize', 'settings_page_cwm-settings'])) {
        //     return;
        // }

        if (!get_option('cwm_disable_block_editor')) {
            echo '<div class="notice notice-warning is-dismissible">';
            echo '<p><strong>Notice:</strong> Tiny Widget Manager will not be operational because the block-based widget editor is currently <strong>enabled</strong>. You can disable it in <a href="' . esc_url(admin_url('options-general.php?page=cwm-settings')) . '">Tiny Widget Manager settings</a>.</p>';
            echo '</div>';
        }
    }

    /**
     * maybe_disable_block_editor
     *
     * @return void
     */
    public function maybe_disable_block_editor()
    {
        return !get_option('cwm_disable_block_editor'); // Return false if the option is checked
    }


    public function filter_widgets_before_output($sidebars_widgets)
    {
        if (is_admin()) return $sidebars_widgets; // Do not interfere in admin
        if ($this->disable) return $sidebars_widgets; // Do not interfere if disabled set in options

        foreach ($sidebars_widgets as $sidebar_id => &$widget_ids) {
            foreach ($widget_ids as $index => $widget_id) {
                $parsed = wp_parse_widget_id($widget_id);
                $id_base = $parsed['id_base'];
                $number = $parsed['number'];

                $option = get_option('widget_' . $id_base);
                if (!isset($option[$number])) continue;

                $instance = $option[$number];

                // Get visibility settings
                $show = true;

                foreach ($this->sections as $section) {
                    $mode = $instance['cwm_visibility_' . $section . '_mode'] ?? false;
                    if (!$mode) continue;

                    $items = $instance['cwm_visibility_' . $section . '_items'] ?? [];
                    $match = $this->match_section($section, $items);
                    // Exit loop and set show to false as soon as "not show" is identified
                    if (($mode === 'show' && !$match) || ($mode === 'hide' && $match)) {
                        $show = false;
                        break;
                    }
                }

                // Check visibility using your logic here
                if (!$show) {
                    unset($widget_ids[$index]);
                }
            }
        }

        return $sidebars_widgets;
    }

    /**
     * match_section
     *
     * @param  mixed $section
     * @param  mixed $items
     * @return void
     */
    private function match_section($section, $items)
    {
        $post_id = get_the_ID();
        $post_type = get_post_type($post_id);

        switch ($section) {
            case 'pages':
                if (in_array('frontpage', $items) && is_front_page()) return true;
                if (in_array('search', $items) && is_search()) return true;
                if (in_array('404', $items) && is_404()) return true;
                if (in_array('archives', $items) && is_archive()) return true;
                return !empty($items) && is_page($items);
                break;
            case 'posts':
                $post_type = $args['post_type'] ?? get_post_type();
                return in_array($post_type, $items);
                break;
            case 'archives':
                foreach ($items as $item) {
                    if (
                        (post_type_exists($item) && is_post_type_archive($item)) ||
                        (taxonomy_exists($item) && is_tax($item)) ||
                        ($item == 'author' && is_author())
                    ) return true;
                }
                return false;
            case 'roles':
                if (!$this->loggedin) return in_array('logged_out', $items);
                if (in_array('logged_in', $items)) return true;
                foreach ($this->user->roles as $role) {
                    if (in_array($role, $items)) return true;
                }
                return false;
            case 'devices':
                if (in_array('mobile', $items) && $this->mobile) return true;
                // if (in_array('tablet', $items) && $this->tablet ) return true;
                if (in_array('desktop', $items) && !$this->mobile /* && !$this->tablet */) return true;
                return false;
        }
        return false;
    }


    /**
     * add_custom_widget_classes
     *
     * @param  mixed $params
     * @return void
     */
    public function add_custom_widget_classes($params)
    {
        global $wp_registered_widgets;

        $widget_id = $params[0]['widget_id'];
        $widget_obj = $wp_registered_widgets[$widget_id];

        if (!is_array($widget_obj['callback']) || !is_object($widget_obj['callback'][0])) {
            return $params;
        }

        $widget_instance = $widget_obj['callback'][0];
        $option_name = $widget_instance->option_name ?? '';

        if ($option_name) {
            $all_instances = get_option($option_name);
            $widget_number = $widget_obj['params'][0]['number'] ?? null;

            if ($widget_number !== null && isset($all_instances[$widget_number])) {
                $instance = $all_instances[$widget_number];
                $custom_class = trim(preg_replace('/\s+/', ' ', $instance['cwm_custom_classes'] ?? ''));

                if (!empty($custom_class)) {
                    $params[0]['before_widget'] = preg_replace(
                        '/class=["\']([^"\']*)["\']/',
                        'class="$1 ' . esc_attr($custom_class) . '"',
                        $params[0]['before_widget']
                    );
                }
            }
        }

        return $params;
    }
}
