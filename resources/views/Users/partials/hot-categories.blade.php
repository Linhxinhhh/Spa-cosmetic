<section class="hot-categories py-8">
  <div class="container mx-auto">
    <h2 class="text-2xl font-extrabold mb-6 tracking-wide">DANH MỤC HOT</h2>

    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 lg:grid-cols-7 gap-x-12 gap-y-10">
      @forelse($hotCategories as $category)
        <a href="{{ route('users.products.byCategory', ['category' => $category->slug]) }}" class="group block text-center">
          {{-- hộp ảnh --}}
          <div class="mx-auto aspect-square w-[110px] sm:w-[120px] bg-gray-100 rounded-3xl
                      flex items-center justify-center shadow-[0_1px_0_#eee_inset]
                      transition-transform duration-200 group-hover:-translate-y-1">
            <img
              src="{{ $category->image ? asset('storage/'.$category->image) : asset('images/default-category.png') }}"
              alt="{{ $category->category_name }}"
              class="category-image"
            >
          </div>

          {{-- tên danh mục --}}
          <p class="mt-3 text-[15px] leading-6 font-medium text-gray-800
                    group-hover:text-pink-500 transition-colors
                    line-clamp-2 break-words">
            {{ $category->category_name }}
          </p>
        </a>
      @empty
        <p class="col-span-7 text-center text-gray-500">Chưa có danh mục con nào.</p>
      @endforelse
    </div>
  </div>
</section>

<style>
.line-clamp-2{
  display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden;
}

.hot-categories .grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(110px, 1fr));
  gap: 2.5rem 2rem; /* gap-y gap-x */
  justify-items: center;
  align-items: start;
}

.hot-categories .grid a {
  text-align: center;
  text-decoration: none;
}
.category-image {
  width: 120px;             /* tăng kích thước khung ảnh */
  height: 120px;
  object-fit: contain;      
  border-radius: 24px;      /* tăng nhẹ bo góc cho cân đối */
  background-color: #f3f4f6;
  display: block;
  margin: 0 auto;
  padding: 12px;             /* tăng padding tương ứng */
  transition: transform .25s ease;
}



.hot-categories .grid a:hover .category-image {
  transform: scale(1.05);
}
</style>
