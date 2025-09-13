# Quick Start Guide

Get the Altus 4 Laravel Example running in 5 minutes.

## Prerequisites

- PHP 8.2+
- Composer
- Node.js 18+
- MySQL 8.0+
- [Altus 4 API](https://github.com/altus4/core) running

## 1. Clone and Setup

```bash
# Clone the repository
git clone <your-repo-url>
cd altus4-laravel-example

# Run the setup script
./setup.sh
```

## 2. Configure Environment

Edit `.env` file with your settings:

```bash
# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=altus4_example
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password

# Altus 4 API
ALTUS_API_KEY=your_altus_api_key
ALTUS_BASE_URL=http://localhost:3000/api/v1
ALTUS_DATABASE_ID=your_database_id
```

## 3. Start Altus 4 API

In your Altus 4 API directory:

```bash
npm run dev
# API starts at http://localhost:3000
```

## 4. Register Database with Altus 4

```bash
curl -X POST http://localhost:3000/api/v1/databases \
  -H "Authorization: Bearer YOUR_ALTUS_API_KEY" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Laravel Example Products",
    "host": "127.0.0.1",
    "port": 3306,
    "database": "altus4_example",
    "username": "your_db_user",
    "password": "your_db_password"
  }'
```

Copy the returned database ID and update `ALTUS_DATABASE_ID` in your `.env`.

## 5. Start Laravel Application

```bash
php artisan serve
# Application starts at http://localhost:8000
```

## 6. Test the Search

1. Visit http://localhost:8000
2. Try searching for products like "laptop", "gaming", or "wireless"
3. Notice the AI-enhanced search results with relevance scores

## Docker Alternative

```bash
# Start with Docker
docker-compose up -d

# The application will be available at http://localhost:8000
```

## Troubleshooting

### Common Issues

1. **"Missing Altus configuration"**
   - Ensure all Altus environment variables are set
   - Verify the Altus 4 API is running

2. **"No search results"**
   - Check that your database is registered with Altus 4
   - Verify the database ID in your configuration

3. **Database connection failed**
   - Ensure MySQL is running
   - Check database credentials in `.env`

### Debug Mode

Enable debug logging:

```bash
# In .env
LOG_LEVEL=debug
APP_DEBUG=true
```

### Health Checks

```bash
# Check Laravel application
curl http://localhost:8000

# Check Altus API
curl -H "Authorization: Bearer YOUR_API_KEY" \
     http://localhost:3000/health
```

## Next Steps

- Read the full [README.md](README.md) for detailed documentation
- Explore the [API integration patterns](README.md#api-integration-patterns)
- Check out the [code examples](README.md#usage-examples)
- Learn about [performance optimization](README.md#performance-optimization)

---

**Need help?** Check the [troubleshooting section](README.md#troubleshooting) in the main README.