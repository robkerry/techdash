<?php

namespace App\Notifications;

use App\Models\Team;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Mpociot\Teamwork\TeamInvite;

class TeamInvitationNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Team $team,
        public TeamInvite $invite
    ) {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $acceptUrl = route('account.teams.invitations.accept', ['token' => $this->invite->accept_token]);

        return (new MailMessage)
            ->subject('You have been invited to join ' . $this->team->name)
            ->greeting('Hello!')
            ->line('You have been invited to join the team **' . $this->team->name . '** by ' . $this->team->owner->name . '.')
            ->line('Click the button below to accept the invitation and join the team.')
            ->action('Accept Invitation', $acceptUrl)
            ->line('If you did not expect this invitation, you can safely ignore this email.')
            ->line('This invitation will expire if not accepted.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'team_id' => $this->team->id,
            'team_name' => $this->team->name,
            'invite_id' => $this->invite->id,
        ];
    }
}
