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
        Schema::create('group_expense_splits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('group_expense_id');
            $table->unsignedBigInteger('member_id'); // group_member_id
            $table->decimal('amount', 10, 2);
            $table->enum('split_type', ['equal', 'exact', 'percentage'])->default('equal');
            $table->boolean('is_paid')->default(false);
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->foreign('group_expense_id')->references('id')->on('group_expenses')->onDelete('cascade');
            $table->foreign('member_id')->references('id')->on('group_members')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_expense_splits');
    }
};
