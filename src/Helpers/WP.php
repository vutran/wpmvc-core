<?php

namespace WPMVC\Helpers;

/**
 * Static class with proxy methods
 */
class WP
{
    /**
     * @access public
     * @static
     * @param string $filter
     * @param mixed $value
     * @return mixed
     */
    public static function applyFilters($filter, $value)
    {
        if (function_exists('apply_filters')) {
            return apply_filters($filter, $value);
        }
        return $value;
    }

    /**
     * @access public
     * @static
     * @return bool
     */
    public static function isFrontPage()
    {
        return is_front_page();
    }

    /**
     * @access public
     * @static
     * @return bool
     */
    public static function isHome()
    {
        return is_home();
    }

    /**
     * @access public
     * @static
     * @return bool
     */
    public static function is404()
    {
        return is_404();
    }

    /**
     * @access public
     * @static
     * @return bool
     */
    public static function isPage()
    {
        return is_page();
    }

    /**
     * @access public
     * @static
     * @return bool
     */
    public static function isSearch()
    {
        return is_search();
    }

    /**
     * @access public
     * @static
     * @return bool
     */
    public static function isTax()
    {
        return is_tax();
    }

    /**
     * @access public
     * @static
     * @return bool
     */
    public static function isTag()
    {
        return is_tag();
    }

    /**
     * @access public
     * @static
     * @return bool
     */
    public static function isPostTypeArchive()
    {
        return is_post_type_archive();
    }

    /**
     * @access public
     * @static
     * @return bool
     */
    public static function isSingle()
    {
        return is_single();
    }

    /**
     * @access public
     * @static
     * @return string
     */
    public static function getPostType()
    {
        return get_post_type();
    }

    /**
     * @access public
     * @static
     * @return string
     */
    public static function getCurrentPageName()
    {
        global $pagename;
        return $pagename;
    }

    /**
     * @access public
     * @static
     * @return mixed
     */
    public static function getQueryVar($key)
    {
        return get_query_var($key);
    }

    /**
     * @access public
     * @static
     * @param array $post
     * @return mixed
     */
    public static function getPost($post)
    {
        return get_post($post);
    }

    /**
     * @access public
     * @static
     * @param array $data
     * @return mixed
     */
    public static function wpInsertPost($data)
    {
        return wp_insert_post($data);
    }

    /**
     * @access public
     * @static
     * @param array $data
     * @return mixed
     */
    public static function wpUpdatePost($data)
    {
        return wp_update_post($data);
    }

    /**
     * @access public
     * @static
     * @param int $postId
     * @param string $key
     * @param mixed $value
     * @return mixed
     */
    public static function updatePostMeta($postId, $key, $value)
    {
        return update_post_meta($postId, $key, $value);
    }

    /**
     * @access public
     * @static
     * @param int $postId
     * @param bool $force
     * @return mixed
     */
    public static function deletePost($postId, $force)
    {
        return wp_delete_post($postId, $force);
    }

    /**
     * @access public
     * @static
     * @param mixed $value
     * @return bool
     */
    public static function isWpError($value)
    {
        return is_wp_error($value);
    }

    /**
     * @access public
     * @static
     * @param string $fileKey
     * @param int $postId
     * @return bool|int
     */
    public static function mediaHandleUpload($fileKey, $postId)
    {
        return media_handle_upload($fileKey, $postId);
    }

    /**
     * @access public
     * @static
     * @param int $postId
     * @param int $attachmentId
     * @return bool
     */
    public static function setPostThumbnail($postId, $attachmentId)
    {
        return set_post_thumbnail($postId, $attachmentId);
    }

    /**
     * @access public
     * @static
     * @param string $name
     * @param mixed $value
     * @param int $expires
     * @return bool
     */
    public static function setTransient($name, $value, $expires)
    {
        return set_transient($name, $value, $expires);
    }

    /**
     * @access public
     * @static
     * @param string $name
     * @return mixed
     */
    public static function getTransient($name)
    {
        return get_transient($name);
    }

    /**
     * @access public
     * @static
     * @param int $postId
     * @return array
     */
    public static function getPostCustom($postId)
    {
        return get_post_custom($postId);
    }
}
