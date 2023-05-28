<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use PDF;

class InformeGerencialMail extends Mailable
{
    use Queueable, SerializesModels;
    public $mailData;
    protected $datosEmprendimientos;
    protected $resumenEmpredimientos;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($mailData,$datosEmprendimientos, $resumenEmpredimientos)
    {
        $this->mailData = $mailData;
        $this->datosEmprendimientos = $datosEmprendimientos;
        $this->resumenEmpredimientos = $resumenEmpredimientos;
    }

    public function build()
    {
        $pdf = PDF::loadView('emails.informeGerencial',
            array(
                'datosEmprendimientos' => $this->datosEmprendimientos,
                'resumenEmpredimientos' => $this->resumenEmpredimientos
            )
        );

        return $this->subject('Informe Gerencial')
            ->view('emails.informeGerencialMail') // Este es un ejemplo. Aquí debes poner la vista de tu cuerpo de correo.
            ->attachData($pdf->output(), "InformeGerencial.pdf", [
                'mime' => 'application/pdf',
            ]);
    }
}
