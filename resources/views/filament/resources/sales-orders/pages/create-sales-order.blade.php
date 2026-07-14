@php
    $productsGrouped = [];
    foreach (App\Models\ProductPresentation::with('product.category')->get() as $pres) {
        $cat = $pres->product->category?->name ?? 'Sin categoría';
        $productsGrouped[$cat][$pres->id] = "{$pres->product->name} — {$pres->presentation_type} {$pres->format}";
    }
@endphp

<x-filament-panels::page>
    <form wire:submit="create" class="space-y-6">
        {{ $this->form }}

        <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
            <h3 class="mb-4 text-lg font-medium text-gray-900 dark:text-white">Agregar producto</h3>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Producto</label>
                    <select wire:model.live="addPresentationId"
                            class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                        <option value="">Seleccioná...</option>
                        @foreach($productsGrouped as $cat => $options)
                            <optgroup label="{{ $cat }}">
                                @foreach($options as $id => $label)
                                    <option value="{{ $id }}">{{ $label }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Cantidad</label>
                    <input type="number" step="0.001" min="0.001" wire:model.live="addQuantity"
                           class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Precio ($)</label>
                    <input type="number" step="0.01" min="0" wire:model.live="addUnitPrice"
                           class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                </div>
            </div>

            <div class="mt-3">
                <button type="button" wire:click="addItem"
                        class="inline-flex items-center gap-2 rounded-lg bg-primary-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900">
                    + Agregar
                </button>
            </div>
        </div>

        @if(count($items) > 0)
            <div class="overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Producto</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Presentación</th>
                            <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Cant.</th>
                            <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Precio $</th>
                            <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Subtotal</th>
                            <th class="px-4 py-3 text-center" style="width:50px"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-900">
                        @foreach($items as $index => $item)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-900 dark:text-white">{{ $item['product_name'] }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $item['presentation_label'] }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-right text-sm text-gray-900 dark:text-white">{{ $item['quantity'] }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-right text-sm text-gray-900 dark:text-white">${{ number_format($item['unit_price_usd'], 2) }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-right text-sm font-medium text-gray-900 dark:text-white">${{ number_format($item['subtotal_usd'], 2) }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-center">
                                    <button type="button" wire:click="removeItem({{ $index }})"
                                            class="inline-flex items-center rounded-lg bg-danger-100 px-2 py-1 text-sm text-danger-700 hover:bg-danger-200 dark:bg-danger-900/30 dark:text-danger-400"
                                            title="Eliminar">
                                        ✕
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <td colspan="4" class="px-4 py-3 text-right text-sm font-medium text-gray-700 dark:text-gray-300">Total:</td>
                            <td class="px-4 py-3 text-right text-sm font-bold text-gray-900 dark:text-white">
                                ${{ number_format(array_sum(array_column($items, 'subtotal_usd')), 2) }}
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endif

        <div class="flex gap-4">
            <button type="submit"
                    class="inline-flex items-center gap-2 rounded-lg bg-primary-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900">
                Guardar orden
            </button>
        </div>
    </form>
</x-filament-panels::page>
