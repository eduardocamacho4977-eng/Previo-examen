<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $table = 'students';

    protected $fillable = [
        'nombre',
        'apellidos',
        'fecha_nacimiento',
        'expediente',
        'module_id',
        'imagen_url',
      'imagen_public_id',
    ];

     protected $guarded =[
      'urlFoto'
    ];



    public function modulo()
    {
        return $this->belongsTo('App\Models\Modulo');
    }
    
}
