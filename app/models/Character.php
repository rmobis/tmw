<?php

class Character extends Eloquent {

	/**
	 * Indicates if the model should be timestamped.
	 *
	 * @var bool
	 */
	public $timestamps = true;





	/**
	 * Create the relation of lists a character belongs to.
	 *
	 * @return Illuminate\Database\Query\Builder
	 */
	public function lists() {
		return $this->belongsToMany('CharacterList', 'list_characters')
					->withPivot('observation');
	}





	/**
	 * Parse the online attribute into readable strings.
	 *
	 * @return string
	 */
	protected function getStatusAttribute() {
		return $this->online ? 'online' : 'offline';
	}

	/**
	 * Get the abbreviation for the vocation attribute.
	 *
	 * @return string
	 */
	protected function getVocAttribute() {
		return preg_replace('/[^A-Z]/', '', $this->vocation);
	}





	/**
	 * Set a where clause filtering out characters which have not been updated
	 * in the last minute.
	 *
	 * @param  integer                           $minutes
	 * @return Illuminate\Database\Query\Builder
	 */
	public static function outOfDate($minutes = 1) {
		$date = new DateTime();
		$date->sub(new DateInterval('PT'.$minutes.'M'));

		return static::where('updated_at', '<', $date);
	}

	/**
	 * @needdocs
	 */
	public function isBrandNew() {
		return $this->updated_at === $this->created_at;
	}
}