<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Throwable;

class UserManagementController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorizeManager($request);

        $actor = $request->user();
        $visibleRoleOptions = $this->visibleRoleOptionsForActor($actor);
        $requestedRole = (string) $request->query('role');
        $roleFilter = array_key_exists($requestedRole, $visibleRoleOptions) ? $requestedRole : null;
        $requestedStatus = (string) $request->query('status');
        $statusFilter = in_array($requestedStatus, ['active', 'inactive'], true) ? $requestedStatus : null;

        $usersQuery = User::query()
            ->when(
                $actor?->role === User::ROLE_ADMIN,
                fn ($q) => $q->whereNotIn('role', [User::ROLE_SUPERADMIN, User::ROLE_ADMIN])
            )
            ->when($statusFilter === 'active', fn ($q) => $q->where('is_active', true))
            ->when($statusFilter === 'inactive', fn ($q) => $q->where('is_active', false))
            ->when($roleFilter, fn ($q) => $q->where('role', $roleFilter))
            ->orderBy('id');

        return view('users.index', [
            'users' => $usersQuery->paginate(8)->withQueryString(),
            'roleOptions' => User::roleOptions(),
            'visibleRoleOptions' => $visibleRoleOptions,
            'isSuperAdmin' => $actor?->role === User::ROLE_SUPERADMIN,
            'activeRole' => $roleFilter,
            'activeStatus' => $statusFilter,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeManager($request);

        $allowedRoles = $this->allowedRolesForActor($request->user());

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'role' => ['required', Rule::in($allowedRoles)],
            'password' => ['required', 'string', 'min:8'],
            'is_active' => ['nullable', 'boolean'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $avatarPath = null;
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
        }

        $managedUser = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
            'password' => Hash::make($data['password']),
            'is_active' => (bool) ($data['is_active'] ?? false),
            'avatar' => $avatarPath,
        ]);

        $welcomeEmailSent = $this->sendWelcomeCredentialsEmail(
            $managedUser,
            $data['password'],
            $request->user()
        );

        $response = back()->with(
            'success',
            $welcomeEmailSent
                ? 'User created successfully and welcome email sent.'
                : 'User created successfully.'
        );

        if (! $welcomeEmailSent) {
            return $response->withErrors([
                'mail' => 'Welcome email could not be sent. Check mail configuration to deliver the login details.',
            ]);
        }

        return $response;
    }

    public function update(Request $request, User $managedUser): RedirectResponse
    {
        $this->authorizeManager($request);

        if (! $this->canManageUser($request->user(), $managedUser)) {
            return back()->withErrors(['user' => 'You do not have permission to update this user.']);
        }

        $allowedRoles = $this->allowedRolesForActor($request->user());

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($managedUser->id)],
            'role' => ['required', Rule::in($allowedRoles)],
            'password' => ['nullable', 'string', 'min:8'],
            'is_active' => ['nullable', 'boolean'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $updateData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
            'is_active' => (bool) ($data['is_active'] ?? false),
        ];

        if (! empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            if ($managedUser->avatar && ! str_starts_with($managedUser->avatar, 'http')) {
                Storage::disk('public')->delete($managedUser->avatar);
            }
            $updateData['avatar'] = $path;
        }

        $managedUser->update($updateData);

        return back()->with('success', 'User updated successfully.');
    }

    public function destroy(Request $request, User $managedUser): RedirectResponse
    {
        $this->authorizeManager($request);

        if ($request->user()->id === $managedUser->id) {
            return back()->withErrors(['user' => 'You cannot delete your own account.']);
        }

        if (! $this->canManageUser($request->user(), $managedUser)) {
            return back()->withErrors(['user' => 'You do not have permission to delete this user.']);
        }

        $managedUser->delete();

        return back()->with('success', 'User deleted successfully.');
    }

    public function resendWelcomeEmail(Request $request, User $managedUser): RedirectResponse
    {
        $this->authorizeManager($request);

        if (! $this->canManageUser($request->user(), $managedUser)) {
            return back()->withErrors(['user' => 'You do not have permission to resend this user email.']);
        }

        $mailSent = $this->sendWelcomeCredentialsEmail(
            $managedUser,
            null,
            $request->user(),
            true
        );

        $response = back()->with(
            'success',
            $mailSent
                ? 'Access email resent successfully.'
                : 'Access email could not be resent.'
        );

        if (! $mailSent) {
            return $response->withErrors([
                'mail' => 'Access email could not be resent. Check mail configuration and try again.',
            ]);
        }

        return $response;
    }

    private function authorizeManager(Request $request): void
    {
        abort_unless(
            in_array($request->user()?->role, [User::ROLE_SUPERADMIN, User::ROLE_ADMIN], true),
            403
        );
    }

    /**
     * @return list<string>
     */
    private function allowedRolesForActor(User $actor): array
    {
        if ($actor->role === User::ROLE_SUPERADMIN) {
            return User::ROLES;
        }

        return [
            User::ROLE_MANAGER_HR,
            User::ROLE_IT,
            User::ROLE_TRAINER,
            User::ROLE_STUDENT,
            User::ROLE_DEMO,
        ];
    }

    private function canManageUser(User $actor, User $target): bool
    {
        if ($actor->role === User::ROLE_SUPERADMIN) {
            return true;
        }

        return ! in_array($target->role, [User::ROLE_SUPERADMIN, User::ROLE_ADMIN], true);
    }

    /**
     * @return array<string, string>
     */
    private function visibleRoleOptionsForActor(?User $actor): array
    {
        $options = User::roleOptions();

        if ($actor?->role === User::ROLE_SUPERADMIN) {
            return $options;
        }

        return collect($options)->except([User::ROLE_SUPERADMIN, User::ROLE_ADMIN])->all();
    }

    private function sendWelcomeCredentialsEmail(
        User $managedUser,
        ?string $plainPassword,
        ?User $actor,
        bool $isResend = false
    ): bool
    {
        $roleLabel = User::roleOptions()[$managedUser->role] ?? ucfirst((string) $managedUser->role);
        $appName = $this->resolveMailBrandName();
        $loginUrl = route('login');
        $subject = $isResend ? 'Your account access details' : 'Welcome to '.$appName;
        $html = $this->renderWelcomeCredentialsEmailHtml(
            appName: $appName,
            userName: $managedUser->name,
            roleLabel: $roleLabel,
            email: $managedUser->email,
            plainPassword: $plainPassword,
            loginUrl: $loginUrl,
            createdBy: $actor?->name,
            isActive: $managedUser->is_active,
            isResend: $isResend
        );

        try {
            Mail::html($html, function ($mail) use ($managedUser, $subject): void {
                $mail->to($managedUser->email, $managedUser->name)
                    ->subject($subject);
            });

            return true;
        } catch (Throwable $exception) {
            report($exception);

            return false;
        }
    }

    private function renderWelcomeCredentialsEmailHtml(
        string $appName,
        string $userName,
        string $roleLabel,
        string $email,
        ?string $plainPassword,
        string $loginUrl,
        ?string $createdBy,
        bool $isActive,
        bool $isResend
    ): string {
        $details = [
            ['label' => 'Name', 'value' => $userName],
            ['label' => 'Role', 'value' => $roleLabel],
            ['label' => 'Email', 'value' => $email],
        ];

        if (! empty($plainPassword)) {
            $details[] = ['label' => 'Temporary Password', 'value' => $plainPassword];
        }

        $warningParts = [];
        if (! $isActive) {
            $warningParts[] = 'Your account is currently inactive. Please contact admin if you cannot log in yet.';
        }
        if ($isResend && empty($plainPassword)) {
            $warningParts[] = 'Use your existing password to sign in. If you cannot remember it, ask admin to update your password first.';
        }

        return $this->renderMailShell(
            eyebrow: $isResend ? 'Account Reminder' : 'Account Ready',
            title: $isResend ? 'Your account access details' : 'Welcome to '.$appName,
            subtitle: $isResend
                ? 'Here is a fresh copy of your account access information for the platform.'
                : 'Your new account is ready. Use the details below to log in and start using the platform.',
            greeting: 'Hello '.$userName.',',
            intro: $isResend
                ? 'Your account access summary for '.$appName.' has been resent below.'
                : 'Your account has been created successfully for '.$appName.'.',
            primaryBoxTitle: 'Login Details',
            primaryRows: $details,
            metaNote: $createdBy ? ($isResend ? 'Sent by: '.$createdBy : 'Created by: '.$createdBy) : null,
            warningText: $warningParts !== [] ? implode(' ', $warningParts) : null,
            actionLabel: 'Login Now',
            actionUrl: $loginUrl,
            secondaryActionLabel: null,
            secondaryActionUrl: null,
            closing: ! empty($plainPassword)
                ? 'For security, please change your password after your first login.'
                : 'Sign in with your current password and keep your account details secure.',
            footerText: 'This is an automated email from '.$appName.'.'
        );
    }

    private function renderMailShell(
        string $eyebrow,
        string $title,
        string $subtitle,
        string $greeting,
        string $intro,
        string $primaryBoxTitle,
        array $primaryRows,
        ?string $metaNote,
        ?string $warningText,
        ?string $actionLabel,
        ?string $actionUrl,
        ?string $secondaryActionLabel,
        ?string $secondaryActionUrl,
        string $closing,
        string $footerText
    ): string {
        $brandNameRaw = $this->resolveMailBrandName();
        $appName = e($brandNameRaw);
        $eyebrow = e($eyebrow);
        $title = e($title);
        $subtitle = e($subtitle);
        $greeting = e($greeting);
        $intro = e($intro);
        $primaryBoxTitle = e($primaryBoxTitle);
        $closing = e($closing);
        $footerText = e($footerText);
        $logoDataUri = $this->resolveMailLogoDataUri();

        $primaryHtml = collect($primaryRows)->map(function (array $row): string {
            return sprintf(
                '<div style="margin-bottom:10px; padding:11px 13px; border-radius:16px; background:linear-gradient(180deg, #ffffff, #f7faff); border:1px solid #dce5f2; font-size:13px; line-height:1.55; box-shadow:0 10px 18px rgba(15, 43, 79, 0.05);"><strong style="display:block; color:#6d7f98; font-size:10px; letter-spacing:0.12em; text-transform:uppercase; margin-bottom:4px;">%s</strong><span style="color:#13243d; font-weight:700;">%s</span></div>',
                e((string) ($row['label'] ?? '')),
                e((string) ($row['value'] ?? ''))
            );
        })->implode('');

        $metaHtml = $metaNote
            ? '<div style="margin:0 0 14px; padding:11px 13px; border-radius:16px; background:linear-gradient(135deg, rgba(13, 93, 209, 0.08), rgba(122, 92, 255, 0.07)); border:1px solid #d8e1f4; color:#445a78; font-size:13px; line-height:1.65;">'.e($metaNote).'</div>'
            : '';
        $warningHtml = $warningText
            ? '<div style="margin:0 0 16px; padding:12px 14px; border-radius:16px; background:linear-gradient(135deg, #fff8ea, #ffe7b9); border:1px solid #f0d39a; color:#8b5a12; font-size:13px; line-height:1.65; box-shadow:0 10px 18px rgba(240, 179, 90, 0.14);">'.e($warningText).'</div>'
            : '';
        $primaryActionHtml = ($actionLabel && $actionUrl)
            ? '<div style="margin:0 0 10px;"><a href="'.e($actionUrl).'" style="display:inline-block; padding:11px 20px; border-radius:999px; background:linear-gradient(135deg, #0d5dd1, #7a5cff); color:#ffffff; text-decoration:none; font-size:13px; font-weight:700; letter-spacing:0.01em; box-shadow:0 14px 22px rgba(27, 75, 177, 0.22);">'.e($actionLabel).'</a></div>'
            : '';
        $secondaryActionHtml = ($secondaryActionLabel && $secondaryActionUrl)
            ? '<div style="margin:0 0 18px;"><a href="'.e($secondaryActionUrl).'" style="display:inline-block; padding:10px 16px; border-radius:999px; background:linear-gradient(135deg, #fff7e8, #fff2d5); color:#9a5b00; text-decoration:none; font-size:13px; font-weight:700; border:1px solid #f0ddb9; box-shadow:0 10px 16px rgba(240, 179, 90, 0.12);">'.e($secondaryActionLabel).'</a></div>'
            : '';
        $logoHtml = $logoDataUri !== ''
            ? '<div style="display:inline-flex; align-items:center; justify-content:center; padding:11px 15px; border-radius:20px; background:radial-gradient(circle at left top, rgba(240, 179, 90, 0.28), rgba(240, 179, 90, 0) 38%), rgba(255,255,255,0.98); box-shadow:0 14px 26px rgba(8, 18, 34, 0.16); border:1px solid rgba(255,255,255,0.55);"><img src="'.$logoDataUri.'" alt="'.$appName.'" style="display:block; width:170px; max-width:100%; height:auto;"></div>'
            : '<div style="display:inline-block; padding:8px 12px; border-radius:999px; background:rgba(255,255,255,0.16); color:#ffffff; font-size:11px; letter-spacing:0.12em; text-transform:uppercase; font-weight:700;">'.$appName.'</div>';
        $brandStripHtml = '<div style="margin-top:14px;"><span style="display:inline-block; margin-right:6px; margin-bottom:6px; padding:6px 10px; border-radius:999px; background:rgba(255,255,255,0.15); color:#ffffff; font-size:10px; letter-spacing:0.08em; text-transform:uppercase; font-weight:700;">Learning Hub</span><span style="display:inline-block; margin-right:6px; margin-bottom:6px; padding:6px 10px; border-radius:999px; background:rgba(240,179,90,0.2); color:#ffe3a7; font-size:10px; letter-spacing:0.08em; text-transform:uppercase; font-weight:700;">Skill Growth</span><span style="display:inline-block; margin-bottom:6px; padding:6px 10px; border-radius:999px; background:rgba(122,92,255,0.18); color:#dfd6ff; font-size:10px; letter-spacing:0.08em; text-transform:uppercase; font-weight:700;">Academic Mantra</span></div>';

        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$title}</title>
</head>
<body style="margin:0; padding:0; background:#eaf2ff; font-family:'Segoe UI', Arial, Helvetica, sans-serif; color:#16324f;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:radial-gradient(circle at 12% 8%, rgba(79, 140, 255, 0.18) 0%, rgba(79, 140, 255, 0) 42%), radial-gradient(circle at 88% 12%, rgba(122, 92, 255, 0.14) 0%, rgba(122, 92, 255, 0) 45%), linear-gradient(160deg, #eaf2ff, #f2f4ff, #ffffff); margin:0; padding:18px 10px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:660px; background:#ffffff; border-radius:24px; overflow:hidden; border:1px solid rgba(255,255,255,0.42); box-shadow:0 24px 46px rgba(7, 18, 34, 0.14);">
                    <tr>
                        <td style="padding:22px 24px; background:linear-gradient(160deg, rgba(11, 32, 58, 0.96), rgba(54, 45, 120, 0.9), rgba(15, 45, 80, 0.92)); color:#ffffff;">
                            <div style="margin-bottom:14px;">{$logoHtml}</div>
                            <div style="font-size:11px; letter-spacing:1px; text-transform:uppercase; opacity:0.85; margin-bottom:8px;">{$eyebrow}</div>
                            <div style="font-size:24px; line-height:1.15; font-weight:700; max-width:15ch;">{$title}</div>
                            <div style="font-size:13px; line-height:1.6; opacity:0.92; margin-top:8px; max-width:52ch;">{$subtitle}</div>
                            {$brandStripHtml}
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:20px 24px 18px;">
                            <p style="margin:0 0 10px; font-size:14px; line-height:1.6; color:#1a2f4b;">{$greeting}</p>
                            <p style="margin:0 0 14px; font-size:13px; line-height:1.7; color:#52657f;">{$intro}</p>
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin:0 0 14px; border-collapse:separate; border-spacing:0;">
                                <tr>
                                    <td style="padding:16px; background:linear-gradient(180deg, #f7faff, #eef4ff); border:1px solid #d9e5f5; border-radius:18px;">
                                        <div style="font-size:11px; font-weight:700; text-transform:uppercase; color:#6b7c93; margin-bottom:10px; letter-spacing:0.08em;">{$primaryBoxTitle}</div>
                                        {$primaryHtml}
                                    </td>
                                </tr>
                            </table>
                            {$metaHtml}
                            {$warningHtml}
                            {$primaryActionHtml}
                            {$secondaryActionHtml}
                            <p style="margin:0; font-size:13px; line-height:1.7; color:#5d6d84;">{$closing}</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:14px 24px 18px; color:#617089; font-size:11px; line-height:1.65; border-top:1px solid #e4ebf5; background:linear-gradient(180deg, #fbfdff, #f6f9ff);">
                            <div style="font-weight:700; color:#1c3150; font-size:12px;">{$appName}</div>
                            <div style="margin-top:4px;">{$footerText}</div>
                            <div style="margin-top:4px; color:#8b9ab1;">Learning. Skills. Growth.</div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
HTML;
    }

    private function resolveMailBrandName(): string
    {
        $brandName = trim((string) config('app.name', ''));

        if ($brandName === '' || $brandName === 'Laravel') {
            return 'Academic Mantra Services';
        }

        return $brandName;
    }

    private function resolveMailLogoDataUri(): string
    {
        $logoPath = public_path('images/logo.webp');

        if (! is_file($logoPath)) {
            return '';
        }

        $logoContents = file_get_contents($logoPath);

        if ($logoContents === false) {
            return '';
        }

        return 'data:image/webp;base64,'.base64_encode($logoContents);
    }
}
