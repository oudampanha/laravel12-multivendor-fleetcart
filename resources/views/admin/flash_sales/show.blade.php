@extends('admin.layouts.master_layout')

@section('pageTitle', 'Flash Sale Details')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Flash Sale Details</h4>
        <div class="card-tools">
          <a href="{{ route('admin.flash-sales.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
          </a>
          @if(Route::has('admin.flash-sales.edit'))
            <a href="{{ route('admin.flash-sales.edit', $flashSale->id) }}" class="btn btn-warning">
              <i class="fas fa-edit"></i> Edit
            </a>
          @endif
        </div>
      </div>
      <div class="card-body">
        <table class="table table-borderless">
          <tr>
            <th width="200">ID:</th>
            <td>{{ $flashSale->id ?? 'N/A' }}</td>
          </tr>
          @php
            $attrs = method_exists($flashSale ?? null, 'getAttributes') ? $flashSale->getAttributes() : (array)($flashSale ?? []);
          @endphp
          @foreach($attrs as $key => $value)
            @continue(in_array($key, ['id', 'password', 'remember_token']))
            <tr>
              <th>{{ ucwords(str_replace('_', ' ', $key)) }}:</th>
              <td>
                @if(is_array($value) || is_object($value))
                  <pre>{{ json_encode($value, JSON_PRETTY_PRINT) }}</pre>
                @else
                  {{ $value ?? 'N/A' }}
                @endif
              </td>
            </tr>
          @endforeach
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
