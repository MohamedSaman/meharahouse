{{-- resources/views/livewire/admin/customer.blade.php --}}
<div class="space-y-5">

    {{-- Flash Messages --}}
    @if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
         class="flex items-center gap-3 p-4 bg-green-50 border border-green-200 rounded-xl text-green-700 text-sm font-medium">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ session('success') }}
    </div>
    @endif

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="font-[Poppins] font-bold text-xl text-[#0F172A]">Customers</h2>
            <p class="text-sm text-[#64748B]">{{ $users->total() }} registered users</p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card p-4 flex flex-col sm:flex-row gap-3 flex-wrap">
        <div class="flex items-center gap-2 bg-[#F1F5F9] rounded-lg px-3 py-2 flex-1 max-w-sm">
            <svg class="w-4 h-4 text-[#64748B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input wire:model.live.debounce.400ms="search" type="text" placeholder="Search by name or email..." class="bg-transparent text-sm outline-none flex-1 placeholder-[#94A3B8]">
        </div>
        <select wire:model.live="filterRole" class="form-input text-sm py-2 w-auto">
            <option value="customer">Customers</option>
            <option value="staff">Staff</option>
            <option value="admin">Admins</option>
            <option value="">All Roles</option>
        </select>
        {{-- Date Range --}}
        <div class="flex items-center gap-2 flex-wrap">
            <div class="flex items-center gap-1.5 bg-[#F8FAFC] border border-[#E2E8F0] rounded-lg px-2 py-1.5">
                <svg class="w-4 h-4 text-[#94A3B8] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <input wire:model.live="dateFrom" type="date"
                       class="text-xs text-[#475569] bg-transparent border-none outline-none w-32">
                <span class="text-xs text-[#94A3B8]">—</span>
                <input wire:model.live="dateTo" type="date"
                       class="text-xs text-[#475569] bg-transparent border-none outline-none w-32">
            </div>
            {{-- Quick Presets --}}
            <div class="flex gap-1" x-data="{
                setRange(from, to) {
                    $wire.set('dateFrom', from);
                    $wire.set('dateTo', to);
                },
                today() {
                    let d = new Date().toISOString().split('T')[0];
                    this.setRange(d, d);
                },
                last7() {
                    let to   = new Date();
                    let from = new Date(); from.setDate(from.getDate() - 6);
                    this.setRange(from.toISOString().split('T')[0], to.toISOString().split('T')[0]);
                },
                thisMonth() {
                    let now  = new Date();
                    let from = new Date(now.getFullYear(), now.getMonth(), 1).toISOString().split('T')[0];
                    let to   = now.toISOString().split('T')[0];
                    this.setRange(from, to);
                },
                lastMonth() {
                    let now  = new Date();
                    let from = new Date(now.getFullYear(), now.getMonth()-1, 1).toISOString().split('T')[0];
                    let to   = new Date(now.getFullYear(), now.getMonth(), 0).toISOString().split('T')[0];
                    this.setRange(from, to);
                }
            }">
                <button @click="today()"      class="px-2 py-1 text-[10px] font-semibold rounded-md bg-[#F1F5F9] text-[#475569] hover:bg-[#E2E8F0] transition-colors">Today</button>
                <button @click="last7()"      class="px-2 py-1 text-[10px] font-semibold rounded-md bg-[#F1F5F9] text-[#475569] hover:bg-[#E2E8F0] transition-colors">7d</button>
                <button @click="thisMonth()"  class="px-2 py-1 text-[10px] font-semibold rounded-md bg-[#F1F5F9] text-[#475569] hover:bg-[#E2E8F0] transition-colors">Month</button>
                <button @click="lastMonth()"  class="px-2 py-1 text-[10px] font-semibold rounded-md bg-[#F1F5F9] text-[#475569] hover:bg-[#E2E8F0] transition-colors">Last Mo</button>
                @if($dateFrom || $dateTo)
                <button wire:click="clearDates"
                        class="px-2 py-1 text-[10px] font-semibold rounded-md bg-red-50 text-red-500 hover:bg-red-100 transition-colors">
                    Clear
                </button>
                @endif
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Orders</th>
                        <th>Role</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr wire:key="{{ $user->id }}">
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-[#0F172A] to-[#334155] flex items-center justify-center shrink-0">
                                    <span class="text-[#F59E0B] text-xs font-bold">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                </div>
                                <span class="text-sm font-semibold text-[#0F172A]">{{ $user->name }}</span>
                            </div>
                        </td>
                        <td><span class="text-sm text-[#475569]">{{ $user->email }}</span></td>
                        <td><span class="text-sm text-[#475569]">{{ $user->phone ?? '—' }}</span></td>
                        <td>
                            <button wire:click="viewCustomer({{ $user->id }})"
                                    class="text-sm font-semibold text-[#0F172A] hover:text-[#F59E0B] transition-colors">
                                {{ $user->orders_count }}
                            </button>
                        </td>
                        <td>
                            <select wire:change="updateRole({{ $user->id }}, $event.target.value)"
                                    class="text-xs border border-[#E2E8F0] rounded-lg px-2 py-1 text-[#475569] focus:outline-none focus:border-[#F59E0B]">
                                <option value="customer" {{ $user->role === 'customer' ? 'selected' : '' }}>Customer</option>
                                <option value="staff"    {{ $user->role === 'staff'    ? 'selected' : '' }}>Staff</option>
                                <option value="admin"    {{ $user->role === 'admin'    ? 'selected' : '' }}>Admin</option>
                            </select>
                        </td>
                        <td><span class="text-xs text-[#94A3B8]">{{ $user->created_at->format('d M Y') }}</span></td>
                        <td>
                                <button wire:click="viewCustomer({{ $user->id }})"
                                    class="inline-flex items-center justify-center h-8 w-8 rounded-lg bg-slate-100 text-slate-600 hover:bg-blue-100 hover:text-blue-700 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-sm" title="View Profile">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-12 text-[#94A3B8]">No users found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($users->hasPages())
        <div class="px-5 py-4 border-t border-[#F1F5F9]">
            {{ $users->links() }}
        </div>
        @endif
    </div>

    {{-- Customer Detail Modal --}}
    @if($showDetail && $selectedUser)
    <div class="fixed inset-0 bg-slate-950/50 backdrop-blur-sm z-50 flex items-center justify-center p-4" x-data @click.self="$wire.set('showDetail', false)">
        <div class="bg-white rounded-2xl shadow-2xl ring-1 ring-slate-200/80 w-full max-w-lg max-h-[90vh] overflow-y-auto" @click.stop>
            <div class="flex items-center justify-between px-6 py-4 border-b border-[#E2E8F0] sticky top-0 bg-white">
                <div>
                    <h3 class="font-[Poppins] font-bold text-lg text-[#0F172A]">{{ $selectedUser->name }}</h3>
                    <p class="text-xs text-[#64748B]">{{ $selectedUser->email }}</p>
                </div>
                <button wire:click="$set('showDetail', false)" class="p-2 rounded-lg text-[#64748B] hover:bg-[#F1F5F9]">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-6 space-y-4">
                {{-- Profile Details --}}
                <div class="bg-[#F8FAFC] rounded-xl p-4 space-y-2 text-sm">
                    <div class="flex justify-between"><span class="text-[#64748B]">Phone</span><span class="font-medium text-[#0F172A]">{{ $selectedUser->phone ?? '—' }}</span></div>
                    <div class="flex justify-between"><span class="text-[#64748B]">Role</span><span class="badge badge-navy">{{ ucfirst($selectedUser->role) }}</span></div>
                    <div class="flex justify-between"><span class="text-[#64748B]">Joined</span><span class="font-medium text-[#0F172A]">{{ $selectedUser->created_at->format('d M Y') }}</span></div>
                    <div class="flex justify-between"><span class="text-[#64748B]">Total Orders</span><span class="font-bold text-[#0F172A]">{{ $selectedUser->orders_count }}</span></div>
                </div>

                {{-- Recent Orders --}}
                @if($selectedUser->orders->isNotEmpty())
                <div>
                    <h4 class="font-semibold text-sm text-[#0F172A] mb-3">Recent Orders</h4>
                    <div class="space-y-2">
                        @foreach($selectedUser->orders as $order)
                        <div class="flex items-center justify-between py-2 border-b border-[#F1F5F9] last:border-0 text-sm">
                            <div>
                                <p class="font-mono font-bold text-[#0F172A]">{{ $order->order_number }}</p>
                                <p class="text-xs text-[#64748B]">{{ $order->created_at->format('d M Y') }}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold text-[#0F172A]">Rs. {{ number_format($order->total, 0) }}</p>
                                <span class="text-xs font-semibold {{ match($order->status) { 'delivered'=>'text-green-600', 'cancelled'=>'text-red-500', 'pending'=>'text-yellow-600', default=>'text-blue-600' } }}">{{ ucfirst($order->status) }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @else
                <p class="text-sm text-[#94A3B8] text-center py-4">No orders placed yet.</p>
                @endif
            </div>
        </div>
    </div>
    @endif

</div>
