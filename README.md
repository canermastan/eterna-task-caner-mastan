# Blog Yönetim Sistemi

🚀 **Live Demo:** [https://eterna.canermastan.com](https://eterna.canermastan.com)

## 🏗️ Mimari Yapı

### Backend Mimarisi (Laravel 12)
Proje temiz mimari prensiplerini takip eder ve katmanlı bir yapı sunar:

#### **Core Layers**
- **Models**: İlişkiler ve iş mantığı içeren Eloquent ORM modelleri
- **Controllers**: HTTP isteklerini yöneten RESTful API kontrolcüleri
- **Services**: İş mantıklarını içeren iş katmanı
- **Repositories**: Veri tabanı işlemlerini soyutlayan veri erişim katmanı
- **Policies**: Kaynak erişim kontrolü için yetkilendirme mantığı
- **DTOs**: Tür güvenli veri işleme için veri transfer nesneleri
- **Resources**: API yanıt dönüşüm katmanı
- **Requests**: Form doğrulama ve girdi temizleme

#### **Key Design Patterns**
- **Repository Pattern**: Abstracts data access logic
- **Service Layer Pattern**: Encapsulates business logic
- **Policy Pattern**: Handles authorization logic
- **Factory Pattern**: Database seeding and testing

### Frontend Mimarisi (Vue.js 3)
- **Vue 3 Composition API**: Modern reaktif programlama
- **Vue Router**: İstemci tarafı yönlendirme
- **TanStack Query**: Sunucu verisi yönetimi ve cache
- **Tailwind CSS**: Utility-first CSS framework'ü
- **Composables**: Yeniden kullanılabilir iş mantığı
- **Real-time Updates**: Laravel Echo ile WebSocket entegrasyonu

## 🛠️ Kurulum

### 🚀 Hızlı Kurulum
Projeyi hızlıca ayağa kaldırmaya hazır hale getirmek için aşağıdaki komutu çalıştırabilirsiniz:
```bash
chmod +x setup.sh
./setup.sh
```

### Manuel Kurulum
### 1. Projeyi Klonlayın
```bash
git clone https://github.com/canermastan/eterna-task-caner-mastan.git
cd eterna-task-caner-mastan
```

### 2. Bağımlılıkları Yükleyin
```bash
composer install
npm install
```
⚠️ Not: PHP gd extension'ının yüklü olması gerekmektedir

### 3. Ortam Değişkenlerini Ayarlayın
```bash
cp .env.example .env
```

### 4. Uygulama Anahtarını Oluşturun
```bash
php artisan key:generate
```

### 5. Veritabanını Hazırlayın
```bash
php artisan migrate
```

### 6. Seed Data Yükleyin
```bash
php artisan db:seed
```

###  7. Testleri Çalıştırın
```bash
php artisan test
```

### 7. Sunucuları Başlatın

**Terminal 1 - Laravel Sunucusu:**
```bash
php artisan serve
```

**Terminal 2 - Vite Development Server:**
```bash
npm run dev
```

**Terminal 3 - Reverb WebSocket Server:**
```bash
php artisan reverb:start
```
Not: Queue sistemi şu an "sync" modunda çalışmaktadır. Dolayısıyla ayrı worker başlatmanıza gerek yok.


## 📊 Seed Data İçeriği

- **10 Kategori**: Teknoloji, Yazılım Geliştirme, Web Tasarımı, vb.
- **20 Yayınlanmış Yazı**: Blog yazıları
- **8 Taslak Yazı**: Henüz yayınlanmamış yazılar
- **15+ Kullanıcı**: Farklı rollerde test kullanıcıları
- **30+ Yorum**: Onaylı, bekleyen ve reddedilen yorumlar

## 🔧 API Endpoints
Proje Postman koleksiyonu ile birlikte gelmektedir. Koleksiyon dosyası docs/ dizininde bulunabilir.

## 🎯 Kullanım Senaryoları

### Admin Kullanıcısı
1. `admin@eterna.net.tr` ile giriş yapın (pw: 12345678)
2. Admin panelinden yazıları ve yorumları yönetin
3. Yorumları onaylayın/reddedin
4. Kategorileri yönetin

### Yazar Kullanıcısı
1. `writer@eterna.net.tr` ile giriş yapın  (pw: 12345678)
2. Yeni yazı oluşturun
3. Kendi yazılarınızı düzenleyin
4. Yorumları görüntüleyin
5. Yorum yapın

### Normal Kullanıcı
1. `user@eterna.net.tr` ile giriş yapın  (pw: 12345678)
2. Yazıları okuyun
3. Yorum yapın
4. Diğer kullanıcıların yorumlarını görüntüleyin

## 🧪 Test

```bash
php artisan test
```

## ⚠️ Known Issues

- Mobil cihazlarda bazı ağ veya tarayıcı kısıtlamalarından dolayı anlık mesajlaşma (Reverb) bağlantısı stabil çalışmayabilir. Masaüstü ortamında sorunsuz çalışmaktadır. 
- Frontend tarafında hızlı geliştirme sürecinden dolayı component-based yapı tercih edilmemiştir. Ancak normalde UI tasarımlarını genelde component-based geliştiririm. Bu proje özelinde tasarım, hızlı üretim amacıyla bu şekilde uygulanmıştır.
- Notification özelliğinin backend (DB, mail, Reverb) implementasyonu tamamlanmıştır ancak frontend entegrasyonu yapılmamıştır. Backend tarafında tam olarak çalışmaktadır.