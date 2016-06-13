#!/bin/bash

# switch to theme directory
cd /app

# validate composer.json
composer validate

# install packages
composer install
