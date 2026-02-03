<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('event_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            $table->string('title');
            $table->decimal('price', 10, 2);
            $table->integer('quantity');
            $table->integer('sold_count')->default(0);
            $table->dateTime('sales_start_datetime');
            $table->dateTime('sales_end_datetime');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['event_id', 'is_active']);
            $table->index('sales_start_datetime');
        });
    }

    public function down()
    {
        Schema::dropIfExists('event_tickets');
    }
};
