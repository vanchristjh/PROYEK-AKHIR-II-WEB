# Deployment Without Nginx

This guide explains how to deploy the application using Docker without Nginx, using PHP's built-in web server instead.

## Development Environment

To run the application locally:

```bash
docker-compose up -d
```

This will start:
- PHP container with built-in web server (accessible at http://localhost:80)
- MySQL database container

## Production Environment

To deploy to production:

```bash
# Using PowerShell
./docker-deploy-prod.ps1

# Using Bash
./docker-deploy-prod.sh
```

## Important Notes

1. **PHP's Built-in Server Limitations**:
   - The PHP built-in server is designed for development, not production
   - It processes one request at a time, which may cause performance issues under heavy load
   - It doesn't handle static files as efficiently as dedicated web servers

2. **Security Considerations**:
   - The built-in server lacks many security features provided by web servers like Nginx
   - Consider adding a CDN or dedicated static file server for production

3. **Performance**:
   - For production environments with significant traffic, consider reverting to Nginx
   - Alternatively, look into using Apache or Caddy as lighter alternatives to Nginx

## Customization

You can adjust the server settings by modifying the `CMD` instruction in the Dockerfiles:

```dockerfile
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000", "--other-options"]
```

## Reverting to Nginx

If you need to revert to using Nginx, restore the original Docker configuration files or check the git history for the previous configuration.
