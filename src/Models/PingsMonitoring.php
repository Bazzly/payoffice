<?php

namespace Bazzly\Payoffice\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PingsMonitoring extends Model
{
    use HasFactory;

    /**
     * Set table name.
     *
     * @var string
     */
    protected $table = 'pings_monitoring';

    /**
     * Guarded columns.
     *
     * @var array
     */
    protected $guarded = ['id'];

    protected $fillable = [
        'name',
        'apiurl',
        'serverStatus',
        'serverPing',
        'userPing',
        'is_default'
    ];


}