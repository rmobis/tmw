<?php

class CharacterList extends Eloquent {

	/**
	 * Authorization type for showing the list.
	 */
	const AUTH_SHOW = 'show';

	/**
	 * Authorization type for editing the list.
	 */
	const AUTH_EDIT = 'edit';





	/**
	 * Indicates if the model should be timestamped.
	 *
	 * @var bool
	 */
	public $timestamps = true;

	/**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
	//protected $hidden = array('edit_password', 'show_password');

	/**
	 * Base used to convert hash to id.
	 *
	 * @var string
	 */
	private static $base = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';





	/**
	 * Handles hashing the show password when it's set.
	 *
	 * @param  mixed $val
	 * @return void
	 */
	protected function getShowPasswordAttribute($val) {
		return $this->attributes['show_password'];
	}

	/**
	 * Handles hashing the edit password when it's set.
	 *
	 * @param  mixed $val
	 * @return void
	 */
	protected function getEditPasswordAttribute($val) {
		return $this->attributes['edit_password'];
	}

	/**
	 * Handles hashing the show password when it's set.
	 *
	 * @param  mixed $val
	 * @return void
	 */
	protected function setShowPasswordAttribute($val) {
		$this->attributes['show_password'] = empty($val) ? null : Hash::make($val);
	}

	/**
	 * Handles hashing the edit password when it's set.
	 *
	 * @param  mixed $val
	 * @return void
	 */
	protected function setEditPasswordAttribute($val) {
		$this->attributes['edit_password'] = Hash::make($val);
	}

	/**
	 * Handles setting empty string to null.
	 *
	 * @param  mixed $val
	 * @return void
	 */
	protected function setDescriptionAttribute($val) {
		$this->attributes['description'] = is_null($val) || strlen($val) === 0 ? null : $val;
	}

	/**
	 * Retrieve the hashed ID; this is extremely useful for the linking.
	 *
	 * @return string
	 */
	protected function getHashAttribute() {
		return static::toHash($this->id);
	}

	/**
	 * Check whether the list is public, that is, whether it's viewable without
	 * a password.
	 *
	 * @return bool
	 */
	protected function getPublicAttribute() {
		return $this->showPassword === null;
	}





	/**
	 * Create the relation of characters belonging to this list.
	 *
	 * @return Illuminate\Database\Query\Builder
	 */
	public function characters() {
		return $this->belongsToMany('Character', 'list_characters')
					->withPivot('observation');
	}





	/**
	 * @needdocs
	 */
	public function hasBrandNewCharacter() {
		foreach ($this->characters as $char) {
			if ($char->isBrandNew()) {
				return true;
			}
		}

		return false;
	}









	/**
	 * Check if the user has authorization to view/edit this list.
	 *
	 * @param  string $type
	 * @return bool
	 */
	public function checkLogin($type) {
		$cookieName = md5($type.'_'.$this->id);
		$passwordProp = $type.'Password';

		if ($type === self::AUTH_SHOW and $this->public) {
			return true;
		} elseif(Cookie::get($cookieName) === md5($this->$passwordProp)) {
			return true;
		} elseif ($type === self::AUTH_SHOW) {
			return $this->checkLogin(self::AUTH_EDIT);
		}

		return false;
	}

	/**
	 * Attempt to 'login' to  the list; if it succeeds it creates and stores on
	 * session flash data a cookie to allow the user to be remembered the next
	 * time it visits the list. If it fails, it stores the validator instance on
	 * the session flash data.
	 *
	 * @param  string $password
	 * @param  string $type
	 * @return bool
	 */
	public function attemptLogin($password, $type) {
		Log::info('Attempting to login with password: ' . $password);
		Log::info('Type is: ' . $type);

		if ($this->checkLogin($type)) {
			return true;
		} else {
			$passwordProp = $type.'Password';
			Log::info('passwordProp is: ' . $passwordProp);
			$validator = Validator::make(
				array('password' => $password),
				array('password' => array(
					'required',
					'hash:'.$this->$passwordProp
				))
			);

			if ($validator->passes()) {
				$this->createCookie($this->$passwordProp, $type);
				return true;
			}
		}

		Session::flash('errors', $validator);
		return false;
	}

	/**
	 * Create a cookie to allow the user to be remembered the next time it
	 * visits the list. Stores it in the flash session data.
	 *
	 * @param  string $password
	 * @param  string $type
	 * @return void
	 */
	private function createCookie($password, $type) {
		$cookieName = md5($type.'_'.$this->id);

		Session::flash('rememberCookie', Cookie::forever($cookieName, md5($password)));
	}





	/**
	 * Convert from hash to id.
	 *
	 * @param  string  $value
	 * @return int
	 */
	public static function toId($value) {
		$hashid = new Hashids('9gXh45b9rIgkTeGtrS8cbRrIr69LnhLK', 8);
		return $hashid->decrypt($value)[0];
	}

	/**
	 * Convert from id to hash.
	 *
	 * @param  integer $value
	 * @return string
	 */
	public static function toHash($value) {
		$hashid = new Hashids('9gXh45b9rIgkTeGtrS8cbRrIr69LnhLK', 8);
		return $hashid->encrypt($value);
	}
}