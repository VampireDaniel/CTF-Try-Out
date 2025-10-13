# Using latest debian image as of 2024-12-26
FROM debian:latest@sha256:6001f38763856a0181c479eb4844467ea3dcb843335e0d8404e6b366a271c9ae

# Install the packages of the application
RUN apt-get update && apt-get install -y \
    supervisor php-fpm nginx samba python3 python3-pip socat php-sqlite3 \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

RUN pip3 install semgrep --break-system-packages

# Copy the configuration files
COPY config/supervisord.conf /etc/supervisord.conf
COPY config/nginx.conf /etc/nginx/nginx.conf
COPY config/fpm.conf /etc/php/8.2/fpm/php-fpm.conf

# Copy the application files
COPY challenge/application /www/application
#COPY --chown=root:root challenge/validate.py /tmp
#COPY --chown=root:root challenge/semgrep_rules.yaml /tmp
COPY --chown=root:root challenge/flag.txt /flag.txt

# Set the permissions
RUN chmod 400 /flag.txt
RUN chmod -R 777 /www && chmod -R a=rX /www/application/public/assets
#RUN chmod 400 /tmp/validate.py && chmod 400 /tmp/semgrep_rules.yaml
RUN chown -R www-data:www-data /var/lib/nginx

# Set the working directory
WORKDIR /www/application

# Disable Python caching
ENV PYTHONDONTWRITEBYTECODE=1
ENV PYTHONBUFFERED=1

# Configure Samba
RUN echo "[app]" >> /etc/samba/smb.conf && \
    echo "path = /www/application" >> /etc/samba/smb.conf && \
    echo "browsable = yes" >> /etc/samba/smb.conf && \
    echo "writable = yes" >> /etc/samba/smb.conf && \
    echo "guest ok = yes" >> /etc/samba/smb.conf && \
    echo "read only = no" >> /etc/samba/smb.conf

# Expose necessary ports
EXPOSE 3000 445

# Start socat (as an additional service) and supervisord
CMD service smbd start && /usr/bin/supervisord -c /etc/supervisord.conf