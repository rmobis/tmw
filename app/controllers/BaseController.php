<?php

class BaseController extends Controller {
	/**
	 * The layout view to be used on all models.
	 *
	 * @var string
	 */
	protected $layout = 'layout';

	/**
	 * Setup the layout used by the controller.
	 *
	 * @return void
	 */
	protected function setupLayout() {
		if ( ! is_null($this->layout))
		{
			$this->layout = View::make($this->layout);
		}
	}

}