<?php

namespace App\Mail;

use App\Models\ContactMessage;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/** Mail to info@ for each message from the contact form. Replying goes straight to the sender. */
class NewContactMessage extends Mailable
{
    public function __construct(public ContactMessage $contactMessage) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            replyTo: [new Address($this->contactMessage->email, $this->contactMessage->name)],
            subject: 'Bericht via de website: ' . ($this->contactMessage->subject ?: $this->contactMessage->name),
        );
    }

    public function content(): Content
    {
        return new Content(text: 'mail.contact-message');
    }
}
