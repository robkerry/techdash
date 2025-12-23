<?php

/**
 * IDE Helper file for Laravel helper functions
 * This file helps IntelliPHense and other IDEs understand Laravel's helper functions
 * 
 * This file is autoloaded via composer.json to provide IDE support
 */

namespace {
    if (!function_exists('auth')) {
        /**
         * Get the available auth instance.
         *
         * @param string|null $guard
         * @return \Illuminate\Contracts\Auth\Guard|\Illuminate\Contracts\Auth\StatefulGuard
         */
        function auth($guard = null)
        {
            if (is_null($guard)) {
                return \Illuminate\Support\Facades\Auth::guard();
            }

            return \Illuminate\Support\Facades\Auth::guard($guard);
        }
    }
}

namespace Illuminate\Contracts\Auth {
    /**
     * @method \App\Models\User|null user()
     * @method bool check()
     * @method bool guest()
     * @method \Illuminate\Contracts\Auth\Authenticatable|null loginUsingId($id, $remember = false)
     * @method bool once(array $credentials = [])
     * @method bool onceUsingId($id)
     * @method bool validate(array $credentials = [])
     * @method void setUser(\Illuminate\Contracts\Auth\Authenticatable $user)
     * @method bool attempt(array $credentials = [], $remember = false)
     * @method bool login(\Illuminate\Contracts\Auth\Authenticatable $user, $remember = false)
     * @method bool logout()
     */
    interface Guard
    {
        /**
         * Get the currently authenticated user.
         *
         * @return \App\Models\User|null
         */
        public function user();
    }

    /**
     * @method \App\Models\User|null user()
     * @method bool check()
     * @method bool guest()
     * @method \Illuminate\Contracts\Auth\Authenticatable|null loginUsingId($id, $remember = false)
     * @method bool once(array $credentials = [])
     * @method bool onceUsingId($id)
     * @method bool validate(array $credentials = [])
     * @method void setUser(\Illuminate\Contracts\Auth\Authenticatable $user)
     * @method bool attempt(array $credentials = [], $remember = false)
     * @method bool login(\Illuminate\Contracts\Auth\Authenticatable $user, $remember = false)
     * @method bool logout()
     */
    interface StatefulGuard extends Guard
    {
    }
}

