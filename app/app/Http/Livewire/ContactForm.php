<?php

namespace App\Http\Livewire;

use Bo\Contact\Models\Contact;
use Livewire\Component;

class ContactForm extends Component
{
    public $name;
    public $email;
    public $content;

    protected $rules = [
        'name'    => 'required|min:6|max:255',
        'email'   => 'required|email|min:6|max:255',
        'content' => 'required|min:6|max:500',
    ];

    public function render()
    {
        return view('livewire.contact-form');
    }

    public function submit()
    {
        $this->validate();

        Contact::create([
            'name' => $this->name,
            'email' => $this->email,
            'content' => $this->content,
        ]);

        $this->reset();

        session()->flash('success_create_contact', 'Gửi liên hệ thành công, chúng tôi sẽ liên hệ với bạn sớm nhất.');
    }
}
