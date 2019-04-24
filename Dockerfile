FROM nginx:alpine
RUN mkdir /webroot
COPY ./webroot /webroot
COPY ./site.conf /etc/nginx/conf.d/default.conf
