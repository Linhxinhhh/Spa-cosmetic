<?php


namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGuideRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    protected function prepareForValidation(): void
    {
        // Nếu form gửi content thay vì content_html thì tự map
        if ($this->filled('content') && !$this->filled('content_html')) {
            $this->merge(['content_html' => $this->input('content')]);
        }
        // Ép status về 0/1
        if ($this->has('status')) {
            $this->merge(['status' => (int)$this->input('status')]);
        }
    }

    public function rules(): array
    {
        return [
            'title'        => ['required','string','max:255'],
            'category_id'  => ['required','integer','exists:guide_categories,category_id'],
            'excerpt'      => ['nullable','string','max:65535'],
            'content_html' => ['required','string'], // <-- KHỚP FORM
            'thumbnail'    => ['nullable','image','max:2048'],
            'status'       => ['required','in:0,1'],
            'published_at' => ['nullable','date'],
            'tags'         => ['nullable','array'],
            'tags.*'       => ['integer','exists:guide_tags,tag_id'],
            'seo_title'        => ['nullable','string','max:255'],
            'seo_description'  => ['nullable','string','max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'tags.array'     => 'Định dạng thẻ không hợp lệ.',
            'tags.*.integer' => 'Mỗi thẻ phải là ID hợp lệ.',
            'tags.*.exists'  => 'Một hoặc nhiều thẻ không tồn tại.',
        ];
    }
}