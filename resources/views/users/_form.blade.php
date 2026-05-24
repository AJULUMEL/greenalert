@php
    $isEdit = isset($user) && $user;
    $action = $isEdit ? route('users.update', $user) : route('users.store');
@endphp

<form action="{{ $action }}" method="POST">
    @csrf
    @if($isEdit)
        @method('PUT')
    @endif

    <div class="form-group">
        <label>Name</label>
        <input name="name" value="{{ old('name', $isEdit ? $user->name : '') }}" class="form-control" required />
    </div>

    <div class="form-group">
        <label>Email</label>
        <input name="email" type="email" value="{{ old('email', $isEdit ? $user->email : '') }}" class="form-control" required />
    </div>

    <div class="form-group">
        <label>Role</label>
        <select name="role" class="form-control">
            @foreach($roles as $key => $label)
                <option value="{{ $key }}" {{ old('role', $isEdit ? $user->role : '') == $key ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label>Password {{ $isEdit ? '(leave empty to keep current)' : '' }}</label>
        <input name="password" type="password" class="form-control" {{ $isEdit ? '' : 'required' }} />
    </div>

    <div class="form-group">
        <label>Confirm Password</label>
        <input name="password_confirmation" type="password" class="form-control" {{ $isEdit ? '' : 'required' }} />
    </div>

    <div class="mt-3">
        <button class="btn btn-success">{{ $isEdit ? 'Update' : 'Create' }}</button>
        <a href="{{ route('users.index') }}" class="btn btn-secondary">Cancel</a>
    </div>
</form>
