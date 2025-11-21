<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class UpdateGuideRequest extends FormRequest {
    public function authorize(): bool { return true; }
    public function rules(): array {
        return [
      'title'           => 'sometimes|required|string|max:255',
            'category_id'     => 'nullable|integer|exists:guide_categories,category_id',
            'excerpt'         => 'nullable|string|max:65535',
            'content_html'    => 'sometimes|required|string',
            'thumbnail'       => 'nullable|image|max:2048',
            'status'          => 'required|in:0,1',              // <-- QUAN TRỌNG
            'tags'            => 'nullable|array',
            'tags.*'          => 'integer|exists:guide_tags,tag_id',
            'published_at'    => 'nullable|date',
            'seo_title'       => 'nullable|string|max:255',
            'seo_description' => 'nullable|string|max:255',
        ];
    }
}
