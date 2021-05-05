<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use Tymon\JWTAuth\Exceptions\PayloadException;
use Tymon\JWTAuth\Exceptions\InvalidClaimException;
use Tymon\JWTAuth\Exceptions\JWTException;

class JwtMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
    		if (! $token = JWTAuth::getToken()) {
                return response()->json(['msg' => 'token.not_provided'], 400);
            }
            if (! $user = JWTAuth::parseToken()->authenticate()) {
               return response()->json(['msg' => 'user_not_found'], 404);
            }
        } catch (TokenExpiredException $e) {
            Log::error('TokenExpiredException: ' .$e->getMessage());
            return response()->json(['msg' => 'token.expired'], 401); //$statusCode = 401

        } catch (TokenBlacklistedException $e) {
            Log::error('TokenBlacklistedException: ' .$e->getMessage());
            return response()->json(['msg' => 'token.blacklisted'], 401); //$statusCode = 401

        } catch (TokenInvalidException $e) {
            Log::error('TokenInvalidException: ' .$e->getMessage());
            return response()->json(['msg' => 'token.invalid'], 400); //$statusCode = 400

        } catch (PayloadException $e) {
            Log::error('PayloadException: ' .$e->getMessage());
            return response()->json(['msg' => 'token.expired'], 500); //$statusCode = 500

        } catch (InvalidClaimException $e) {
            Log::error('InvalidClaimException: ' .$e->getMessage());
            return response()->json(['msg' => 'token.invalid'], 400); //$statusCode = 400

        } catch (JWTException $e) {
            Log::error('JWTException: ' .$e->getMessage());
            return response()->json(['msg' => 'token.absent'], 500); //$statusCode = 500

        } catch (\Exception $e) {
            Log::error('Exception: ' .$e->getMessage());
            return response()->json(['msg' => 'token.invalid'], 500); //$statusCode = 500
        }

        return $next($request);
    }
}
