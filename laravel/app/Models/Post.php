<?php

namespace App\Models;

use Database\Factories\PostFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

#[Fillable([
    'judul',
    'slug',
    'ringkasan',
    'isi',
    'cover_path',
    'status',
    'published_at',
    'user_id',
])]
class Post extends Model
{
    /** @use HasFactory<PostFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function excerpt(int $limit = 140): string
    {
        if (! empty($this->ringkasan)) {
            return $this->ringkasan;
        }

        $cleaned = (string) preg_replace('/<(script|style)\b[^>]*>.*?<\/\1>/is', ' ', (string) $this->isi);
        $spaced = (string) preg_replace('/<\/(p|h2|h3|h4|li|blockquote|div|figure)>|<br\s*\/?>/i', ' ', $cleaned);
        $plain = trim((string) preg_replace('/\s+/', ' ', strip_tags($spaced)));

        return Str::limit($plain, $limit);
    }

    public function renderedContent(): string
    {
        $raw = trim((string) $this->isi);
        if ($raw === '') {
            return '';
        }

        $hasHtmlBlocks = preg_match('/<(p|h2|h3|h4|ul|ol|li|blockquote|figure|img|hr|br|strong|em|b|i|u|s|a)\b/i', $raw) === 1;

        if (! $hasHtmlBlocks) {
            $paragraphs = [];
            foreach (explode("\n\n", $raw) as $p) {
                $trimmed = trim($p);
                if ($trimmed !== '') {
                    $paragraphs[] = '<p>'.nl2br(e($trimmed)).'</p>';
                }
            }

            return implode("\n", $paragraphs);
        }

        $withoutScripts = (string) preg_replace('/<(script|style|iframe|object|embed)\b[^>]*>.*?<\/\1>/is', '', $raw);
        $allowed = '<p><h2><h3><h4><ul><ol><li><blockquote><strong><b><em><i><u><s><a><img><figure><figcaption><hr><br>';
        $clean = strip_tags($withoutScripts, $allowed);
        $clean = (string) preg_replace('/\son[a-z]+\s*=\s*(?:"[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $clean);
        $clean = (string) preg_replace('/(href|src)\s*=\s*(?:"\s*javascript:[^"]*"|\'\s*javascript:[^\']*\'|\s*javascript:[^\s>]+)/i', '$1="#"', $clean);

        return $clean;
    }
}
