@extends('layouts.app')

@section('title', 'Add Bar Order')
@section('page_title', 'New Bar Order')

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('bar.store') }}" method="POST" id="barForm">
                @csrf

                <div class="row mb-4">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Guest / Customer *</label>
                        <select name="customer_id" id="customer_id" class="form-select" required>
                            <option value="">Select Guest...</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id || ($selectedBooking && $selectedBooking->customer_id == $customer->id) ? 'selected' : '' }}>
                                    {{ $customer->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Assign to Booking (Optional)</label>
                        <select name="booking_id" id="booking_id" class="form-select">
                            <option value="">Walk-in / No Booking</option>
                            @foreach($bookings as $b)
                                <option value="{{ $b->id }}" data-customer="{{ $b->customer_id }}" {{ old('booking_id') == $b->id || ($selectedBooking && $selectedBooking->id == $b->id) ? 'selected' : '' }}>
                                    Room {{ $b->room->room_number }} - {{ $b->customer->name }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Will be added to their final bill at checkout.</small>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label class="form-label d-flex justify-content-between">
                            <span>Order Items *</span>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="addItemBtn">
                                <i class="fas fa-plus"></i> Add Item
                            </button>
                        </label>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm" id="itemsTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>Item Name *</th>
                                        <th width="15%">Quantity *</th>
                                        <th width="20%">Unit Price (GH₵) *</th>
                                        <th width="20%">Notes</th>
                                        <th width="5%"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <select name="items[0][bar_item_id]"
                                                class="form-select form-select-sm item-select" required>
                                                <option value="">Select Item...</option>
                                                @foreach($barItems as $bi)
                                                    <option value="{{ $bi->id }}" data-price="{{ $bi->price }}"
                                                        data-stock="{{ $bi->stock_quantity }}">
                                                        {{ $bi->name }} (Stock: {{ $bi->stock_quantity }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            <input type="hidden" name="items[0][item_name]" class="item-name-hidden">
                                        </td>
                                        <td>
                                            <input type="number" name="items[0][quantity]"
                                                class="form-control form-control-sm quantity-input" min="1" value="1"
                                                required>
                                            <small class="text-danger stock-warning d-none">Insufficient stock!</small>
                                        </td>
                                        <td>
                                            <input type="number" name="items[0][unit_price]"
                                                class="form-control form-control-sm price-input" step="0.01" min="0"
                                                value="0.00" required>
                                        </td>
                                        <td>
                                            <input type="text" name="items[0][notes]" class="form-control form-control-sm">
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline-danger remove-item"
                                                disabled><i class="fas fa-times"></i></button>
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="2" class="text-end">Total Amount Estimated:</th>
                                        <th colspan="3">
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text">GH₵</span>
                                                <input type="text" id="totalAmount"
                                                    class="form-control form-control-sm bg-white" readonly value="0.00">
                                            </div>
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-glass-martini-alt"></i> Submit
                            Order</button>
                        <a href="{{ route('bar.index') }}" class="btn btn-light ms-2">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let itemIndex = 1;

            const barItems = @json($barItems);

            function calculateTotal() {
                let total = 0;
                document.querySelectorAll('#itemsTable tbody tr').forEach(row => {
                    const qty = parseFloat(row.querySelector('.quantity-input').value) || 0;
                    const price = parseFloat(row.querySelector('.price-input').value) || 0;
                    total += (qty * price);

                    // Check stock
                    const select = row.querySelector('.item-select');
                    const selectedOption = select.options[select.selectedIndex];
                    const stock = parseFloat(selectedOption.getAttribute('data-stock')) || 0;
                    const warning = row.querySelector('.stock-warning');
                    if (qty > stock && select.value !== "") {
                        warning.classList.remove('d-none');
                    } else {
                        warning.classList.add('d-none');
                    }
                });
                document.getElementById('totalAmount').value = total.toFixed(2);
            }

            function updateItemInfo(row) {
                const select = row.querySelector('.item-select');
                const selectedOption = select.options[select.selectedIndex];
                const priceInput = row.querySelector('.price-input');
                const nameHidden = row.querySelector('.item-name-hidden');

                if (select.value) {
                    priceInput.value = selectedOption.getAttribute('data-price');
                    nameHidden.value = selectedOption.text.split(' (Stock:')[0].trim();
                } else {
                    priceInput.value = '0.00';
                    nameHidden.value = '';
                }
                calculateTotal();
            }

            document.getElementById('addItemBtn').addEventListener('click', function () {
                const tbody = document.querySelector('#itemsTable tbody');
                const tr = document.createElement('tr');
                let options = '<option value="">Select Item...</option>';
                barItems.forEach(item => {
                    options += `<option value="${item.id}" data-price="${item.price}" data-stock="${item.stock_quantity}">${item.name} (Stock: ${item.stock_quantity})</option>`;
                });

                tr.innerHTML = `
                            <td>
                                <select name="items[${itemIndex}][bar_item_id]" class="form-select form-select-sm item-select" required>
                                    ${options}
                                </select>
                                <input type="hidden" name="items[${itemIndex}][item_name]" class="item-name-hidden">
                            </td>
                            <td>
                                <input type="number" name="items[${itemIndex}][quantity]" class="form-control form-control-sm quantity-input" min="1" value="1" required>
                                <small class="text-danger stock-warning d-none">Insufficient stock!</small>
                            </td>
                            <td>
                                <input type="number" name="items[${itemIndex}][unit_price]" class="form-control form-control-sm price-input" step="0.01" min="0" value="0.00" required>
                            </td>
                            <td>
                                <input type="text" name="items[${itemIndex}][notes]" class="form-control form-control-sm">
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-outline-danger remove-item"><i class="fas fa-times"></i></button>
                            </td>
                        `;
                tbody.appendChild(tr);
                itemIndex++;

                const rows = document.querySelectorAll('#itemsTable tbody tr');
                rows.forEach(row => {
                    row.querySelector('.remove-item').disabled = rows.length === 1;
                });

                calculateTotal();
            });

            document.getElementById('itemsTable').addEventListener('change', function (e) {
                if (e.target.classList.contains('item-select')) {
                    updateItemInfo(e.target.closest('tr'));
                }
            });

            document.getElementById('itemsTable').addEventListener('click', function (e) {
                if (e.target.closest('.remove-item')) {
                    const rows = document.querySelectorAll('#itemsTable tbody tr');
                    if (rows.length > 1) {
                        e.target.closest('tr').remove();
                        calculateTotal();
                    }
                    const remainingRows = document.querySelectorAll('#itemsTable tbody tr');
                    remainingRows.forEach(row => {
                        row.querySelector('.remove-item').disabled = remainingRows.length === 1;
                    });
                }
            });

            document.getElementById('itemsTable').addEventListener('input', function (e) {
                if (e.target.classList.contains('quantity-input') || e.target.classList.contains('price-input')) {
                    calculateTotal();
                }
            });

            const bookingSelect = document.getElementById('booking_id');
            const customerSelect = document.getElementById('customer_id');

            if (bookingSelect && customerSelect) {
                bookingSelect.addEventListener('change', function () {
                    const selectedOption = this.options[this.selectedIndex];
                    const customerId = selectedOption.getAttribute('data-customer');
                    if (customerId) {
                        customerSelect.value = customerId;
                    }
                });
            }
        });
    </script>
@endpush