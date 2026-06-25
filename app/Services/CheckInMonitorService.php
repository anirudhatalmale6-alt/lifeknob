<?php

namespace App\Services;

use App\Models\CheckInModel;
use App\Models\CheckInSettingModel;
use App\Models\AlertModel;
use App\Models\FamilyMemberModel;
use App\Models\UserModel;

class CheckInMonitorService
{
    public function run(): array
    {
        $settingModel = new CheckInSettingModel();
        $elders = $settingModel->getActiveElderSettings();

        $checkInModel = new CheckInModel();
        $alertModel = new AlertModel();
        $notificationService = new NotificationService();

        $results = ['reminders' => 0, 'alerts' => 0, 'checked' => 0];

        foreach ($elders as $elder) {
            $results['checked']++;

            if ($this->isQuietHours($elder)) {
                continue;
            }

            $latest = $checkInModel->getLatestCheckIn($elder->user_id);
            $lastCheckInTime = $latest ? strtotime($latest->created_at) : 0;
            $elapsed = time() - $lastCheckInTime;
            $frequencySeconds = $elder->frequency_hours * 3600;

            $reminderThreshold = $frequencySeconds - ($elder->reminder_minutes * 60);
            if ($elapsed >= $reminderThreshold && $elapsed < $frequencySeconds) {
                $minutesLeft = max(1, (int)(($frequencySeconds - $elapsed) / 60));
                $notificationService->send(
                    $elder->user_id,
                    'reminder',
                    'Time to check in!',
                    "Please press \"I'm OK\" within the next {$minutesLeft} minutes.",
                    ['type' => 'checkin_reminder']
                );
                $results['reminders']++;
                continue;
            }

            $alertThreshold = $frequencySeconds + ($elder->alert_delay_minutes * 60);
            if ($elapsed >= $alertThreshold) {
                $existingAlert = $alertModel
                    ->where('elder_id', $elder->user_id)
                    ->where('type', 'missed_checkin')
                    ->where('is_resolved', 0)
                    ->first();

                if ($existingAlert) {
                    continue;
                }

                $user = (new UserModel())->find($elder->user_id);
                $userName = $user ? $user->name : 'Unknown';
                $hoursAgo = round($elapsed / 3600, 1);

                $groups = model('FamilyGroupModel')->getGroupsForUser($elder->user_id);
                foreach ($groups as $group) {
                    $alertModel->insert([
                        'group_id'   => $group->id,
                        'elder_id'   => $elder->user_id,
                        'type'       => 'missed_checkin',
                        'message'    => "{$userName} has not checked in for {$hoursAgo} hours.",
                        'created_at' => date('Y-m-d H:i:s'),
                    ]);

                    $familyMemberModel = new FamilyMemberModel();
                    $familyMembers = $familyMemberModel->getFamilyInGroup($group->id);
                    foreach ($familyMembers as $member) {
                        $notificationService->send(
                            $member->user_id,
                            'alert',
                            "[ALERT] {$userName} missed check-in",
                            "{$userName} has not checked in for {$hoursAgo} hours. Please check on them.",
                            ['type' => 'missed_checkin', 'elder_id' => $elder->user_id, 'group_id' => $group->id]
                        );
                    }
                }

                $results['alerts']++;
            }
        }

        return $results;
    }

    private function isQuietHours(object $settings): bool
    {
        if (empty($settings->quiet_hours_start) || empty($settings->quiet_hours_end)) {
            return false;
        }

        $tz = new \DateTimeZone($settings->timezone ?? 'UTC');
        $now = new \DateTime('now', $tz);
        $currentTime = $now->format('H:i:s');

        $start = $settings->quiet_hours_start;
        $end = $settings->quiet_hours_end;

        if ($start <= $end) {
            return $currentTime >= $start && $currentTime <= $end;
        }

        return $currentTime >= $start || $currentTime <= $end;
    }
}
