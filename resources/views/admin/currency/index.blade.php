<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-app-dark leading-tight">
            Currency Rates
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto space-y-6 sm:px-6 lg:px-8">
            @if ($error)
                <div class="alert alert-danger mb-4" role="alert">
                    {{ $error }}
                </div>
            @endif

            <div class="bg-app-card border border-app-border px-6 py-10 shadow-lg sm:rounded-lg">
                <form method="GET" action="{{ route('admin.currency.index') }}">
                    <h3 class="text-center text-lg font-semibold text-app-muted">
                        We Use Mid-Market Exchange Rates
                    </h3>

                    <div class="mt-14 grid gap-6 lg:grid-cols-[1fr_1.35fr_auto_1.35fr] lg:items-end">
                        <div>
                            <label for="amount" class="mb-3 block text-sm font-bold text-app-muted">Amount</label>
                            <input id="amount" name="amount" type="number" step="0.01" min="0" class="block h-14 w-full rounded-md border border-app-border bg-white px-4 text-base text-app-dark shadow-md focus:border-primary focus:ring-primary" value="{{ $amount }}">
                            <x-input-error :messages="$errors->get('amount')" class="mt-2" />
                        </div>

                        <div>
                            <label class="mb-3 block text-sm font-bold text-app-muted">From</label>
                            <input type="hidden" id="from_currency" name="from_currency" value="{{ $fromCurrency }}">

                            <div class="currency-dropdown relative" data-target="from_currency">
                                <button type="button" class="currency-dropdown-button flex h-14 w-full items-center justify-between rounded-md border border-app-border bg-white px-4 text-left text-base text-app-dark shadow-md focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary">
                                    <span class="currency-selected-label">{{ $currencyLabels[$fromCurrency] }}</span>
                                    <span class="text-app-muted">⌄</span>
                                </button>

                                <div class="currency-dropdown-menu absolute left-0 top-[4.25rem] z-40 hidden w-full overflow-hidden rounded-md border border-app-border bg-white shadow-xl">
                                    <div class="border-b border-app-border p-3">
                                        <input type="text" class="currency-search block w-full rounded-md border-app-border px-3 py-2 text-sm focus:border-primary focus:ring-primary" placeholder="Search currencies...">
                                    </div>

                                    <div class="max-h-80 overflow-y-auto py-1">
                                        @foreach ($currencies as $currency)
                                            <button type="button" class="currency-option flex w-full items-center gap-3 px-4 py-2.5 text-left text-sm hover:bg-blue-50 {{ $fromCurrency === $currency ? 'bg-blue-100 text-primary' : 'text-app-dark' }}" data-value="{{ $currency }}" data-label="{{ $currencyLabels[$currency] }}">
                                                <span class="font-semibold">{{ $currency }}</span>
                                                <span class="currency-option-label text-app-muted">{{ $currencyLabels[$currency] }}</span>
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <x-input-error :messages="$errors->get('from_currency')" class="mt-2" />
                        </div>

                        <button type="button" id="swap-currencies" class="mx-auto flex h-14 w-20 items-center justify-center rounded-full border border-app-border bg-white text-lg font-bold text-primary shadow-md">
                            &lt;-&gt;
                        </button>

                        <div>
                            <label class="mb-3 block text-sm font-bold text-app-muted">To</label>
                            <input type="hidden" id="to_currency" name="to_currency" value="{{ $toCurrency }}">

                            <div class="currency-dropdown relative" data-target="to_currency">
                                <button type="button" class="currency-dropdown-button flex h-14 w-full items-center justify-between rounded-md border border-app-border bg-white px-4 text-left text-base text-app-dark shadow-md focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary">
                                    <span class="currency-selected-label">{{ $currencyLabels[$toCurrency] }}</span>
                                    <span class="text-app-muted">⌄</span>
                                </button>

                                <div class="currency-dropdown-menu absolute left-0 top-[4.25rem] z-40 hidden w-full overflow-hidden rounded-md border border-app-border bg-white shadow-xl">
                                    <div class="border-b border-app-border p-3">
                                        <input type="text" class="currency-search block w-full rounded-md border-app-border px-3 py-2 text-sm focus:border-primary focus:ring-primary" placeholder="Search currencies...">
                                    </div>

                                    <div class="max-h-80 overflow-y-auto py-1">
                                        @foreach ($currencies as $currency)
                                            <button type="button" class="currency-option flex w-full items-center gap-3 px-4 py-2.5 text-left text-sm hover:bg-blue-50 {{ $toCurrency === $currency ? 'bg-blue-100 text-primary' : 'text-app-dark' }}" data-value="{{ $currency }}" data-label="{{ $currencyLabels[$currency] }}">
                                                <span class="font-semibold">{{ $currency }}</span>
                                                <span class="currency-option-label text-app-muted">{{ $currencyLabels[$currency] }}</span>
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <x-input-error :messages="$errors->get('to_currency')" class="mt-2" />
                        </div>
                    </div>

                    <div class="mt-12 flex justify-center">
                        <button type="submit" class="min-w-56 rounded-full bg-primary px-8 py-3 text-sm font-semibold uppercase tracking-widest text-white shadow-md hover:bg-blue-700">
                            Convert
                        </button>
                    </div>

                    <div class="mt-8 text-center">
                        <p class="text-2xl font-bold text-primary">
                            {{ number_format($amount, 2) }} {{ $fromCurrency }} = {{ number_format($convertedAmount, 4) }} {{ $toCurrency }}
                        </p>
                        <p class="mt-1 text-sm text-app-muted">
                            1.00 {{ $fromCurrency }} = {{ number_format($selectedRate, 6) }} {{ $toCurrency }}
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function () {
                $('.currency-dropdown-button').on('click', function () {
                    const dropdown = $(this).closest('.currency-dropdown');

                    $('.currency-dropdown').not(dropdown).find('.currency-dropdown-menu').addClass('hidden');
                    dropdown.find('.currency-dropdown-menu').toggleClass('hidden');
                    dropdown.find('.currency-search').val('').trigger('input').focus();
                });

                $('.currency-search').on('input', function () {
                    const search = $(this).val().toLowerCase();
                    const menu = $(this).closest('.currency-dropdown-menu');

                    menu.find('.currency-option').each(function () {
                        const text = $(this).data('label').toLowerCase();
                        $(this).toggleClass('hidden', ! text.includes(search));
                    });
                });

                $('.currency-option').on('click', function () {
                    const dropdown = $(this).closest('.currency-dropdown');
                    const target = dropdown.data('target');
                    const value = $(this).data('value');
                    const label = $(this).data('label');

                    $('#' + target).val(value);
                    dropdown.find('.currency-selected-label').text(label);
                    dropdown.find('.currency-option').removeClass('bg-blue-100 text-primary').addClass('text-app-dark');
                    $(this).addClass('bg-blue-100 text-primary').removeClass('text-app-dark');
                    dropdown.find('.currency-dropdown-menu').addClass('hidden');
                });

                $('#swap-currencies').on('click', function () {
                    const fromValue = $('#from_currency').val();
                    const toValue = $('#to_currency').val();
                    const fromLabel = $('[data-target="from_currency"] .currency-selected-label').text();
                    const toLabel = $('[data-target="to_currency"] .currency-selected-label').text();

                    $('#from_currency').val(toValue);
                    $('#to_currency').val(fromValue);
                    $('[data-target="from_currency"] .currency-selected-label').text(toLabel);
                    $('[data-target="to_currency"] .currency-selected-label').text(fromLabel);

                    $(this).closest('form').submit();
                });

                $(document).on('click', function (event) {
                    if (! $(event.target).closest('.currency-dropdown').length) {
                        $('.currency-dropdown-menu').addClass('hidden');
                    }
                });
            });
        </script>
    @endpush
</x-app-layout>
