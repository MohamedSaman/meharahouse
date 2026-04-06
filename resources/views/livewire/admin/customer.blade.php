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
    @if(session('error'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
         class="flex items-center gap-3 p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm font-medium">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ session('error') }}
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
                            <div class="flex items-center gap-1.5">
                                {{-- View --}}
                                <button wire:click="viewCustomer({{ $user->id }})"
                                        class="inline-flex items-center justify-center h-8 w-8 rounded-lg bg-slate-100 text-slate-600 hover:bg-blue-100 hover:text-blue-700 transition-all duration-200 hover:-translate-y-0.5" title="View">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </button>
                                {{-- Edit --}}
                                <button wire:click="editCustomer({{ $user->id }})"
                                        class="inline-flex items-center justify-center h-8 w-8 rounded-lg bg-slate-100 text-slate-600 hover:bg-amber-100 hover:text-amber-700 transition-all duration-200 hover:-translate-y-0.5" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                {{-- Delete --}}
                                <button wire:click="confirmDelete({{ $user->id }})"
                                        class="inline-flex items-center justify-center h-8 w-8 rounded-lg bg-slate-100 text-slate-600 hover:bg-red-100 hover:text-red-600 transition-all duration-200 hover:-translate-y-0.5" title="Delete">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
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

    {{-- ── Edit Modal ─────────────────────────────────────────────────── --}}
    @if($showEdit)
    <div class="fixed inset-0 bg-slate-950/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl ring-1 ring-slate-200/80 w-full max-w-md" @click.stop>
            <div class="flex items-center justify-between px-6 py-4 border-b border-[#E2E8F0]">
                <h3 class="font-[Poppins] font-bold text-lg text-[#0F172A]">Edit Customer</h3>
                <button wire:click="closeEdit" class="p-2 rounded-lg text-[#64748B] hover:bg-[#F1F5F9]">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Full Name *</label>
                    <input wire:model="editName" type="text" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400/40 focus:border-amber-400 @error('editName') border-red-400 @enderror">
                    @error('editName')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Email *</label>
                    <input wire:model="editEmail" type="email" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400/40 focus:border-amber-400 @error('editEmail') border-red-400 @enderror">
                    @error('editEmail')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Phone</label>
                    <input wire:model="editPhone" type="tel" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400/40 focus:border-amber-400" placeholder="+94761265772">
                    @error('editPhone')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Role *</label>
                    <select wire:model="editRole" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400/40 focus:border-amber-400">
                        <option value="customer">Customer</option>
                        <option value="staff">Staff</option>
                        <option value="admin">Admin</option>
                    </select>
                    @error('editRole')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-[#E2E8F0]">
                <button wire:click="closeEdit" class="px-4 py-2 rounded-lg text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 transition-colors">Cancel</button>
                <button wire:click="saveCustomer" wire:loading.attr="disabled" wire:target="saveCustomer"
                        class="px-4 py-2 rounded-lg text-sm font-semibold text-white bg-amber-500 hover:bg-amber-400 transition-colors disabled:opacity-60">
                    <span wire:loading.remove wire:target="saveCustomer">Save Changes</span>
                    <span wire:loading wire:target="saveCustomer">Saving...</span>
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- ── Delete Confirm Modal ─────────────────────────────────────── --}}
    @if($showDelete)
    <div class="fixed inset-0 bg-slate-950/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl ring-1 ring-slate-200/80 w-full max-w-sm" @click.stop>
            <div class="p-6 text-center">
                <div class="w-14 h-14 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-7 h-7 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </div>
                <h3 class="font-[Poppins] font-bold text-lg text-slate-800 mb-1">Delete Customer</h3>
                <p class="text-sm text-slate-500 mb-6">Are you sure you want to delete <strong class="text-slate-800">{{ $deleteName }}</strong>? This action cannot be undone.</p>
                <div class="flex gap-3">
                    <button wire:click="closeDelete" class="flex-1 px-4 py-2 rounded-lg text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 transition-colors">Cancel</button>
                    <button wire:click="deleteCustomer" wire:loading.attr="disabled" wire:target="deleteCustomer"
                            class="flex-1 px-4 py-2 rounded-lg text-sm font-semibold text-white bg-red-500 hover:bg-red-600 transition-colors disabled:opacity-60">
                        <span wire:loading.remove wire:target="deleteCustomer">Delete</span>
                        <span wire:loading wire:target="deleteCustomer">Deleting...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

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
