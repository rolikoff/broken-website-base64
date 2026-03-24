FROM php:8.3-cli

WORKDIR /app
COPY index.php render.php ./

EXPOSE 8080

CMD ["php", "-S", "0.0.0.0:8080", "-t", "/app"]
