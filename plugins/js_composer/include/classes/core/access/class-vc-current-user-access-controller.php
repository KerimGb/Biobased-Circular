<?php
/**
 * Controls access for the current user.
 *
 * Manages user permissions, capabilities, and access rules.
 * Extends Vc_Role_Access_Controller to handle user-specific
 * permissions and roles in the Visual Composer context.
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

require_once vc_path_dir( 'CORE_DIR', 'access/class-vc-role-access-controller.php' );

/**
 * Class Vc_Current_User_Access_Controller
 */
class Vc_Current_User_Access_Controller extends Vc_Role_Access_Controller {

	/**
	 * Sets the access control part and validates user login status.
	 *
	 * @param string $part
	 *
	 * @return $this
	 */
	public function part( $part ) {
		$this->part = $part;
		// we also check for user "logged_in" status.
		require_once ABSPATH . 'wp-includes/pluggable.php';
		$isUserLoggedIn = is_user_logged_in();
		$this->setValidAccess( $isUserLoggedIn && $this->getValidAccess() ); // send current status to upper level.

		return $this;
	}

	/**
	 *  Performs a capability check across multiple arguments using a callback function.
	 *
	 * @param callable $callback
	 * @param bool $valid
	 * @param array $argsList
	 *
	 * @return $this
	 */
	public function wpMulti( $callback, $valid, $argsList ) {
		if ( $this->getValidAccess() ) {
			require_once ABSPATH . 'wp-includes/pluggable.php';
			$access = ! $valid;
			$vcapp = vcapp();
			foreach ( $argsList as &$args ) {
				if ( ! is_array( $args ) ) {
					$args = [ $args ];
				}
				array_unshift( $args, 'current_user_can' );
				$this->setValidAccess( true );
				$vcapp->call( $callback, $args );
				if ( $valid === $this->getValidAccess() ) {
					$access = $valid;
					break;
				}
			}
			$this->setValidAccess( $access );
		}

		return $this;
	}

	/**
	 * Check WordPress capability. Should be valid one cap at least.
	 *
	 * @return $this
	 */
	public function wpAny() {
		if ( $this->getValidAccess() ) {
			$args = func_get_args();
			$this->wpMulti( [
				$this,
				'check',
			], true, $args );
		}

		return $this;
	}

	/**
	 * Check WordPress capability. Should be valid all caps.
	 *
	 * @return $this
	 */
	public function wpAll() {
		if ( $this->getValidAccess() ) {
			$args = func_get_args();
			$this->wpMulti( [
				$this,
				'check',
			], false, $args );
		}

		return $this;
	}

	/**
	 * Get capability for current user.
	 *
	 * @param string $rule
	 *
	 * @return bool
	 */
	public function getCapRule( $rule ) {
		$roleRule = $this->getStateKey() . '/' . $rule;

		return current_user_can( $roleRule );
	}

	/**
	 * Add capability to role.
	 *
	 * @param string $rule
	 * @param bool $value
	 *
	 * @return $this
	 */
	public function setCapRule( $rule, $value = true ) {
		$roleRule = $this->getStateKey() . '/' . $rule;

		wp_get_current_user()->add_cap( $roleRule, $value );

		return $this;
	}

	/**
	 * Can user do what he doo.
	 * Any rule has three types of state: true, false, string.
	 *
	 * @param string $rule
	 * @param bool|true $checkState
	 *
	 * @return $this
	 * @throws \Exception
	 */
	public function can( $rule = '', $checkState = true ) {
		$part = $this->getPart();
		if ( empty( $part ) ) {
			throw new \Exception( 'partName for User\Access is not set, please use ->part(partName) method to set!' );
		}

		if ( is_super_admin() ) {
			$this->setValidAccess( true );

			return $this;
		}

		if ( $this->getValidAccess() ) {
			if ( $this->is_admin_with_not_editable_part( $part ) ) {
				$this->setValidAccess( true );

				return $this;
			}
			$rule = $this->updateMergedCaps( $rule );

			if ( true === $checkState ) {
				$state = $this->getState();
				$return = true === $state || ( is_null( $state ) && current_user_can( 'edit_posts' ) );
				if ( true !== $return ) {
					if ( is_bool( $state ) || '' === $rule ) {
						$return = (bool) $state;
					} elseif ( '' !== $rule ) {
						$return = $this->getCapRule( $rule );
					}
				}
			} else {
				$return = $this->getCapRule( $rule );
			}
			$this->setValidAccess( $return );
		}

		return $this;
	}

	/**
	 * Check if user is administrator and part is not editable for admin role.
	 *
	 * @param string $part
	 * @return bool
	 */
	public function is_admin_with_not_editable_part( $part ) {
        // phpcs:ignore:WordPress.WP.Capabilities.RoleFound
		if ( current_user_can( 'administrator' ) ) {
			$admin_parts_list = [
				'backend_editor',
			];

			return ! in_array( $part, $admin_parts_list );
		} else {
			return false;
		}
	}

	/**
	 * Set state.
	 *
	 * @param mixed $value
	 */
	public function setState( $value = true ) {
		if ( false === $value && is_null( $value ) ) {
			wp_get_current_user()->remove_cap( $this->getStateKey() );
		} else {
			wp_get_current_user()->add_cap( $this->getStateKey(), $value );
		}

		return $this;
	}

	/**
	 * Get state of the Vc access rules part.
	 *
	 * @return mixed;
	 * @throws \Exception
	 */
	public function getState() {
		$currentUser = wp_get_current_user();
		$allCaps = $currentUser->get_role_caps();

		if ( $this->is_admin_with_not_editable_part( $this->getPart() ) ) {
			return true;
		}
		$capKey = $this->getStateKey();
		$state = null;
		if ( array_key_exists( $capKey, $allCaps ) ) {
			$state = $allCaps[ $capKey ];
		}

		// if state of rule not saving in settings we should get default value of it.
		if ( is_null( $state ) && isset( $currentUser->roles ) ) {
			foreach ( $currentUser->roles as $role ) {
				$state = vc_role_access()->who( $role )->part( $this->getPart() )->getState();

				if ( is_null( $state ) ) {
					continue;
				} else {
					break;
				}
			}
		}

		return apply_filters( 'vc_user_access_with_' . $this->getPart() . '_get_state', $state, $this->getPart() );
	}

	/**
	 * Get all capabilities for current user.
	 *
	 * @return array
	 */
	public function getAllCaps() {
		$currentUser = wp_get_current_user();
		$allCaps = $currentUser->get_role_caps();

		$wpbCaps = [];
		foreach ( $allCaps as $key => $value ) {
			if ( preg_match( '/^' . $this->getStateKey() . '\//', $key ) ) {
				$rule = preg_replace( '/^' . $this->getStateKey() . '\//', '', $key );
				$wpbCaps[ $rule ] = $value;
			}
		}

		return $wpbCaps;
	}
}
