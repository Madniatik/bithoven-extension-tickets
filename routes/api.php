<?php

use Illuminate\Support\Facades\Route;
use Bithoven\Tickets\Http\Controllers\Api\TicketApiController;

Route::middleware(['api', 'extension.enabled:tickets', 'auth:sanctum'])->prefix('api/v1')->name('api.')->group(function () {
    Route::apiResource('tickets', TicketApiController::class);
    
    // Ticket actions
    Route::post('tickets/{ticket}/assign', [TicketApiController::class, 'assign'])->name('tickets.assign');
    Route::post('tickets/{ticket}/comments', [TicketApiController::class, 'addComment'])->name('tickets.comments');
    Route::post('tickets/{ticket}/close', [TicketApiController::class, 'close'])->name('tickets.close');
    Route::post('tickets/{ticket}/reopen', [TicketApiController::class, 'reopen'])->name('tickets.reopen');
});
