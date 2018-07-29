<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class validateRequest extends Request
{
		

		public function authorize()
		{

				return true;
		}
		public function rules()
		{

			return [
			'no_kartu'   =>  'required'
			];
		}

		    //
}
