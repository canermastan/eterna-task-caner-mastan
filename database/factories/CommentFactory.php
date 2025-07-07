<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Comment>
 */
class CommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $commentTemplates = [
            'Çok faydalı bir yazı olmuş, teşekkürler! {detail}',
            'Harika bir rehber! Özellikle {detail} çok işime yaradı. Devamını bekliyorum.',
            'Bu yaklaşımı ben de denedim ve gerçekten çok etkili. {detail}',
            'Yazınızda bahsettiğiniz teknikleri uyguladım ve sonuçlar gerçekten memnun edici. {detail}',
            'Çok açıklayıcı bir yazı olmuş. {detail}',
            'Bu konuda daha önce hiç düşünmemiştim. {detail}',
            'Pratik örneklerle desteklenmiş çok değerli bir içerik. {detail}',
            'Bu yaklaşımı projelerimde kullanmaya başladım. {detail}',
            'Yazınızda bahsettiğiniz best practice\'leri takip etmeye başladım. {detail}',
            'Bu konuda daha detaylı bir yazı yazabilir misiniz? {detail}',
        ];

        $details = [
            'Bu konuda daha detaylı bilgi verebilir misiniz?',
            'Özellikle pratik örnekler çok değerli.',
            'Önerileriniz için teşekkürler.',
            'Yeni başlayanlar için mükemmel bir kaynak.',
            'Yeni bir perspektif kazandım.',
            'Devamını bekliyorum.',
            'Gerçekten çok faydalı oldu.',
            'Sonuçlar harika!',
            'Çok merak ediyorum.',
            'Özellikle performans konusundaki öneriler çok değerli.',
            'Büyük iyileştirmeler elde ettim.',
            'Daha iyi anladım.',
            'Aynı sonuçları alırım umarım.',
            'Mükemmel bir rehber.',
            'Daha fazla örnek verebilir misiniz?',
            'Gerçekten çok etkili.',
            'Takımımızla paylaştım.',
            'Bu tür yazıların devamını bekliyorum.',
            'Pratik uygulamaları merak ediyorum.',
        ];

        $template = fake()->randomElement($commentTemplates);
        $detail = fake()->randomElement($details);
        $comment = str_replace('{detail}', $detail, $template);

        return [
            'user_id' => User::factory(),
            'content' => $comment,
            'status' => fake()->randomElement(['pending', 'approved', 'rejected']),
            'parent_id' => null,
        ];
    }

    /**
     * Indicate that the comment is approved.
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
        ]);
    }

    /**
     * Indicate that the comment is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    /**
     * Indicate that the comment is rejected.
     */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'rejected',
        ]);
    }

    /**
     * Indicate that the comment is a reply.
     */
    public function reply(Comment $parent): static
    {
        return $this->state(fn (array $attributes) => [
            'parent_id' => $parent->id,
        ]);
    }
} 