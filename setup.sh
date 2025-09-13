#!/bin/bash

# Altus 4 Laravel Example Setup Script
# This script helps set up the Laravel example application for Altus 4 API

set -e  # Exit on any error

echo "🚀 Setting up Altus 4 Laravel Example Application"
echo "=================================================="

# Check if .env exists
if [ ! -f .env ]; then
    echo "📋 Copying .env.example to .env..."
    cp .env.example .env
    echo "✅ Environment file created"
else
    echo "⚠️  .env file already exists, skipping..."
fi

# Install PHP dependencies
echo "📦 Installing PHP dependencies..."
composer install

# Install Node.js dependencies
echo "📦 Installing Node.js dependencies..."
npm install

# Generate application key if not set
if grep -q "APP_KEY=$" .env; then
    echo "🔑 Generating application key..."
    php artisan key:generate
    echo "✅ Application key generated"
else
    echo "⚠️  Application key already set, skipping..."
fi

# Check database configuration
echo "🗄️  Checking database configuration..."
if grep -q "DB_DATABASE=$" .env || grep -q "DB_DATABASE=laravel" .env; then
    echo "⚠️  Please configure your database settings in .env file:"
    echo "   DB_CONNECTION=mysql"
    echo "   DB_HOST=127.0.0.1"
    echo "   DB_PORT=3306"
    echo "   DB_DATABASE=altus4_example"
    echo "   DB_USERNAME=your_db_user"
    echo "   DB_PASSWORD=your_db_password"
    echo ""
    echo "   Also configure Altus 4 API settings:"
    echo "   ALTUS_API_KEY=your_altus_api_key"
    echo "   ALTUS_BASE_URL=http://localhost:3000/api/v1"
    echo "   ALTUS_DATABASE_ID=your_database_id"
    echo ""
    read -p "Have you configured the database and Altus settings? (y/N): " -n 1 -r
    echo
    if [[ ! $REPLY =~ ^[Yy]$ ]]; then
        echo "❌ Please configure your .env file and run this script again"
        exit 1
    fi
fi

# Run database migrations
echo "🗄️  Running database migrations..."
if php artisan migrate --force; then
    echo "✅ Database migrations completed"
else
    echo "❌ Database migrations failed. Please check your database configuration."
    exit 1
fi

# Seed database
echo "🌱 Seeding database with sample data..."
if php artisan db:seed --force; then
    echo "✅ Database seeded successfully"
else
    echo "⚠️  Database seeding failed, but continuing..."
fi

# Build frontend assets
echo "🎨 Building frontend assets..."
npm run build

# Clear and cache configuration
echo "🧹 Clearing and caching configuration..."
php artisan config:clear
php artisan config:cache
php artisan route:cache

echo ""
echo "🎉 Setup completed successfully!"
echo ""
echo "📋 Next steps:"
echo "1. Make sure your Altus 4 API is running (http://localhost:3000)"
echo "2. Register your database with the Altus 4 API"
echo "3. Update ALTUS_DATABASE_ID in your .env file"
echo "4. Start the development server: php artisan serve"
echo ""
echo "🌐 Your application will be available at: http://localhost:8000"
echo ""
echo "📚 For more information, see the README.md file"