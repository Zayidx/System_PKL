<?php

namespace App\Observers;

use App\Models\Prakerin;
use App\Events\PrakerinSelesaiEvent;

class PrakerinObserver
{
    /**
     * Handle the Prakerin "updated" event.
     */
    public function updated(Prakerin $prakerin): void
    {
        \Log::info('Observer updated triggered', [
            'prakerin_id' => $prakerin->id_prakerin,
            'old_status' => $prakerin->getOriginal('status_prakerin'),
            'new_status' => $prakerin->status_prakerin,
            'is_dirty' => $prakerin->isDirty('status_prakerin')
        ]);

        // Cek apakah status berubah menjadi 'selesai'
        if ($prakerin->isDirty('status_prakerin') && $prakerin->status_prakerin === 'selesai') {
            \Log::info('Status berubah menjadi selesai, dispatching event...', [
                'prakerin_id' => $prakerin->id_prakerin
            ]);
            
            // Dispatch event untuk mengirim email
            event(new PrakerinSelesaiEvent($prakerin));
            
            \Log::info('Event PrakerinSelesaiEvent dispatched', [
                'prakerin_id' => $prakerin->id_prakerin
            ]);
        } else {
            \Log::info('Observer tidak dispatch event', [
                'prakerin_id' => $prakerin->id_prakerin,
                'reason' => 'Status tidak berubah menjadi selesai atau tidak dirty'
            ]);
        }
    }

    /**
     * Method public untuk testing email (legacy)
     */
    public function testSendEmail(Prakerin $prakerin): void
    {
        event(new PrakerinSelesaiEvent($prakerin));
    }
} 