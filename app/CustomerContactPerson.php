<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomerContactPerson extends Model
{
    protected $table = 'customer_contact_persons';
    protected $guarded = ['id'];

    public function contact()
    {
        return $this->belongsTo(Contact::class, 'contact_id');
    }
}
