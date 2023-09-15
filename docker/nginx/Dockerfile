FROM  nginx:stable-alpine

ARG USER
ARG GROUP
ARG UID
ARG GID

RUN addgroup -g ${GID} ${GROUP} \
    && adduser -G ${GROUP} -u ${UID} ${USER} -s /bin/sh -D ${USER}

RUN mkdir -p /var/www/html \
	&& chown -R ${USER}:${GROUP} /var/www/html
