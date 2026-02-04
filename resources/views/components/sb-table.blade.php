@props([
    'headers' => [],
    'striped' => true,
    'hover' => true,
])

@php
    $tableClasses = 'min-w-full divide-y divide-gray-200';
    $tbodyClasses = $striped ? 'bg-white divide-y divide-gray-200' : 'bg-white divide-y divide-gray-200';
@endphp

<div {{ $attributes->only('class') }} class="overflow-x-auto">
    <div class="inline-block min-w-full align-middle">
        <div class="overflow-hidden border border-gray-200 rounded-lg shadow-sm">
            <table class="{{ $tableClasses }}">
                @if(count($headers) > 0)
                    <thead class="bg-gray-50">
                        <tr>
                            @foreach($headers as $header)
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    {{ $header }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                @endif
                
                <tbody class="{{ $tbodyClasses }}">
                    @if(isset($rows) && $rows->count() > 0)
                        @foreach($rows as $row)
                            <tr class="{{ $hover ? 'hover:bg-gray-50' : '' }} transition-colors">
                                {{ $row }}
                            </tr>
                        @endforeach
                    @else
                        {{ $slot }}
                    @endif
                </tbody>
            </table>
            
            @if(isset($rows) && $rows->count() === 0)
                <x-sb-empty-state 
                    icon="table"
                    message="No records found"
                    description="There are no items to display in this table."
                />
            @endif
        </div>
    </div>
</div>
