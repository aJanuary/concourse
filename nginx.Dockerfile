FROM nginx:1.31.6

# nginx only serves the static files; every other path goes to php-fpm in
# the web image, so the PHP sources stay out of this one
ADD ./docker/nginx.conf /etc/nginx/conf.d/default.conf
ADD ./site/web/502.html /var/www/html/502.html
ADD ./site/web/resources /var/www/html/resources
