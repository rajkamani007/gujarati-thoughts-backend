<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('posters', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->foreignId('sub_category_id')->nullable()->after('category_id')->constrained()->nullOnDelete();
            $table->string('bg_color')->default('#000000')->after('title');
            $table->string('text_color')->default('#ffffff')->after('bg_color');
            $table->string('logo_align')->default('left')->after('text_color');
            $table->string('image_align')->default('center')->after('logo_align');
        });
    }

    public function down(): void
    {
        Schema::table('posters', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropForeign(['sub_category_id']);
            $table->dropColumn(['category_id', 'sub_category_id', 'bg_color', 'text_color', 'logo_align', 'image_align']);
        });
    }
};
