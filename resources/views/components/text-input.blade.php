@props(['disabled' => false])

@if ($attributes->get('type') === 'password')
    <div class="position-relative" style="position: relative; display: flex; align-items: center; width: 100%;">
        <input {{ $attributes->merge(['class' => 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full pr-16 pe-16']) }} @disabled($disabled)>
        <div style="position: absolute; right: 10px; display: flex; gap: 8px; align-items: center;">
            <button type="button" class="text-xs text-muted"
                style="background: transparent; border: none; padding: 0; cursor: pointer; display: flex; align-items: center; justify-content: center; height: 1.5rem; width: 1.5rem;"
                onclick="this.parentElement.previousElementSibling.value = ''; this.parentElement.previousElementSibling.focus();"
                title="Clear">
                <i class="fas fa-times"></i>
            </button>
            <button type="button" class="text-xs text-muted"
                style="background: transparent; border: none; padding: 0; cursor: pointer; display: flex; align-items: center; justify-content: center; height: 1.5rem; width: 1.5rem;"
                onclick="let input = this.parentElement.previousElementSibling; if (input.type === 'password') { input.type = 'text'; this.innerHTML = '<i class=\'fas fa-eye-slash\'></i>'; } else { input.type = 'password'; this.innerHTML = '<i class=\'fas fa-eye\'></i>'; }"
                title="Toggle Visibility">
                <i class="fas fa-eye"></i>
            </button>
        </div>
    </div>
@else
    <input {{ $attributes->merge(['class' => 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm']) }} @disabled($disabled)>
@endif