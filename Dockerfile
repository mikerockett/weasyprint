FROM php:8.5-cli-alpine

RUN apk -q add --no-cache \
    py3-pip \
    py3-pillow \
    py3-cffi \
    py3-brotli \
    gcc \
    musl-dev \
    python3-dev \
    pango \
    fontconfig-dev \
    font-noto

ARG WEASYPRINT_VERSION=68.0
RUN pip3 -q install weasyprint==${WEASYPRINT_VERSION} --break-system-packages

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app
