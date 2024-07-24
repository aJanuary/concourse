FROM nginx:1.27.0

# Copy the php files into the container - for prod
# these will be overlayed with the dev env via compose
ADD ./docker/nginx.conf /etc/nginx/conf.d/default.conf
ADD ./site/web/ /var/www/html/
