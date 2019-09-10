<?php

class HelpController extends \Loaner\AdminController {
    
    public function showChangePassword()
    {
    	$this->layout->content = View::make('login.change_password');
    }
    
    public function processChangePassword()
    {
        // validate the input fields, create rules for the inputs
        $rules = array(
                'opassword' => 'required',
        		'password' => 'required|alphaNum|min:3|confirmed', // password can only be alphanumeric and has to be greater than 3 characters
        		'password_confirmation' => 'required|alphaNum|min:3'
        );
        
        // run the validation rules on the inputs from the form
        $validator = Validator::make(Input::all(), $rules);
        
        // if the validator fails, redirect back to the form
        if ($validator->fails())
        {
        	return Redirect::to('profile/password')
        	->withErrors($validator); // send back all errors to the change password form
        }
        else
        {
        	$user = User::query()->where('userid', '=', Auth::getUser()->userid )->where('password', '=', sha1(Input::get('opassword')))->first();
        
        	// if the old password entered is correct
        	if ($user)
        	{
        		// change the password to new password
        	    
        	    User::query()->where('userid', '=', Auth::getUser()->userid )->update(array('password' => sha1(Input::get('password'))));
        	    return Redirect::intended('profile/password')->with('message', 'Password changed successfully');
           	}
        	else
        	{
        		// validation not successful, old password for user is incorrect
        		return Redirect::to('profile/password')->withErrors(array('password' => 'Old password entered is incorrect' ));
        	}
        
        }   
    }

    
} 