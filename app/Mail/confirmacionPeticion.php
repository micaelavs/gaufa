<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class confirmacionPeticion extends Mailable
{
    use Queueable, SerializesModels;

    
    public $texto;
    //public $flag;

    public function __construct($texto)
    {
        $this->texto = $texto;
    }

    public function build()
    {   
        return $this->view('emails.confirmacionPeticion',['texto' => $this->texto]);
    }
}