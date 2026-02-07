<div class="row g-3">
    @for ($i = 0; $i < 24; $i++)
        @php
            $time = str_pad($i, 2, '0', STR_PAD_LEFT) . ':00:00';
            $booking = $bookings->get($time);
            $isOccupied = !is_null($booking);
            $displayTime = \Carbon\Carbon::createFromTime($i, 0)->format('g:i A');
            
            $remaining = 0;
            $totalPaid = 0;

            if ($isOccupied) {
                // حساب سعر الساعة
                $priceEntry = $booking->field->prices
                    ->where('from_time', '<=', $time)
                    ->where('to_time', '>', $time)
                    ->first();
                $totalPrice = $priceEntry ? $priceEntry->price : 0;

                // حساب إجمالي المدفوع من جدول المدفوعات
                $totalPaid = $booking->payments->sum('amount');
                $remaining = max(0, $totalPrice - $totalPaid);
            }
        @endphp

        <div class="col-md-3 col-6">
            <div class="card {{ $isOccupied ? 'bg-danger border-danger' : 'bg-success border-success' }} text-white shadow-sm h-100">
                <div class="card-body text-center p-2 d-flex flex-column justify-content-between">
                    <div>
                        <h6 class="fw-bold mb-1">{{ $displayTime }}</h6>
                        @if($isOccupied)
                            <div class="fw-bold" style="font-size: 0.85rem;">{{ $booking->user_name }}</div>
                            <div style="font-size: 0.75rem;">مدفوع: {{ $totalPaid }} | باقٍ: {{ $remaining }}</div>
                        @else
                            <small class="d-block mb-2">متاح</small>
                            <button class="btn btn-light btn-sm fw-bold w-100" onclick="openBookingModal('{{ $time }}')">احجز الآن</button>
                        @endif
                    </div>

                    @if($isOccupied)
                        <div class="mt-2 pt-2 border-top border-white border-opacity-25">
                            @if($remaining > 0)
                                <form action="{{ route('admin.bookings.collect', $booking->id) }}" method="POST" class="d-block mb-1">
                                    @csrf
                                    <input type="hidden" name="amount_paid" value="{{ $remaining }}">
                                    <button type="submit" class="btn btn-sm btn-light text-danger fw-bold w-100" 
                                            onclick="return confirm('هل استلمت مبلغ {{ $remaining }} ج.م؟')">
                                        تحصيل {{ $remaining }} ج
                                    </button>
                                </form>
                            @else
                                <span class="badge bg-white text-success w-100 mb-1">خالص <i class="fas fa-check-circle"></i></span>
                            @endif
                            
                            <div class="d-flex justify-content-around align-items-center">
                                {{-- <button type="button" class="btn btn-link btn-sm text-white p-0" 
                                        onclick='openEditModal({!! json_encode($booking) !!})'>
                                    <i class="fas fa-edit"></i> <small>تعديل</small>
                                </button> --}}
                                
                                <button type="button" class="btn btn-link btn-sm text-white p-0" 
                                        onclick="if(confirm('إلغاء الحجز؟')) { document.getElementById('delete-form-{{$booking->id}}').submit(); }">
                                    <i class="fas fa-trash-alt text-white-50"></i> <small class="text-white-50">إلغاء</small>
                                </button>
                                <form id="delete-form-{{$booking->id}}" action="{{ route('admin.bookings.destroy', $booking->id) }}" method="POST" style="display:none;">
                                    @csrf @method('DELETE')
                                </form>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endfor
</div>