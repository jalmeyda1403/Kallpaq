<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentoDependencia extends Model
{
   public $incrementing = true;

    /**
     * El nombre de la tabla asociada con el modelo.
     * @var string
     */
    protected $table = 'documento_dependencias';

    /**
     * Los atributos que se pueden asignar masivamente.
     * @var array<int, string>
     */
    protected $fillable = [
        'padre_documento_id',
        'padre_version_id',
        'hijo_id',
    ];

    /**
     * No se necesitan timestamps (created_at, updated_at) para este modelo pivote
     * a menos que quieras registrar cuándo se creó la dependencia.
     * Si tu tabla los tiene, déjalo en true. Si no, ponlo en false.
     * @var bool
     */
    public $timestamps = true;
}
