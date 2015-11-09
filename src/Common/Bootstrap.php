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
        $hooks = glob($this->getTemplatePath() . '/app/hooks/*');
        if ($hooks && count($hooks)) {
            foreach ($hooks as $hook) {
                if (file_exists($hook) && is_file($hook)) {
                    require_once($hook);
                }
            }
        }

        // Auto-load included files
        $incs = glob($this->getTemplatePath() . '/app/inc/*');
        if ($incs && count($incs)) {
            foreach ($incs as $inc) {
                if (file_exists($inc) && is_file($inc)) {
                    require_once($inc);
                }
            }
        }
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
        return apply_filters('wpmvc_core_path', $this->corePath);
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
        return apply_filters('wpmvc_template_path', $this->templatePath);
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
        return apply_filters('wpmvc_template_dir', $this->templateDir);
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
        return apply_filters('wpmvc_template_url', $this->templateUrl);
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
        $coreViewPath = apply_filters('wpmvc_core_views_path', $this->getCorePath() . '/Views/');
        $appViewPath = apply_filters('wpmvc_app_views_path', $this->getTemplatePath() . '/app/views/');
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
        $theBody->setFile(apply_filters('wpmvc_body_file', $theBody->getFile()));

        echo $theHeader->output();
        echo $theBody->output();
        echo $theFooter->output();
    }
}
