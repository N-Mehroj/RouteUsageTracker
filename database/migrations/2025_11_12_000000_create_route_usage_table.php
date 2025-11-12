<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('route_usage', function (Blueprint $table) {
            $table->id();
            $table->string('route_name', 255);
            $table->string('route_path', 500);
            $table->string('method', 10);
            $table->string('route_type', 50)->default('web'); // web, api, admin, etc.
            $table->unsignedBigInteger('usage_count')->default(0);
            $table->timestamp('first_used_at')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();

            $table->unique(['route_name', 'method']);
            $table->index(['route_type', 'usage_count']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('route_usage');
    }
};
