<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Departments gain children.
     *
     * The live store splits most of its categories by handset - "Силиконов гръб
     * за Samsung", "Протектори за Xiaomi" - but this storefront already models
     * that as the `device_family` filter, and a construction like silicone or
     * leather as `case_type`. Those stay filters: a category row per handset
     * would mean two URLs for the same products and would split the facet
     * counts between them.
     *
     * What nests here is the division the filters cannot express - a power bank
     * is not a charger, a women's perfume is not a men's. One level covers it:
     * a leaf is where products live, and a department sums whatever sits
     * beneath it.
     */
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->foreignId('parent_id')
                ->nullable()
                ->after('id')
                ->constrained('categories')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropConstrainedForeignId('parent_id');
        });
    }
};
