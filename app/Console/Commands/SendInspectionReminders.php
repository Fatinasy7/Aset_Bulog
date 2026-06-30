<?php

namespace App\Console\Commands;

use App\Mail\InspectionReminderMail;
use App\Models\Pic;
use App\Models\Notification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendInspectionReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-inspection-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send daily inspection reminders to PIC users.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $pics = Pic::with('assets')->has('assets')->get();
        $sent = 0;

        foreach ($pics as $pic) {
            $assets = $pic->assets;
            if ($assets->isEmpty()) {
                continue;
            }

            Mail::mailer('log')->to($pic->email)->send(new InspectionReminderMail($pic, $assets));

            Notification::create([
                'user_id' => null,
                'role' => 'user_pic',
                'title' => 'Pengingat Pemeriksaan Aset',
                'message' => "Pengingat harian: Anda memiliki {$assets->count()} aset untuk pemeriksaan.",
                'data' => [
                    'asset_ids' => $assets->pluck('id')->values()->all(),
                ],
            ]);

            $sent++;
        }

        $this->info("Inspection reminders sent to {$sent} PIC(s).");

        return 0;
    }
}
