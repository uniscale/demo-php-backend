<?php

namespace Uniscale\PrivateDemoPhpBackend\Services;

use Ramsey\Uuid\Uuid;
use Uniscale\Http\Result;
use Uniscale\Platform\Platform;
use Uniscale\PrivateDemoPhpBackend\GetOrRegister;
use Uniscale\PrivateDemoPhpBackend\Service;
use Uniscale\Uniscaledemo\Account\Account\UserFull;
use Uniscale\Uniscaledemo\Account_1_0\Functionality\Servicetomodule\Account\Getusers\Quicklookup\LookupUsers;
use Uniscale\Uniscaledemo\Account_1_0\Functionality\Servicetomodule\Account\Getusers\Searchusersbyhandle\SearchAllUsers;
use Uniscale\Utilisation\Types\FeatureContext;

/**
 * AccountService handles requests for the Account service.
 */
class AccountService extends Service
{
    /**
     * @var UserFull[] $users
     */
    private array $users = [];

    /**
     * Constructor initializes the AccountService with a specific port and service name.
     */
    public function __construct()
    {
        parent::__construct(5298, 'Account');
    }

    /**
     * Initializes the session and registers interceptors for account functionalities.
     *
     * This method sets up interceptors to handle requests such as user registration,
     * user lookup, and searching users by handle.
     *
     * @return void
     */
    protected function initializeSession(): void
    {
        // Register the timeline functionality interceptor with access to the forwarding session
        $this->session = Platform::builder()
            ->withInterceptors(function ($i) {
                $i->interceptRequest(
                    GetOrRegister::FEATURE_ID,
                    GetOrRegister::handle(function (string $input, FeatureContext $ctx) {
                        // Search for an existing user by handle
                        foreach ($this->users as $u) {
                            if ($u->handle === $input) {
                                // Return the existing user wrapped in a Result object
                                return Result::ok($u);
                            }
                        }

                        // If no existing user is found, create a new one
                        $newUserIdentifier = UUID::uuid4();
                        $user = new UserFull();
                        $user->userIdentifier = $newUserIdentifier;
                        $user->handle = $input;

                        // Add the new user to the users array
                        $this->users[] = $user;

                        // Return the newly created user wrapped in a Result object
                        return Result::ok($user);
                    })
                );

                $i->interceptRequest(
                    LookupUsers::FEATURE_ID,
                    LookupUsers::handle(function ($input, FeatureContext $ctx) {
                        $response = $this->findUsersByIdentifiers((array)$input);

                        return Result::ok($response);
                    })
                );

                $i->interceptRequest(
                    SearchAllUsers::FEATURE_ID,
                    SearchAllUsers::handle(function (string $input, FeatureContext $ctx) {
                        $response = $this->findUsersByHandle($input);

                        return Result::ok($response);
                    })
                );
            })
            ->build();
    }

    /**
     * Filters users based on their unique identifiers.
     *
     * @param array<string> $input An array of userIdentifiers to filter by.
     * @return array<UserFull> The filtered array of UserFull objects.
     */
    protected function findUsersByIdentifiers(array $input): array
    {
        return array_filter($this->users, function (UserFull $u) use ($input) {
            return in_array($u->userIdentifier, $input);
        });
    }

    /**
     * Filters users based on whether their handle contains the input string (case-insensitive).
     *
     * @param string $input The string to search for in user handles.
     * @return array<UserFull> The filtered array of UserFull objects.
     */
    protected function findUsersByHandle(string $input): array
    {
        // Convert the input to lowercase for case-insensitive comparison
        $input = strtolower($input);

        // Filter users where handle contains the input string (case-insensitive)
        return array_filter($this->users, function (UserFull $u) use ($input) {
            return stripos($u->handle, $input) !== false;
        });
    }
}