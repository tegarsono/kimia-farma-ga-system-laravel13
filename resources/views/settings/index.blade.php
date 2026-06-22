@extends('layouts.ga-nosidebar')
@section('title', 'Image Settings')
@section('page-title', 'System Image Settings')

@push('styles')
    <style>
        .img-preview {
            max-height: 60px;
            object-fit: contain;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
        }

        .setting-card {
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 1.2rem;
            background: #fafafa;
            transition: box-shadow .2s;
        }

        .setting-card:hover {
            box-shadow: 0 4px 16px rgba(0, 0, 0, .08);
        }

        .setting-label {
            font-weight: 700;
            font-size: .85rem;
            color: #2d3748;
        }

        .setting-key {
            font-size: .72rem;
            color: #a0aec0;
            font-family: monospace;
        }
    </style>
@endpush

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-info d-flex gap-2 align-items-center">
                <i class="fas fa-info-circle fs-5"></i>
                <div>Set all images displayed on the website (logo, background, etc.). Images can be provided via
                    <strong>URL</strong> or uploaded <strong>directly</strong>.
                </div>
            </div>
        </div>
    </div>

    @php
        $imageKeys = [
            'logo_main' => 'Logo Utama',
            'bg_login' => 'Background Halaman Login',
            'bg_lupa_password' => 'Forgot Password Background',
            'logo_profile_topbar' => 'Logo Profil Topbar',
            'foto_profil_default' => 'Foto Profil Default',
            'logo_driver_navbar' => 'Logo Driver Navbar',
            'logo_driver_footer' => 'Logo Driver Footer',
            'logo_acmonitoring_header' => 'Logo AC Monitoring Header',
            'logo_tab' => 'Logo Tab Browser (Favicon)',
        ];
    @endphp

    <div class="row g-3">
        @foreach($imageKeys as $key => $label)
            @php $setting = $settings[$key] ?? null; @endphp
            <div class="col-12 col-md-6">
                <div class="setting-card">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <div class="setting-label">{{ $label }}</div>
                            <div class="setting-key">key: {{ $key }}</div>
                        </div>
                        @if($setting && $setting->image_value)
                            <img src="{{ $setting->image_type === 'url' ? $setting->image_value : asset('storage/' . $setting->image_value) }}"
                                alt="{{ $label }}" class="img-preview" onerror="this.style.display='none'">
                        @endif
                    </div>

                    {{-- Tab URL / Upload --}}
                    <ul class="nav nav-tabs nav-sm mb-2" id="tab-{{ $key }}">
                        <li class="nav-item">
                            <button
                                class="nav-link {{ (!$setting || $setting->image_type === 'url') ? 'active' : '' }} py-1 px-2"
                                style="font-size:.8rem" onclick="switchTab('{{ $key }}','url')">
                                <i class="fas fa-link me-1"></i>URL
                            </button>
                        </li>
                        <li class="nav-item">
                            <button
                                class="nav-link {{ ($setting && $setting->image_type === 'upload') ? 'active' : '' }} py-1 px-2"
                                style="font-size:.8rem" onclick="switchTab('{{ $key }}','upload')">
                                <i class="fas fa-upload me-1"></i>Upload
                            </button>
                        </li>
                    </ul>

                    {{-- URL Form --}}
                    <div id="pane-url-{{ $key }}"
                        style="{{ ($setting && $setting->image_type === 'upload') ? 'display:none' : '' }}">
                        <form method="POST" action="{{ route('settings.update') }}">
                            @csrf
                            <input type="hidden" name="image_key" value="{{ $key }}">
                            <input type="hidden" name="image_type" value="url">
                            <div class="input-group input-group-sm">
                                <input type="url" name="image_url" class="form-control" placeholder="https://..."
                                    value="{{ $setting && $setting->image_type === 'url' ? $setting->image_value : '' }}">
                                <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
                            </div>
                        </form>
                    </div>

                    {{-- Upload Form --}}
                    <div id="pane-upload-{{ $key }}"
                        style="{{ (!$setting || $setting->image_type !== 'upload') ? 'display:none' : '' }}">
                        <form method="POST" action="{{ route('settings.upload') }}" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="image_key" value="{{ $key }}">
                            <div class="input-group input-group-sm">
                                <input type="file" name="image_file" class="form-control" accept="image/*">
                                <button type="submit" class="btn btn-success btn-sm">Upload</button>
                            </div>
                            @if($setting && $setting->image_type === 'upload' && $setting->image_value)
                                <small class="text-muted">File: {{ basename($setting->image_value) }}</small>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>


@endsection

@push('scripts')
    <script>
        function switchTab(key, tab) {
            document.getElementById('pane-url-' + key).style.display = tab === 'url' ? '' : 'none';
            document.getElementById('pane-upload-' + key).style.display = tab === 'upload' ? '' : 'none';
            // update nav-link active state
            document.querySelectorAll('#tab-' + key + ' .nav-link').forEach(el => el.classList.remove('active'));
            event.target.classList.add('active');
        }
    </script>
@endpush