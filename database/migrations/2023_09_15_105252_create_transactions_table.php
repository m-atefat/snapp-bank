<?php

use App\Enums\TransactionStatus;
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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('card_id')->constrained('cards');
            $table->foreignId('source_transaction_id')->nullable()->constrained('transactions');

            $table->boolean('is_deposit')->default(false)->index();
            $table->unsignedTinyInteger('type')->index();

            $table->enum(
                'status',
                TransactionStatus::values()
            )->default(TransactionStatus::INIT->value)->index();

            $table->decimal('amount', 13, 0);
            $table->unsignedDecimal('balance', 13,0)->default(0);
            $table->string('track_id')->nullable()->index();
            $table->timestamp('done_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
