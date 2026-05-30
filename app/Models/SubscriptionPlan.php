<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    protected $fillable = [
        'slug',
        'name',
        'price',
        'months',
        'period_label',
        'badge',
        'description',
        'features',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'float',
            'months' => 'integer',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
            'features' => 'array',
        ];
    }

    /** @return array<int, string> */
    public static function defaultFeatures(): array
    {
        return [
            'Acceso ilimitado a TODOS los cursos grabados (asíncronos)',
            'Acceso a clases en vivo exclusivas',
            'Certificaciones incluidas sin costo adicional',
            'Acceso a comunidad privada activa',
            'Actualizaciones constantes de contenido',
            'Contenido orientado al mercado laboral',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    /** @return array<int, string> */
    public function featureList(): array
    {
        $features = $this->features;

        if (is_array($features) && count($features) > 0) {
            return array_values(array_filter(array_map('strval', $features)));
        }

        return self::defaultFeatures();
    }

    public function toPublicConfig(): array
    {
        return [
            'id' => $this->slug,
            'name' => $this->name,
            'badge' => $this->badge,
            'price' => (float) $this->price,
            'period' => $this->period_label,
            'description' => $this->description ?? '',
            'features' => $this->featureList(),
        ];
    }
}
