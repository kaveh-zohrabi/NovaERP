<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Support\BaseService;
use Illuminate\Support\Facades\Mail;

class EmailNotificationService extends BaseService
{
    public function __construct(
        private readonly NotificationPreferenceService $preferenceService,
        private readonly NotificationTemplateService $templateService,
    ) {}

    public function send(User $user, string $type, string $subject, string $body, array $params = []): bool
    {
        if (! $this->preferenceService->isChannelEnabled($user->id, 'email', $type)) {
            return false;
        }

        $rendered = $this->renderContent($type, $user, $params);
        $finalSubject = $rendered['subject'] ?? $subject;
        $finalBody = $rendered['body'] ?? $body;

        try {
            Mail::raw($finalBody, function ($message) use ($user, $finalSubject) {
                $message->to($user->email)
                    ->subject($finalSubject);
            });

            return true;
        } catch (\Exception) {
            return false;
        }
    }

    public function sendToUser(User $user, string $type, string $title, string $message, array $params = []): bool
    {
        $companyId = $user->company_id ?? 1;

        return $this->send($user, $type, $title, $message, $params);
    }

    private function renderContent(string $type, User $user, array $params): array
    {
        $defaultParams = array_merge([
            'user_name' => $user->name,
            'user_email' => $user->email,
        ], $params);

        $rendered = $this->templateService->render($type, $user->company_id ?? 1, $defaultParams);

        return $rendered ?? ['subject' => null, 'body' => null];
    }
}
