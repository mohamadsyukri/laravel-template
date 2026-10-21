<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menus', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('label');
            $table->string('icon', 50)->nullable();
            // Null for a group heading; otherwise the route the item opens, which is also the
            // permission that decides whether the item is shown.
            $table->string('route_name')->nullable()->unique();
            $table->unsignedSmallInteger('sort')->default(0);
            $table->timestamps();

            $table->foreign('parent_id')->references('id')->on('menus')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
