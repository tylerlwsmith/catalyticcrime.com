###############
# Development #
###############
FROM nginxinc/nginx-unprivileged:1.21.3-alpine AS development

WORKDIR /srv/app/

##################
# Compile Assets #
##################
FROM node:16.9-alpine3.14 AS assets

WORKDIR /srv/app
COPY webapp/ .

RUN npm install
RUN npm run production

##############
# Production #
##############
FROM development AS production

COPY webserver/production/*.template /etc/nginx/templates/
COPY --from=assets /srv/app/public /srv/app/public