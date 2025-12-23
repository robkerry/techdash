<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PublicSuffix extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'suffix',
        'type',
    ];

    /**
     * Get all ICANN public suffixes.
     */
    public static function getIcannSuffixes(): \Illuminate\Support\Collection
    {
        return static::where('type', 'icann')
            ->orderByRaw('LENGTH(suffix) DESC')
            ->pluck('suffix');
    }
}
