<?php
/*
Plugin Name: isosec-mem
Description: Diverse funksjoner for medlemsregister
Version: 1.0
Author: Pål Bergquist
Author URI: https://isosec.no
*/
if (!function_exists('wp_enqueue_scripts')) die('Not proper Wordpress initialization!');

if ( !class_exists( 'ISOSEC_Mem' ) ) {
    class ISOSEC_Mem
    {
        public $plugin_dir;
        private $ctx;

        public function init() {
            spl_autoload_register([$this, 'autoload_classes']);
            $this->ctx = new ISOSEC_Context();
            $this->ctx->pluginDir = dirname(__FILE__);
            $this->ctx->pluginUrl = plugins_url('', __FILE__);
            $this->ctx->htmlDir = dirname(__FILE__).'/html';
            $this->ctx->imageUrl = plugins_url('images', __FILE__);
            //$this->ctx->uploadDir =  wp_upload_dir()['basedir'] . '/chadm_uploads';
            $this->ctx->cssUrl = plugins_url('css', __FILE__);
            $this->ctx->jsUrl = plugins_url('js', __FILE__);

            add_shortcode('isosec_mem', array($this ,"isosec_shortcode"));
            return;
        }

        public function autoload_classes($class) {
            $mydir = dirname(__FILE__);  // Ctx not yet ready
            $file = $mydir . '/classes/' . str_replace('_', '-', strtolower($class)) . '.php';
            if (file_exists($file)) {
                include $file;
            }
        }

        public function isosec_shortcode( $atts = [], $content, $shortcode) {
            // do something to $content
            // always return
            //ob_start();
            $ctx = $this->ctx;
            $html = $ctx->getHtmlObj();
            //$html_parts = $ctx->getHtmlPartsObj();
            $page_tmpl = $html->getTemplate('memberList.html', 'rad');
            $page = "";
            $users = get_users();
            foreach($users as $user) {
                $dict['display_name'] = $user->display_name;
                $dict['user_email'] = $user->user_email;
                $page .= $html->replace($page_tmpl, $dict);
            }
            $page_tmpl = $html->getTemplate('memberList.html', 'tabell');
            $dict['tabell'] = $page;
            $page = $html->replace($page_tmpl, $dict);
            return $page;
        }
    }
    $obj = new ISOSEC_Mem();
    $obj->init();
}

?>
