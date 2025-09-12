@php
  $languages = \App\Models\Language::where('is_active', 1)->get();
  $currentLanguage = $languages->where('code', app()->getLocale())->first();
@endphp

<div class="dropdown flag-dropdown me-3 custom-language-dropdown">
  <a href="javascript:void(0);" class="d-inline-flex align-items-center" data-bs-toggle="dropdown" aria-expanded="false">
  @foreach(App\Models\Language::where('is_active', 1)->get() as $language)
    @if($language->code == app()->getLocale())
    {{-- <img src="{{ assetUrl() }}assets/frontend/img/flags/{{ $language->code }}.png" alt="flag"> --}}
    <i class="flag-icon {{ $language->flag }} me-2"></i>
    {{ $language->name }}
    @endif
  @endforeach
  </a>
  <ul class="dropdown-menu p-2 mt-2">
  @foreach(App\Models\Language::where('is_active', 1)->get() as $language)
    <li>
    <a class="dropdown-item rounded d-flex align-items-center" href="javascript:void(0);" onclick="selectLanguage('{{ $language->code }}', '{{ $language->name }}','{{ $language->flag }}')">
      {{-- <img src="{{ assetUrl() }}assets/frontend/img/flags/{{ $language->code }}.png" class="me-2" alt="flag"> --}}
      <i class="flag-icon {{ $language->flag }} me-2"></i>
      {{ $language->name }}
    </a>
    </li>
  @endforeach
  </ul>
</div>

<style>
.custom-language-dropdown {
    position: relative;
    display: inline-block;
    min-width: 110px;
}

.selected-language {
    display: flex;
    align-items: center;
    padding: 8px 12px;
    border: 1px solid #ced4da;
    border-radius: 4px;
    background: white;
    cursor: pointer;
    font-size: 14px;
}

.selected-language img {
    width: 20px;
    height: 15px;
    margin-right: 8px;
}

.selected-language i {
    margin-left: auto;
    font-size: 12px;
}

.language-options {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 1px solid #ced4da;
    border-radius: 4px;
    margin-top: 4px;
    display: none;
    z-index: 1000;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.language-options.show {
    display: block;
}

.language-option {
    display: flex;
    align-items: center;
    padding: 8px 12px;
    cursor: pointer;
    transition: background 0.2s;
}

.language-option:hover {
    background: #f8f9fa;
}

.language-option img {
    width: 20px;
    height: 15px;
    margin-right: 8px;
}
</style>

<script>
function toggleDropdown() {
    const options = document.getElementById('languageOptions');
    options.classList.toggle('show');
}

function selectLanguage(locale, name, flagSrc) {
    changeLanguage(locale);
}

function changeLanguage(locale) {
    window.location = '{{ url('/change-language') }}/' + locale;
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    const dropdown = document.querySelector('.custom-language-dropdown');
    if (!dropdown.contains(event.target)) {
        document.getElementById('languageOptions').classList.remove('show');
    }
});
</script>
