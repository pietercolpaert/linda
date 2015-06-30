<?php namespace Tdt\Dapps\Controllers;

/**
 *
 * @copyright (C) 2011, 2014 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Michiel Vancoillie <michiel@okfn.be>
 */

use View;

class AuthController extends \Controller
{

    public function getLogin()
    {
        return View::make('auth.login')->with('title', 'Login | Linda')
                                     ->with('page_title', 'Authentication')
                                     ->with('message', \Input::get('message'));
    }

    public function postLogin()
    {
        try {
            $credentials = array(
                'email'    => \Input::get('username'),
                'password' => \Input::get('password'),
                );

            $return = \Input::get('return', 'dataset');
            $user = \Sentry::authenticateAndRemember($credentials);

            // Success! Redirect back
            return \Redirect::to($return);
        } catch (\Cartalyst\Sentry\Users\LoginRequiredException $e) {
            $message = 'Username is required.';
        } catch (\Cartalyst\Sentry\Users\PasswordRequiredException $e) {
            $message = 'Password is required.';
        } catch (\Cartalyst\Sentry\Users\WrongPasswordException $e) {
            $message = 'Username and/or password incorrect.';
        } catch (\Cartalyst\Sentry\Users\UserNotFoundException $e) {
            $message = 'Username and/or password incorrect.';
        } catch (\Cartalyst\Sentry\Users\UserNotActivatedException $e) {
            $message = 'User is not activated.';
        } catch (\Cartalyst\Sentry\Throttling\UserSuspendedException $e) {
            $message = 'User is suspended.';
        } catch (\Cartalyst\Sentry\Throttling\UserBannedException $e) {
            $message = 'User is banned.';
        }

        return \Redirect::to('login?return=' . $return . '&message=' . $message);
    }

    public function getLogout()
    {
        // Logs the user out
        \Sentry::logout();

        return \Redirect::to('/');
    }
}
