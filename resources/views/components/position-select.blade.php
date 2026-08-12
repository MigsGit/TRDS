@props([
    'name',
    'id',
    'label' => '',
])

@php
    $positionOptions = [
        ['value' => '', 'label' => 'Select Position', 'disabled' => true, 'selected' => true],
        ['value' => 'ALL', 'label' => 'Display All'],
        ['value' => 'Operator', 'label' => 'Operator'],
        ['value' => 'Inspector', 'label' => 'Inspector'],
        // ['value' => 'CN', 'label' => 'CN'],
        // ['value' => 'CNF3', 'label' => 'CN-F3'],
        // ['value' => 'PPDCN', 'label' => 'PPD-CN'],
        // ['value' => 'PPDTS', 'label' => 'PPD-TS'],
        // ['value' => 'PPDF3', 'label' => 'PPD-F3'],
        // ['value' => 'YF', 'label' => 'YF'],
    ];
@endphp

<div class="form-group">
    <label for="{{ $id }}">{{ $label }}</label>
    <select class="form-control select2bs4" style="width: 100%;" name="{{ $name }}" id="{{ $id }}">
        @foreach ($positionOptions as $option)
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
