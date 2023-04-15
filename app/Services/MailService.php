<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;

class MailService
{
    public function sendEmailConfirmation(array $to, array $data, string $subject): void
    {
        Mail::send('confirm_email_customer_email', $data, function ($message) use ($to, $subject) {
            $message->to($to)->subject($subject);
            $message->from(config('mail.from.address'), config('mail.from.name'));
        });
    }

    public function sendEmailRecoverPassword(string $to, array $data, string $subject): void
    {
        Mail::send('forgot_password_confirmation_email', $data, function ($message) use ($to, $subject) {
            $message->to($to)->subject($subject);
            $message->from(config('mail.from.address'), config('mail.from.name'));
        });
    }

    public function sendCompanyCreatedConfirmation(string $to, array $data, string $subject): void
    {
        Mail::send('company_created_confirmation_email', $data, function ($message) use ($to, $subject) {
            $message->to($to)->subject($subject);
            $message->from(config('mail.from.address'), config('mail.from.name'));
        });
    }

    public function sendNewTeamMemberInvitation(string $to, array $data, string $subject): void
    {
        Mail::send('team_member_invitation_email', $data, function ($message) use ($to, $subject) {
            $message->to($to)->subject($subject);
            $message->from(config('mail.from.address'), config('mail.from.name'));
        });
    }

    public function sendModeratorInvitation(string $to, string $subject): void
    {
        Mail::send('moderator_invitation_email', ['name' => $to], function ($message) use ($to, $subject) {
            $message->to($to)->subject($subject);
            $message->from(config('mail.from.address'), config('mail.from.name'));
        });
    }

    public function sendGameApproveEmail(string $to, array $data, string $subject): void
    {
        Mail::send('game_approved_email', $data, function ($message) use ($to, $subject) {
            $message->to($to)->subject($subject);
            $message->from(config('mail.from.address'), config('mail.from.name'));
        });
    }

    public function sendGameInformationEmail(string $to, array $data, string $subject): void
    {
        Mail::send('game_changes_from_es_info_email', $data, function ($message) use ($to, $subject) {
            $message->to($to)->subject($subject);
            $message->from(config('mail.from.address'), config('mail.from.name'));
        });
    }

    public function sendPayoutRequestConfirmation(string $to, array $data, string $subject): void
    {
        Mail::send('payout_request_confirmation_email', $data, function ($message) use ($to, $subject) {
            $message->to($to)->subject($subject);
            $message->from(config('mail.from.address'), config('mail.from.name'));
        });
    }

    public function sendPayoutRequestCreation(string $to, array $data, string $subject): void
    {
        Mail::send('payout_request_created_email', $data, function ($message) use ($to, $subject) {
            $message->to($to)->subject($subject);
            $message->from(config('mail.from.address'), config('mail.from.name'));
        });
    }
}
