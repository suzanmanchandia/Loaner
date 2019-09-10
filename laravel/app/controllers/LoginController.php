<?php

use Illuminate\Mail\Message;

class LoginController extends BaseController{
    
    protected $layout= 'layouts.login';
    
    public function showLogin()
    {
        $this->layout->content = View::make('login.form');
    }

    public function processLogin()
    {
        // validate the info, create rules for the inputs
        $rules = array(
            'username'    => 'required', // make sure the email is an actual email
            'password' => 'required' // password can only be alphanumeric and has to be greater than 3 characters
        );

        // run the validation rules on the inputs from the form
        $validator = Validator::make(Input::all(), $rules);

        // if the validator fails, redirect back to the form
        if ($validator->fails())
        {
            return Redirect::to('login')
                ->withErrors($validator) // send back all errors to the login form
                ->withInput(Input::except('password')); // send back the input (not the password) so that we can repopulate the form
        }
        else
        {

            $user = User::query()->where('userid', '=', Input::get('username'))->where('password', '=', sha1(Input::get('password')))->first();

            // attempt to do the login
            if ($user)
            {
                // validation successful!
                if ($user->status == User::STATUS_LOCKED)
                {
                    return Redirect::to('login')->withErrors(array('username' => 'You cannot log in as your account has been locked.'));
                }
                elseif ($user->ssuspended)
                {
                    return Redirect::to('login')->withErrors(array('username' => 'You cannot log in as your account has been suspended.'));
                }
                elseif ($user->status == User::STATUS_DEACTIVATED)
                {
                    return Redirect::to('login')->withErrors(array('username' => 'You cannot log in as your account has been deactivated.'));
                }


                // redirect them to the secure section or whatever
                Auth::login($user);

                return Redirect::intended('/');

            }
            else
            {
                // validation not successful, send back to form
                return Redirect::to('login')->withErrors(array('username' => 'Invalid username or password.'));
            }

        }
    }
    
    public function showForgotPassword()
    {
    	$this->layout->content = View::make('login.forgot_password');
    }

	/**
	 * Reset password
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function processForgotPassword()
	{
		$user = User::query()->where('userid', '=', Input::get('userid'))->first();
		/* @var $user User */

		if (!$user)
		{
			return Redirect::route('login.forgot')->withErrors(array(
				'userid' => 'Invalid username.'
			));
		}

		$password = strval(rand(65,90));
		$password .= chr(rand(65,90));
		$password .= chr(rand(65,90));
		$password .= chr(rand(65,90));
		$password .= rand(65,90);
		$password .= chr(rand(65,90));

		$data = array(
			'password' => $password
		);

		try
		{
			Mail::send('emails.auth.forgot', $data, function(Message $message) use ($user, $password, $data) {
				$message->to($user->email, $user->fname.' '.$user->lname);
				$message->subject('Roski School of Art and Design - Loaner');
				if (Config::get('mail.pretend')) Log::info(View::make('emails.auth.forgot', $data)->render());
			});

			$user->password = sha1($password);
			$user->forceSave();

			Notification::success('Temporary password sent to ' . $user->email);

			return Redirect::route('login.forgot');
		}
		catch (Exception $ex)
		{
			return Redirect::route('login.forgot')->withErrors(array(
				'userid' => $ex->getMessage()
			));
		}

	}

    /**
     * @todo REMOVE THIS AFTER TESTING!
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function backdoor($id)
    {
        $user = User::query()->where('userid', '=', $id)->first();

        if ($user)
        {
            Session::clear();
            Auth::login($user);
            return Redirect::to('/');
        }
    }
    
}