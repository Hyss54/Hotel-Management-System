@extends('layouts.app')

@section('title', 'Manage Bar Order')
@section('page_title', 'Bar Order: ' . $bar->order_number)

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header border-bottom-0 pb-0 bg-white">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Bar Order Details</h5>
                        <span
                            class="badge bg-{{ $bar->status == 'served' ? 'success' : ($bar->status == 'cancelled' ? 'danger' : 'warning') }} fs-6">
                            {{ mb_strtoupper(str_replace('_', ' ', $bar->status)) }}
                        </span>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <div class="row mb-4">
                        <div class="col-sm-6">
                            <h6 class="text-muted mb-1">Guest Information</h6>
                            <strong>{{ $bar->customer->name }}</strong><br>
                            <i class="fas fa-phone fa-fw text-muted"></i> {{ $bar->customer->phone }}
                        </div>
                        <div class="col-sm-6 text-sm-end mt-3 mt-sm-0">
                            <h6 class="text-muted mb-1">Location Details</h6>
                            @if($bar->booking)
                                <strong>Room {{ $bar->booking->room->room_number }}</strong><br>
                                Booking: {{ $bar->booking->booking_number }}
                            @else
                                <span class="text-muted">Walk-in</span>
                            @endif
                        </div>
                    </div>

                    <div class="bg-light rounded p-3 mb-4">
                        <div class="row">
                            <div class="col-12">
                                <small class="text-muted d-block">Order Placed At</small>
                                <strong>{{ $bar->created_at->format('M d, Y - H:i') }}</strong>
                                <small class="text-muted ms-3">({{ $bar->created_at->diffForHumans() }})</small>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h6 class="text-muted">Order Items</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Item Name</th>
                                        <th>Qty</th>
                                        <th>Price</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($bar->items as $item)
                                        <tr>
                                            <td>{{ $item->item_name }}</td>
                                            <td>{{ $item->quantity }}</td>
                                            <td>GH₵{{ number_format($item->unit_price, 2) }}</td>
                                            <td>GH₵{{ number_format($item->total, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    @if($bar->notes)
                        <div class="mb-4">
                            <h6 class="text-muted">Notes</h6>
                            <div class="p-3 border rounded bg-white">
                                {!! nl2br(e($bar->notes)) !!}
                            </div>
                        </div>
                    @endif

                    <div class="d-flex gap-2">
                        <form action="{{ route('bar.updateStatus', $bar) }}" method="POST"
                            class="d-flex gap-2 align-items-center">
                            @csrf
                            @method('PATCH')
                            <select name="status" class="form-select w-auto">
                                <option value="pending" {{ $bar->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="served" {{ $bar->status == 'served' ? 'selected' : '' }}>Served</option>
                                <option value="cancelled" {{ $bar->status == 'cancelled' ? 'selected' : '' }}>Cancelled
                                </option>
                            </select>
                            <button type="submit" class="btn btn-warning"><i class="fas fa-sync-alt"></i> Update
                                Status</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charges Summary -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-warning text-dark">Billing Information</div>
                <div class="card-body text-center">
                    <h4 class="text-dark mb-3">GH₵{{ number_format($bar->total_amount, 2) }}</h4>
                    @if($bar->booking)
                        <div class="alert alert-info py-2 small mb-0">
                            <i class="fas fa-info-circle"></i> This charge will be logged to Room
                            {{ $bar->booking->room->room_number }}'s final bill.
                        </div>
                    @else
                        <div class="alert alert-warning py-2 small mb-0">
                            <i class="fas fa-cash-register"></i> Walk-in order. Ensure payment is collected.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection