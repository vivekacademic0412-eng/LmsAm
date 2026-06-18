<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\State;
use App\Models\City;
use App\Models\Payment;

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
            'name'      => $this->name,
            'email'     => $this->email,
            'phone'     => $this->phone,
            'state_id'  => $this->state_id,
            'city_id'   => $this->city_id,
            'amount'    => 999,
            'status'    => 'success',
            'gateway'   => 'Direct',
        ]);

        $this->showSuccess = true;

        $this->dispatch('payment-success');
    }
    public function render()
    {
        return view('livewire.payment-form');
    }
}
