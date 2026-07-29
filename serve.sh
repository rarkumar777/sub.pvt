#!/bin/bash
echo "Starting Laravel with increased File Upload Limits (100MB)..."
cd public && php -d upload_max_filesize=100M -d post_max_size=100M -S 127.0.0.1:8000 ../vendor/laravel/framework/src/Illuminate/Foundation/resources/server.php
