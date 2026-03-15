<section>
    <header class="mb-4">
        <h5 class="text-primary fw-bold">
            {{ __('Update Password') }}
        </h5>

        <p class="text-muted small">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-3">
        @csrf
        @method('put')

        <div class="mb-3">
            <label for="update_password_current_password" class="form-label">{{ __('Current Password') }}</label>
            <div class="input-group">
                <input id="update_password_current_password" name="current_password" type="password"
                    class="form-control" autocomplete="current-password">
                <button class="btn btn-outline-secondary" type="button" tabindex="-1"
                    onclick="let input = this.previousElementSibling; if (input.type === 'password') { input.type = 'text'; this.innerHTML = '<i class=\'fas fa-eye-slash\'></i>'; } else { input.type = 'password'; this.innerHTML = '<i class=\'fas fa-eye\'></i>'; }">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
            @if($errors->updatePassword->get('current_password'))
                <div class="text-danger small mt-1">{{ $errors->updatePassword->first('current_password') }}</div>
            @endif
        </div>

        <div class="mb-3">
            <label for="update_password_password" class="form-label">{{ __('New Password') }}</label>
            <div class="input-group">
                <input id="update_password_password" name="password" type="password" class="form-control"
                    autocomplete="new-password">
                <button class="btn btn-outline-secondary" type="button" tabindex="-1"
                    onclick="let input = this.previousElementSibling; if (input.type === 'password') { input.type = 'text'; this.innerHTML = '<i class=\'fas fa-eye-slash\'></i>'; } else { input.type = 'password'; this.innerHTML = '<i class=\'fas fa-eye\'></i>'; }">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
            @if($errors->updatePassword->get('password'))
                <div class="text-danger small mt-1">{{ $errors->updatePassword->first('password') }}</div>
            @endif
        </div>

        <div class="mb-3">
            <label for="update_password_password_confirmation" class="form-label">{{ __('Confirm Password') }}</label>
            <div class="input-group">
                <input id="update_password_password_confirmation" name="password_confirmation" type="password"
                    class="form-control" autocomplete="new-password">
                <button class="btn btn-outline-secondary" type="button" tabindex="-1"
                    onclick="let input = this.previousElementSibling; if (input.type === 'password') { input.type = 'text'; this.innerHTML = '<i class=\'fas fa-eye-slash\'></i>'; } else { input.type = 'password'; this.innerHTML = '<i class=\'fas fa-eye\'></i>'; }">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
            @if($errors->updatePassword->get('password_confirmation'))
                <div class="text-danger small mt-1">{{ $errors->updatePassword->first('password_confirmation') }}</div>
            @endif
        </div>

        <div class="d-flex align-items-center gap-3 mt-4">
            <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>

            @if (session('status') === 'password-updated')
                <span class="text-success small animate-fade-out">{{ __('Saved.') }}</span>
            @endif
        </div>
    </form>
</section>