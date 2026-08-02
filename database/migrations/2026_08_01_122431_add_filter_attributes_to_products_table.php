<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The storefront filters on what a product fits and what it is made of.
     * Both live in the product name on the source site, but text matching is
     * no basis for a facet, so they become columns of their own.
     *
     * Both are nullable: a perfume fits no handset, and only a case has a
     * construction.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('device_family')->nullable()->after('brand_id');
            $table->string('case_type')->nullable()->after('device_family');

            $table->index(['category_id', 'device_family']);
            $table->index(['category_id', 'case_type']);
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['category_id', 'device_family']);
            $table->dropIndex(['category_id', 'case_type']);

            $table->dropColumn(['device_family', 'case_type']);
        });
    }
};
