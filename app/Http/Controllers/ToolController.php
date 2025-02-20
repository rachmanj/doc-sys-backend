<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

class ToolController extends Controller
{
    public function getUserRoles()
    {
        try {
            // Get token from the Authorization header
            $token = str_replace('Bearer ', '', request()->header('Authorization'));

            // Find the token in the database
            $accessToken = PersonalAccessToken::findToken($token);

            if (!$accessToken) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid token'
                ], 401);
            }

            // Get the user associated with the token
            $user = $accessToken->tokenable;

            $userRoles = $user->getRoleNames()->toArray();

            return $userRoles;
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getUserPermissions()
    {
        try {
            // Get token from the Authorization header
            $token = str_replace('Bearer ', '', request()->header('Authorization'));

            // Find the token in the database
            $accessToken = PersonalAccessToken::findToken($token);

            if (!$accessToken) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid token'
                ], 401);
            }

            // Get the user associated with the token
            $user = $accessToken->tokenable;

            $userPermissions = $user->getAllPermissions()->pluck('name')->toArray();

            return $userPermissions;
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getUserProject()
    {
        try {
            // Get token from the Authorization header
            $token = str_replace('Bearer ', '', request()->header('Authorization'));

            // Find the token in the database
            $accessToken = PersonalAccessToken::findToken($token);

            if (!$accessToken) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid token'
                ], 401);
            }

            $user = $accessToken->tokenable;

            return $user->project;
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
