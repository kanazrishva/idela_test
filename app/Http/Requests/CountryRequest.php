<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CountryRequest extends FormRequest
{

  public function prepareForValidation() 
  {
    $this->errorBag = "country.{$this->route('country')->id}";
  }

  /**
   * Determine if the user is authorized to make this request.
   *
   * @return bool
   */
  public function authorize()
  {
    return Auth::check();
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array
   */
  public function rules()
  {
      return [
        'country' => ['required', 'unique:countries,country']
      ];
  }
}
