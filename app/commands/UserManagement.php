<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use Cartalyst\Sentry\Users\Eloquent\User;

class UserManagement extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'app:user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manage users for the application. If no options are passed, a list of current admin users is shown.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $create = $this->option('create');
        $delete = $this->option('delete');

        if (!empty($create)) {

            $name = $this->ask('Enter the name of the user:');

            while (empty($name)) {
                $name = $this->ask('Enter the name of the user:');
            }

            $password = $this->secret('Enter the password for the user:');

            while (empty($password) || strlen($password) < 5) {
                $this->info('Make sure the password is longer than 5 characters');

                $password = $this->secret('Enter the password for the user:');
            }

            $pwConfirm = $this->secret('Re-enter the password to confirm:');

            while ($pwConfirm != $password) {
                $pwConfirm = $this->secret('The passwords did not match, re-enter the password to confirm:');
            }

            // Create 'everyone' user and group
            Sentry::getUserProvider()->create(array(
                'email'       => $name,
                'password'    => $pwConfirm,
                'first_name'  => $name,
                'last_name'   => '',
                'activated'   => 1,
            ));

            $this->info('The user ' . $name . ' has been created and activated.');

        } else if (!empty($delete)) {

            $name = $this->ask('Enter the name of the user you want to delete.');

            while (empty($name)) {
                $name = $this->ask('Enter the name of the user you want to delete.');
            }

            $user = User::where(['email' => $name])->first();

            if (empty($user)) {

                $this->error('No user with the name ' . $name . ' has been found.');
            } else {

                if ($this->confirm('The user ' . $name . ' has been found, are you sure you want do delete? [yes|no]')) {

                    $user->delete();

                } else {
                    $this->info('The user ' . $name . ' has not been removed.');
                }
            }

        } else {

            // Get all of the users
            $users = User::all(['email'])->toArray();

            foreach ($users as $user) {

                if ($user['email'] != 'everyone') {
                    $this->info($user['email']);
                }
            }
        }
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array(
        );
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array(
            array('create', 'c', InputOption::VALUE_NONE, 'Create a new admin user.', null),
            array('delete', 'd', InputOption::VALUE_NONE, 'Delete an admin user.', null),
        );
    }

}
