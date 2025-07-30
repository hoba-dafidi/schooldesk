<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add the column as unsignedBigInteger and nullable
            if (!Schema::hasColumn('users', 'attribution_campaign_id')) {
                $table->unsignedBigInteger('attribution_campaign_id')->nullable()->after('referred_by');
            }

            // Add the foreign key
            $table->foreign('attribution_campaign_id')
                  ->references('id')
                  ->on('affiliate_campaigns')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['attribution_campaign_id']);
            $table->dropColumn('attribution_campaign_id');
        });
    }
};
