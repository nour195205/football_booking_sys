<div class="slots-grid">
    @for ($i = 0; $i < 24; $i++)
        @php
            // تنسيق الوقت ليطابق قاعدة البيانات (00:00:00)
            $time = str_pad($i, 2, '0', STR_PAD_LEFT) . ':00:00';
            
            // البحث عن حجز في هذا الوقت
            $booking = $bookings->get($time);
            $isOccupied = !is_null($booking);
            
            // تنسيق الوقت للعرض (1:00 PM)
            $displayTime = \Carbon\Carbon::createFromTime($i, 0)->format('g:i A');
        @endphp

        <div class="slot-item">
            <div class="card {{ $isOccupied ? 'bg-occupied' : 'bg-available' }} shadow-sm border-0" 
                 onclick="{{ $isOccupied ? '' : "openBookingModal('$time')" }}">
                
                <div class="card-body p-2 text-center">
                    <div class="slot-time fw-bold">{{ $displayTime }}</div>
                    
                    @if($isOccupied)
                        <div class="slot-status">
                            <i class="fas fa-lock small me-1"></i> محجوز
                        </div>
                        <div class="booked-name mt-1">{{ $booking->user_name }}</div>
                    @else
                        <div class="slot-status">
                            <i class="fas fa-check-circle small me-1"></i> متاح
                        </div>
                        <div class="click-to-book mt-1">اضغط للحجز</div>
                    @endif
                </div>
            </div>
        </div>
    @endfor
</div>

<style>
    /* تنسيقات مخصصة للموبايل لليوزر */
    .slots-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr); /* مربعين في كل صف */
        gap: 12px;
    }
    .card { border-radius: 15px !important; transition: 0.2s; }
    
    /* لون المتاح (أخضر هادي) */
    .bg-available { background-color: #d1fae5; color: #065f46; border: 1px solid #10b981 !important; }
    
    /* لون المحجوز (رمادي أو أحمر باهت) */
    .bg-occupied { background-color: #fee2e2; color: #991b1b; opacity: 0.9; cursor: not-allowed; }
    
    .slot-time { font-size: 1.1rem; }
    .slot-status { font-size: 0.75rem; font-weight: 600; text-transform: uppercase; }
    .booked-name { font-size: 0.85rem; font-weight: 700; border-top: 1px solid rgba(0,0,0,0.05); padding-top: 4px; }
    .click-to-book { font-size: 0.7rem; opacity: 0.8; }
</style>