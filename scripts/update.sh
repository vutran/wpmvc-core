#!/bin/bash

# switch to theme directory
cd /app

# update composer
composer selfupdate

# validate composer.json
composer validate

# install packages
composer update
