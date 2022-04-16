<?php
/** @var WC_Gateway_Reepay_Checkout $gateway */
/** @var WC_Order $order */
/** @var int $order_id */
/** @var array $order_data */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

?>

<?php
	$amount_to_capture = 0;
	$order_total = rp_prepare_amount( $order->get_total(), $order_data['currency'] );
	if( $order_total  <=  $order_data['authorized_amount'] ) {
		$amount_to_capture = $order_total;
	} else {
		$amount_to_capture = $order_data['authorized_amount'];
	}
?>

<ul class="order_action">
	<li class="reepay-admin-section-li-header">
		<?php echo esc_html__( 'State', 'woocommerce-gateway-reepay-checkout' ); ?>: <?php echo $order_data['state']; ?>
	</li>

	<?php $order_is_cancelled = ( $order->get_meta( '_reepay_order_cancelled', true ) === '1' ); ?>
	<?php if ($order_is_cancelled && 'cancelled' != $order_data['state']): ?>
		<li class="reepay-admin-section-li-small">
			<?php echo esc_html__( 'Order is cancelled', 'woocommerce-gateway-reepay-checkout' ); ?>
		</li>
	<?php endif; ?>

	<li class="reepay-admin-section-li">
		<span class="reepay-balance__label">
			<?php echo esc_html__( 'Remaining balance', 'woocommerce-gateway-reepay-checkout' ); ?>:
		</span>
		<span class="reepay-balance__amount">
			<span class='reepay-balance__currency'>
				&nbsp;
			</span>
			<?php echo wc_price( rp_make_initial_amount( $order_data['authorized_amount'] - $order_data['settled_amount'], $order_data['currency'] ) ); ?>
		</span>
	</li>
	<li class="reepay-admin-section-li">
		<span class="reepay-balance__label">
			<?php echo esc_html__( 'Total authorized', 'woocommerce-gateway-reepay-checkout' ); ?>:
		</span>
		<span class="reepay-balance__amount">
			<span class='reepay-balance__currency'>
				&nbsp;
			</span>
			<?php echo wc_price( rp_make_initial_amount( $order_data['authorized_amount'], $order_data['currency'] ) ); ?>
	   </span>
	</li>

	<input id="reepay_order_id" type="hidden" data-order-id="<?php echo $order_id; ?>">
	<input id="reepay_order_total" type="hidden" value ="<?php echo rp_make_initial_amount( $amount_to_capture, $order_data['currency'] ) . ' ' . $order_data['currency']; ?>" data-order-total="<?php echo rp_make_initial_amount( $amount_to_capture, $order_data['currency'] ); ?>">

	<li class="reepay-admin-section-li">
		<span class="reepay-balance__label">
			<?php echo esc_html__( 'Total settled', 'woocommerce-gateway-reepay-checkout' ); ?>:
		</span>
		<span class="reepay-balance__amount">
			<span class='reepay-balance__currency'>
				&nbsp;
			</span>
			<?php echo wc_price( rp_make_initial_amount( $order_data['settled_amount'], $order_data['currency'] ) ); ?>
		</span>
	</li>
	<li class="reepay-admin-section-li">
		<span class="reepay-balance__label">
			<?php echo esc_html__( 'Total refunded', 'woocommerce-gateway-reepay-checkout' ); ?>:
		</span>
		<span class="reepay-balance__amount">
			<span class='reepay-balance__currency'>
				&nbsp;
			</span>
			<?php echo wc_price( rp_make_initial_amount( $order_data['refunded_amount'], $order_data['currency'] ) ); ?>
		</span>
	</li>
	<li style='font-size: xx-small'>&nbsp;</li>
	<?php if ( $order_data['settled_amount'] == 0 && ! in_array( $order_data['state'], array( 'cancelled', 'created') ) && !$order_is_cancelled ): ?>
		<li class="reepay-full-width">
			<a class="button button-primary" data-action="reepay_capture" id="reepay_capture" data-nonce="<?php echo wp_create_nonce( 'reepay' ); ?>" data-order-id="<?php echo $order_id; ?>" data-confirm="<?php echo __( 'You are about to CAPTURE this payment', 'woocommerce-gateway-reepay-checkout' ); ?>">
				<?php echo sprintf( __( 'Capture Full Amount (%s)', 'woocommerce-gateway-reepay-checkout' ), wc_price( rp_make_initial_amount( $order_data['authorized_amount'], $order_data['currency'] ) ) ); ?>
			</a>
		</li>
	<?php endif; ?>

	<?php if ( $order_data['settled_amount'] == 0 && ! in_array( $order_data['state'], array( 'cancelled', 'created') ) && ! $order_is_cancelled ): ?>
		<li class="reepay-full-width">
			<a class="button" data-action="reepay_cancel" id="reepay_cancel" data-confirm="<?php echo __( 'You are about to CANCEL this payment', 'woocommerce-gateway-reepay-checkout' ); ?>" data-nonce="<?php echo wp_create_nonce( 'reepay' ); ?>" data-order-id="<?php echo $order_id; ?>">
				<?php echo esc_html__( 'Cancel remaining balance', 'woocommerce-gateway-reepay-checkout' ); ?>
			</a>
		</li>
		<li style='font-size: xx-small'>&nbsp;</li>
	<?php endif; ?>

	<?php if ( $order_data['authorized_amount'] > $order_data['settled_amount'] && ! in_array( $order_data['state'], array( 'cancelled', 'created') ) && !$order_is_cancelled ): ?>
		<li class="reepay-admin-section-li-header">
			<?php echo esc_html__( 'Partly capture', 'woocommerce-gateway-reepay-checkout' ); ?>
		</li>
		<li class="reepay-balance last">
			<span class="reepay-balance__label" style="margin-right: 0;">
				<?php echo esc_html__( 'Capture amount', 'woocommerce-gateway-reepay-checkout' ); ?>:
			</span>
			<span class="reepay-partly_capture_amount">
				<input id="reepay-capture_partly_amount-field" class="reepay-capture_partly_amount-field" type="text" autocomplete="off" size="6" value="<?php echo ( rp_make_initial_amount( $order_data['authorized_amount'] - $order_data['settled_amount'], $order_data['currency'] ) ); ?>" />
			</span>
		</li>
		<li class="reepay-full-width">
			<a class="button" id="reepay_capture_partly" data-nonce="<?php echo wp_create_nonce( 'reepay' ); ?>" data-order-id="<?php echo $order_id; ?>">
				<?php echo esc_html__( 'Capture Specified Amount', 'woocommerce-gateway-reepay-checkout' ); ?>
			</a>
		</li>
		<li style='font-size: xx-small'>&nbsp;</li>
	<?php endif; ?>

	<?php if ( $order_data['settled_amount'] > $order_data['refunded_amount'] && ! in_array( $order_data['state'], array( 'cancelled', 'created' ) ) && ! $order_is_cancelled ): ?>
		<li class="reepay-admin-section-li-header">
			<?php echo esc_html__( 'Partly refund', 'woocommerce-gateway-reepay-checkout' ); ?>
		</li>
		<li class="reepay-balance last">
			<span class="reepay-balance__label" style='margin-right: 0;'>
				<?php echo esc_html__( 'Refund amount', 'woocommerce-gateway-reepay-checkout' ); ?>:
			</span>
			<span class="reepay-partly_refund_amount">
				<input id="reepay-refund_partly_amount-field" class="reepay-refund_partly_amount-field" type="text" size="6" autocomplete="off" value="<?php echo rp_make_initial_amount( $order_data['settled_amount'] - $order_data['refunded_amount'], $order_data['currency'] ); ?>" />
			</span>
		</li>
		<li class="reepay-full-width">
			<a class="button" id="reepay_refund_partly" data-nonce="<?php echo wp_create_nonce( 'reepay' ); ?>" data-order-id="<?php echo $order_id; ?>">
				<?php echo esc_html__( 'Refund Specified Amount', 'woocommerce-gateway-reepay-checkout' ); ?>
			</a>
		</li>
		<li style='font-size: xx-small'>&nbsp;</li>
	<?php endif; ?>

	<li class="reepay-admin-section-li-header-small">
		<?php echo esc_html__( 'Order ID', 'woocommerce-gateway-reepay-checkout' ) ?>
	</li>
	<li class="reepay-admin-section-li-small">
		<?php echo $order_data["handle"]; ?>
	</li>
	<li class="reepay-admin-section-li-header-small">
		<?php echo esc_html__( 'Transaction ID', 'woocommerce-gateway-reepay-checkout' ) ?>
	</li>
	<li class="reepay-admin-section-li-small">
		<?php echo $order_data["id"]; ?>
	</li>

	<?php if ( isset( $order_data['transactions'][0] ) && isset( $order_data['transactions'][0]['card_transaction'] )) : ?>
		<li class="reepay-admin-section-li-header-small">
			<?php echo esc_html__( 'Card number', 'woocommerce-gateway-reepay-checkout' ); ?>
		</li>
		<li class="reepay-admin-section-li-small">
			<?php echo esc_html( rp_format_credit_card( $order_data['transactions'][0]['card_transaction']['masked_card'] ) ); ?>
		</li>
		<p>
		<center>
			<img src="<?php echo $gateway->get_logo( $order_data['transactions'][0]['card_transaction']['card_type'] ); ?>" class="reepay-admin-card-logo" />
		</center>
		</p>
	<?php endif; ?>

	<?php if( isset( $order_data['transactions'][0]['mps_transaction'] ) ): ?>
		 <center>
			<img src="<?php echo $gateway->get_logo( 'ms_subscripiton' ); ?>" class="reepay-admin-card-logo" />
		</center>
	<?php endif; ?>
</ul>
