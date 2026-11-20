<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Modulo extends Model
{
    use HasFactory;

    protected $table = 'modules';

    protected $fillable = [
        'codigo',
        'nombre',
    ];


    public function estudiantes()
    {
        return $this->hasMany('App\Models\Student');
    }

    
  public function teachers(){
    return $this->belongsToMany('App\Models\Teacher','modulo_teachers');

    }
}

