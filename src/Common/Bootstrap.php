<?php

namespace WPMVC\Common;

// Import namespaces
use WPMVC\Models\View;

/**
 * Core WPMVC Bootstrap
 */
class Bootstrap
{
    /**
     * The full server path to the wpmvc core directory
     *
     * @access protected
     * @var string
     */
    protected $corePath;

    /**
     * The full server path to the template directory
     *
     * @access protected
     * @var string
     */
    protected $templatePath;

    /**
     * The template directory (excludes the hostname and protocol)
     *
     * @access protected
     * @var string
     */
    protected $templateDir;

    /**
     * The full URL to the template directory
     *
     * @access protected
     * @var string
     */
    protected $templateUrl;

    /**
     * Initializes the WordPress theme
     *
     * @access public
     * @param array $options
     * @param string $options['templateDir'] (default: "")
     * @return void
     */
    public function __construct($options = array())
    {
        $defaults = [
            'templatePath' => '',
            'templateDir' => '',
            'templateUrl' => ''
        ];
        $options = array_merge($defaults, $options);
        $this
            ->setCorePath(dirname(__DIR__))
            ->setTemplatePath($options['templatePath'])
            ->setTemplateDir($options['templateDir'])
            ->setTemplateUrl($options['templateUrl']);

        // Auto-load hook files
        $this->autoloadPath($this->filter('wpmvc_app_hooks_path', $this->getTemplatePath() . '/app/hooks/*'));

        // Auto-load included files
        $this->autoloadPath($this->filter('wpmvc_app_inc_path', $this->getTemplatePath() . '/app/inc/*'));
    }

    /**
     * Autoloads all files in the given path
     *
     * @access public
     * @param string $path
     * @return void
     */
    public function autoloadPath($path)
    {
        $files = glob($path);
        if ($files && count($files)) {
            foreach ($files as $file) {
                if (file_exists($file) && is_file($file)) {
                    require_once($file);
                }
            }
        }
    }

    /**
     * Route to the WordPress apply_filters() function if available
     *
     * If the function apply_filters() doesn't exist, return the original value
     *
     * @access protected
     * @param string $filter
     * @param mixed $value
     * @return mixed
     */
    protected function filter($filter, $value)
    {
        if (function_exists('apply_filters')) {
            return apply_filters($filter, $value);
        }
        return $value;
    }

    /**
     * Sets the core path
     *
     * @access public
     * @param string $path
     * @return self
     */
    public function setCorePath($path)
    {
        $this->corePath = $path;
        return $this;
    }

    /**
     * Gets the core path
     *
     * @access public
     * @return string
     */
    public function getCorePath()
    {
        return $this->filter('wpmvc_core_path', $this->corePath);
    }

    /**
     * Sets the template path
     *
     * @access public
     * @param string $path
     * @return self
     */
    public function setTemplatePath($path)
    {
        $this->templatePath = $path;
        return $this;
    }

    /**
     * Gets the template path
     *
     * @access public
     * @return string
     */
    public function getTemplatePath()
    {
        return $this->filter('wpmvc_template_path', $this->templatePath);
    }

    /**
     * Sets the template directory
     *
     * @access public
     * @param string $dir
     * @return self
     */
    public function setTemplateDir($dir)
    {
        $this->templateDir = $dir;
        return $this;
    }

    /**
     * Gets the template directory
     *
     * @access public
     * @return string
     */
    public function getTemplateDir()
    {
        return $this->filter('wpmvc_template_dir', $this->templateDir);
    }

    /**
     * Sets the template URL
     *
     * @access public
     * @param string $url
     * @return self
     */
    public function setTemplateUrl($url)
    {
        $this->templateUrl = $url;
        return $this;
    }

    /**
     * Gets the template URL
     *
     * @access public
     * @return string
     */
    public function getTemplateUrl()
    {
        return $this->filter('wpmvc_template_url', $this->templateUrl);
    }

    /**
     * Create a new View instance
     *
     * @access public
     * @param string $file (default : null)
     * @return \WPMVC\Models\View
     */
    public function createView($file = null)
    {
        $view = new View($this->getTemplatePath() . '/app/views/');
        if ($file) {
            $view->setFile($file);
        }
        return $view;
    }

    /**
     * Begin the routing
     *
     * @access public
     * @return void
     */
    public function init()
    {
        $coreViewPath = $this->filter('wpmvc_core_views_path', $this->getCorePath() . '/Views/');
        $appViewPath = $this->filter('wpmvc_app_views_path', $this->getTemplatePath() . '/app/views/');
        // Create a new view and set the default path as the current path
        $theHeader = new View($coreViewPath);
        $theBody = new View($appViewPath);
        $theFooter = new View($coreViewPath);

        // Set the header view
        $theHeader->setFile('header');
        $theHeader->setVar('app', $this);
        // Set the footer view
        $theFooter->setFile('footer');
        $theFooter->setVar('app', $this);

        // If the front page is requested
        if (is_front_page() || is_home()) {
            $theBody->setFile('home');
            $theBody->setVar('app', $this);
        } else {
            // Retrieve the requested post type
            $postType = get_query_var('post_type');
            if (is_404()) {
                // 404 view
                $theBody->setFile('404');
            } elseif (is_search()) {
                // Search index
                $theBody->setFile('search/index');
            } elseif (is_tax()) {
                // Taxonomy archive
                $taxonomy = get_query_var('taxonomy');
                $theBody->setFile(sprintf('taxonomy/%s/index', $taxonomy));
            } elseif (is_tag()) {
                // Tag archive
                $theBody->setFile('tag/index');
            } elseif (is_page()) {
                global $pagename;
                // Page view
                $theBody->setFile($pagename);
                // If view file doesn't exist, fallback to the page.php view
                if (!$theBody->hasFile()) {
                    $theBody->setFile('page');
                }
            } elseif (is_post_type_archive()) {
                // Post type archive
                $theBody->setFile(sprintf('%s/index', $postType));
            } elseif (is_single()) {
                // Retrieve the current requested post type (applies to pages, and post single and archive views)
                $postType = get_post_type();
                // Post permalink
                $theBody->setFile(sprintf('%s/single', $postType));
            }
        }

        // Apply the body file filter
        $theBody->setFile($this->filter('wpmvc_body_file', $theBody->getFile()));

        echo $theHeader->output();
        echo $theBody->output();
        echo $theFooter->output();
    }
}
