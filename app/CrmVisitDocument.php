<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CrmVisitDocument extends Model
{
    protected $guarded = ['id'];
    public function visit() { return $this->belongsTo(CrmVisit::class, 'visit_id'); }
}
