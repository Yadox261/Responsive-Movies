<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /**
     * Envía un mensaje de WhatsApp al número indicado.
     *
     * @param string $phone   Número completo con código de país (ej: +525512345678)
     * @param string $message Cuerpo del mensaje
     * @return bool
     */
    public function sendMessage($phone, $message): bool
    {
        if (empty($phone)) {
            Log::warning('WhatsAppService: Intento de enviar mensaje a un número vacío.');
            return false;
        }

        // 1. Limpiar espacios, guiones y paréntesis del número
        $phone = preg_replace('/[^0-9+]/', '', $phone);

        // 2. Si no empieza con '+', añadir código de México por defecto
        if (!str_starts_with($phone, '+')) {
            $phone = '+52' . $phone;
        }

        // Credenciales de la API de UltraMsg (modo prueba del proyecto de referencia)
        $token    = 'll2o57w2pmkle9td';
        $instance = 'instance174035';
        $url      = "https://api.ultramsg.com/{$instance}/messages/chat";

        try {
            $response = Http::withoutVerifying()->asForm()->post($url, [
                'token' => $token,
                'to'    => $phone,
                'body'  => $message,
            ]);

            if ($response->successful()) {
                Log::info("WhatsApp [Word of the Movies] enviado exitosamente a {$phone}");
                return true;
            } else {
                Log::error("Error al enviar WhatsApp a {$phone}: " . $response->body());
                return false;
            }
        } catch (\Exception $e) {
            Log::error("Excepción al intentar enviar WhatsApp: " . $e->getMessage());
            return false;
        }
    }
}
