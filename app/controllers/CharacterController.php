<?php

class CharacterController extends BaseController {

	public function updateCharacters() {
		// If the request does not come from ourselves, we abort it; no one
		// should be able to update the characters from outside the server, as
		// that could lead to several attacks.
		if ($_SERVER['SERVER_ADDR'] !== $_SERVER['REMOTE_ADDR'] and
			$_SERVER['REMOTE_ADDR'] !== '66.147.244.90') {
			return App::abort('401', 'You are not authorized to view this page.');
		}

		CharacterUpdater::updateNewCharacters();
		CharacterUpdater::updateOnline();
		CharacterUpdater::updateNoLongerOnline();

		$msg = 'Updated chars in '.sprintf('%f', microtime(true) - LARAVEL_START).'s';
		Log::info($msg);
		return $msg;
	}

}