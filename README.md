# WPMVC Core

> Simple framework for building WordPress themes/apps

## Quick Start

See below for a quick usage example. All files are relative to your theme directory.

#### functions.php
````php
<?php

// Instantiate the framework
$app = new \WPMVC\Common\Bootstrap(array(
    'templatePath' => TEMPLATEPATH,
    'templateDir' => str_replace(WP_CONTENT_DIR, '', WP_CONTENT_URL . TEMPLATEPATH),
    'templateUrl' => get_template_directory_uri()
));

// Create a view
$myView = $app->createView('slug-a/slug-b');

// set view variables
$myView->set([
  'foo' => 'Hello',
  'bar' => 'World',
]);

// prints the view
echo $myView->output();
````

#### views/slug-a/slug-b.php

````php
<?php

echo $foo . ' ' . $bar;

// outputs "Hello World";
````

## Filters

| Name | Default Value | Description |
| :--- | :--- | :--- |
| `wpmvc_template_url` | | The URL to the template directory |
| `wpmvc_template_dir` | | The path name to the template |
| `wpmvc_template_path` | | The full path to the template directory |
| `wpmvc_core_path` | | The full path to the wpmvc core directory |
| `wpmvc_core_views_path` | | The path to the core's Views folder |
| `wpmvc_app_views_path` | | The path to the application's Views folder |
| `wpmvc_app_inc_path` | | The path to the application's inc folder |
| `wpmvc_app_hooks_path` | | The path to the application's hooks folder |
| `wpmvc_header_file` | | The output of the header file |
| `wpmvc_header_output` | | The output of the header file |
| `wpmvc_footer_file` | | The output of the footer file |
| `wpmvc_footer_output` | | The output of the footer file |
| `wpmvc_body_file` | | The name of the body file |
| `wpmvc_body_output` | | The output of the body file |

## Actions

| Name | Default Value | Description |
| :--- | :--- | :--- |
| `wpmvc_theme_footer` | | Ran just before the `wp_footer()` |
| `wpmvc_theme_header` | | Ran right after the opening `<body>` tag |

# Docker

## Install

```bash
$ docker-compose up install
```

## Update Composer and Packages

```bash
$ docker-compose up update
```

## PHP Code Sniffer

```bash
$ docker-compose up phpcs
```

## Validate PHP

```bash
# --rm          automatically remove the container on exit
# -v            map volume host volume to container (host:container)
# php:5.6       image name:tag
# php           command to run in container
# script.php    PHP script to run
$ docker run --rm -v "$PWD":/app -w /app php:5.6 php /path/to/script.php
```
