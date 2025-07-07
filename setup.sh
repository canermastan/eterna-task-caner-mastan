#!/bin/bash

echo "Eterna Blog Yönetim Sistemi Kurulum Scripti"
echo "========================================"

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

check_error() {
    if [ $? -ne 0 ]; then
        echo -e "${RED}❌ Hata: $1${NC}"
        exit 1
    fi
}

success() {
    echo -e "${GREEN}✅ $1${NC}"
}

warning() {
    echo -e "${YELLOW}⚠️  $1${NC}"
}

echo "📦 Composer bağımlılıkları yükleniyor..."
composer install
check_error "Composer bağımlılıkları yüklenemedi"

echo "📦 NPM bağımlılıkları yükleniyor..."
npm install
check_error "NPM bağımlılıkları yüklenemedi"

echo "📄 Ortam dosyası (.env) oluşturuluyor..."
cp .env.example .env
check_error ".env dosyası kopyalanamadı"

echo "🔑 Uygulama anahtarı oluşturuluyor..."
php artisan key:generate
check_error "Uygulama anahtarı oluşturulamadı"

echo "🗄️  Veritabanı migration'ları çalıştırılıyor..."
php artisan migrate
check_error "Migration'lar çalıştırılamadı"

echo "🌱 Seed data yükleniyor... (PostSeeder biraz bekletebilir)"
php artisan db:seed
check_error "Seed data yüklenemedi"

echo "🔗 Storage link oluşturuluyor..."
php artisan storage:link
check_error "Storage link oluşturulamadı"

echo "🧹 Cache temizleniyor..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo " 🧪 Testler çalıştırılıyor..."
php artisan test
check_error "Testler çalıştırılamadı. Lütfen manuel olarak çalıştırın -> php artisan test"

success "Kurulum tamamlandı!"

echo ""
echo "🎯 Test Kullanıcıları:"
echo "======================"
echo "Admin:     admin@eterna.net.tr / 12345678"
echo "Yazar:     writer@eterna.net.tr / 12345678"
echo "Kullanıcı: user@eterna.net.tr / 12345678"
echo ""

echo "🚀 Sunucuları başlatmak için:"
echo "============================="
echo "Terminal 1: php artisan serve"
echo "Terminal 2: npm run dev"
echo "Terminal 3: php artisan reverb:start"
echo ""
echo "⚡ Queue sistemi şu an 'sync' modunda olduğu için ayrı worker başlatmanıza gerek yok."


success "Proje başarıyla kuruldu! 🎉" 