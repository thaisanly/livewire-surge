<div>
    <h1 class="text-2xl font-semibold text-gray-900">Dashboard</h1>

    <div class="py-4">
        <div class="mt-2 flex justify-between">
            <div class="flex w-3/4">
                <label for="search" class="sr-only">Search</label>

                <x-input.text id="search" wire:model="search" placeholder="Search Transactions..." />

                <x-button.link wire:click="$toggle('showFilters')" class="ml-4 text-sm">@if ($showFilters) Hide @endif Advanced Search...</x-button.link>

            </div>

            <div class="w-1/4 flex justify-end space-x-2">
                <x-dropdown label="Bulk Actions">
                    <x-dropdown.item type="button" wire:click="exportChecked" class="flex items-center space-x-1">
                        <x-icon.download class="text-cool-gray-400" /> <span>Export CSV</span>
                    </x-dropdown.item>

                    <x-dropdown.item type="button" wire:click="confirmDelete" class="flex items-center space-x-1">
                        <x-icon.trash  class="text-cool-gray-400" /> <span>Delete</span>
                    </x-dropdown.item>
                </x-dropdown>

                <x-button.primary wire:click="create" class="flex items-center space-x-1">
                    <x-icon.plus/> <span class="font-medium tracking-wide">New</span>
                </x-button.primary>
            </div>
        </div>

        <div>
            @if ($showFilters)
                <div class="my-4 bg-cool-gray-200 rounded p-4 flex relative">
                    <div class="w-1/2 pr-2 space-y-3">
                        <x-input.group inline label="Status" for="filter-status" :error="$errors->first('filters.status')">
                            <x-input.select wire:model="filters.status" id="filter-status">
                                <option value="">Select Status...</option>
                                <option value="processing">Processing</option>
                                <option value="success">Success</option>
                                <option value="failed">Failed</option>
                            </x-input.select>
                        </x-input.group>

                        <x-input.group inline label="Above Amount" for="filter-min" :error="$errors->first('filters.amount-min')">
                            <x-input.money wire:model.lazy="filters.amount-min" id="filter-min" />
                        </x-input.group>

                        <x-input.group inline label="Below Amount" for="filter-max" :error="$errors->first('filters.amount-max')">
                            <x-input.money wire:model.lazy="filters.amount-max" id="filter-max" />
                        </x-input.group>
                    </div>

                    <div class="w-1/2 pl-2 space-y-3">
                        <x-input.group inline label="After Date" for="filter-after" :error="$errors->first('filters.date-after')">
                            <x-input.date wire:model="filters.date-after" id="filter-after" placeholder="Before..." />
                        </x-input.group>

                        <x-input.group inline label="Before Date" for="filter-before" :error="$errors->first('filters.date-before')">
                            <x-input.date wire:model="filters.date-before" id="filter-before" placeholder="After..." />
                        </x-input.group>

                        <button wire:click="resetFilters" class="absolute bottom-0 focus:outline-none focus:underline font-medium px-6 py-4 right-0 text-cool-gray-600 text-sm">Reset Filters</button>
                    </div>
                </div>
            @endif
        </div>

        <div class="flex flex-col mt-4">
            <x-table>
                <x-slot name="head">
                    <x-table.heading class="pr-0 w-8"><x-input.checkbox wire:model="checkedPage"/></x-table.heading>
                    <x-table.heading wire:click="sortBy('title')" sortable :direction="$sortField === 'title' ? $sortDirection : null">Transaction</x-table.heading>
                    <x-table.heading wire:click="sortBy('amount')" sortable :direction="$sortField === 'amount' ? $sortDirection : null">Amount</x-table.heading>
                    <x-table.heading wire:click="sortBy('status')" sortable :direction="$sortField === 'status' ? $sortDirection : null">Status</x-table.heading>
                    <x-table.heading wire:click="sortBy('created_at')" sortable :direction="$sortField === 'created_at' ? $sortDirection : null">Date</x-table.heading>
                    <x-table.heading></x-table.heading>
                </x-slot>

                <x-slot name="body">
                    @if ($checkedPage)
                        <x-table.row class="bg-cool-gray-100">
                            <x-table.cell colspan="6">
                                @if ($checkedAll)
                                    You've currently selected all <strong>{{ $transactions->total() }}</strong> results.
                                @else
                                    You've currently selected <strong>{{ $transactions->count() }}</strong> results, would you like to select all <strong>{{ $transactions->total() }}</strong>?
                                    <button wire:click="checkAll" class="font-medium ml-2 text-blue-600 tracking-wide underline">Select All</button>
                                @endif
                            </x-table.cell>
                        </x-table.row>
                    @endif

                    @forelse ($transactions as $transaction)
                        <x-table.row wire:key="table.row.{{ $transaction->id }}" wire:loading.class.delay="opacity-50">
                            <x-table.cell class="pr-0">
                                <x-input.checkbox wire:model="checked" value="{{ $transaction->id }}"/>
                            </x-table.cell>

                            <x-table.cell class="max-w-0 w-full">
                                <div class="flex">
                                    <a href="#" class="group inline-flex space-x-2 truncate text-sm leading-5">
                                        <x-icon.cash class="text-cool-gray-400 group-hover:text-cool-gray-500" />

                                        <p class="text-cool-gray-500 truncate group-hover:text-cool-gray-900 transition ease-in-out duration-150">
                                            {{ $transaction->title }}
                                        </p>
                                    </a>
                                </div>
                            </x-table.cell>

                            <x-table.cell>
                                <span class="text-cool-gray-900 font-medium">${{ $transaction->amount }} </span>USD
                            </x-table.cell>

                            <x-table.cell>
                                @php $color = ['processing' => 'indigo', 'success' => 'green', 'failed' => 'red'][$transaction->status] ?? 'gray'; @endphp

                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium leading-4 bg-{{ $color }}-100 text-{{ $color }}-800 capitalize">
                                    {{ $transaction->status }}
                                </span>
                            </x-table.cell>

                            <x-table.cell>
                                {{ $transaction->created_at->format('M d, Y') }}
                            </x-table.cell>

                            <x-table.cell>
                                <x-button.link wire:click="edit({{ $transaction->id }})">Edit</x-button.link>
                            </x-table.cell>
                        </x-table.row>
                    @empty
                        <x-table.row>
                            <x-table.cell colspan="6">
                                <div class="flex items-center justify-center py-12 space-x-2 text-cool-gray-400">
                                    <x-icon.inbox class="w-8 h-8" />
                                    <span class="font-medium text-2xl">
                                        No transactions found...
                                    </span>
                                </div>
                            </x-table.cell>
                        </x-table.row>
                    @endforelse
                </x-slot>
            </x-table>

            <div class="py-4">
                {{ $transactions->links() }}
            </div>
        </div>
    </div>

    <form wire:submit.prevent="save">
        <x-modal.dialog wire:model.defer="showEditModal">
            <x-slot name="title">
                Edit Transaction
            </x-slot>

            <x-slot name="content">
                @if ($editing)
                    <div>
                        <x-input.group label="Title" for="title" :error="$errors->first('editing.title')">
                            <x-input.text wire:model="editing.title" id="title" autofocus/>
                        </x-input.group>

                        <x-input.group label="Status" for="status" :error="$errors->first('editing.status')">
                            <x-input.select wire:model="editing.status" id="status">
                                <option value="processing">Processing</option>
                                <option value="success">Success</option>
                                <option value="failed">Failed</option>
                            </x-input.select>
                        </x-input.group>

                        <x-input.group label="Amount" for="amount" :error="$errors->first('editing.amount')">
                            <x-input.money wire:model="editing.amount" id="amount" />
                        </x-input.group>

                        <x-input.group label="Date" for="date" :error="$errors->first('editing.date')">
                            <x-input.date wire:model="editing.date" id="date" placeholder="MM/DD/YYYY" />
                        </x-input.group>
                    </div>
                @endif
            </x-slot>

            <x-slot name="footer">
                <x-button.secondary wire:click="$set('showEditModal', false)">Cancel</x-button.secondary>

                <x-button.primary type="submit">Save</x-button.primary>
            </x-slot>
        </x-modal.dialog>
    </form>

    <form wire:submit.prevent="deleteChecked">
        <x-modal.confirmation wire:model.defer="showDeleteModal">
            <x-slot name="title">
                Are you sure?
            </x-slot>

            <x-slot name="content">
                This action is irreversible.
            </x-slot>

            <x-slot name="footer">
                <x-button.secondary wire:click="$set('showDeleteModal', false)">Cancel</x-button.secondary>

                <x-button.primary type="submit">Delete</x-button.primary>
            </x-slot>
        </x-modal.dialog>
    </form>
</div>
