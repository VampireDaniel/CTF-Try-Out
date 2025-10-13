FROM node:24-bookworm-slim

RUN apt-get update && apt-get install -y --no-install-recommends \
    supervisor \
    nginx \
    && rm -rf /var/lib/apt/lists/*

# Set working directory
WORKDIR /app

# Dependencies
COPY ./challenge/package.json ./package.json
RUN yarn

# Copy challenge code and assets
COPY ./challenge/ .

# Copy configuration files
COPY config/supervisord.conf /etc/supervisord.conf
COPY config/nginx.conf /etc/nginx/nginx.conf

# Copy flag
COPY flag.txt /flag.txt

# Expose webserver port
EXPOSE 1337

# Run supervisor
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
