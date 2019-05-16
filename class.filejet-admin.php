<?php

class Filejet_Admin
{

    const NONCE = 'filejet-config-data';

    const TAB_CONFIGURATION = 'configuration';
    const TAB_MUTATIONS = 'mutations';

    private static $initiated = false;
    private static $notices = array();


    public static function init()
    {
        if (!self::$initiated) {
            self::init_hooks();
        }

        switch (Filejet_Action::validate($_POST['action'] ?? null)) {
            case Filejet_Action::ENTER_KEY:
                self::enter_api_key();
                break;
            case Filejet_Action::ADD_MUTATION_SETTING:
                self::add_mutation_setting();
                break;
            case Filejet_Action::ADD_IGNORE_SETTING:
                self::add_ignore_setting();
                break;
            case Filejet_Action::DELETE_MUTATION_SETTING:
                self::delete_setting(Filejet::CONFIG_MUTATIONS);
                break;
            case Filejet_Action::DELETE_IGNORE_SETTING:
                self::delete_setting(Filejet::CONFIG_IGNORED);
                break;
        }
    }

    public static function init_hooks()
    {
        self::$initiated = true;
        add_action('admin_init', array('Filejet_Admin', 'admin_init'));
        add_action('admin_menu', array('Filejet_Admin', 'admin_menu'));
        add_action('admin_notices', array('Filejet_Admin', 'display_notice'));
        add_action('admin_enqueue_scripts', array('Filejet_Admin', 'load_resources'));
        add_action('after_setup_theme', 'myplugin_after_setup_theme');
        add_filter('all_plugins', array('Filejet_Admin', 'modify_plugin_description'));
        add_filter('plugin_action_links_' . FILEJET_PLUGIN_BASENAME, array('Filejet_Admin', 'addPluginActionLinks'));
    }

    function addPluginActionLinks($action_links)
    {
        $settings_link = '<a href="options-general.php?page=' . FILEJET_PLUGIN_BASENAME . '">' . __('Settings', FILEJET_PLUGIN_BASENAME) . '</a>';
        array_unshift($action_links, $settings_link);

        return $action_links;
    }


    public static function admin_init()
    {
        load_plugin_textdomain('filejet');
    }


    public static function display_api_key_warning()
    {
        Filejet::view('notice', array('type' => 'plugin'));
    }

    public static function display_notice()
    {
        global $hook_suffix;


        if (in_array($hook_suffix, ['plugins.php', 'index.php', 'upload.php']) && !Filejet::get_api_key()) {
            self::display_api_key_warning();
        }
    }


    /**
     * When FileJet is active, remove the "Activate Filejet" step from the plugin description.
     */
    public static function modify_plugin_description($all_plugins)
    {
        if (isset($all_plugins['filejet/filejet.php'])) {
            if (Filejet::get_api_key()) {
                $all_plugins['filejet/filejet.php']['Description'] = __('FileJet Professional, your digital asseet optimization service', 'filejet');
            } else {
                $all_plugins['filejet/filejet.php']['Description'] = __('FileJet Professional, your digital asseet optimization service. To get started, just go to <a href="admin.php?page=filejet-keys">your FileJet Settings page</a> to set up your API key.', 'filejet');
            }
        }

        return $all_plugins;
    }

    public static function admin_menu()
    {
        add_options_page('FileJet Pro', 'FileJet Pro', 'manage_options', FILEJET_PLUGIN_BASENAME, ['Filejet_Admin', 'display_page']);

    }

    public static function admin_head()
    {
        if (!current_user_can('manage_options')) {
            return;
        }
    }

    public static function admin_plugin_settings_link($links)
    {
        $settings_link = '<a href="' . esc_url(self::get_page_url()) . '">' . __('Settings', 'filejet') . '</a>';
        array_unshift($links, $settings_link);
        return $links;
    }

    public static function get_page_url($page = FILEJET_PLUGIN_BASENAME)
    {

        return add_query_arg(['page' => $page], admin_url('admin.php'));
    }

    public static function get_page_url_with_tab($tab = self::TAB_CONFIGURATION, $page = FILEJET_PLUGIN_BASENAME)
    {
        return add_query_arg(['page' => $page, 'tab' => $tab], admin_url('admin.php'));
    }

    public static function tab_is_allowed($tab)
    {
        return in_array(
            $tab,
            [
                self::TAB_CONFIGURATION,
                self::TAB_MUTATIONS
            ],
            true
        );
    }

    public static function load_resources()
    {
        global $hook_suffix;
        $isSettingPage = preg_match('/settings_page_(.*)\/filejet/', $hook_suffix) !== false;

        if (in_array(
                $hook_suffix, apply_filters(
                'Filejet_Admin_page_hook_suffixes', array(
                    'index.php', // dashboard
                    'plugins.php',
                    'upload.php',
                    'options-general.php'
                )
            ), true
            ) || $isSettingPage
        ) {
            wp_register_style('filejet.css', plugin_dir_url(__FILE__) . 'assets/filejet.css', array(), FILEJET_VERSION);
            wp_enqueue_style('filejet.css');
        }
    }

    public static function enter_api_key()
    {
        if (!current_user_can('manage_options')) {
            die(__('Cheatin&#8217; uh?', 'Filejet'));
        }

        if (!wp_verify_nonce($_POST['_wpnonce'], self::NONCE)) {
            return false;
        }

        if (Filejet::predefined_credentials()) {
            return false; //should not get here, credentials from config
        }

        $storage_id = preg_replace('/[^a-z0-9]/i', '', $_POST['storageId']);
        $new_key = preg_replace('/[^a-z0-9]/i', '', $_POST['key']);
        $new_secret = preg_replace('/[^a-z0-9]/i', '', $_POST['secret']);
        $old_key = Filejet::get_api_key();

        if (empty($new_key)) {
            if (!empty($old_key)) {
                delete_option('filejet_api_key');
                delete_option('filejet_storage_id');
                delete_option('secret');
                self::$notices[] = 'new-key-empty';
            }
        } elseif ($new_key != $old_key) {
            self::save_key($storage_id, $new_key, $new_secret);
        }

        return true;
    }


    public static function add_mutation_setting()
    {
        if (!current_user_can('manage_options')) {
            die(__('Cheatin&#8217; uh?', 'Filejet'));
        }

        if (!wp_verify_nonce($_POST['_wpnonce'], self::NONCE)) {
            return false;
        }

        $mutation = preg_replace('/[^a-z0-9-_]/i', '', $_POST['mutation']);
        $class = preg_replace('/[^a-z0-9-_]/i', '', $_POST['class']);

        if (in_array($class, ['fj-image', 'fj-image-loaded'])) {
            return false;
        }

        $config = [];

        $config_current = Filejet::get_config();
        $config = array_merge($config, $config_current[Filejet::CONFIG_MUTATIONS] ?? []);


        if (!empty($class) && !empty($mutation)) {
            $config[$class] = $mutation;
            $config_current[Filejet::CONFIG_MUTATIONS] = $config;
            update_option('filejet_config', json_encode($config_current));
        }

        return true;
    }

    public static function add_ignore_setting()
    {
        if (!current_user_can('manage_options')) {
            die(__('Cheatin&#8217; uh?', 'Filejet'));
        }

        if (!wp_verify_nonce($_POST['_wpnonce'], self::NONCE)) {
            return false;
        }

        $class = preg_replace('/[^a-z0-9-_]/i', '', $_POST['class']);

        if (in_array($class, ['fj-image', 'fj-image-loaded'])) {
            return false;
        }

        $config = [];

        $config_current = Filejet::get_config();
        $config = array_merge($config, $config_current[Filejet::CONFIG_IGNORED] ?? []);


        if (!empty($class)) {
            $config[$class] = $class;
            $config_current[Filejet::CONFIG_IGNORED] = $config;
            update_option('filejet_config', json_encode($config_current));
        }

        return true;
    }


    public static function delete_setting(string $option)
    {
        if (!current_user_can('manage_options')) {
            die(__('Cheatin&#8217; uh?', 'Filejet'));
        }

        if (false === in_array($option, Filejet::OPTIONS)) {
            die(__('Invalid option', 'Filejet'));
        }


        if (!wp_verify_nonce($_POST['_wpnonce'], self::NONCE)) {
            return false;
        }

        $class = preg_replace('/[^a-z0-9-_]/i', '', $_POST['class']);

        $config = Filejet::get_config();
        $option_config = $config[$option] ?? [];
        if (!empty($class) && array_key_exists($class, $option_config)) {
            unset($option_config[$class]);

            $config[$option] = $option_config;
            update_option('filejet_config', json_encode($config));
        }

        return true;
    }


    public static function save_key($storage_id, $api_key, $secret)
    {
        update_option('filejet_api_key', $api_key);
        update_option('filejet_storage_id', $storage_id);
        update_option('filejet_secret', $secret);
        self::$notices['status'] = 'new-key-valid';
    }

    public static function dashboard_stats()
    {

    }


    public static function plugin_action_links($links, $file)
    {
        if ($file == plugin_basename(plugin_dir_url(__FILE__) . '/filejet.php')) {
            $links[] = '<a href="' . esc_url(self::get_page_url()) . '">' . esc_html__('Settings', 'filejet') . '</a>';
        }

        return $links;
    }


    public static function display_page()
    {
        if (!Filejet::get_api_key() || (isset($_GET['view']) && $_GET['view'] == 'welcome')) {
            self::display_welcome_page();
        } else {
            self::display_configuration_page();
        }
    }

    public static function display_welcome_page()
    {


        $filejet_user = false;

        // if ( isset( $_GET['token'] ) && preg_match('/^(\d+)-[0-9a-f]{20}$/', $_GET['token'] ) )
        //     $filejet_user = self::verify_wpcom_key( '', '', array( 'token' => $_GET['token'] ) );
        // elseif ( $jetpack_user = self::get_jetpack_user() )
        //     $filejet_user = self::verify_wpcom_key( $jetpack_user['api_key'], $jetpack_user['user_id'] );

        // if ( isset( $_GET['action'] ) ) {
        //     if ( $_GET['action'] == 'save-key' ) {
        //         if ( is_object( $filejet_user ) ) {
        //             self::save_key( $filejet_user->api_key );
        //             self::display_configuration_page();
        //             return;
        //         }
        //     }
        // }

        Filejet::view('welcome', compact('filejet_user'));

    }

    public static function display_status()
    {
        Filejet::view('notice', array('type' => 'service-ok'));
    }

    public static function display_configuration_page()
    {
        $api_key = Filejet::get_api_key();


        $notices = array();

        Filejet::view('setup', compact('api_key', 'filejet_user', 'notices'));
    }


}
