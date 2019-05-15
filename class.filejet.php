<?php

require('./vendor/filejet/filejet-external-php/src/ReplaceHtml.php');

class Filejet
{
    private static $initiated = false;
    private static $filejetHandler;

    const CONFIG_MUTATIONS = 'mutations';
    const CONFIG_IGNORED = 'ignored';

    const OPTIONS = [
        self::CONFIG_MUTATIONS,
        self::CONFIG_IGNORED
    ];

    public static function init()
    {
        self::$filejetHandler = new FileJet\External\ReplaceHtml(self::get_storage_id(), 'data-fj-src', null, self::get_secret());

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
        include($file);
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
        return apply_filters('filejet_get_api_key', defined('FILEJET_API_KEY') ? constant('FILEJET_API_KEY') : get_option('filejet_api_key'));
    }

    public static function get_secret()
    {
        return apply_filters('filejet_get_secret', defined('FILEJET_SECRET') ? constant('FILEJET_SECRET') : get_option('filejet_secret'));
    }


    public static function get_config()
    {
        return json_decode(apply_filters('filejet_config', defined('FILEJET_CONFIG') ? constant('FILEJET_CONFIG') : get_option('filejet_config')), true);
    }

    public static function get_mutations()
    {
        $config = self::get_config();
        return $config[self::CONFIG_MUTATIONS] ?? [];
    }

    public static function get_ignored()
    {
        $config = self::get_config();
        return $config[self::CONFIG_IGNORED] ?? [];
    }

    public static function get_storage_id()
    {
        return apply_filters('filejet_get_storage_id', defined('FILEJET_STORAGE_ID') ? constant('FILEJET_STORAGE_ID') : get_option('filejet_storage_id'));
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

    public static function content_filter($content)
    {
        return self::$filejetHandler->replaceImages($content, \Filejet::get_ignored(), \Filejet::get_mutations());
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


    public static function excerpt_filter($content)
    {
        return self::$filejetHandler->replaceImages($content, \Filejet::get_ignored(), \Filejet::get_mutations());
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
     * @static
     */

    public static function plugin_activation()
    {
        if (version_compare($GLOBALS['wp_version'], FILEJET__MINIMUM_WP_VERSION, '<')) {
            load_plugin_textdomain('filejet');
            $message = '<strong>' . sprintf(esc_html__('Filejet %s requires WordPress %s or higher.', 'filejet'), FILEJET_VERSION, FILEJET__MINIMUM_WP_VERSION) . '</strong> ' . sprintf(__('Please <a href="%1$s">upgrade WordPress</a> to a current version, or <a href="%2$s">downgrade to version 2.4 of the Filejet plugin</a>.', 'filejet'), 'https://codex.wordpress.org/Upgrading_WordPress', 'https://wordpress.org/extend/plugins/filejet/download/');
            Filejet::bail_on_activation($message);
        }
    }

    /**
     * Removes all connection options
     * @static
     */
    public static function plugin_deactivation()
    {

    }

    public static function is_rest()
    {
        $prefix = rest_get_url_prefix();
        if (defined('REST_REQUEST') && REST_REQUEST
            || isset($_GET['rest_route'])
            && strpos(trim($_GET['rest_route'], '\\/'), $prefix, 0) === 0)
            return true;

        $rest_url = wp_parse_url(site_url($prefix));
        $current_url = wp_parse_url(add_query_arg(array()));
        return strpos($current_url['path'], $rest_url['path'], 0) === 0;
    }
}
