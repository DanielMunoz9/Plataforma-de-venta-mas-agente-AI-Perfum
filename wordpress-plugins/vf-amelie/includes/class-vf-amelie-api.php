<?php
/**
 * VF Amélie API - Handles communication with Flask backend
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class VF_Amelie_API {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        // Constructor
    }
    
    /**
     * Send chat message to Flask backend
     */
    public function send_chat_message($message, $api_base_url, $api_secret = '') {
        $endpoint = rtrim($api_base_url, '/') . '/api/ai/chat';
        
        $body = json_encode(array(
            'message' => $message
        ));
        
        $headers = array(
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        );
        
        // Add API key if provided
        if (!empty($api_secret)) {
            $headers['X-API-KEY'] = $api_secret;
        }
        
        $args = array(
            'method' => 'POST',
            'headers' => $headers,
            'body' => $body,
            'timeout' => 30,
            'sslverify' => false // You may want to enable this in production
        );
        
        $response = wp_remote_request($endpoint, $args);
        
        if (is_wp_error($response)) {
            return new WP_Error('api_error', 'Error de conexión: ' . $response->get_error_message());
        }
        
        $status_code = wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);
        
        if ($status_code !== 200) {
            return new WP_Error('api_error', 'Error del servidor: ' . $status_code);
        }
        
        $data = json_decode($body, true);
        
        if (!$data || !isset($data['reply'])) {
            return new WP_Error('api_error', 'Respuesta inválida del servidor');
        }
        
        return array(
            'reply' => $data['reply'],
            'timestamp' => current_time('mysql')
        );
    }
    
    /**
     * Test API connection
     */
    public function test_connection($api_base_url, $api_secret = '') {
        $test_message = 'test connection';
        
        try {
            $response = $this->send_chat_message($test_message, $api_base_url, $api_secret);
            
            if (is_wp_error($response)) {
                return array(
                    'success' => false,
                    'message' => $response->get_error_message()
                );
            }
            
            return array(
                'success' => true,
                'message' => 'Conexión exitosa',
                'response' => $response['reply']
            );
            
        } catch (Exception $e) {
            return array(
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            );
        }
    }
}