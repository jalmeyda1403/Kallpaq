<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoDocumento extends Model
{  
    protected $table = 'tipo_documentos';
    
        protected $fillable = [
            'sigla',
            'nombre',
            'estado',
            'inactive_at',
        ];
    
        // Accesor para obtener el estado legible
        public function getEstadoAttribute($value)
        {
            return $value ? 'Activo' : 'Inactivo';
        }
    
        // Mutador para establecer el estado legible
        public function setEstadoAttribute($value)
        {
            $this->attributes['estado'] = $value == 'Activo';
        }
    
        // Mutador para establecer el campo inactive_at a null si el estado se cambia a activo
        public function setInactiveAtAttribute($value)
        {
            $this->attributes['inactive_at'] = $this->estado ? null : $value;
        }
    
       
       
}
