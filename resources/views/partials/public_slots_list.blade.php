<div class="public-slots-list">
    @for ($i = 0; $i < 24; $i++)
        @php
            $time = str_pad($i, 2, '0', STR_PAD_LEFT) . ':00:00';
            $booking = $bookings->get($time);
            $isOccupied = !is_null($booking);
            $displayTime = \Carbon\Carbon::createFromTime($i, 0)->format('g:i A');
        @endphp

        <div class="slot-card {{ $isOccupied ? 'occupied' : 'available' }}">
            <div class="time-part">
                <i class="bi bi-clock"></i> {{ $displayTime }}
            </div>
            <div class="info-part">
                @if($isOccupied)
                    <span class="status-badge">محجوز</span>
                    <span class="user-name">{{ $booking->user_name }}</span>
                @else
                    <span class="status-badge">متاح</span>
                    <span class="tap-text">تواصل للحجز</span>
                @endif
            </div>
        </div>
    @endfor
</div>

<style>
    .public-slots-list { display: flex; flex-direction: column; gap: 20px; }
    .slot-card {
        display: flex;
        align-items: center;
        padding: 35px 40px;
        border-radius: 20px;
        border: 1px solid #e0e0e0;
        background: #fff;
        transition: transform 0.2s, box-shadow 0.2s;
        min-height: 120px;
    }
    .slot-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 6px 16px rgba(0,0,0,0.08);
    }
    .slot-card.occupied { background-color: #fff5f5; border-left: 12px solid #fc8181; }
    .slot-card.available { background-color: #f0fff4; border-left: 12px solid #68d391; }
    
    .time-part { font-size: 2rem; font-weight: 900; min-width: 180px; color: #2d3748; display: flex; align-items: center; gap: 15px; }
    .info-part { display: flex; align-items: center; gap: 30px; flex-grow: 1; justify-content: flex-end; }
    
    .status-badge { font-size: 1.2rem; padding: 10px 25px; border-radius: 50px; font-weight: bold; }
    .occupied .status-badge { background: #feb2b2; color: #9b2c2c; }
    .available .status-badge { background: #c6f6d5; color: #22543d; }
    
    .user-name { font-size: 1.8rem; font-weight: 800; color: #1a202c; }
    .tap-text { font-size: 1.4rem; color: #718096; font-weight: 600; }

    /* Mobile Responsive Styles */
    @media (max-width: 768px) {
        .public-slots-list { gap: 10px; }
        .slot-card {
            padding: 15px 20px;
            min-height: auto;
            border-radius: 15px;
            border-left-width: 8px !important;
        }
        
        .time-part {
            font-size: 1.2rem;
            min-width: auto;
            gap: 8px;
        }
        
        .info-part { gap: 10px; }
        
        .status-badge {
            font-size: 0.9rem;
            padding: 5px 15px;
        }
        
        .user-name { font-size: 1rem; }
        .tap-text { font-size: 0.9rem; }
    }
</style>