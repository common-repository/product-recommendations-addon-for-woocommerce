<?php
/**
 * Contact Model
 *
 * This file contains the Contact class which handles the creation of contacts
 * and sending them to MailMint via webhook.
 *
 * @package RexTheme\RexDynamicDiscount\Models
 * @since 1.0.4
 */

namespace RexTheme\RexProductRecommendationsForWoocommerce\Models;

/**
 * Class Contact
 *
 * This class handles the creation of contacts and sending them to MailMint via webhook.
 *
 * @package RexTheme\RexDynamicDiscount\Models
 * @since 1.0.4
 */
class Contact {

	protected $webhook_url = array( REXPRR_PRODUCT_RECOMMENDATIONS_WEBHOOK_URL );

	/**
	 * Email
	 *
	 * @var string
	 * @since 1.0.4
	 */
	protected $email = '';

	/**
	 * Name
	 *
	 * @var string
	 * @since 1.0.4
	 */
	protected $name = '';

	/**
	 * Constructor
	 *
	 * @param string $email
	 * @param string $name
	 * @since 1.0.4
	 */
	public function __construct( $email, $name ) {
		$this->email = $email;
		$this->name  = $name;
	}


	/**
	 * Create contact to MailMint via webhook
	 *
	 * @return array
	 * @since 1.0.4
	 */
	public function create_contact_via_webhook() {
		if ( ! $this->email ) {
			return array(
				'success' => false,
			);
		}

		$response = array(
			'success' => true,
		);

		$json_body_data = wp_json_encode(
            array(
				'email'      => $this->email,
				'first_name' => $this->name,
            )
        );

		try {
			if ( ! empty( $this->webhook_url ) ) {
				foreach ( $this->webhook_url as $url ) {
					$response = wp_remote_post(
                        $url,
                        array(
							'headers' => array(
								'Content-Type' => 'application/json',
							),
							'body'    => $json_body_data,
                        )
                    );
				}
			}
		} catch ( \Exception $e ) {
			$response = array(
				'success' => false,
			);
		}

		return $response;
	}
}
