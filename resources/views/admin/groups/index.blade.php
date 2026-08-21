@extends('layouts.admin')

@section('title', 'Kelola Grup')

@section('content')
    <h1 class="font-heading text-xl font-bold text-primary-dark mb-1">Kelola Grup</h1>
    <p class="text-sm text-gray-500 mb-6">Daftar kelompok yang muncul di form notulensi publik</p>

    <div class="grid md:grid-cols-3 gap-6">
        <form method="POST" action="{{ route('admin.groups.store') }}"
              class="md:col-span-1 bg-white rounded-2xl border border-emerald-100 shadow-sm p-5 h-fit space-y-3">
            @csrf
            <h2 class="font-heading font-semibold text-sm">Tambah Grup Baru</h2>

            @if ($errors->any())
                <p class="text-xs text-red-500">{{ $errors->first() }}</p>
            @endif

            <div>
                <label class="block text-xs font-medium mb-1.5">Nama Grup</label>
                <input type="text" name="name" required
                       class="w-full rounded-xl border-gray-200 text-sm focus:border-primary focus:ring-primary">
            </div>
            <div>
                <label class="block text-xs font-medium mb-1.5">Deskripsi (opsional)</label>
                <textarea name="description" rows="2"
                          class="w-full rounded-xl border-gray-200 text-sm focus:border-primary focus:ring-primary"></textarea>
            </div>
            <button class="w-full bg-primary hover:bg-primary-light text-white text-sm font-semibold py-2.5 rounded-xl transition">
                Tambah Grup
            </button>
        </form>

        <div class="md:col-span-2 bg-white rounded-2xl border border-emerald-100 shadow-sm overflow-hidden h-fit">
            <table class="w-full text-sm">
                <thead class="bg-emerald-50 text-primary-dark text-xs uppercase">
                    <tr>
                        <th class="text-left px-4 py-3">Nama Grup</th>
                        <th class="text-left px-4 py-3">Jumlah Notulensi</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-emerald-50">
                    @forelse ($groups as $group)
                        <tr class="hover:bg-emerald-50/50">
                            <td class="px-4 py-3 font-medium text-primary-dark">{{ $group->name }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $group->minutes_count }}</td>
                            <td class="px-4 py-3 text-right">
                                <form method="POST" action="{{ route('admin.groups.destroy', $group) }}"
                                      onsubmit="return confirm('Hapus grup ini? Semua notulensi terkait juga akan terhapus.');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-xs font-semibold text-red-500 hover:underline">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-4 py-10 text-center text-gray-400">Belum ada grup.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
