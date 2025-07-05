# Katkıda Bulunma Kılavuzu

Bu projeye katkıda bulunmak istediğiniz için teşekkür ederiz! Bu kılavuz, projeye katkıda bulunma sürecini açıklamaktadır.

## 🚀 Proje Yapısı

Bu proje, modern Laravel best practice'lerini kullanarak geliştirilmiştir:

### Katmanlar
- **Models**: Eloquent modelleri (`app/Models/`)
- **Controllers**: API endpoint'leri (`app/Http/Controllers/Api/`)
- **Services**: İş mantığı (`app/Services/`)
- **Repositories**: Veri erişim katmanı (`app/Repositories/`)
- **DTOs**: Veri transfer objeleri (`app/DataObjects/`)
- **Policies**: Yetkilendirme kuralları (`app/Policies/`)
- **Resources**: API response formatı (`app/Http/Resources/`)
- **Requests**: Form validasyon (`app/Http/Requests/`)

## 📝 Kodlama Standartları

### 1. PSR-12 Standartları
```bash
vendor/bin/pint
```

### 2. Naming Conventions
- **Classes**: PascalCase (`CommentService`)
- **Methods**: camelCase (`createComment()`)
- **Variables**: camelCase (`$commentData`)
- **Constants**: SCREAMING_SNAKE_CASE (`MAX_COMMENT_LENGTH`)

### 3. Dosya Organizasyonu
```
app/
├── Contracts/           # Interface'ler
├── DataObjects/         # DTO sınıfları
├── Http/
│   ├── Controllers/Api/ # API Controllers
│   ├── Requests/       # Form Requests
│   └── Resources/      # API Resources
├── Models/             # Eloquent Models
├── Policies/          # Authorization
├── Repositories/      # Data Access Layer
├── Services/          # Business Logic
└── Traits/           # Reusable Code
```

## 🔧 Geliştirme Süreci

### 1. Yeni Özellik Ekleme

1. **Branch oluştur**:
```bash
git checkout -b feature/new-feature-name
```

2. **Tests yaz**:
```bash
php artisan test
```

3. **Commit message formatı**:
```
feat: add comment rating system

- Add rating field to comments table
- Implement rating service logic
- Add rating validation rules
```

### 2. Repository Pattern

Yeni bir entity eklerken:

```php
// 1. Interface oluştur
interface EntityRepositoryInterface
{
    public function findById(int $id): ?Entity;
    public function create(EntityData $data): Entity;
}

// 2. Implementation oluştur
class EntityRepository implements EntityRepositoryInterface
{
    // Implementation
}

// 3. Service Provider'da bind et
$this->app->bind(EntityRepositoryInterface::class, EntityRepository::class);
```

### 3. Service Pattern

```php
class EntityService
{
    public function __construct(
        private EntityRepositoryInterface $repository
    ) {}
    
    public function createEntity(array $data, User $user): Entity
    {
        // Business logic
        $entityData = EntityData::fromArray($data);
        return $this->repository->create($entityData);
    }
}
```

## 🧪 Testing

### Test Kategorileri
- **Unit Tests**: İş mantığı testleri
- **Feature Tests**: API endpoint testleri
- **Integration Tests**: Servislerin entegrasyonu

### Test Yazma
```php
class CommentServiceTest extends TestCase
{
    public function test_can_create_comment()
    {
        $user = User::factory()->create();
        $data = ['content' => 'Test comment'];
        
        $comment = $this->commentService->createComment($data, $user);
        
        $this->assertInstanceOf(Comment::class, $comment);
    }
}
```

## 📊 Database

### Migration İsimlendirme
```bash
# Tablo oluşturma
php artisan make:migration create_comments_table

# Kolon ekleme
php artisan make:migration add_rating_to_comments_table

# Foreign key ekleme
php artisan make:migration add_foreign_keys_to_comments_table
```

### Model Relationships
```php
// Relationship tanımlarında type hint kullan
public function user(): BelongsTo
{
    return $this->belongsTo(User::class);
}

public function comments(): HasMany
{
    return $this->hasMany(Comment::class);
}
```

## 🔐 Security

### 1. Authorization
- Her endpoint için Policy kontrolü
- Sensitive data için additional checks

### 2. Validation
- Her input için Form Request
- Custom validation rules

### 3. Sanitization
- HTML content temizleme
- XSS protection

## 📝 Documentation

### 1. Code Comments
```php
/**
 * Create a new comment with business logic validation.
 *
 * @param array $data Comment data
 * @param User $user Authenticated user
 * @return Comment Created comment
 * @throws ValidationException
 */
public function createComment(array $data, User $user): Comment
```

### 2. API Documentation
- Endpoint açıklamaları
- Request/Response örnekleri
- Error handling

## 🚀 Deployment

### Pre-deployment Checklist
- [ ] All tests passing
- [ ] Code style check
- [ ] Migration files checked
- [ ] Config cache cleared
- [ ] Routes cached

### Commands
```bash
php artisan test
vendor/bin/pint --test
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 💡 Best Practices

### 1. Error Handling
```php
try {
    $result = $this->service->performAction();
    return $this->successResponse($result);
} catch (ValidationException $e) {
    return $this->validationErrorResponse($e->errors());
} catch (Exception $e) {
    Log::error('Action failed', ['error' => $e->getMessage()]);
    return $this->internalServerErrorResponse();
}
```

### 2. Caching
```php
Cache::remember("cache_key_{$id}", 300, function () use ($id) {
    return $this->repository->findById($id);
});
```

### 3. Logging
```php
Log::info('Comment created', [
    'comment_id' => $comment->id,
    'user_id' => $user->id,
    'action' => 'create'
]);
```

## 📞 İletişim

Sorularınız için:
- GitHub Issues açın
- Documentation'ı güncelleyin
- Code review sürecine katılın

---

Katkılarınız için teşekkür ederiz! 🙏 