@extends('Users.layouts.home')

@section('title', 'So sánh sản phẩm')

@push('styles')
<style>
  :root {
    --primary-color: #2563eb;
    --primary-hover: #1d4ed8;
    --success-color: #059669;
    --danger-color: #dc2626;
    --warning-color: #d97706;
    --gray-50: #f9fafb;
    --gray-100: #f3f4f6;
    --gray-200: #e5e7eb;
    --gray-300: #d1d5db;
    --gray-400: #9ca3af;
    --gray-500: #6b7280;
    --gray-600: #4b5563;
    --gray-700: #374151;
    --gray-800: #1f2937;
    --gray-900: #111827;
    --border-radius: 16px;
    --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
    --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
    --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
    --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
  }

  body {
    background:white;
    min-height: 100vh;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
  }

  .compare-container {
    background: rgba(255, 255, 255, 0.95);
   
    border-radius: 24px;
    box-shadow: var(--shadow-xl);
    margin: 2rem auto;
    padding: 2rem;
    max-width: 1400px;
  }

  .page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    flex-wrap: wrap;
    gap: 1rem;
  }

  .page-title {
    color: var(--gray-900);
    font-weight: 700;
    font-size: 2rem;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 1rem;
    position: relative;
  }

  .page-title::after {
    content: '';
    position: absolute;
    bottom: -8px;
    left: 0;
    width: 60px;
    height: 4px;
    background: linear-gradient(90deg, var(--primary-color), var(--success-color));
    border-radius: 2px;
  }

  .compare-count {
    background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.875rem;
    font-weight: 600;
    margin-left: 1rem;
  }

  .header-actions {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
  }

  .btn {
    border: none;
    border-radius: 12px;
    padding: 0.75rem 1.5rem;
    font-weight: 600;
    font-size: 0.875rem;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
    cursor: pointer;
  }

  .btn-primary {
    background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));
    color: white;
  }

  .btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
    color: white;
  }

  .btn-outline-danger {
    background: white;
    color: var(--danger-color);
    border: 2px solid var(--danger-color);
  }

  .btn-outline-danger:hover {
    background: var(--danger-color);
    color: white;
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
  }

  .empty-state {
    text-align: center;
    padding: 4rem 2rem;
    background: white;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow-md);
  }

  .empty-state-icon {
    font-size: 5rem;
    margin-bottom: 1.5rem;
    opacity: 0.6;
  }

  .empty-state h3 {
    color: var(--gray-700);
    margin-bottom: 1rem;
    font-size: 1.5rem;
  }

  .empty-state p {
    color: var(--gray-500);
    margin-bottom: 2rem;
    font-size: 1.1rem;
    max-width: 500px;
    margin-left: auto;
    margin-right: auto;
  }

  .products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 2rem;
    margin-bottom: 3rem;
  }

  .product-card {
    background: white;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow-md);
    overflow: hidden;
    transition: all 0.3s ease;
    position: relative;
    border: 2px solid transparent;
  }

  .product-card:hover {
    transform: translateY(-8px);
    box-shadow: var(--shadow-xl);
    border-color: var(--primary-color);
  }

  .product-card.comparing {
    border-color: var(--success-color);
    box-shadow: 0 0 0 4px rgba(5, 150, 105, 0.1), var(--shadow-lg);
  }

  .card-header {
    position: relative;
    overflow: hidden;
    height: 250px;
    background: var(--gray-50);
  }

  .product-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
  }

  .product-card:hover .product-image {
    transform: scale(1.05);
  }

  .remove-btn {
    position: absolute;
    top: 12px;
    right: 12px;
    background: rgba(220, 38, 38, 0.9);
    color: white;
    border: none;
    border-radius: 50%;
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    backdrop-filter: blur(4px);
  }

  .remove-btn:hover {
    background: var(--danger-color);
    transform: scale(1.1);
  }

  .compare-badge {
    position: absolute;
    top: 12px;
    left: 12px;
    background: linear-gradient(135deg, var(--success-color), #047857);
    color: white;
    padding: 0.375rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.25rem;
  }

  .price-badge {
    position: absolute;
    bottom: 12px;
    right: 12px;
    background: rgba(220, 38, 38, 0.95);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-weight: 700;
    font-size: 0.875rem;
    backdrop-filter: blur(4px);
  }

  .card-body {
    padding: 1.5rem;
  }

  .product-title {
    font-size: 1.125rem;
    font-weight: 700;
    color: var(--gray-900);
    margin-bottom: 0.75rem;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }

  .product-title a {
    color: inherit;
    text-decoration: none;
    transition: color 0.3s ease;
  }

  .product-title a:hover {
    color: var(--primary-color);
  }

  .product-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
  }

  .category-tag {
    background: linear-gradient(135deg, #eff6ff, #dbeafe);
    color: var(--primary-color);
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 500;
    border: 1px solid #bfdbfe;
  }

  .stock-status {
    font-size: 0.75rem;
    font-weight: 600;
    padding: 0.25rem 0.5rem;
    border-radius: 12px;
  }

  .in-stock {
    background: #f0fdf4;
    color: var(--success-color);
  }

  .out-of-stock {
    background: #fef2f2;
    color: var(--danger-color);
  }

  .product-price {
    font-size: 1.5rem;
    font-weight: 800;
    color: var(--danger-color);
    margin-bottom: 1rem;
    display: flex;
    align-items: baseline;
    gap: 0.5rem;
  }

  .price-original {
    font-size: 1rem;
    color: var(--gray-400);
    text-decoration: line-through;
    font-weight: 500;
  }

  .discount-badge {
    background: var(--danger-color);
    color: white;
    padding: 0.25rem 0.5rem;
    border-radius: 8px;
    font-size: 0.75rem;
    font-weight: 600;
  }

  .product-description {
    color: var(--gray-600);
    font-size: 0.875rem;
    line-height: 1.5;
    margin-bottom: 1.5rem;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }

  .card-actions {
    display: flex;
    gap: 0.75rem;
  }

  .btn-detail {
    flex: 1;
    background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));
    color: white;
    border: none;
    padding: 0.75rem 1rem;
    border-radius: 10px;
    font-weight: 600;
    font-size: 0.875rem;
    text-decoration: none;
    text-align: center;
    transition: all 0.3s ease;
  }

  .btn-detail:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
    color: white;
  }

  .btn-cart {
    background: linear-gradient(135deg, var(--success-color), #047857);
    color: white;
    border: none;
    padding: 0.75rem;
    border-radius: 10px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .btn-cart:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
  }

  .filter-toolbar {
    background: white;
    padding: 1.5rem;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow-md);
    margin-bottom: 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
  }

  .filter-group {
    display: flex;
    align-items: center;
    gap: 1rem;
    flex-wrap: wrap;
  }

  .filter-select {
    padding: 0.5rem 1rem;
    border: 2px solid var(--gray-200);
    border-radius: 10px;
    background: white;
    color: var(--gray-700);
    font-weight: 500;
    transition: all 0.3s ease;
  }

  .filter-select:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
  }

  .view-toggle {
    display: flex;
    background: var(--gray-100);
    border-radius: 10px;
    padding: 4px;
  }

  .view-btn {
    padding: 0.5rem 1rem;
    border: none;
    background: none;
    color: var(--gray-600);
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
  }

  .view-btn.active {
    background: white;
    color: var(--primary-color);
    box-shadow: var(--shadow-sm);
  }

  .comparison-summary {
    background: white;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow-md);
    padding: 2rem;
    margin-bottom: 2rem;
  }

  .summary-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
  }

  .summary-item {
    text-align: center;
    padding: 1rem;
    border-radius: 12px;
    background: var(--gray-50);
  }

  .summary-number {
    font-size: 2rem;
    font-weight: 800;
    color: var(--primary-color);
    margin-bottom: 0.5rem;
  }

  .summary-label {
    color: var(--gray-600);
    font-weight: 500;
    font-size: 0.875rem;
  }

  /* Responsive Design */
  @media (max-width: 768px) {
    .compare-container {
      margin: 1rem;
      padding: 1rem;
      border-radius: 16px;
    }

    .page-header {
      flex-direction: column;
      align-items: flex-start;
      gap: 1rem;
    }

    .page-title {
      font-size: 1.5rem;
    }

    .products-grid {
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 1.5rem;
    }

    .filter-toolbar {
      flex-direction: column;
      align-items: stretch;
    }

    .filter-group {
      justify-content: space-between;
    }

    .card-actions {
      flex-direction: column;
    }
  }

  @media (max-width: 480px) {
    .products-grid {
      grid-template-columns: 1fr;
      gap: 1rem;
    }

    .card-header {
      height: 200px;
    }

    .card-body {
      padding: 1rem;
    }
  }

  /* Animations */
  @keyframes fadeInUp {
    from {
      opacity: 0;
      transform: translateY(30px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  .product-card {
    animation: fadeInUp 0.6s ease-out;
  }

  .product-card:nth-child(1) { animation-delay: 0.1s; }
  .product-card:nth-child(2) { animation-delay: 0.2s; }
  .product-card:nth-child(3) { animation-delay: 0.3s; }
  .product-card:nth-child(4) { animation-delay: 0.4s; }
  .product-card:nth-child(5) { animation-delay: 0.5s; }
  .product-card:nth-child(6) { animation-delay: 0.6s; }

  /* Loading state */
  .loading {
    opacity: 0.6;
    pointer-events: none;
  }

  .loading::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 40px;
    height: 40px;
    border: 4px solid var(--gray-200);
    border-top: 4px solid var(--primary-color);
    border-radius: 50%;
    animation: spin 1s linear infinite;
  }

  @keyframes spin {
    0% { transform: translate(-50%, -50%) rotate(0deg); }
    100% { transform: translate(-50%, -50%) rotate(360deg); }
  }
</style>
@endpush

@section('content')
<div class="compare-container">
 

  @if($products->isEmpty())
    {{-- Empty State --}}
    <div class="empty-state">
      <div class="empty-state-icon">🔍</div>
      <h3>Danh sách so sánh trống</h3>
      <p>Hãy thêm các sản phẩm vào danh sách để so sánh chi tiết về giá cả, tính năng và đặc điểm. Điều này sẽ giúp bạn đưa ra quyết định mua hàng tốt nhất!</p>
      <a href="{{ route('users.products.index') }}" class="btn btn-primary">
        <i class="fas fa-search"></i>
        Khám phá sản phẩm
      </a>
    </div>
  @else
    {{-- Comparison Summary --}}
    <div class="comparison-summary">
      <h4 class="mb-3">
        <i class="fas fa-chart-bar me-2"></i>
        Tổng quan so sánh
      </h4>
      <div class="summary-grid">
        <div class="summary-item">
          <div class="summary-number">{{ $products->count() }}</div>
          <div class="summary-label">Sản phẩm</div>
        </div>
        <div class="summary-item">
          <div class="summary-number">
            {{ number_format($products->min(fn($p) => product_final_price($p)), 0, ',', '.') }}₫
          </div>
          <div class="summary-label">Giá thấp nhất</div>
        </div>
        <div class="summary-item">
          <div class="summary-number">
            {{ number_format($products->max(fn($p) => product_final_price($p)), 0, ',', '.') }}₫
          </div>
          <div class="summary-label">Giá cao nhất</div>
        </div>
        <div class="summary-item">
          <div class="summary-number">{{ $products->pluck('category.category_name')->unique()->count() }}</div>
          <div class="summary-label">Danh mục</div>
        </div>
      </div>
    </div>

    {{-- Filter Toolbar --}}
    <div class="filter-toolbar">
      <div class="filter-group">
        <label class="fw-semibold text-gray-700">Sắp xếp theo:</label>
        <select class="filter-select" onchange="sortProducts(this.value)">
          <option value="name">Tên sản phẩm</option>
          <option value="price-low">Giá: Thấp → Cao</option>
          <option value="price-high">Giá: Cao → Thấp</option>
          <option value="category">Danh mục</option>
        </select>
        
        <select class="filter-select" onchange="filterByCategory(this.value)">
          <option value="">Tất cả danh mục</option>
          @foreach($products->pluck('category.category_name')->unique()->filter() as $category)
            <option value="{{ $category }}">{{ $category }}</option>
          @endforeach
        </select>
      </div>

      <div class="view-toggle">
        <button class="view-btn active" onclick="setView('grid')">
          <i class="fas fa-th"></i>
        </button>
        <button class="view-btn" onclick="setView('list')">
          <i class="fas fa-list"></i>
        </button>
      </div>
    </div>

    {{-- Products Grid --}}
    <div class="products-grid" id="productsGrid">
      @foreach($products as $index => $product)
        @php
          $price = product_final_price($product);
          $originalPrice = (float)($product->price ?? 0);
          $hasDiscount = $price > 0 && $price < $originalPrice;
          $discountPercent = $hasDiscount ? round((($originalPrice - $price) / $originalPrice) * 100) : 0;
          $minPrice = $products->min(fn($p) => product_final_price($p));
          $isBestPrice = $price == $minPrice;
        @endphp
        
        <div class="product-card {{ $isBestPrice ? 'comparing' : '' }}" data-category="{{ $product->category->category_name ?? '' }}" data-price="{{ $price }}">
          <div class="card-header">
            <img src="{{ product_main_src($product) }}" 
                 alt="{{ $product->product_name }}" 
                 class="product-image"
                 loading="lazy"
                 style="object-fit: contain; ">
            
            {{-- Remove Button --}}
            <form action="{{ route('users.compare.remove', $product->product_id) }}"
                  method="POST" 
                  class="d-inline"
                  onsubmit="return confirm('Xóa sản phẩm này khỏi danh sách so sánh?')">
              @csrf
              @method('DELETE')
              <button type="submit" class="remove-btn" title="Xóa khỏi so sánh">
                <i class="fas fa-times"></i>
              </button>
            </form>

            {{-- Best Price Badge --}}
            @if($isBestPrice)
              <div class="compare-badge">
                <i class="fas fa-crown"></i>
                Giá tốt nhất
              </div>
            @endif

            {{-- Price Badge --}}
            <div class="price-badge">
              {{ number_format($price, 0, ',', '.') }}₫
              @if($hasDiscount)
                <div style="font-size: 0.75rem; opacity: 0.9;">
                  -{{ $discountPercent }}%
                </div>
              @endif
            </div>
          </div>

          <div class="card-body">
            <h3 class="product-title">
              <a href="{{ route('users.products.show', $product->slug ?? $product->product_id) }}">
                {{ $product->product_name }}
              </a>
            </h3>

            <div class="product-meta">
              @if($product->category)
                <span class="category-tag">
                  {{ $product->category->category_name }}
                </span>
              @endif
              
              @if(isset($product->stock_quantity))
                <span class="stock-status {{ $product->stock_quantity > 0 ? 'in-stock' : 'out-of-stock' }}">
                  @if($product->stock_quantity > 0)
                    <i class="fas fa-check-circle"></i> Còn hàng
                  @else
                    <i class="fas fa-times-circle"></i> Hết hàng
                  @endif
                </span>
              @endif
            </div>

            <div class="product-price">
              <span>{{ number_format($price, 0, ',', '.') }}₫</span>
              @if($hasDiscount)
                <span class="price-original">{{ number_format($originalPrice, 0, ',', '.') }}₫</span>
                <span class="discount-badge">-{{ $discountPercent }}%</span>
              @endif
            </div>

            @if($product->description)
              <p class="product-description">
                {{ $product->description }}
              </p>
            @endif

            <div class="card-actions">
              <a href="{{ route('users.products.show', $product->slug ?? $product->product_id) }}" 
                 class="btn-detail">
                <i class="fas fa-eye"></i>
                Xem chi tiết
              </a>
           @php
            $addUrl = $product->slug
                ? route('users.cart.add', ['product' => $product->slug])
                : route('users.cart.addById', ['product' => $product->product_id]);
          @endphp

          <form action="{{ $addUrl }}" method="POST" class="mb-4">
            @csrf
            <input type="hidden" name="quantity" value="1">
            <button type="submit" title="Thêm vào giỏ hàng" class="btn-cart">
              <i class="fas fa-cart-plus"></i>
            </button>
          </form>

         



            </div>
          </div>
        </div>
      @endforeach
    </div>

    {{-- Quick Actions --}}
    <div class="row g-3 mt-4">
      <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body text-center">
            <i class="fas fa-shopping-cart fa-2x text-primary mb-3"></i>
            <h5>Thêm tất cả vào giỏ</h5>
            <p class="text-muted small">Thêm tất cả sản phẩm vào giỏ hàng</p>
            <button class="btn btn-primary btn-sm" onclick="addAllToCart()">
              Thêm ngay
            </button>
          </div>
        </div>
      </div>
      
      <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body text-center">
            <i class="fas fa-share-alt fa-2x text-success mb-3"></i>
            <h5>Chia sẻ danh sách</h5>
            <p class="text-muted small">Chia sẻ kết quả so sánh</p>
            <button class="btn btn-success btn-sm" onclick="shareComparison()">
              Chia sẻ
            </button>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body text-center">
            <i class="fas fa-download fa-2x text-info mb-3"></i>
            <h5>Xuất PDF</h5>
            <p class="text-muted small">Tải danh sách so sánh</p>
            <button class="btn btn-info btn-sm" onclick="exportToPDF()">
              Tải xuống
            </button>
          </div>
        </div>
      </div>
    </div>
  @endif
</div>

<script>
function sortProducts(sortBy) {
  const grid = document.getElementById('productsGrid');
  const cards = Array.from(grid.children);
  
  cards.sort((a, b) => {
    switch(sortBy) {
      case 'price-low':
        return parseFloat(a.dataset.price) - parseFloat(b.dataset.price);
      case 'price-high':
        return parseFloat(b.dataset.price) - parseFloat(a.dataset.price);
      case 'category':
        return a.dataset.category.localeCompare(b.dataset.category);
      case 'name':
      default:
        const nameA = a.querySelector('.product-title a').textContent;
        const nameB = b.querySelector('.product-title a').textContent;
        return nameA.localeCompare(nameB);
    }
  });
  
  cards.forEach(card => grid.appendChild(card));
}

function filterByCategory(category) {
  const cards = document.querySelectorAll('.product-card');
  
  cards.forEach(card => {
    if (category === '' || card.dataset.category === category) {
      card.style.display = '';
    } else {
      card.style.display = 'none';
    }  
     });                            
}
function setView(view) {
  const grid = document.getElementById('productsGrid');
  const buttons = document.querySelectorAll('.view-btn');
  
  buttons.forEach(btn => btn.classList.remove('active'));
  
  if (view === 'list') {
    grid.style.gridTemplateColumns = '1fr';
    buttons[1].classList.add('active');
  } else {
    grid.style.gridTemplateColumns = 'repeat(auto-fill, minmax(300px, 1fr))';
    buttons[0].classList.add('active');
  }
}
function addToCart(productId) {
  // Implement AJAX request to add product to cart
  alert('Sản phẩm ' + productId + ' đã được thêm vào giỏ hàng!');
}
function addAllToCart() {
  // Implement AJAX request to add all products to cart
  alert('Tất cả sản phẩm đã được thêm vào giỏ hàng!');
}
function shareComparison() {
  // Implement sharing functionality
  alert('Chức năng chia sẻ đang được phát triển.');
}
function exportToPDF() {
  // Implement PDF export functionality
  alert('Chức năng xuất PDF đang được phát triển.');
}

</script>
<meta name="csrf-token" content="{{ csrf_token() }}">
<script>
function addAllToCart() {
    const productIds = @json($products->pluck('product_id')); // danh sách ID sản phẩm bạn muốn thêm

    productIds.forEach(id => {
        fetch(`/users/cart/add/${slug}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ quantity: 1 })
        });
    });

    // Sau khi thêm, có thể reload hoặc chuyển trang
    setTimeout(() => {
        window.location.href = "{{ route('users.cart.index') }}";
    }, 500);
}
</script>


@endsection