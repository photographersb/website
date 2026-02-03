<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('subject');
            $table->longText('message');
            $table->enum('type', ['contact', 'sponsorship'])->default('contact');
            $table->enum('status', ['pending', 'read', 'resolved'])->default('pending');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->integer('reply_count')->default(0);
            $table->timestamp('last_replied_at')->nullable();
            $table->timestamps();
            
            $table->index('email');
            $table->index('status');
            $table->index('type');
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('contact_messages');
    }
};
