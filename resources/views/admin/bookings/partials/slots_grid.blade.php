<div class="row g-3">
    @for ($i = 0; $i < 24; $i++)
        @php
            $time = str_pad($i, 2, '0', STR_PAD_LEFT) . ':00:00';
            $booking = $bookings->get($time);
            $isOccupied = !is_null($booking);
            $displayTime = \Carbon\Carbon::createFromTime($i, 0)->format('g:i A');
            
            // حساب المتبقي (لو عندك سعر ثابت أو جدول أسعار)
            $price = 200; // مثال: سعر الساعة 200
            $remaining = $isOccupied ? ($price - $booking->deposit) : 0;
        @endphp

        <div class="col-md-3 col-6">
            <div class="card {{ $isOccupied ? 'bg-danger border-danger' : 'bg-success border-success' }} text-white shadow-sm" 
                 onclick="{{ $isOccupied ? "openEditModal('".json_encode($booking)."')" : "openBookingModal('$time')" }}">
                <div class="card-body text-center p-2">
                    <h6 class="fw-bold mb-1">{{ $displayTime }}</h6>
                    @if($isOccupied)
                        <div style="font-size: 0.85rem;">{{ $booking->user_name }}</div>
                        <div style="font-size: 0.75rem;">دفع: {{ $booking->deposit }} | باقي: {{ $remaining }}</div>
                    @else
                        <small>متاح للحجز</small>
                    @endif
                </div>
            </div>
        </div>
    @endfor
</div>