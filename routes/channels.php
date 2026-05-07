<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('orders', function ($user) {
    return $user !== null;
});

Broadcast::channel('cashier', function ($user) {
    return $user && in_array($user->role, ['cashier', 'admin', 'owner'], true);
});

Broadcast::channel('kitchen', function ($user) {
    return $user && in_array($user->role, ['kitchen', 'admin', 'owner'], true);
});

Broadcast::channel('stock', function ($user) {
    return $user && in_array($user->role, ['warehouse', 'admin', 'owner'], true);
});
