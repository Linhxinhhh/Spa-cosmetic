

<div class="form-container">
  <div class="row g-3">
    <div class="col-md-8">
      {{-- Thông tin bài viết --}}
      <div class="form-card">
        <h3 class="section-title">
          <i class="fas fa-info-circle mr-2"></i>Thông tin bài viết
        </h3>

        {{-- Tiêu đề --}}
        <div class="form-group">
          <label class="form-label">
            <i class="fas fa-heading mr-1"></i>Tiêu đề <span class="required-mark">*</span>
          </label>
          <div class="input-icon">
            <i class="fas fa-file-signature"></i>
            <input name="title"
                   value="{{ old('title', $guide->title ?? '') }}"
                   class="form-control form-control-modern @error('title') is-invalid @enderror"
                   placeholder="Nhập tiêu đề..." required>
          </div>
          @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Tóm tắt --}}
        <div class="form-group">
          <label class="form-label"><i class="fas fa-align-left mr-1"></i>Tóm tắt</label>
          <div class="input-icon">
            <i class="fas fa-paragraph" style="top: 20px;"></i>
            <textarea name="excerpt"
                      class="form-control form-control-modern @error('excerpt') is-invalid @enderror"
                      rows="3"
                      placeholder="Viết tóm tắt ngắn gọn...">{{ old('excerpt', $guide->excerpt ?? '') }}</textarea>
          </div>
          @error('excerpt') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Nội dung HTML --}}
        <div class="form-group">
          <label class="form-label"><i class="fas fa-code mr-1"></i>Nội dung (HTML) <span class="required-mark">*</span></label>
          <div class="input-icon">
            <i class="fas fa-file-code" style="top: 20px;"></i>
            <textarea name="content_html"
                      class="form-control form-control-modern @error('content_html') is-invalid @enderror"
                      rows="12" placeholder="<h1>...</h1>" required>{{ old('content_html', $guide->content_html ?? '') }}</textarea>
          </div>
          @error('content_html') <div class="invalid-feedback">{{ $message }}</div> @enderror
          <div class="text-muted mt-2 small"><i class="fas fa-lightbulb mr-1"></i>Có thể dán HTML đã định dạng sẵn.</div>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      {{-- Thiết lập & Chuyên mục --}}
      <div class="form-card">
        <h3 class="section-title"><i class="fas fa-sliders-h mr-2"></i>Thiết lập</h3>

        {{-- Chuyên mục --}}
        <div class="form-group">
          <label class="form-label"><i class="fas fa-layer-group mr-1"></i>Chuyên mục</label>
          <div class="input-icon">
            <i class="fas fa-list"></i>
            <select name="category_id"
                    class="form-control form-control-modern @error('category_id') is-invalid @enderror">
              <option value="">-- Không --</option>
              @foreach($categories as $c)
                <option value="{{ $c->category_id }}"
                  {{ (string)old('category_id', $guide->category_id ?? '') === (string)$c->category_id ? 'selected' : '' }}>
                  {{ $c->name }}
                </option>
              @endforeach
            </select>
          </div>
          @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Thẻ --}}
        @php
          $selectedTags = collect(old('tags', isset($guide) ? $guide->tags->modelKeys() : []))
                          ->map(fn($v) => (int)$v);
        @endphp
        <div class="form-group">
          <label class="form-label"><i class="fas fa-hashtag mr-1"></i>Thẻ (giữ Ctrl để chọn nhiều)</label>
          <div class="input-icon">
            <select name="tags[]" id="guide-tags"
              class="form-control form-control-modern @error('tags') is-invalid @enderror @error('tags.*') is-invalid @enderror"
              multiple size="6" aria-describedby="tagsHelp">
              @foreach($tags as $t)
                <option value="{{ $t->tag_id }}" {{ $selectedTags->contains((int)$t->tag_id) ? 'selected' : '' }}>
                  {{ $t->name }}
                </option>
              @endforeach
            </select>
          </div>
          @error('tags') <div class="invalid-feedback">{{ $message }}</div> @enderror
          @error('tags.*') <div class="invalid-feedback">{{ $message }}</div> @enderror

          <small id="tagsHelp" class="form-text text-muted">Giữ Ctrl (Windows) hoặc ⌘ (macOS) để chọn nhiều thẻ.</small>

          <div class="mt-2 small">
            <span class="text-muted mr-2">Gợi ý nhanh:</span>
            @php $suggestions = [1 => '#Review', 2 => '#TrịMụn', 3 => '#Skincare']; @endphp
            @foreach($suggestions as $id => $label)
              <button type="button"
                      class="btn btn-sm tag-suggestion {{ $selectedTags->contains($id) ? 'btn-info' : 'btn-outline-info' }}"
                      data-tag-id="{{ $id }}">{{ $label }}</button>
            @endforeach
          </div>
        </div>

        {{-- Trạng thái --}}
        <div class="form-group">
          <label class="form-label"><i class="fas fa-toggle-on mr-1"></i>Trạng thái <span class="required-mark">*</span></label>
          @php $st = (string)old('status', isset($guide) ? (string)$guide->status : '1'); @endphp
          <div class="input-icon">
            <i class="fas fa-circle"></i>
            <select name="status" class="form-control form-control-modern @error('status') is-invalid @enderror" required>
              <option value="0" {{ $st === '0' ? 'selected' : '' }}>Nháp</option>
              <option value="1" {{ $st === '1' ? 'selected' : '' }}>Xuất bản</option>
            </select>
          </div>
          @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
      </div>

      {{-- Ảnh bìa --}}
      <div class="form-card">
        <h3 class="section-title"><i class="fas fa-image mr-2"></i>Ảnh bìa</h3>
        <div class="form-group">
          <div class="file-upload-area" id="coverArea" onclick="document.getElementById('thumbnail').click()">
            <div id="coverContent">
              <i class="fas fa-cloud-upload-alt fa-2x mb-2" style="color:#3b82f6;"></i>
              <p class="mb-1" style="color:#1e40af;font-weight:600;">Nhấp để chọn ảnh bìa</p>
              <small class="text-muted">Hỗ trợ: JPG, PNG, WEBP (Tối đa 2MB)</small>
            </div>
            <div id="coverPreview" style="display:none;">
              <img id="coverImg" style="max-width: 180px; max-height: 180px; border-radius: 8px;">
              <p class="mt-2 mb-0 text-success font-weight-bold"><i class="fas fa-check-circle mr-1"></i>Đã chọn ảnh</p>
            </div>

            @if(!empty($guide?->thumbnail))
              <div id="coverCurrent" class="mt-2">
                <img src="{{ asset('storage/'.$guide->thumbnail) }}" class="img-fluid rounded" style="max-height:180px">
                <div class="small text-muted mt-1">Ảnh hiện tại</div>
              </div>
            @endif
          </div>

          <input type="file" name="thumbnail" id="thumbnail"
                 class="form-control @error('thumbnail') is-invalid @enderror"
                 accept="image/*" style="display:none">
          @error('thumbnail') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
      </div>

      {{-- SEO --}}
      <div class="form-card">
        <h3 class="section-title"><i class="fas fa-search mr-2"></i>SEO</h3>
        <div class="form-group">
          <label class="form-label"><i class="fas fa-heading mr-1"></i>SEO title</label>
          <div class="input-icon">
            <i class="fas fa-bullhorn"></i>
            <input name="seo_title"
                   value="{{ old('seo_title', $guide->seo_title ?? '') }}"
                   class="form-control form-control-modern @error('seo_title') is-invalid @enderror"
                   placeholder="Tiêu đề SEO…">
          </div>
          @error('seo_title') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
          <label class="form-label"><i class="fas fa-align-left mr-1"></i>SEO description</label>
          <div class="input-icon">
            <i class="fas fa-quote-left" style="top:20px;"></i>
            <textarea name="seo_description"
                      class="form-control form-control-modern @error('seo_description') is-invalid @enderror"
                      rows="2" placeholder="Mô tả SEO ngắn…">{{ old('seo_description', $guide->seo_description ?? '') }}</textarea>
          </div>
          @error('seo_description') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
      </div>
    </div>
  </div>

  {{-- Nút bấm (vẫn nằm trong form cha) --}}
  <div class="button-group">
    <button class="btn btn-save" type="submit">
      <i class="fas fa-save mr-2"></i>{{ isset($guide) ? 'Cập nhật' : 'Lưu bài viết' }}
    </button>
    <a href="{{ route('admin.guides.index') }}" class="btn btn-cancel">
      <i class="fas fa-times mr-2"></i>Hủy bỏ
    </a>
  </div>
</div>

{{-- JS cho gợi ý thẻ + xem trước ảnh --}}
<script>
(() => {
  function initTagSuggestions() {
    const selectEl = document.getElementById('guide-tags');
    if (!selectEl) return;

    // Hỗ trợ Select2 nếu có
    try {
      if (window.jQuery && jQuery.fn.select2 && !jQuery(selectEl).hasClass('select2-hidden-accessible')) {
        jQuery(selectEl).select2({ placeholder: 'Chọn hoặc tìm kiếm thẻ...', width: '100%' });
      }
    } catch (e) {}

    const syncButtons = () => {
      const selected = new Set(Array.from(selectEl.selectedOptions).map(o => String(o.value)));
      document.querySelectorAll('.tag-suggestion').forEach(btn => {
        const id = String(btn.dataset.tagId);
        const on = selected.has(id);
        btn.classList.toggle('btn-info', on);
        btn.classList.toggle('btn-outline-info', !on);
      });
    };

    document.addEventListener('click', (ev) => {
      const btn = ev.target.closest('.tag-suggestion');
      if (!btn) return;
      const id = String(btn.dataset.tagId);
      const opt = Array.from(selectEl.options).find(o => String(o.value) === id);
      if (!opt) return;
      opt.selected = !opt.selected;
      selectEl.dispatchEvent(new Event('change', { bubbles: true }));
      if (window.jQuery) jQuery(selectEl).trigger('change');
      syncButtons();
    });

    selectEl.addEventListener('change', syncButtons);
    syncButtons();
  }

  function initCoverPreview() {
    const thumbnailInput = document.getElementById('thumbnail');
    const coverPreview = document.getElementById('coverPreview');
    const coverContent = document.getElementById('coverContent');
    const coverImg = document.getElementById('coverImg');
    const coverCurrent = document.getElementById('coverCurrent');

    if (!thumbnailInput) return;

    thumbnailInput.addEventListener('change', function() {
      if (this.files && this.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
          coverImg.src = e.target.result;
          coverPreview.style.display = 'flex';
          coverContent.style.display = 'none';
          if (coverCurrent) coverCurrent.style.display = 'none';
        };
        reader.readAsDataURL(this.files[0]);
      }
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => { initTagSuggestions(); initCoverPreview(); });
  } else {
    initTagSuggestions(); initCoverPreview();
  }
})();
</script>
