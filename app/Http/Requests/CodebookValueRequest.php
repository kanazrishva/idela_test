<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CodebookValueRequest extends FormRequest
{

  public function prepareForValidation() 
  {
    $this->errorBag = "codebookValue.{$this->route('codebookvalue')->id}";
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
          'label' => ['required'],
          'value' => ['required']
        ];
    }
}
