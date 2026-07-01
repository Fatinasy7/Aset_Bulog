@extends('layouts.app')

@section('title', 'Pengaturan - Lumina Asset')
@section('topbar-meta', 'System configuration dan manajemen pengguna')

@push('styles')
    <link rel="stylesheet" href="{{ Vite::asset('resources/css/settings-index.css') }}">
@endpush

@section('content')
<section class="page-header">
    <div>
        <h1 class="page-title">Pengaturan</h1>
        <p class="page-lead">Kelola pengguna, lokasi, dan parameter sistem untuk infrastruktur aset perusahaan.</p>
    </div>
</section>

@if(session('success'))
    <div class="alert-ui alert-success">
        {{ session('success') }}
    </div>
@endif

@if($errors->any())
    <div class="alert-ui alert-danger">
        <strong>Periksa kembali input Anda:</strong>
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<section class="card-surface">
    <div class="card-surface__header">
        <strong>{{ isset($editUser) ? 'Edit Pengguna' : 'Tambah Pengguna Baru' }}</strong>
    </div>
    <div class="card-surface__body">
        <form method="POST" action="{{ isset($editUser) ? route('frontend.settings.user.update', $editUser) : route('frontend.settings.user.store') }}">
            @csrf
            @if(isset($editUser))
                @method('PUT')
            @endif
            <div class="component-grid component-grid--full component-grid--compact">
                <div>
                    <label class="form-label-ui">Nama</label>
                    <input class="form-control-ui" type="text" name="name" value="{{ old('name', isset($editUser) ? $editUser->name : '') }}" placeholder="Nama lengkap">
                    @error('name')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="form-label-ui">Email</label>
                    <input class="form-control-ui" type="email" name="email" value="{{ old('email', isset($editUser) ? $editUser->email : '') }}" placeholder="Email pengguna">
                    @error('email')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="form-label-ui">Role</label>
                    <select class="form-select-ui" name="role">
                        <option value="user_pic" {{ old('role', isset($editUser) ? $editUser->role : '') === 'user_pic' ? 'selected' : '' }}>PIC</option>
                        <option value="admin_it" {{ old('role', isset($editUser) ? $editUser->role : '') === 'admin_it' ? 'selected' : '' }}>Admin IT</option>
                        <option value="manajemen" {{ old('role', isset($editUser) ? $editUser->role : '') === 'manajemen' ? 'selected' : '' }}>Manajemen</option>
                    </select>
                    @error('role')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="form-label-ui">Nomor Telepon</label>
                    <input class="form-control-ui" type="text" name="phone" value="{{ old('phone', isset($editUser) ? $editUser->phone : '') }}" placeholder="0812xxxxxxx">
                    @error('phone')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            <div class="form-actions">
                <button class="btn-ui btn-primary-ui" type="submit">{{ isset($editUser) ? 'Perbarui Pengguna' : 'Tambah Pengguna' }}</button>
                @if(isset($editUser))
                    <a class="btn-ui btn-secondary-ui" href="{{ route('frontend.settings') }}">Batal</a>
                @endif
            </div>
        </form>
    </div>
</section>

<section class="card-surface">
    <div class="card-surface__header">
        <strong>Daftar Pengguna</strong>
    </div>
    <div class="card-surface__body">
        <div class="table-responsive">
            <table class="table-ui">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Telepon</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @if($user->role === 'user_pic') PIC
                                @elseif($user->role === 'admin_it') Admin IT
                                @elseif($user->role === 'manajemen') Manajemen
                                @else {{ ucfirst($user->role) }}
                                @endif
                            </td>
                            <td>{{ $user->phone ?? '-' }}</td>
                            <td class="table-cell-action">
                                <div class="action-buttons">
                                    <a class="btn-ui btn-secondary-ui btn-sm-ui" href="{{ route('frontend.settings', ['edit' => $user->id]) }}">Edit</a>
                                    <form method="POST" action="{{ route('frontend.settings.user.destroy', $user) }}" class="inline-form delete-user-form" data-user-name="{{ $user->name }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn-ui btn-danger-ui btn-sm-ui delete-user-button" type="button">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5">Tidak ada pengguna terdaftar.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination-container">
            {{ $users->links() }}
        </div>
    </div>
</section>

<div id="confirm-delete-modal" class="modal-overlay" aria-hidden="true">
    <div class="modal-card" role="dialog" aria-modal="true" aria-labelledby="confirm-delete-title">
        <h2 id="confirm-delete-title">Konfirmasi Hapus Pengguna</h2>
        <p id="confirm-delete-message" class="modal-message">Apakah Anda yakin ingin menghapus pengguna ini? Tindakan ini tidak dapat dibatalkan.</p>
        <div class="modal-actions">
            <button type="button" id="cancel-delete" class="btn-ui btn-secondary-ui">Batal</button>
            <button type="button" id="confirm-delete" class="btn-ui btn-danger-ui">Hapus</button>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modal = document.getElementById('confirm-delete-modal');
            const message = document.getElementById('confirm-delete-message');
            const cancelButton = document.getElementById('cancel-delete');
            const confirmButton = document.getElementById('confirm-delete');
            let selectedForm = null;

            document.querySelectorAll('.delete-user-button').forEach(button => {
                button.addEventListener('click', function () {
                    selectedForm = this.closest('form');
                    const userName = selectedForm.dataset.userName || 'pengguna ini';
                    message.textContent = `Hapus pengguna ${userName}? Tindakan ini tidak dapat dikembalikan.`;
                    modal.classList.add('is-active');
                    modal.setAttribute('aria-hidden', 'false');
                });
            });

            cancelButton.addEventListener('click', function () {
                modal.classList.remove('is-active');
                modal.setAttribute('aria-hidden', 'true');
                selectedForm = null;
            });

            confirmButton.addEventListener('click', function () {
                if (selectedForm) {
                    selectedForm.submit();
                }
            });

            modal.addEventListener('click', function (event) {
                if (event.target === modal) {
                    modal.classList.remove('is-active');
                    modal.setAttribute('aria-hidden', 'true');
                    selectedForm = null;
                }
            });
        });
    </script>
@endpush

@endsection
