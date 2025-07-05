<?php

namespace App\Core\Constants;

class Cache
{
    // Cache TTL (Time To Live) constants
    public const TTL_SHORT = 300;      // 5 minutes
    public const TTL_MEDIUM = 900;     // 15 minutes
    public const TTL_LONG = 1800;      // 30 minutes
    public const TTL_VERY_LONG = 3600; // 1 hour

    // Cache key prefixes
    public const KEY_PREFIX_CATEGORIES = 'categories';
    public const KEY_PREFIX_POSTS = 'posts';
    public const KEY_PREFIX_COMMENTS = 'comments';
    public const KEY_PREFIX_USERS = 'users';

    // Specific cache keys
    public const KEY_CATEGORIES_ALL = 'categories_all';
    public const KEY_POSTS_ALL = 'posts_all';
    public const KEY_POSTS_ADMIN_ALL = 'posts_admin_all';

    // Cache tags
    public const TAG_CATEGORIES = 'categories';
    public const TAG_POSTS = 'posts';
    public const TAG_COMMENTS = 'comments';
    public const TAG_USERS = 'users';

    /**
     * Generate cache key for categories
     */
    public static function getCategoryKey(string $suffix = ''): string
    {
        return self::KEY_PREFIX_CATEGORIES . ($suffix ? '_' . $suffix : '');
    }

    /**
     * Generate cache key for posts
     */
    public static function getPostKey(string $suffix = ''): string
    {
        return self::KEY_PREFIX_POSTS . ($suffix ? '_' . $suffix : '');
    }

    /**
     * Generate cache key for comments
     */
    public static function getCommentKey(string $suffix = ''): string
    {
        return self::KEY_PREFIX_COMMENTS . ($suffix ? '_' . $suffix : '');
    }

    /**
     * Generate cache key for users
     */
    public static function getUserKey(string $suffix = ''): string
    {
        return self::KEY_PREFIX_USERS . ($suffix ? '_' . $suffix : '');
    }

    /**
     * Get TTL based on cache type
     */
    public static function getTTL(string $type = 'medium'): int
    {
        return match($type) {
            'short' => self::TTL_SHORT,
            'medium' => self::TTL_MEDIUM,
            'long' => self::TTL_LONG,
            'very_long' => self::TTL_VERY_LONG,
            default => self::TTL_MEDIUM,
        };
    }
}