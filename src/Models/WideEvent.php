<?php

namespace Zamion101\WideEvents\Models;

use Illuminate\Database\Eloquent\Model;

class WideEvent extends Model
{
    protected $casts = [
        'payload' => 'array',
    ];

    protected $fillable = [
        'occurred_at',
        'payload',
    ];

    public function __construct(array $attributes = [])
    {
        if (! isset($this->connection)) {
            $this->setConnection(config('wide-events.database_connection'));
        }

        if (! isset($this->table)) {
            $this->setTable(config('wide-events.table_name'));
        }

        parent::__construct($attributes);
    }
}
