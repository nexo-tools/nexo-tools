<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// The tools a user "added" to their account — a local pivot between users and
// the ecosystem registry (config nexo-ecosystem.tools). No cross-tool API calls;
// tool_key is validated against the registry at write time. (M5 "your tools")
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_tools', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('tool_key', 40);
            $table->timestamp('created_at')->nullable();

            $table->unique(['user_id', 'tool_key']); // one row per user+tool
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_tools');
    }
};
