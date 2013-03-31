<?php

class MainController extends BaseController {

	public function index() {
		return $this->layout
					->nest('subpage', 'subpages.home')
					->with('title', 'Most Wanted');
	}

}