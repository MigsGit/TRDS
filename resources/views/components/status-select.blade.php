@props([
    'name',
    'id',
    'label' => '',
])

@php
    $statusOptions = [
        ['value' => '', 'label' => 'Select Status', 'disabled' => true, 'selected' => true],
        ['value' => 'ALL', 'label' => 'Display All'],
        ['value' => 'FORAPP', 'label' => 'PENDING'],
        ['value' => 'OK', 'label' => 'CLOSED'],
        // ['value' => 'MYAPPROVAL', 'label' => 'FOR MY APPROVAL'],
    ];
@endphp

<div class="form-group">
    <label for="{{ $id }}">{{ $label }}</label>
    <select class="form-control select2bs4" style="width: 100%;" name="{{ $name }}" id="{{ $id }}">
        @foreach ($statusOptions as $option)
            <option
                value="{{ $option['value'] }}"
                @if (!empty($option['disabled'])) disabled @endif
                @if (!empty($option['selected'])) selected @endif
            >
                {{ $option['label'] }}
            </option>
        @endforeach
    </select>
</div>
