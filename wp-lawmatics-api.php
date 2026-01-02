<?php
/**
 * Lawmatics API Class for WordPress
 * * Documentation: https://docs.lawmatics.com/
 */

if ( ! class_exists( 'WP_Lawmatics_API' ) ) {

    class WP_Lawmatics_API {

        private $access_token;
        private $base_url = 'https://api.lawmatics.com/v1';

        /**
         * Initialize the class with the Access Token.
         */
        public function __construct( $access_token ) {
            $this->access_token = $access_token;
        }

        /**
         * Generic request handler using WordPress HTTP API.
         */
        private function make_request( $endpoint, $method = 'GET', $body = [] ) {
            $url = $this->base_url . '/' . ltrim( $endpoint, '/' );

            $args = [
                'method'  => $method,
                'timeout' => 30,
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->access_token,
                    'Content-Type'  => 'application/json',
                    'Accept'        => 'application/json',
                ],
            ];

            if ( ! empty( $body ) ) {
                $args['body'] = json_encode( $body );
            }

            $response = wp_remote_request( $url, $args );

            if ( is_wp_error( $response ) ) {
                return $response;
            }

            $code = wp_remote_retrieve_response_code( $response );
            $data = json_decode( wp_remote_retrieve_body( $response ), true );

            if ( $code >= 400 ) {
                return new WP_Error( 'lawmatics_api_error', $data['error'] ?? 'Unknown API Error', $data );
            }

            return $data;
        }

        /**
         * Get Contacts
         * Supports filtering: filter_by, filter_on, filter_with
         */
        public function get_contacts( $params = [] ) {
            $endpoint = 'contacts';
            if ( ! empty( $params ) ) {
                $endpoint = add_query_arg( $params, $endpoint );
            }
            return $this->make_request( $endpoint );
        }

        /**
         * Create a new Contact
         */
        public function create_contact( $data ) {
            return $this->make_request( 'contacts', 'POST', $data );
        }

        /**
         * Get a single Contact by ID
         */
        public function get_contact( $contact_id ) {
            return $this->make_request( "contacts/{$contact_id}" );
        }

        /**
         * Update a Contact
         */
        public function update_contact( $contact_id, $data ) {
            return $this->make_request( "contacts/{$contact_id}", 'PUT', $data );
        }

        /**
         * Create a Matter
         */
        public function create_matter( $data ) {
            return $this->make_request( 'matters', 'POST', $data );
        }

        /**
         * Get Matters
         */
        public function get_matters( $params = [] ) {
            $endpoint = 'matters';
            if ( ! empty( $params ) ) {
                $endpoint = add_query_arg( $params, $endpoint );
            }
            return $this->make_request( $endpoint );
        }
    }
}
