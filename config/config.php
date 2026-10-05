<?php
return ['app_env'=>getenv('APP_ENV')?:'production','app_url'=>getenv('APP_URL')?:'http://localhost:8080','db'=>['host'=>getenv('DB_HOST')?:'127.0.0.1','port'=>getenv('DB_PORT')?:'3306','name'=>getenv('DB_DATABASE')?:'restaurant','user'=>getenv('DB_USERNAME')?:'restaurant','pass'=>getenv('DB_PASSWORD')?:'restaurant']];
