@extends('admin.layouts.master_layout')

@section('pageTitle', 'System Settings')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">System Settings</h4>
        <div class="card-tools">
          <button type="button" class="btn btn-success" id="saveAllSettings">
            <i class="fas fa-save"></i> Save All Settings
          </button>
        </div>
      </div>
      <div class="card-body">
        <form id="settingsForm" action="{{ route('admin.settings.update') }}" method="POST">
          @csrf
          @method('PUT')
          
          <div class="row">
            <!-- General Settings -->
            <div class="col-md-6">
              <div class="card">
                <div class="card-header">
                  <h5>General Settings</h5>
                </div>
                <div class="card-body">
                  @foreach($settings->where('group', 'general') as $setting)
                  <div class="form-group">
                    <label for="setting_{{ $setting->id }}">{{ ucwords(str_replace('_', ' ', $setting->key)) }}</label>
                    @if($setting->type === 'boolean')
                      <select class="form-control" name="settings[{{ $setting->key }}]" id="setting_{{ $setting->id }}">
                        <option value="1" {{ $setting->plain_value == '1' ? 'selected' : '' }}>Yes</option>
                        <option value="0" {{ $setting->plain_value == '0' ? 'selected' : '' }}>No</option>
                      </select>
                    @elseif($setting->type === 'textarea')
                      <textarea class="form-control" name="settings[{{ $setting->key }}]" id="setting_{{ $setting->id }}" rows="3">{{ $setting->plain_value }}</textarea>
                    @else
                      <input type="{{ $setting->type ?? 'text' }}" class="form-control" 
                             name="settings[{{ $setting->key }}]" 
                             id="setting_{{ $setting->id }}" 
                             value="{{ $setting->plain_value }}">
                    @endif
                    @if($setting->description)
                      <small class="form-text text-muted">{{ $setting->description }}</small>
                    @endif
                  </div>
                  @endforeach
                </div>
              </div>
            </div>
            
            <!-- E-commerce Settings -->
            <div class="col-md-6">
              <div class="card">
                <div class="card-header">
                  <h5>E-commerce Settings</h5>
                </div>
                <div class="card-body">
                  @foreach($settings->where('group', 'ecommerce') as $setting)
                  <div class="form-group">
                    <label for="setting_{{ $setting->id }}">{{ ucwords(str_replace('_', ' ', $setting->key)) }}</label>
                    @if($setting->type === 'boolean')
                      <select class="form-control" name="settings[{{ $setting->key }}]" id="setting_{{ $setting->id }}">
                        <option value="1" {{ $setting->plain_value == '1' ? 'selected' : '' }}>Yes</option>
                        <option value="0" {{ $setting->plain_value == '0' ? 'selected' : '' }}>No</option>
                      </select>
                    @elseif($setting->type === 'textarea')
                      <textarea class="form-control" name="settings[{{ $setting->key }}]" id="setting_{{ $setting->id }}" rows="3">{{ $setting->plain_value }}</textarea>
                    @else
                      <input type="{{ $setting->type ?? 'text' }}" class="form-control" 
                             name="settings[{{ $setting->key }}]" 
                             id="setting_{{ $setting->id }}" 
                             value="{{ $setting->plain_value }}">
                    @endif
                    @if($setting->description)
                      <small class="form-text text-muted">{{ $setting->description }}</small>
                    @endif
                  </div>
                  @endforeach
                </div>
              </div>
            </div>
          </div>
          
          <div class="row mt-3">
            <!-- Email Settings -->
            <div class="col-md-6">
              <div class="card">
                <div class="card-header">
                  <h5>Email Settings</h5>
                </div>
                <div class="card-body">
                  @foreach($settings->where('group', 'email') as $setting)
                  <div class="form-group">
                    <label for="setting_{{ $setting->id }}">{{ ucwords(str_replace('_', ' ', $setting->key)) }}</label>
                    @if($setting->key === 'mail_driver')
                      <select class="form-control" name="settings[{{ $setting->key }}]" id="setting_{{ $setting->id }}">
                        <option value="smtp" {{ $setting->plain_value === 'smtp' ? 'selected' : '' }}>SMTP</option>
                        <option value="sendmail" {{ $setting->plain_value === 'sendmail' ? 'selected' : '' }}>Sendmail</option>
                        <option value="mailgun" {{ $setting->plain_value === 'mailgun' ? 'selected' : '' }}>Mailgun</option>
                        <option value="ses" {{ $setting->plain_value === 'ses' ? 'selected' : '' }}>Amazon SES</option>
                      </select>
                    @elseif(strpos($setting->key, 'password') !== false)
                      <input type="password" class="form-control" 
                             name="settings[{{ $setting->key }}]" 
                             id="setting_{{ $setting->id }}" 
                             value="{{ $setting->plain_value }}">
                    @else
                      <input type="{{ $setting->type ?? 'text' }}" class="form-control" 
                             name="settings[{{ $setting->key }}]" 
                             id="setting_{{ $setting->id }}" 
                             value="{{ $setting->plain_value }}">
                    @endif
                    @if($setting->description)
                      <small class="form-text text-muted">{{ $setting->description }}</small>
                    @endif
                  </div>
                  @endforeach
                </div>
              </div>
            </div>
            
            <!-- Payment Settings -->
            <div class="col-md-6">
              <div class="card">
                <div class="card-header">
                  <h5>Payment Settings</h5>
                </div>
                <div class="card-body">
                  @foreach($settings->where('group', 'payment') as $setting)
                  <div class="form-group">
                    <label for="setting_{{ $setting->id }}">{{ ucwords(str_replace('_', ' ', $setting->key)) }}</label>
                    @if($setting->type === 'boolean')
                      <select class="form-control" name="settings[{{ $setting->key }}]" id="setting_{{ $setting->id }}">
                        <option value="1" {{ $setting->plain_value == '1' ? 'selected' : '' }}>Enabled</option>
                        <option value="0" {{ $setting->plain_value == '0' ? 'selected' : '' }}>Disabled</option>
                      </select>
                    @elseif(strpos($setting->key, 'secret') !== false || strpos($setting->key, 'key') !== false)
                      <input type="password" class="form-control" 
                             name="settings[{{ $setting->key }}]" 
                             id="setting_{{ $setting->id }}" 
                             value="{{ $setting->plain_value }}">
                    @else
                      <input type="{{ $setting->type ?? 'text' }}" class="form-control" 
                             name="settings[{{ $setting->key }}]" 
                             id="setting_{{ $setting->id }}" 
                             value="{{ $setting->plain_value }}">
                    @endif
                    @if($setting->description)
                      <small class="form-text text-muted">{{ $setting->description }}</small>
                    @endif
                  </div>
                  @endforeach
                </div>
              </div>
            </div>
          </div>
          
        </form>
        
        <!-- System Information -->
        <div class="row mt-3">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h5>System Information</h5>
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="col-md-3">
                    <strong>PHP Version:</strong><br>
                    <span class="badge badge-info">{{ PHP_VERSION }}</span>
                  </div>
                  <div class="col-md-3">
                    <strong>Laravel Version:</strong><br>
                    <span class="badge badge-success">{{ app()->version() }}</span>
                  </div>
                  <div class="col-md-3">
                    <strong>Database:</strong><br>
                    <span class="badge badge-primary">{{ config('database.default') }}</span>
                  </div>
                  <div class="col-md-3">
                    <strong>Environment:</strong><br>
                    <span class="badge badge-{{ app()->environment() === 'production' ? 'danger' : 'warning' }}">
                      {{ ucfirst(app()->environment()) }}
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
  $('#saveAllSettings').on('click', function() {
    $('#settingsForm').submit();
  });
});
</script>
@endpush
