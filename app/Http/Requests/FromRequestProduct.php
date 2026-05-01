<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FromRequestProduct extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
   public function rules(): array
{
    // On récupère l'ID du produit s'il existe (cas de l'update)
    $product = $this->route('product');
    $productId  = is_object($product) ?$product->id :$product;

    return [
        'name' => 'required|string|max:100|unique:products,name,'.$productId,

        'product_category_id' => 'required|integer|exists:product_categories,id',
        'description' => 'required|string|max:500',

        'photo' => $this->isMethod('POST') ? 'required|image|mimes:png,jpg,jpeg' : 'nullable|image|mimes:png,jpg,jpeg',

        'status' => 'sometimes|in:on,off',
        'price' => 'required | numeric ',
        'files' => 'sometimes|array',
        'files.*' => 'sometimes|image|mimes:png,jpg,jpeg',
    ];
}
}
