<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CrmPlan extends Model
{
    protected $guarded = ['id'];

    public function assignedUser() { return $this->belongsTo(User::class, 'assigned_user_id'); }
    public function group() { return $this->belongsTo(CustomerGroup::class, 'group_id'); }
    public function business() { return $this->belongsTo(Contact::class, 'contact_id'); }
}
