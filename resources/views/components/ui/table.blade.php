@props(['head' => null])

<div class="table-wrapper">
    <table {{ $attributes->class(['table']) }}>
        @if ($head)
            <thead>
                <tr>{{ $head }}</tr>
            </thead>
        @endif

        <tbody>
            {{ $slot }}
        </tbody>
    </table>
</div>
