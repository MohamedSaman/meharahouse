{{-- resources/views/livewire/admin/product.blade.php --}}
<div class="space-y-5">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="font-[Poppins] font-bold text-xl text-[#0F172A]">Products</h2>
            <p class="text-sm text-[#64748B]">526 products in your catalog</p>
        </div>
        <button class="btn-primary btn-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add New Product
        </button>
    </div>

    <div class="card p-4 flex flex-col sm:flex-row gap-3">
        <div class="flex items-center gap-2 bg-[#F1F5F9] rounded-lg px-3 py-2 flex-1 max-w-sm">
            <svg class="w-4 h-4 text-[#64748B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" placeholder="Search products..." class="bg-transparent text-sm outline-none flex-1 placeholder-[#94A3B8]">
        </div>
        <select class="form-input text-sm py-2 w-auto">
            <option>All Categories</option>
            <option>Electronics</option>
            <option>Fashion</option>
            <option>Home & Living</option>
            <option>Sports</option>
        </select>
    </div>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Status</th>
                        <th>Sales</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $products = [
                        ['name' => 'Premium Wireless Headphones', 'cat' => 'Electronics', 'price' => '2,499', 'stock' => 32, 'status' => 'Active', 'sales' => 124, 'image' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=60&auto=format&fit=crop&q=80'],
                        ['name' => 'Leather Weekend Bag', 'cat' => 'Fashion', 'price' => '1,850', 'stock' => 18, 'status' => 'Active', 'sales' => 87, 'image' => 'https://images.unsplash.com/photo-1548036328-c9fa89d128fa?w=60&auto=format&fit=crop&q=80'],
                        ['name' => 'Smart Watch Pro', 'cat' => 'Electronics', 'price' => '4,200', 'stock' => 6, 'status' => 'Low Stock', 'sales' => 203, 'image' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=60&auto=format&fit=crop&q=80'],
                        ['name' => 'Natural Skincare Set', 'cat' => 'Beauty', 'price' => '890', 'stock' => 54, 'status' => 'Active', 'sales' => 56, 'image' => 'https://images.unsplash.com/photo-1556228578-0d85b1a4d571?w=60&auto=format&fit=crop&q=80'],
                        ['name' => 'Running Shoes Pro', 'cat' => 'Sports', 'price' => '3,100', 'stock' => 0, 'status' => 'Out of Stock', 'sales' => 178, 'image' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=60&auto=format&fit=crop&q=80'],
                    ];
                    @endphp
                    @foreach($products as $p)
                    <tr>
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg overflow-hidden bg-[#F1F5F9] shrink-0">
                                    <img src="{{ $p['image'] }}" alt="{{ $p['name'] }}" class="w-full h-full object-cover">
                                </div>
                                <span class="text-sm font-semibold text-[#0F172A]">{{ $p['name'] }}</span>
                            </div>
                        </td>
                        <td><span class="badge badge-navy">{{ $p['cat'] }}</span></td>
                        <td><span class="font-semibold text-sm text-[#0F172A]">ETB {{ $p['price'] }}</span></td>
                        <td>
                            <span class="{{ $p['stock'] === 0 ? 'text-red-500' : ($p['stock'] <= 10 ? 'text-orange-500' : 'text-[#475569]') }} text-sm font-semibold">
                                {{ $p['stock'] }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $p['status'] === 'Active' ? 'badge-success' : ($p['status'] === 'Low Stock' ? 'badge-warning' : 'badge-danger') }}">
                                {{ $p['status'] }}
                            </span>
                        </td>
                        <td><span class="text-sm text-[#475569]">{{ $p['sales'] }}</span></td>
                        <td>
                            <div class="flex gap-1">
                                <button class="p-1.5 rounded-lg text-[#64748B] hover:bg-blue-50 hover:text-blue-600 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                <button class="p-1.5 rounded-lg text-[#64748B] hover:bg-red-50 hover:text-red-500 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
