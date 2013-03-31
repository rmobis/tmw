<?php

use Illuminate\Support\MessageBag;

class ListController extends BaseController {

	/**
	 * @needdocs
	 */
	protected $createListRules = array(
		'list_name' =>'required|max:128',
		'description' => 'max:256',
		'master_password' => 'required|min:6|confirmed',
		'master_password_confirmation' => 'required',
		'public_password' => 'required_with:public_password_confirmation|min:6|confirmed',
	);

	/**
	 * @needdocs
	 */
	protected $createListMessages = array(
		'public_password.required_with' => 'The :attribute confirmation does not match.'
	);

	/**
	 * Handles any attempt to display the contents of a list.
	 *
	 * @route showList
	 * @param CharacterList $list
	 * @return Illuminate\View\View
	 */
	public function showList($list) {
		if ($list->checkLogin(CharacterList::AUTH_SHOW)) {
			return $this->renderSubpage('showList', $list, $list->title.' &middot; Most Wanted', $list->checkLogin(CharacterList::AUTH_EDIT));
		} else {
			return $this->renderSubpage('authList', $list, 'Private List &middot; Most Wanted');
		}
	}

	/**
	 * Handles any attempt to enter the edit mode on a list.
	 *
	 * @route editList
	 * @param CharacterList $list
	 * @return Illuminate\View\View
	 */
	public function editList($list) {
		if ($list->checkLogin(CharacterList::AUTH_EDIT)) {
			return Redirect::route('showList', array($list->hash));
		} else {
			return $this->renderSubpage('authList', $list, 'Password Required &middot; Most Wanted', true);
		}
	}

	/**
	 * Handles the post request of any attempt to login with a show password.
	 *
	 * @route authShowList
	 * @param CharacterList $list
	 * @return Illuminate\Http\RedirectResponse
	 */
	public function authShowList($list) {
		return $this->authList($list, CharacterList::AUTH_SHOW, Input::get('password'));
	}

	/**
	 * Handles the post request of any attempt to login with an edit password.
	 *
	 * @route authEditList
	 * @param CharacterList $list
	 * @return Illuminate\Http\RedirectResponse
	 */
	public function authEditList($list) {
		return $this->authList($list, CharacterList::AUTH_EDIT, Input::get('password'));
	}

	/**
	 * @needdocs
	 */
	public function showCreateList() {
		return $this->renderSubpage('showCreateList', null, 'Create a list &middot; Most Wanted');
	}

	/**
	 * @needdocs
	 */
	public function createList() {
		$validator = Validator::make(Input::all(), $this->createListRules, $this->createListMessages);

		if ($validator->passes()) {
			$list = new CharacterList();
			$list->title = Input::get('list_name');
			$list->description = Input::get('description');
			$list->edit_password = Input::get('master_password');
			$list->show_password = Input::get('public_password');

			$list->save();

			return Redirect::route('showList', $list->hash);
		} else {
			return Redirect::route('showCreateList')
			               ->withErrors($validator);
		}
	}

	/**
	 * @needdocs
	 */
	public function addChar($list) {
		$char = Character::with('lists')
						 ->where('name', '=', Input::get('char'))
						 ->first();

		if (is_null($char)) {
			$char = new Character;
			$char->name = Input::get('char');
			$char->online = true;
			$char->level = 0;
			$char->vocation = 'Knight';
			$char->world = 'Silvera';

			$char->save();
		}

		if (!$char->lists->contains($list->id)) {
			$list->characters()->attach($char);
			$error = false;
		} else {
			$error = true;
			$message = 'This character is already in this list.';
		}

		return Response::json(compact('error', 'message'));
	}

	/**
	 * @needdocs
	 */
	public function removeChar($list) {
		$char = Character::with('lists')
                         ->find(Input::get('char'));

		if (!is_null($char)) {
			$list->characters()->detach($char);
		}

		return Response::json(array());
	}




	/**
	 * Helper function to handle authorization of both edit and view attempts in
	 * a single liner.
	 *
	 * @param CharacterList $list
	 * @param string        $authType
 	 * @param string        $password
 	 * @return Illuminate\Http\RedirectResponse
	 */
	private function authList($list, $type, $password) {
		$redirectRoute = $type.'List';


		if ($list->attemptLogin($password, $type)) {
			return Redirect::route($redirectRoute, array($list->hash))
						   ->withCookie(Session::get('rememberCookie'));
		} else {
			return Redirect::route($redirectRoute, array($list->hash))
						   ->withErrors(Session::get('errors'));
		}

	}

	/**
	 * Helper function to render a subpage using the default layout and bind to
	 * it the list and title in a one-liner.
	 *
	 * @param string        $subpage
	 * @param CharacterList $list
	 * @param string        $title
	 * @return Illuminate\View\View
	 */
	private function renderSubpage($subpage, $list, $title, $edit = false) {
		$createListRules = $this->createListRules;
		return $this->layout
					->nest('subpage', 'subpages.'.$subpage, compact('list', 'edit', 'createListRules'))
					->with('title', $title);
	}

}