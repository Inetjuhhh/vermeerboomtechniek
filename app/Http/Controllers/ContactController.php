<?php

namespace App\Http\Controllers;

use App\Mail\NewContactMessage;
use App\Models\ContactMessage;
use App\Support\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function show()
    {
        return view('contact', ['summary' => session('calculator_summary')]);
    }

    public function store(Request $request)
    {
        // Spam trap filled in: pretend all went well, but save nothing
        if ($request->filled('website')) {
            return redirect()->route('contact')->with('sent', true);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150'],
            'phone' => ['nullable', 'string', 'max:30'],
            'subject' => ['nullable', 'string', 'max:150'],
            'message' => ['required', 'string', 'max:5000'],
        ], [
            'name.required' => 'Vul uw naam in.',
            'email.required' => 'Vul uw e-mailadres in, zodat we kunnen antwoorden.',
            'email.email' => 'Dit e-mailadres klopt niet helemaal. Kijkt u het nog even na?',
            'message.required' => 'Schrijf kort waar we u mee kunnen helpen.',
            '*.max' => 'Dit veld is te lang.',
        ]);

        $message = ContactMessage::create($data + [
            'calculator_summary' => session('calculator_summary'),
        ]);

        Mail::to(Site::get('email'))->send(new NewContactMessage($message));

        session()->forget('calculator_summary');

        return redirect()->route('contact')->with('sent', true);
    }
}
