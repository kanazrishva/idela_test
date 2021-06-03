<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CodebookColumnRequest extends FormRequest
{

  public function prepareForValidation() 
  {
    $this->errorBag = "codebookColumn.{$this->route('codebookcolumn')->id}";
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
          'column_name' => ['required', 'unique:codebookcolumns,column_name'],
        ];
    }
}
