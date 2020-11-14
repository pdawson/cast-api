<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('sites', function (Blueprint $table) {
            $table->id();

            // A site belongs to a domain (host)
            $table->unsignedBigInteger('server_id');

            // The domain name for the site (e.g. test.pauldawson.me)
            $table->string('domain');

            // The name of the site
            $table->string('name');

            // The path relative to the domains www path
            $table->string('path');

            // Is the site active on the domain?
            $table->boolean('active')->default(false);

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('server_id')
                ->references('id')
                ->on('servers')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('sites');
    }
}
