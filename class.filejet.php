<?php

require __DIR__ . '/vendor/filejet/filejet-external-php/src/ReplaceHtml.php';

class Filejet
{
    private static $initiated = false;
    private static $filejetHandler;

    const CONFIG_MUTATIONS = 'mutations';
    const CONFIG_IGNORED = 'ignored';
    const CONFIG_LAZY_LOAD = 'lazy_load';

    const API_KEY = 'filejet_api_key';
    const SECRET = 'filejet_secret';
    const STORAGE_ID = 'filejet_storage_id';

    const OPTIONS = [
        self::CONFIG_MUTATIONS,
        self::CONFIG_IGNORED,
        self::CONFIG_LAZY_LOAD
    ];

    public static function init()
    {
        self::$filejetHandler = new FileJet\External\ReplaceHtml(self::get_storage_id(), null, self::get_secret());

        if (!self::$initiated) {
            self::init_hooks();
        }
    }

    /**
     * Initializes WordPress hooks
     */
    private static function init_hooks()
    {
        self::$initiated = true;
        add_filter('wp_enqueue_scripts', array('Filejet', 'add_theme_scripts'), 0);
        add_filter('wp_head', array('Filejet', 'add_theme_style'), 100);
    }


    public static function my_plugin_options()
    {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }


        echo '<div class="wrap">';
        echo '<p>Here is where the form would go if I actually had options.</p>';
        echo '</div>';
    }


    public static function view($name, array $args = array())
    {
        $args = apply_filters('filejet_view_arguments', $args, $name);

        foreach ($args AS $key => $val) {
            $$key = $val;
        }

        load_plugin_textdomain('filejet');
        $file = FILEJET__PLUGIN_DIR . 'assets/views/' . $name . '.php';
        include $file;
    }


    public static function predefined_credentials()
    {
        if (defined('FILEJET_API_KEY') && defined('FILEJET_STORAGE_ID') && defined('FILEJET_SECRET')) {
            return true;
        }

        return apply_filters('filejet_predefined_credentials', false);
    }

    public static function get_api_key()
    {
        return apply_filters('filejet_get_api_key', defined('FILEJET_API_KEY') ? constant('FILEJET_API_KEY') : get_option(self::API_KEY));
    }

    public static function get_secret()
    {
        return apply_filters('filejet_get_secret', defined('FILEJET_SECRET') ? constant('FILEJET_SECRET') : get_option(self::SECRET));
    }


    public static function get_config()
    {
        $config = json_decode(apply_filters('filejet_config', defined('FILEJET_CONFIG') ? constant('FILEJET_CONFIG') : get_option('filejet_config')), true);
        if (false === is_array($config)) return [];
        
        return $config
    }

    public static function get_mutations()
    {
        $config = self::get_config();
        return array_key_exists(self::CONFIG_MUTATIONS, $config) ? $config[self::CONFIG_MUTATIONS] : [];
    }

    public static function get_ignored()
    {
        $config = self::get_config();
        return array_key_exists(self::CONFIG_IGNORED, $config) ? $config[self::CONFIG_IGNORED] : [];
    }

    public static function get_lazy_loaded()
    {
        $config = self::get_config();
        return array_key_exists(self::CONFIG_LAZY_LOAD, $config) ? $config[self::CONFIG_LAZY_LOAD] : [];
    }

    public static function get_storage_id()
    {
        return apply_filters('filejet_get_storage_id', defined('FILEJET_STORAGE_ID') ? constant('FILEJET_STORAGE_ID') : get_option(self::STORAGE_ID));
    }

    public static function add_theme_style()
    {
        echo "<style>
		img.fj-image:not([src]) {
		  visibility: hidden;
		}
	  </style>";
    }

    public static function add_theme_scripts()
    {
        $datatoBePassed = array(
            'storage_id' => self::get_storage_id(),
            'js_path' => plugin_dir_url(__FILE__) . './assets/',
        );
        wp_localize_script('filejet', 'filejet_vars', $datatoBePassed);
    }

    public static function is_json($string)
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    public static function content_filter($content)
    {
        if(self::is_json($content)) {
            $json = json_decode($content, true);
            self::recursive_scan($json);
            return json_encode($json, true);
        }

        return self::$filejetHandler->replaceImages($content, \Filejet::get_ignored(), \Filejet::get_mutations(), \Filejet::get_lazy_loaded());
    }

    private static function recursive_scan(&$iterator)
    {
        foreach($iterator as $key => &$value){
            if(is_array($value)){
                self::recursive_scan($value);
            } else{
                $value = self::$filejetHandler->replaceImages($value, \Filejet::get_ignored(), \Filejet::get_mutations(), \Filejet::get_lazy_loaded());
            }
        }
    }


    private static function customConfiguration($object)
    {
        $config = self::get_config();

        $classes = explode(' ', $object->getAttribute('class'));
        if (!empty($classes) && !empty($config)) {
            $result = array_intersect_key(array_flip($classes), $config);

            if ($result) {
                $configSetting = $config[key($result)];
                return $config[key($result)];
            }
        }
        return false;
    }

    private static function bail_on_activation($message, $deactivate = true)
    {
        ?>
        <!doctype html>
        <html>
        <head>
            <meta charset="<?php bloginfo('charset'); ?>"/>
            <style>
                * {
                    text-align: center;
                    margin: 0;
                    padding: 0;
                    font-family: "Lucida Grande", Verdana, Arial, "Bitstream Vera Sans", sans-serif;
                }

                p {
                    margin-top: 1em;
                    font-size: 18px;
                }
            </style>
        </head>
        <body>
        <p><?php echo esc_html($message); ?></p>
        </body>
        </html>
        <?php
        if ($deactivate) {
            $plugins = get_option('active_plugins');
            $filejet = plugin_basename(FILEJET__PLUGIN_DIR . 'filejet.php');
            $update = false;
            foreach ($plugins as $i => $plugin) {
                if ($plugin === $filejet) {
                    $plugins[$i] = false;
                    $update = true;
                }
            }

            if ($update) {
                update_option('active_plugins', array_filter($plugins));
            }
        }
        exit;
    }


    /**
     * Attached to activate_{ plugin_basename( __FILES__ ) } by register_activation_hook()
     *
     * @static
     */

    public static function plugin_activation()
    {
        if (version_compare($GLOBALS['wp_version'], FILEJET__MINIMUM_WP_VERSION, '<')) {
            load_plugin_textdomain('filejet');
            $message = '<strong>' . sprintf(esc_html__('Filejet %s requires WordPress %s or higher.', 'filejet'), FILEJET_VERSION, FILEJET__MINIMUM_WP_VERSION) . '</strong> ' . sprintf(__('Please <a href="%1$s">upgrade WordPress</a> to a current version, or <a href="%2$s">downgrade to version 2.4 of the Filejet plugin</a>.', 'filejet'), 'https://codex.wordpress.org/Upgrading_WordPress', 'https://wordpress.org/extend/plugins/filejet/download/');
            Filejet::bail_on_activation($message);
        }

        $config = [];

        $config_current = Filejet::get_config();
        $config = array_merge($config, array_key_exists(Filejet::CONFIG_LAZY_LOAD, $config_current) ? $config_current[Filejet::CONFIG_LAZY_LOAD] : []);

        $config['data-src'] = 'data-src';
        $config['data-srcset'] = 'data-srcset';
        $config_current[Filejet::CONFIG_LAZY_LOAD] = $config;
        update_option('filejet_config', json_encode($config_current));
    }

    /**
     * Removes all connection options
     *
     * @static
     */
    public static function plugin_deactivation()
    {
    }

    /**
     * Removes all connection options
     *
     * @static
     */
    public static function plugin_uninstall()
    {
        delete_option(self::API_KEY);
        delete_option(self::SECRET);
        delete_option(self::STORAGE_ID);
    }

    public static function is_rest()
    {
        global $wp_rewrite;

        if (null === $wp_rewrite || !function_exists('rest_url') || !function_exists('get_rest_url') || empty($_SERVER['REQUEST_URI'])) {
            return false;
        }

        $sRestUrlBase = get_rest_url(get_current_blog_id(), '/');
        $sRestPath = trim(parse_url($sRestUrlBase, PHP_URL_PATH), '/');
        $sRequestPath = trim($_SERVER['REQUEST_URI'], '/');

        return (strpos($sRequestPath, $sRestPath) === 0);
    }

    /**
     * Use for logging variables to debug.log
     * In order to see the logs within the debug.log you need to enable Wordpress logging
     * See https://wordpress.org/support/article/debugging-in-wordpress/
     *
     * @static
     */
    public static function log($var) {
        error_log(print_r($var, true));
    }
}
