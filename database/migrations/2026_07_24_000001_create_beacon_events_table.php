<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Cookieless ecosystem analytics. Deliberately holds NO IP, NO User-Agent, NO
// cookies, NO PII — only an anonymous daily visitor_hash, the emitting tool
// (origin), the truncated path and the day. (AC-BEACON-7)
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('beacon_events', function (Blueprint $table): void {
            $table->id();
            $table->string('origin', 40);              // ecosystem tool slug (allowlisted)
            $table->string('path', 255);               // truncated, no query string
            $table->char('visitor_hash', 64);          // SHA-256, daily-rotating, anonymous
            $table->date('day');                       // aggregation bucket
            $table->char('country', 2)->nullable();    // ISO-3166 alpha-2 when available
            $table->string('ref', 40)->nullable();     // referring tool slug (allowlisted), for cross-tool attribution
            $table->timestamp('created_at')->nullable();

            $table->index(['day', 'origin']);
            $table->index(['origin', 'day']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('beacon_events');
    }
};
