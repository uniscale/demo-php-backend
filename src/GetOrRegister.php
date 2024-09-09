<?php

namespace Uniscale\PrivateDemoPhpBackend;

use Uniscale\Http\Result;
use Uniscale\Models\BackendActions\RequestResponseBackendAction;
use Uniscale\Platform\Types\CallableRequestResponseInterceptor;
use Uniscale\Platform\Types\RequestResponseInterceptor;

/**
 * TODO: Remove! This had to be added as intermediate measure to stay compatible with the older versions.
 */
class GetOrRegister extends RequestResponseBackendAction {
    const FEATURE_ID = "UniscaleDemo.Account_1_0.Functionality.ServiceToModule.Account.Registration.ContinueToApplication.GetOrRegister";
    const ALL_FEATURE_USAGES = "UniscaleDemo.Account_1_0.Functionality.ServiceToModule.Account.Registration.ContinueToApplication.GetOrRegister;";

    public static function with(string $requestBody): GetOrRegister {
        return new GetOrRegister($requestBody);
    }

    public function __construct(string $requestBody) {
        parent::__construct("UniscaleDemo.Account_1_0.Functionality.ServiceToModule.Account.Registration.ContinueToApplication.GetOrRegister",$requestBody);
    }

    /**
     * @param callable $handler A callable that should have the signature function(string $input, FeatureContext $ctx): Result<array>
     * @return CallableRequestResponseInterceptor A function that takes two parameters: string $input and FeatureContext $ctx
     */
    public static function handle(callable $handler) {
        return new CallableRequestResponseInterceptor($handler);
    }

    /**
     * @param callable $handler A callable that should have the signature function(string $input, FeatureContext $ctx): Result<array>
     * @return RequestResponseInterceptor A function that takes two parameters: string $input and FeatureContext $ctx
     */
    public static function handleAsync(callable $handler) {
        return new CallableRequestResponseInterceptor($handler);
    }

    /**
     * @param callable $handler A callable that should have the signature function(string $input, FeatureContext $ctx): Result<array>
     * @return RequestResponseInterceptor A function that takes two parameters: string $input and FeatureContext $ctx
     */
    public static function handleDirect(callable $handler) {
        return new CallableRequestResponseInterceptor(function ($input, $ctx) use ($handler) {
            $result = call_user_func($handler, get_object_vars($input), $ctx);
            return Result::ok($result);
        });
    }

    /**
     * @param callable $handler A callable that should have the signature function(string $input, FeatureContext $ctx): Result<array>
     * @return RequestResponseInterceptor A function that takes two parameters: string $input and FeatureContext $ctx
     */
    public static function handleAsyncDirect(callable $handler) {
        return new CallableRequestResponseInterceptor(function ($input, $ctx) use ($handler) {
            $result = call_user_func($handler, get_object_vars($input), $ctx);
            return Result::ok($result);
        });
    }
}