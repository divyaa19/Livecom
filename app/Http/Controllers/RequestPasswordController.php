<?php
namespace App\Http\Controllers;
use App\Traits\SendsPasswordResetEmails;
use App\Models\Oc_customer;
class RequestPasswordController extends Controller
{
  use SendsPasswordResetEmails;
  public function __construct()
  {
      $this->broker = 'oc_customer';
  }
}