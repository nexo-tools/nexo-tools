<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

/**
 * A single cookieless pageview. Never carries IP, User-Agent, cookies or any
 * PII — only the anonymous daily visitor_hash and coarse aggregates. (AC-BEACON-7)
 */
#[Fillable(['origin', 'path', 'visitor_hash', 'day', 'country', 'ref'])]
class BeaconEvent extends Model
{
    // Ingest-only writes created_at explicitly; there is no updated_at.
    public const UPDATED_AT = null;

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'day' => 'date',
        ];
    }
}
