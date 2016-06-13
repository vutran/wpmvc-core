#!/bin/bash

cd /app

./vendor/bin/phpcs --standard=/app/ruleset.xml src
