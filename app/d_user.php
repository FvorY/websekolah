<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use DB;
use Auth;

class d_user extends Model implements AuthenticatableContract, CanResetPasswordContract
{
  use Authenticatable,
      CanResetPassword;

  protected $table = 'd_user';
  protected $primaryKey = 'u_id';
  public $incrementing = false;
  public $remember_token = false;
  //public $timestamps = false;

  const UPDATED_AT = 'u_update';
  const CREATED_AT = 'u_insert';

  protected $fillable = ['u_id', 'u_username', 'u_password'];
}
