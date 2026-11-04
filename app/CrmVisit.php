<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CrmVisit extends Model
{
    protected $guarded = ['id'];
    protected $with = ['documents'];

    public function user() { return $this->belongsTo(User::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function group() { return $this->belongsTo(CustomerGroup::class, 'group_id'); }
    public function business() { return $this->belongsTo(Contact::class, 'contact_id'); }
    public function documents() { return $this->hasMany(CrmVisitDocument::class, 'visit_id'); }
}
