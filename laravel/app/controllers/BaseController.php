<?php

class BaseController extends Controller {

	/**
	 * Setup the layout used by the controller.
	 *
	 * @return void
	 */
	protected function setupLayout()
	{
		if ( ! is_null($this->layout))
		{
			$this->layout = View::make($this->layout);
		}
	}

	/**
	 * Return referring link or fallback route
	 * @param $route
	 * @param array $parameters
	 * @return string
	 */
	protected function previousOrRoute($route, array $parameters = null)
	{
		if (Request::header('referer')) {
			$url  = URL::previous();

			$home = route('home');

			if (strpos($url, $home) === false)
			{
				$url = route($route, $parameters);
			}
		}
		else {
			$url = route($route, $parameters);
		}

		return $url;
	}

}
