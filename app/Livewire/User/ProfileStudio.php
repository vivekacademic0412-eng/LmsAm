<?php

namespace App\Livewire\User;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;

class ProfileStudio extends Component
{
    use WithFileUploads;

    public $user;

    public $name;
    public $email;
    public $avatarUpload;

    public $selectedAvatar;

    public array $avatars = [];

    public function mount()
    {
        $this->user = Auth::user();

        $this->name = $this->user->name;
        $this->email = $this->user->email;

        $this->selectedAvatar = $this->user->avatar_url;

        $this->avatars = [
            'https://api.dicebear.com/9.x/adventurer/svg?seed=1',
            'https://api.dicebear.com/9.x/adventurer/svg?seed=2',
            'https://api.dicebear.com/9.x/adventurer/svg?seed=3',
            'https://api.dicebear.com/9.x/adventurer/svg?seed=4',
            'https://api.dicebear.com/9.x/adventurer/svg?seed=5',
            'https://api.dicebear.com/9.x/adventurer/svg?seed=6',
            'https://api.dicebear.com/9.x/fun-emoji/svg?seed=1',
            'https://api.dicebear.com/9.x/fun-emoji/svg?seed=2',
        ];
    }

    public function selectAvatar($avatar)
    {
        $this->selectedAvatar = $avatar;
        // dd($this->selectedAvatar);
    }

   public function save()
{
    $this->validate([
        'name' => 'required|min:3',
        'email' => 'required|email',
        'avatarUpload' => 'nullable|image|max:2048',
    ]);

    $avatar = $this->selectedAvatar;

    if ($this->avatarUpload) {
        $path = $this->avatarUpload->store('avatars', 'public');
        $avatar = $path;
    }

    $this->user->update([
        'name' => $this->name,
        'email' => $this->email,
        'avatar' => $avatar,
    ]);

    // 🔥 SweetAlert trigger event
    $this->dispatch('profile-updated');
}

    public function render()
    {
        return view(
            'livewire.user.profile-studio'
        );
    }
}