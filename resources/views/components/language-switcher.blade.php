
<div class="custom-language-dropdown">
    <div class="selected-language" onclick="toggleDropdown()">
      @php
        $currentLanguage = \App\Models\Language::where('code', app()->getLocale())->first();
      @endphp
      {{-- <img src="{{ assetUrl() }}{{ $currentLanguage ? $currentLanguage->flag : 'images/flags/en.png' }}" alt=""> --}}
      <i class="flag-icon {{ $currentLanguage ? $currentLanguage->flag : 'flag-icon-us' }} me-2 mr-2"></i>
      <span>{{ $currentLanguage ? $currentLanguage->name : 'English' }}</span>
      <i class="fas fa-chevron-down"></i>
    </div>
    <div class="language-options" id="languageOptions">
      @foreach(\App\Models\Language::all() as $language)
        <div class="language-option" onclick="selectLanguage('{{ $language->code }}', '{{ $language->name }}','{{ $language->flag }}')">
          {{-- <img src="{{ assetUrl() }}{{ $language->flag }}" alt=""> --}}
          <i class="flag-icon {{ $language->flag }} me-2 mr-2"></i>
          <span>{{ $language->name }}</span>
        </div>
      @endforeach
    </div>
</div>

<style>
.custom-language-dropdown {
    position: relative;
    display: inline-block;
    min-width: 120px;
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
