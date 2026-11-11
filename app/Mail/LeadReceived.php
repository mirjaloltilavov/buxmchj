<?php

namespace App\Mail;

use App\Models\Lead;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class LeadReceived extends Mailable
{
    use Queueable;

    public function __construct(public Lead $lead, public bool $isTest = false)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: ($this->isTest ? '[Sinov] ' : '').'Yangi ariza: '.$this->lead->name.' · '.$this->lead->phone);
    }

    public function content(): Content
    {
        return new Content(view: 'mail.lead');
    }
}
