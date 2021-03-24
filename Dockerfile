FROM clusterexpert/php:lighttpd

COPY ./ ./
EXPOSE $PORT
