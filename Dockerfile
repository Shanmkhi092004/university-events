# Use the official PHP image
FROM php:8.2-apache

# Copy your project files to the Apache server root
COPY . /var/www/html/

# Expose port 80
EXPOSE 80
