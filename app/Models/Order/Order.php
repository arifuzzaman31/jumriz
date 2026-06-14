<?php

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table      = 'orders';
    protected $primaryKey = 'id';

    // relation with user or customer

    public function user()
    {
 
       return $this->belongsTo('App\User')->withDefault([
            'id'    => 0,
            'email' => 'No email',
            'phone' => 'No Number',
            'name'  => 'Unknown customer',
        ]);       
    }

    // relation with order details

    public function order_details()
    {
        return $this->hasMany('App\Models\Order\OrderDetails');
    }

    // relation with trail table

    public function trial_product()
    {

      return $this->hasMany('App\Models\Order\TrialProduct');

    }

    // relation with area

    public function shipping_area()
    {

        return $this->belongsTo('App\Models\Setting\ShippingArea')->withDefault(['id' => 0, 'city' => 'Unknown']);

    }

    public function provider()
    {
        return $this->belongsTo('App\Models\Setting\PaymentSetting', 'payment_method')
            ->withDefault(['id' => 0, 'provider' => 'N/A']);
    }
}
