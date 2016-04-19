<?php

namespace WPMVC\Common;

// Import namespaces
use \WPMVC\Helpers\WP;
use \WPMVC\Models\View;

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
     * @param string $options['templatePath'] (default: "")
     * @param string $options['templateDir'] (default: "")
     * @param string $options['templateUrl'] (default: "")
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

        // Auto-load hooks/includeds
        $this->autoloadPath(WP::applyFilters('wpmvc_app_hooks_path', $this->getTemplatePath() . '/app/hooks/*'));
        $this->autoloadPath(WP::applyFilters('wpmvc_app_inc_path', $this->getTemplatePath() . '/app/inc/*'));
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
                if (file_exists($file) && is_dir($file)) {
                    $this->autoloadPath($file . "/*");
                }
                if (file_exists($file) && is_file($file)) {
                    require_once($file);
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
        return WP::applyFilters('wpmvc_core_path', $this->corePath);
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
        return WP::applyFilters('wpmvc_template_path', $this->templatePath);
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
        return WP::applyFilters('wpmvc_template_dir', $this->templateDir);
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
        return WP::applyFilters('wpmvc_template_url', $this->templateUrl);
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
        $coreViewPath = WP::applyFilters('wpmvc_core_views_path', $this->getCorePath() . '/Views/');
        $appViewPath = WP::applyFilters('wpmvc_app_views_path', $this->getTemplatePath() . '/app/views/');
        // Create a new view and set the default path as the current path
        $theHeader = new View($coreViewPath);
        $theBody = new View($appViewPath);
        $theFooter = new View($coreViewPath);

        // Set the header view
        $theHeader->setFile(WP::applyFilters('wpmvc_header_file', 'header'));
        $theHeader->setVar('app', $this);
        // Set the footer view
        $theFooter->setFile(WP::applyFilters('wpmvc_footer_file', 'footer'));
        $theFooter->setVar('app', $this);

        // If the front page is requested
        if (WP::isFrontPage() || WP::isHome()) {
            $theBody->setFile('home');
            $theBody->setVar('app', $this);
        } else {
            // Retrieve the requested post type
            $postType = WP::getQueryVar('post_type');
            if (WP::is404()) {
                // 404 view
                $theBody->setFile('404');
            } elseif (WP::isSearch()) {
                // Search index
                $theBody->setFile('search/index');
            } elseif (WP::isTax()) {
                // Taxonomy archive
                $taxonomy = WP::getQueryVar('taxonomy');
                $theBody->setFile(sprintf('taxonomy/%s/index', $taxonomy));
            } elseif (WP::isTag()) {
                // Tag archive
                $theBody->setFile('tag/index');
            } elseif ( WP::isCategory()) {
                // Category archive
                $theBody->setFile('category/index');
            } elseif (WP::isPage()) {
                // Page view
                $theBody->setFile(WP::getCurrentPageName());
                // If view file doesn't exist, fallback to the page.php view
                if (!$theBody->hasFile()) {
                    $theBody->setFile('page');
                }
            } elseif (WP::isPostTypeArchive()) {
                // Post type archive
                $theBody->setFile(sprintf('%s/index', $postType));
            } elseif (WP::isSingle()) {
                // Retrieve the current requested post type (applies to pages, and post single and archive views)
                $postType = WP::getPostType();
                // Post permalink
                $theBody->setFile(sprintf('%s/single', $postType));
            }
        }

        // Apply the body file filter
        $theBody->setFile(WP::applyFilters('wpmvc_body_file', $theBody->getFile()));

        echo WP::applyFilters('wpmvc_header_output', $theHeader->output());
        echo WP::applyFilters('wpmvc_body_output', $theBody->output());
        echo WP::applyFilters('wpmvc_footer_output', $theFooter->output());
    }
}
