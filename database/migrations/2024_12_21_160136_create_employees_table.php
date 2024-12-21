<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();

            $table->uuid()->unique();

            $table->string('user_id')
                ->unique();

            $table->foreign('user_id')
                ->references('uuid')
                ->on('users')
                ->onDelete('cascade');

            $table->string('company_id');

            $table->foreign('company_id')
                ->references('uuid')
                ->on('partner_companies')
                ->onDelete('cascade');

            $table->date('hire_date');

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->softDeletes();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
