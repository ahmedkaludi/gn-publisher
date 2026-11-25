<?php
/**
 * Black Friday / Cyber Monday Promotional Banner Class
 *
 * @author   Magazine3
 * @category Admin
 * @package  GN Publisher
 * @version  1.5.32
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Only load and run on admin side.
if ( ! is_admin() ) {
	return;
}

class GNPUB_Black_Friday_Banner {

	/**
	 * Expiry timestamp for the campaign matching other Magazine3 plugins exactly.
	 */
	const EXPIRY_DATE = 'Dec 31, 2026 23:59:59 GMT+0530';

	/**
	 * Coupon code used in the campaign.
	 */
	const COUPON_CODE = 'BFCM2026';

	/**
	 * User meta key for tracking dismissal timestamp.
	 */
	const DISMISS_META_KEY = 'gnpub_bfcm_banner_dismissed_time';

	/**
	 * Initialize hooks.
	 */
	public static function init() {
		if ( ! is_admin() ) {
			return;
		}
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );
		add_action( 'admin_notices', array( __CLASS__, 'render_banner' ), -99999 );
		add_action( 'wp_ajax_gnpub_dismiss_black_friday_banner', array( __CLASS__, 'ajax_dismiss' ) );
	}

	/**
	 * Check if the banner should be active.
	 *
	 * @return bool
	 */
	public static function is_banner_active() {
		static $is_active = null;

		if ( null !== $is_active ) {
			return $is_active;
		}

		$current_time = time();
		$expiry_time  = strtotime( self::EXPIRY_DATE );

		// 1. Check if campaign date has passed (after 31st Dec 2026).
		if ( $current_time > $expiry_time ) {
			$is_active = false;
			return false;
		}

		// 2. Check if dismissed within the last 7 days.
		$user_id = get_current_user_id();
		if ( $user_id ) {
			$dismissed_time = get_user_meta( $user_id, self::DISMISS_META_KEY, true );
			if ( ! empty( $dismissed_time ) ) {
				$days_passed = ( $current_time - (int) $dismissed_time ) / DAY_IN_SECONDS;
				if ( $days_passed < 7 ) {
					$is_active = false;
					return false;
				}
			}
		}

		$is_active = true;
		return true;
	}

	/**
	 * Check if current screen is the GN Publisher Settings page.
	 *
	 * @param string $hook Screen hook.
	 * @return bool
	 */
	public static function is_settings_screen( $hook = '' ) {
		if ( 'settings_page_gn-publisher-settings' === $hook ) {
			return true;
		}

		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;

		if ( $screen && 'settings_page_gn-publisher-settings' === $screen->id ) {
			return true;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( isset( $_GET['page'] ) && 'gn-publisher-settings' === $_GET['page'] ) {
			return true;
		}

		return false;
	}

	/**
	 * Enqueue styles and scripts for the banner.
	 *
	 * @param string $hook Screen hook.
	 */
	public static function enqueue_assets( $hook ) {
		if ( ! self::is_settings_screen( $hook ) || ! self::is_banner_active() ) {
			return;
		}

		$suffix   = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		$version  = defined( 'GNPUB_VERSION' ) ? GNPUB_VERSION : '1.5.32';
		$css_file = GNPUB_PATH . 'assets/css/gn-admin-black-friday-banner' . $suffix . '.css';
		$css_ver  = file_exists( $css_file ) ? filemtime( $css_file ) : $version;
		$js_file  = GNPUB_PATH . 'assets/js/gn-admin-black-friday-banner' . $suffix . '.js';
		$js_ver   = file_exists( $js_file ) ? filemtime( $js_file ) : $version;

		wp_enqueue_style(
			'gn-admin-bf-banner-css',
			GNPUB_URL . '/assets/css/gn-admin-black-friday-banner' . $suffix . '.css',
			array(),
			$css_ver
		);

		wp_enqueue_script(
			'gn-admin-bf-banner-js',
			GNPUB_URL . '/assets/js/gn-admin-black-friday-banner' . $suffix . '.js',
			array( 'jquery' ),
			$js_ver,
			true
		);

		wp_localize_script(
			'gn-admin-bf-banner-js',
			'gnpub_bf_banner_data',
			array(
				'ajax_url'    => admin_url( 'admin-ajax.php' ),
				'action'      => 'gnpub_dismiss_black_friday_banner',
				'nonce'       => wp_create_nonce( 'gnpub_bf_banner_nonce' ),
				'expiry_date' => self::EXPIRY_DATE,
				'coupon_code' => self::COUPON_CODE,
			)
		);
	}

	/**
	 * AJAX handler to record banner dismissal for 7 days.
	 */
	public static function ajax_dismiss() {
		check_ajax_referer( 'gnpub_bf_banner_nonce', 'security' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Unauthorized', 'gn-publisher' ) ), 403 );
		}

		$user_id = get_current_user_id();
		if ( $user_id ) {
			update_user_meta( $user_id, self::DISMISS_META_KEY, time() );
			wp_send_json_success( array( 'message' => esc_html__( 'Banner dismissed for 7 days.', 'gn-publisher' ) ) );
		}

		wp_send_json_error( array( 'message' => esc_html__( 'Failed to record dismissal.', 'gn-publisher' ) ) );
	}

	/**
	 * Render the Black Friday / Cyber Monday promotional banner HTML.
	 */
	public static function render_banner() {
		static $rendered = false;
		if ( $rendered ) {
			return;
		}

		if ( ! self::is_settings_screen() || ! self::is_banner_active() ) {
			return;
		}

		$rendered = true;

		$current_time = time();
		$expiry_time  = strtotime( self::EXPIRY_DATE );
		$diff         = max( 0, $expiry_time - $current_time );

		$days    = str_pad( floor( $diff / DAY_IN_SECONDS ), 2, '0', STR_PAD_LEFT );
		$hours   = str_pad( floor( ( $diff % DAY_IN_SECONDS ) / HOUR_IN_SECONDS ), 2, '0', STR_PAD_LEFT );
		$minutes = str_pad( floor( ( $diff % HOUR_IN_SECONDS ) / MINUTE_IN_SECONDS ), 2, '0', STR_PAD_LEFT );
		$seconds = str_pad( floor( $diff % MINUTE_IN_SECONDS ), 2, '0', STR_PAD_LEFT );

		$pricing_url = esc_url( 'https://gnpublisher.com/bfcm-2026/' );
		?>
		<div class="wrap gnpub-bf-banner-container">
			<div id="gnpub-bf-discount-bar" class="gnpub-bf-discount-bar gnpub-bf-theme-universal-luxury" role="region" aria-label="<?php esc_attr_e( 'Black Friday 60% Discount Banner', 'gn-publisher' ); ?>">
				<input id="gnpub-bf-coupon-expiry" type="hidden" value="<?php echo esc_attr( self::EXPIRY_DATE ); ?>">
				<button type="button" class="gnpub-bf-bar-close-btn" aria-label="<?php esc_attr_e( 'Dismiss Black Friday banner for 7 days', 'gn-publisher' ); ?>">&times;</button>
				
				<div class="gnpub-bf-bar-inner">
					<!-- Zone 1: Offer Text & 60% Hook -->
					<div class="gnpub-bf-col-offer">
						<div class="gnpub-bf-urgency-row">
							<span class="gnpub-bf-urgency-pill">
								<span class="gnpub-bf-pulse-dot"></span>
								⚡ <?php esc_html_e( 'BLACK FRIDAY / CYBER MONDAY / CHRISTMAS SALE', 'gn-publisher' ); ?>
							</span>
						</div>
						<div class="gnpub-bf-headline-group">
							<div class="gnpub-bf-badge-60">
								<span class="gnpub-bf-num"><?php esc_html_e( '60%', 'gn-publisher' ); ?></span>
								<span class="gnpub-bf-off"><?php esc_html_e( 'OFF', 'gn-publisher' ); ?></span>
							</div>
							<div class="gnpub-bf-text-details">
								<div class="gnpub-bf-main-title"><?php esc_html_e( 'BIGGEST SALE IN OUR HISTORY', 'gn-publisher' ); ?> <span class="gnpub-bf-highlight-gold"><?php esc_html_e( '— NEVER BEFORE SEEN!', 'gn-publisher' ); ?></span></div>
								<div class="gnpub-bf-sub-title"><?php esc_html_e( 'Sale is here! 60% off on Everything for a very limited time!!!', 'gn-publisher' ); ?></div>
							</div>
							<div class="gnpub-bf-coupon-badge" title="<?php esc_attr_e( 'Click to copy coupon code', 'gn-publisher' ); ?>">
								<span class="gnpub-bf-coupon-label"><?php esc_html_e( 'CODE:', 'gn-publisher' ); ?></span>
								<strong class="gnpub-bf-coupon-code"><?php echo esc_html( self::COUPON_CODE ); ?></strong>
								<span class="gnpub-bf-copy-icon">📋</span>
								<span class="gnpub-bf-copy-tooltip"><?php esc_html_e( 'Copied!', 'gn-publisher' ); ?></span>
							</div>
						</div>
					</div>

					<!-- Zone 2: High-Urgency Countdown Clock -->
					<div class="gnpub-bf-col-clock">
						<div class="gnpub-bf-clock-header">
							<span>⏳ <?php esc_html_e( 'OFFER EXPIRES IN:', 'gn-publisher' ); ?></span>
						</div>
						<div id="gnpub-bf-clockdiv" class="gnpub-bf-clockdiv" aria-live="polite">
							<div class="gnpub-bf-clock-unit">
								<span class="gnpub-bf-clock-num gnpub-bf-days"><?php echo esc_html( $days ); ?></span>
								<span class="gnpub-bf-clock-label"><?php esc_html_e( 'Days', 'gn-publisher' ); ?></span>
							</div>
							<span class="gnpub-bf-clock-sep">:</span>
							<div class="gnpub-bf-clock-unit">
								<span class="gnpub-bf-clock-num gnpub-bf-hours"><?php echo esc_html( $hours ); ?></span>
								<span class="gnpub-bf-clock-label"><?php esc_html_e( 'Hours', 'gn-publisher' ); ?></span>
							</div>
							<span class="gnpub-bf-clock-sep">:</span>
							<div class="gnpub-bf-clock-unit">
								<span class="gnpub-bf-clock-num gnpub-bf-minutes"><?php echo esc_html( $minutes ); ?></span>
								<span class="gnpub-bf-clock-label"><?php esc_html_e( 'Mins', 'gn-publisher' ); ?></span>
							</div>
							<span class="gnpub-bf-clock-sep">:</span>
							<div class="gnpub-bf-clock-unit">
								<span class="gnpub-bf-clock-num gnpub-bf-seconds"><?php echo esc_html( $seconds ); ?></span>
								<span class="gnpub-bf-clock-label"><?php esc_html_e( 'Secs', 'gn-publisher' ); ?></span>
							</div>
						</div>
					</div>

					<!-- Zone 3: High-Converting CTA Button -->
					<div class="gnpub-bf-col-cta">
						<a class="gnpub-bf-bar-btn" href="<?php echo esc_url( $pricing_url ); ?>" target="_blank" rel="noopener noreferrer">
							<span class="gnpub-bf-btn-text"><?php esc_html_e( 'Claim The Offer Now', 'gn-publisher' ); ?></span>
							<span class="gnpub-bf-btn-icon">⚡</span>
						</a>
						<span class="gnpub-bf-btn-sub">🔒 <?php esc_html_e( '30 Days Money Back Guarantee', 'gn-publisher' ); ?></span>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
}

// Initialize banner hooks.
GNPUB_Black_Friday_Banner::init();
