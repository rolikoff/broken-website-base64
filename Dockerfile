FROM php:8.3-cli

WORKDIR /app
COPY index.php render.php ./

EXPOSE 9009

CMD ["php", "-S", "0.0.0.0:9009", "-t", "/app"]
