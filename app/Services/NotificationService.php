<?php

namespace App\Services;

use App\Models\HutNotification;
use App\Models\Incident;
use App\Models\UsageSession;
use App\Models\User;
use App\Models\UserPref;

class NotificationService
{
    // Roles to notify for incidents
    const INCIDENT_ROLES = [
        'super_admin', 'facility_manager', 'study_director',
        'qa_manager', 'project_manager', 'field_site_manager',
    ];

    // Roles to notify for activity start/end
    const ACTIVITY_ROLES = [
        'super_admin', 'facility_manager', 'study_director',
        'project_manager', 'data_manager', 'field_site_manager',
    ];

    /**
     * Notify relevant users about a reported incident.
     */
    public static function notifyIncident(Incident $incident): void
    {
        $incident->load(['hut.site', 'project']);

        $hutName  = $incident->hut?->name ?? 'Case inconnue';
        $siteName = $incident->hut?->site?->name ?? '';

        // Build message
        $message = "Incident signalé : \"{$incident->title}\" dans {$hutName}";
        if ($siteName) $message .= " ({$siteName})";
        $message .= ".";

        // Add current project info if the hut is in use
        $currentUsage = $incident->hut?->currentUsage();
        if ($currentUsage) {
            $projectCode = $currentUsage->project?->project_code ?? "Projet #{$currentUsage->project_id}";
            $phase       = $currentUsage->phase_name ? " — {$currentUsage->phase_name}" : '';
            $message    .= " ⚠️ Cette case est actuellement utilisée par le projet {$projectCode}{$phase}.";
            $message    .= " Jours restants dans l'activité : {$currentUsage->days_remaining}j.";
        }

        $users = User::whereIn('role', self::INCIDENT_ROLES)->where('active', 1)->get();
        $url   = route('incidents.show', $incident->id);

        foreach ($users as $user) {
            $pref = UserPref::forUser($user->id);
            if (!$pref->notify_incidents) continue;

            HutNotification::create([
                'user_id' => $user->id,
                'type'    => 'incident_reported',
                'title'   => "Incident : {$incident->title}",
                'message' => $message,
                'data'    => [
                    'incident_id'  => $incident->id,
                    'severity'     => $incident->severity,
                    'hut_name'     => $hutName,
                    'project_code' => $currentUsage?->project?->project_code,
                ],
                'url' => $url,
            ]);
        }
    }

    /**
     * Notify for activity (session) start.
     */
    public static function notifyActivityStart(UsageSession $session): void
    {
        $session->load('project');

        $roles = self::ACTIVITY_ROLES;
        if ($session->project?->is_glp) $roles[] = 'qa_manager';

        $hutCount    = $session->projectUsages()->count();
        $projectCode = $session->project?->project_code ?? "Projet #{$session->project_id}";
        $phase       = $session->phase_name ? " — {$session->phase_name}" : '';
        $dateStart   = $session->date_start->format('d/m/Y');
        $dateEnd     = $session->date_end->format('d/m/Y');

        $message = "L'activité en cases expérimentales du projet {$projectCode}{$phase} a démarré."
            . " {$hutCount} case(s) utilisée(s) du {$dateStart} au {$dateEnd}."
            . " Durée : {$session->duration_in_days} jours.";

        $users = User::whereIn('role', array_unique($roles))->where('active', 1)->get();
        $url   = route('projects.show', $session->project_id);

        foreach ($users as $user) {
            $pref = UserPref::forUser($user->id);
            if (!$pref->notify_activity_start) continue;

            HutNotification::create([
                'user_id' => $user->id,
                'type'    => 'activity_started',
                'title'   => "Démarrage activité — {$projectCode}",
                'message' => $message,
                'data'    => ['session_id' => $session->id, 'project_code' => $projectCode],
                'url'     => $url,
            ]);
        }

        $session->update(['notifications_sent_start' => true]);
    }

    /**
     * Notify for activity (session) end/completion.
     */
    public static function notifyActivityEnd(UsageSession $session): void
    {
        $session->load('project');

        $roles = self::ACTIVITY_ROLES;
        if ($session->project?->is_glp) $roles[] = 'qa_manager';

        $projectCode = $session->project?->project_code ?? "Projet #{$session->project_id}";
        $phase       = $session->phase_name ? " — {$session->phase_name}" : '';
        $dateEnd     = $session->date_end->format('d/m/Y');

        $message = "L'activité en cases expérimentales du projet {$projectCode}{$phase} s'est terminée le {$dateEnd}.";

        $users = User::whereIn('role', array_unique($roles))->where('active', 1)->get();
        $url   = route('projects.show', $session->project_id);

        foreach ($users as $user) {
            $pref = UserPref::forUser($user->id);
            if (!$pref->notify_activity_end) continue;

            HutNotification::create([
                'user_id' => $user->id,
                'type'    => 'activity_ended',
                'title'   => "Fin d'activité — {$projectCode}",
                'message' => $message,
                'data'    => ['session_id' => $session->id, 'project_code' => $projectCode],
                'url'     => $url,
            ]);
        }

        $session->update(['notifications_sent_end' => true]);
    }

    /**
     * Check and fire start/end notifications for sessions that haven't been notified yet.
     * Called from dashboard load.
     */
    public static function checkAndFirePendingNotifications(): void
    {
        $today = now()->toDateString();

        // Sessions that started today or earlier and haven't sent start notification
        \App\Models\UsageSession::where('date_start', '<=', $today)
            ->where('notifications_sent_start', false)
            ->whereNull('deleted_at')
            ->each(fn($s) => self::notifyActivityStart($s));

        // Sessions that ended yesterday or earlier and haven't sent end notification
        \App\Models\UsageSession::where('date_end', '<', $today)
            ->where('notifications_sent_end', false)
            ->whereNull('deleted_at')
            ->each(fn($s) => self::notifyActivityEnd($s));
    }
}
