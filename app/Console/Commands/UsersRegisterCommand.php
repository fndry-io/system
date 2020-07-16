<?php

namespace Foundry\System\Console\Commands;

use Foundry\System\Inputs\User\UserRegisterInput;
use Illuminate\Console\Command;
use Foundry\System\Services\UserService;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class UsersRegisterCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'foundry:users:register
        {username : The username}
        {display_name : The users display name} 
        {email : The email address of the user to create}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Registers a new user in the application';

	/**
	 * @var UserService
	 */
    protected $service;

	/**
	 * UsersRegisterCommand constructor.
	 *
	 * @param UserService $service
	 */
    public function __construct(UserService $service)
    {
        parent::__construct();
        $this->service = $service;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
	    $arguments = $this->arguments();
	    $password = $this->secret('What is the password? (leave empty to auto generate a password)');
	    $password_confirmation = null;
	    if ($password) {
		    $password_confirmation = $this->secret('Confirm your password');
	    }
	    $super = $this->choice('Should this user be a Super Admin', ['No', 'Yes'], 0);

	    if (empty($password)) {
		    $password = $password_confirmation = str_shuffle(Str::ucfirst(Str::random(4)) . '#' . random_int(2343, 9127));
	    }
	    $arguments['password'] = $password;
	    $arguments['password_confirmation'] = $password_confirmation;

	    $entity = new UserRegisterInput($arguments);

	    try {
            $entity->validate();
            $response = $this->service->register($entity);
            if ($response->isSuccess()) {
                $user = $response->getData();
                if ($super) {
                    $user->super_admin = true;
                    $user->save();
                }
                $user = $user->only(['id', 'username', 'display_name', 'email', 'super_admin']);
                $user['password'] = $password;
                $this->info('User registered');
                $this->table(['ID', 'Username', 'Display Name', 'Email', 'Super Admin', 'Password'], [$user]);
                return;
            }
        } catch (ValidationException $e) {
            $this->error('User could not be registered. See below for errors.');
            $errors = $e->validator;
            $this->table(['Error'], $errors->errors());
        } catch (\Throwable $e) {
            $error = $e->getError();
            $this->table(['Error'], [(array) $error]);
        }

    }
}
