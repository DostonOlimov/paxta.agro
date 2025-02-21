<tr style="background-color: #90aec6 !important;">
    @foreach ($filters as $filter)
        @if(!empty($filter))
            <td>
                @if ($filter['type'] === 'select')
                    <select class="form-control {{ $filter['class'] ?? '' }}" name="{{ $filter['name'] }}">
                        <option value="" selected>{{ $filter['placeholder'] ?? 'Tanlang' }}</option>
                        @foreach ($filter['options'] as $key => $value)
                            <option value="{{ $key }}" {{ isset($filterValues[$filter['name']]) && $filterValues[$filter['name']] == $key ? 'selected' : '' }}>
                                {{ $value }}
                            </option>
                        @endforeach
                    </select>
                @elseif ($filter['type'] === 'text')
                    <form>
                        <input type="text" class="form-control {{ $filter['class'] ?? '' }}" name="{{ $filter['name'] }}" value="{{ $filterValues[$filter['fname']] ?? '' }}" placeholder="{{ $filter['placeholder'] ?? '' }}">
                    </form>
                @endif
            </td>
        @else
            <td></td>
        @endif
    @endforeach
</tr>
