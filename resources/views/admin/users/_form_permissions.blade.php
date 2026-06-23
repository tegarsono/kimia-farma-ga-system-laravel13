@foreach($permissionMap as $permName => $label)
    @php
        $checked = isset($selectedPermissions[$permName]) && $selectedPermissions[$permName] === true;
    @endphp
    <div class="form-check">
        <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permName }}" {{ $checked ? 'checked' : '' }}>
        <label class="form-check-label">{{ $label }}</label>
    </div>
@endforeach

