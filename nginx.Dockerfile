FROM nginx:1.27.0

# Copy the php files into the container - for prod
# these will be overlayed with the dev env via compose
ADD ./docker/nginx-dev.conf /etc/nginx/conf.d/default.conf
ADD ./site/web/resources /var/www/html/resources
