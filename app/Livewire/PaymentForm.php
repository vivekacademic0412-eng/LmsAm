<?php

namespace App\Livewire;

use App\Mail\DemoLoginCredentialsMail;
use Livewire\Component;
use App\Models\State;
use App\Models\City;
use App\Models\DemoAccessToken;
use App\Models\DemoTypeSelection;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
class PaymentForm extends Component
{
    public $name;
    public $email;
    public $phone;

    public $state_id;
    public $city_id;

    public $states = [];
    public $cities = [];
    public $amount = 999;
    public function mount()
    {
   
        if (auth()->check()) {
        $this->name = auth()->user()->name .' '. auth()->user()->last_name;
        $this->email = auth()->user()->email;
         $this->phone = auth()->user()->contact;
    }
        $this->states = State::orderBy('name')->get();
    }


    public function updatedStateId($value)
    {
        $this->cities = City::where('state_id', $value)
            ->orderBy('name')
            ->get();

        $this->city_id = '';
    }

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email',
        'phone' => 'required|min:10|max:15',
        'state_id' => 'required',
        'city_id' => 'required',
    ];
    public $showSuccess = false;
    public $redirectSeconds = 1;

    public function save()
    {
        $this->validate();

        Payment::create([
            'user_id'  =>auth()->user()->id,
            'name'      => $this->name,
            'email'     => $this->email,
            'phone'     => $this->phone,
            'state_id'  => $this->state_id,
            'city_id'   => $this->city_id,
            'amount'    => 999,
            'status'    => 'success',
            'gateway'   => 'Direct',
        ]);
         DemoTypeSelection::where('demo_user_id',auth()->user()->id)->update([
            'is_confirm' =>2,
            'status'=>'completed',
        ]);
        $user = User::findOrFail(auth()->user()->id);
        $token = Str::uuid();
        DemoAccessToken::create([
            'user_id'    => $user->id,
            'token'      => $token,
            'expires_at' => now()->addDays(3),
        ]);
        $url = route('demo.secure.login', $token);

        Mail::to($user->email)->send(
            new DemoLoginCredentialsMail(
                $user,
                $url
            )
        );
        $this->showSuccess = true;

        $this->dispatch('payment-success');
    }
    public function render()
    {
        return view('livewire.payment-form');
    }
}
