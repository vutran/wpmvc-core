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
    public static function isCategory()
    {
        return is_category();
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

    /**
     * Scaffolds settings group, sections, and fields
     *
     * @link Section: https://codex.wordpress.org/Function_Reference/add_settings_section
     * @link Field: https://codex.wordpress.org/Function_Reference/add_settings_field
     *
     * @access public
     * @static
     * @param string $groupName                             The name of the settings group
     * @param array $options                                An array of settings sections to create in the settings group
     * @param string $options[]['id']                       The id/name of the section
     * @param string $options[]['title']                    The header line for the section
     * @param string $options[]['callback']                 A callback function for the section
     * @param string $options[]['page']                     The page slug where to display the section
     * @param array $options[]['fields']                    An array of fields for the section
     * @param array $options[]['fields'][]['id']            The field ID/name. This is also the wp_options key.
     * @param string $options[]['fields'][]['title']        The title of the field.
     * @param string $options[]['fields'][]['type']         HTML input type (enum: "text", "number", "password")
     * @return void
     */
    public static function registerSettingsGroup($groupName, $options)
    {
        // iterate through all settings fields groups and build it
        foreach ($options as $section) {
            // first, create the section
            add_settings_section($section['id'], $section['title'], $section['callback'], $section['page']);
            // iterate through all fields in the section and add it
            foreach ($section['fields'] as $field) {
                // register the settings
                register_setting($groupName, $field['id']);
                // set the callback args
                $args = array(
                    'label_for' => $field['id'],
                    'id' => $field['id'],
                    'type' => $field['type'],
                );
                // add the settings field
                add_settings_field($field['id'], $field['title'], function ($args) {
                    if (isset($args['type'])) {
                        $value = get_option($args['id']);
                        $extraParams = '';
                        // set default field type if necessary
                        switch ($args['type']) {
                            case 'text':
                                // no break
                            case 'password':
                                // no break
                            case 'number':
                                // let it pass...
                                break;
                            case 'checkbox':
                                // let it pass...
                                $extraParams = sprintf(' %s', $value ? 'checked="checked"' : '');
                                $value = 1;
                                break;
                            default:
                                // fallback on text
                                $args['type'] = 'text';
                                break;
                        }
                    } else {
                        // set the default type
                        $args['type'] = 'text';
                    }
                    echo sprintf(
                        '<input id="%s" type="%s" name="%s" value="%s" size="80" %s />',
                        $args['id'],
                        $args['type'],
                        $args['id'],
                        $value,
                        $extraParams
                    );
                }, $section['page'], $section['id'], $args);
            }
        }
    }
}
