<?php

namespace App\Core\Data\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CommentCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'post_id' => [
                'required',
                'integer',
                'exists:posts,id',
            ],
            'content' => [
                'required',
                'string',
                'min:3',
                'max:1000',
            ],
            'parent_id' => [
                'nullable',
                'integer',
                'exists:comments,id',
                'different:id',
            ],
        ];
    }

    /**
     * Get custom error messages.
     */
    public function messages(): array
    {
        return [
            'post_id.required' => 'Post ID is required.',
            'post_id.exists' => 'The post you are trying to comment on was not found.',
            'content.required' => 'Comment content is required.',
            'content.min' => 'Comment must be at least 3 characters.',
            'content.max' => 'Comment may not be greater than 1000 characters.',
            'content.regex' => 'Comment must contain valid content.',
            'parent_id.exists' => 'The comment you are trying to reply to was not found.',
            'parent_id.different' => 'You cannot reply to yourself.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'post_id' => 'post',
            'content' => 'comment content',
            'parent_id' => 'parent comment',
        ];
    }
}
