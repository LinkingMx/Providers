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
        Schema::create('mail_tests', function (Blueprint $table) {
            $table->id();
            
            // Usuario que ejecutó la prueba
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');
            
            // Tipo de prueba de correo
            $table->string('test_type'); // 'provider_welcome', 'smtp_test', etc.
            
            // Destinatario del correo de prueba
            $table->string('recipient_email');
            
            // Estado del envío
            $table->enum('status', ['pending', 'sent', 'failed'])
                ->default('pending');
            
            // Timestamp de envío exitoso
            $table->timestamp('sent_at')->nullable();
            
            // Mensaje de error si falló
            $table->text('error_message')->nullable();
            
            // Datos del correo enviado (para debugging)
            $table->json('mail_data')->nullable();
            
            // Log de eventos del correo
            $table->json('events_log')->nullable();
            
            $table->timestamps();
            
            // Índices
            $table->index(['status', 'created_at']);
            $table->index(['test_type', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mail_tests');
    }
};
