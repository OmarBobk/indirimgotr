<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

// User-specific private channels
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    try {
        return (int) $user->id === (int) $id;
    } catch (\Exception $e) {
        Log::error('Channel auth error for App.Models.User.{id}: ' . $e->getMessage());
        return false;
    }
});

// Admin dashboard channel
Broadcast::channel('admin.dashboard', function ($user) {
    try {
        return $user->hasRole('admin');
    } catch (\Exception $e) {
        Log::error('Channel auth error for admin.dashboard: ' . $e->getMessage());
        return false;
    }
});

// Salesperson-specific channels
Broadcast::channel('salesperson.{id}', function ($user, $id) {
    try {
        return (int) $user->id === (int) $id || $user->hasRole('admin');
    } catch (\Exception $e) {
        Log::error('Channel auth error for salesperson.{id}: ' . $e->getMessage());
        return false;
    }
});

// Shop-specific channels
Broadcast::channel('shop.{id}', function ($user, $id) {
    try {
        // Allow access if a user is an admin, shop owner, or assigned salesperson
        return $user->hasRole('admin') ||
               ($user->ownedShop && $user->ownedShop->id == $id) ||
               $user->assignedShops()->where('id', $id)->exists();
    } catch (\Exception $e) {
        Log::error('Channel auth error for shop.{id}: ' . $e->getMessage());
        return false;
    }
});

// Public channels (no authorization required)
Broadcast::channel('shops', function ($user) {
    return true; // Anyone can listen to shop events
});

Broadcast::channel('orders', function ($user) {
    return true; // Anyone can listen to order events
});
