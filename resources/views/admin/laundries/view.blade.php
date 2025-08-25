@extends('layouts.admin')

@section('title', 'عرض المغسلة - ' . (($nameData = json_decode($laundry->getRawOriginal('name'), true)) ? (is_string($nameData[app()->getLocale()] ?? $laundry->name) ? ($nameData[app()->getLocale()] ?? $laundry->name) : 'مغسلة') : (is_string($laundry->name) ? $laundry->name : 'مغسلة')))

@section('content')
<div class="container-fluid">
    <!-- Page Title -->
    <div class="page-header">
        <h1>عرض تفاصيل المغسلة</h1>
        <p>
            @php
                $nameData = json_decode($laundry->getRawOriginal('name'), true);
                $displayName = $nameData && is_array($nameData) ? ($nameData[app()->getLocale()] ?? $laundry->name) : $laundry->name;
            @endphp
            {{ is_string($displayName) ? $displayName : 'مغسلة' }}
        </p>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    <!-- Error Message -->
    @if(session('error'))
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle"></i>
            {{ session('error') }}
        </div>
    @endif

    <!-- Laundry Details -->
    <div class="section-container">
        <!-- Laundry Profile Header with Logo -->
        <div class="laundry-profile-header">
            <div class="laundry-avatar">
                @if($laundry->logo)
                    <img src="{{ asset('storage/' . $laundry->logo) }}" alt="شعار المغسلة" class="profile-image" 
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="default-avatar" style="display: none;">
                        <i class="fas fa-tshirt"></i>
                    </div>
                @else
                    <div class="default-avatar">
                        <i class="fas fa-tshirt"></i>
                    </div>
                @endif
            </div>
            <div class="laundry-info">
                <h2>
                    @php
                        $nameData = json_decode($laundry->getRawOriginal('name'), true);
                        $displayName = $nameData && is_array($nameData) ? ($nameData[app()->getLocale()] ?? $laundry->name) : $laundry->name;
                    @endphp
                    {{ is_string($displayName) ? $displayName : 'مغسلة' }}
                </h2>
                <p class="laundry-role">
                    <span class="role-badge laundry">مغسلة</span>
                </p>
                @if($laundry->logo)
                    <small class="image-info">شعار المغسلة متاح</small>
                @else
                    <small class="image-info">لا يوجد شعار</small>
                @endif
            </div>
        </div>

        <!-- Basic Information -->
        <div class="info-section">
            <div class="section-header">
                <h3><i class="fas fa-info-circle"></i> المعلومات الأساسية</h3>
            </div>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">اسم المغسلة</div>
                    <div class="info-value">
                        @php
                            $nameData = json_decode($laundry->getRawOriginal('name'), true);
                            $displayName = $nameData && is_array($nameData) ? ($nameData[app()->getLocale()] ?? $laundry->name) : $laundry->name;
                        @endphp
                        {{ is_string($displayName) ? $displayName : 'غير محدد' }}
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">البريد الإلكتروني</div>
                    <div class="info-value">{{ $laundry->user && is_string($laundry->user->email) ? $laundry->user->email : 'غير محدد' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">رقم الهاتف</div>
                    <div class="info-value">{{ is_string($laundry->phone) ? $laundry->phone : 'غير محدد' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">حالة الحساب</div>
                    <div class="info-value">
                        @switch($laundry->user->status)
                            @case('approved')
                                <span class="status-badge active">نشط</span>
                                @break
                            @case('pending')
                                <span class="status-badge pending">في الانتظار</span>
                                @break
                            @case('rejected')
                                <span class="status-badge rejected">مرفوض</span>
                                @break
                            @default
                                <span class="status-badge inactive">غير نشط</span>
                        @endswitch
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">حالة المغسلة</div>
                    <div class="info-value">
                        @switch($laundry->status)
                            @case('online')
                                <span class="status-badge active">متصل</span>
                                @break
                            @case('offline')
                                <span class="status-badge inactive">غير متصل</span>
                                @break
                            @case('maintenance')
                                <span class="status-badge pending">صيانة</span>
                                @break
                            @default
                                <span class="status-badge inactive">غير محدد</span>
                        @endswitch
                    </div>
                </div>
            </div>
        </div>

        <!-- Location Information -->
        <div class="info-section">
            <div class="section-header">
                <h3><i class="fas fa-map-marker-alt"></i> معلومات الموقع</h3>
            </div>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">المدينة</div>
                    <div class="info-value">
                        @if($laundry->city)
                            @php
                                $cityName = json_decode($laundry->city->getRawOriginal('name'), true);
                                $displayCityName = $cityName && is_array($cityName) ? ($cityName[app()->getLocale()] ?? $cityName['ar'] ?? $cityName['en'] ?? 'غير محدد') : 'غير محدد';
                            @endphp
                            {{ is_string($displayCityName) ? $displayCityName : 'غير محدد' }}
                        @else
                            غير محدد
                        @endif
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">العنوان</div>
                    <div class="info-value">
                        @php
                            $addressData = json_decode($laundry->getRawOriginal('address'), true);
                            $displayAddress = $addressData && is_array($addressData) ? ($addressData[app()->getLocale()] ?? $addressData['ar'] ?? $addressData['en'] ?? 'غير محدد') : 'غير محدد';
                        @endphp
                        {{ is_string($displayAddress) ? $displayAddress : 'غير محدد' }}
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">خط الطول</div>
                    <div class="info-value">{{ is_numeric($laundry->longitude) ? number_format($laundry->longitude, 6) : 'غير محدد' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">خط العرض</div>
                    <div class="info-value">{{ is_numeric($laundry->latitude) ? number_format($laundry->latitude, 6) : 'غير محدد' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">ساعات العمل</div>
                    <div class="info-value">
                        @if($laundry->working_hours)
                            @php
                                $hours = is_array($laundry->working_hours) ? $laundry->working_hours : json_decode($laundry->working_hours, true);
                            @endphp
                            @if($hours && is_array($hours))
                                @foreach($hours as $day => $time)
                                    <div>{{ is_string($day) ? $day : 'يوم' }}: {{ is_string($time) ? $time : 'متاح' }}</div>
                                @endforeach
                            @else
                                متاح
                            @endif
                        @else
                            غير محدد
                        @endif
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">خدمة التوصيل</div>
                    <div class="info-value">
                        @if($laundry->delivery_available)
                            <span class="status-badge active">متاحة</span>
                        @else
                            <span class="status-badge inactive">غير متاحة</span>
                        @endif
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">خدمة الاستلام</div>
                    <div class="info-value">
                        @if($laundry->pickup_available)
                            <span class="status-badge active">متاحة</span>
                        @else
                            <span class="status-badge inactive">غير متاحة</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Social Media Links -->
        @if($laundry->facebook || $laundry->instagram || $laundry->whatsapp)
        <div class="info-section">
            <div class="section-header">
                <h3><i class="fas fa-share-alt"></i> روابط التواصل الاجتماعي</h3>
            </div>
            <div class="social-links">
                @if($laundry->facebook)
                    <a href="{{ $laundry->facebook }}" target="_blank" class="social-link facebook">
                        <i class="fab fa-facebook"></i>
                        فيسبوك
                    </a>
                @endif
                @if($laundry->instagram)
                    <a href="{{ $laundry->instagram }}" target="_blank" class="social-link instagram">
                        <i class="fab fa-instagram"></i>
                        انستغرام
                    </a>
                @endif
                @if($laundry->whatsapp)
                    <a href="https://wa.me/{{ $laundry->whatsapp }}" target="_blank" class="social-link whatsapp">
                        <i class="fab fa-whatsapp"></i>
                        واتساب
                    </a>
                @endif
            </div>
        </div>
        @endif





        <!-- Status Change -->
        <div class="status-dropdown">
            <span class="status-label">تغيير الحالة:</span>
            <select class="status-select" 
                    data-entity-id="{{ $laundry->id }}" 
                    data-entity-type="laundry"
                    onchange="window.adminViewForm.changeEntityStatus({{ $laundry->id }}, this.value, 'laundry')">
                <option value="">اختر الحالة</option>
                <option value="approved" {{ $laundry->user->status == 'approved' ? 'selected' : '' }}>نشط</option>
                <option value="pending" {{ $laundry->user->status == 'pending' ? 'selected' : '' }}>في الانتظار</option>
                <option value="rejected" {{ $laundry->user->status == 'rejected' ? 'selected' : '' }}>مرفوض</option>
                <option value="suspended" {{ $laundry->user->status == 'suspended' ? 'selected' : '' }}>معلق</option>
            </select>
        </div>

        <!-- Actions -->
        <div class="action-section">
                            <a href="{{ route('admin.laundries.edit', $laundry) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i>
                تعديل
            </a>
                            <a href="{{ route('admin.laundries') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                العودة للقائمة
            </a>
        </div>
    </div>

    <!-- Services Section -->
    <div class="section-container">
        <div class="section-header">
            <h3><i class="fas fa-tshirt"></i> الخدمات المقدمة</h3>
        </div>
        
        @if($laundry->services()->count() > 0)
            <div class="services-grid">
                @foreach($laundry->services()->get() as $service)
                <div class="service-card">
                    <div class="service-header">
                        @if($service->image)
                            <img src="{{ asset('storage/' . $service->image) }}" alt="{{ $service->name }}" class="service-image">
                        @else
                            <div class="default-service-image">
                                <i class="fas fa-tshirt"></i>
                            </div>
                        @endif
                        <div class="service-status">
                            {{ $service->status == 'active' ? 'نشط' : 'غير نشط' }}
                        </div>
                    </div>
                    <div class="service-content">
                        <h4>{{ $service->name }}</h4>
                        <div class="service-description">{{ $service->description ?? 'لا يوجد وصف' }}</div>
                        <div class="service-details">
                            <div class="service-price">
                                <span class="price-label">السعر:</span>
                                <span class="price-value">{{ $service->price ?? 0 }} ريال</span>
                            </div>
                            <div class="service-coins">
                                <span class="coins-label">النقاط:</span>
                                <span class="coins-value">{{ $service->coin_cost ?? 0 }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="service-actions">
                        <a href="{{ route('admin.services.edit', $service) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-edit"></i>
                            تعديل
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="no-data">
                <i class="fas fa-tshirt"></i>
                <p>لا توجد خدمات متاحة</p>
            </div>
        @endif
    </div>

            

        <!-- Orders Section -->
        <div class="section-container">
            <div class="section-header">
                <h3><i class="fas fa-shopping-cart"></i> تفاصيل الطلبات</h3>
            </div>
        
        @if($laundry->orders()->count() > 0)
            <div class="orders-table">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th data-sortable>رقم الطلب</th>
                            <th data-sortable>العميل</th>
                            <th data-sortable>نوع الخدمة</th>
                            <th data-sortable>المبلغ</th>
                            <th data-sortable>الحالة</th>
                            <th data-sortable>التاريخ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($laundry->orders()->with(['user'])->latest()->get() as $order)
                        <tr>
                            <td>#{{ $order->id }}</td>
                            <td>{{ $order->user->name ?? 'غير محدد' }}</td>
                            <td>
                                @if($order->target_type === 'App\Models\Service')
                                    <span class="badge badge-service">خدمة</span>
                                @elseif($order->target_type === 'App\Models\Package')
                                    <span class="badge badge-package">باقة</span>
                                @else
                                    <span class="badge badge-other">{{ $order->target_type }}</span>
                                @endif
                            </td>
                            <td class="order-amount">
                                @if($order->coins > 0)
                                    <span class="coins">{{ $order->coins }} نقطة</span>
                                @endif
                                @if($order->price > 0)
                                    <span class="price">{{ $order->price }} ريال</span>
                                @endif
                            </td>
                            <td>
                                @switch($order->status)
                                    @case('pending')
                                        <span class="status-badge order-pending">في الانتظار</span>
                                        @break
                                    @case('in_process')
                                        <span class="status-badge order-in_process">قيد المعالجة</span>
                                        @break
                                    @case('completed')
                                        <span class="status-badge order-completed">مكتمل</span>
                                        @break
                                    @case('canceled')
                                        <span class="status-badge order-canceled">ملغي</span>
                                        @break
                                    @default
                                        <span class="status-badge">{{ $order->status }}</span>
                                @endswitch
                            </td>
                            <td>{{ $order->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="no-data">
                <i class="fas fa-shopping-cart"></i>
                <p>لا توجد طلبات</p>
            </div>
        @endif
    </div>

    <!-- Ratings Section -->
    <div class="section-container">
        <div class="section-header">
            <h3><i class="fas fa-star"></i> التقييمات</h3>
        </div>
        
        @if($laundry->ratings()->count() > 0)
            <!-- Ratings Summary -->
            <div class="ratings-summary">
                <div class="overall-rating">
                    @php
                        $averageRating = $laundry->ratings()->avg('rating') ?? 0;
                        $totalRatings = $laundry->ratings()->count();
                    @endphp
                    <div class="rating-number">{{ number_format($averageRating, 1) }}</div>
                    <div class="rating-stars-large">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="fas fa-star {{ $i <= $averageRating ? 'filled' : '' }}"></i>
                        @endfor
                    </div>
                    <div class="total-ratings-large">{{ $totalRatings }} تقييم</div>
                </div>
                
                <div class="rating-breakdown">
                    @for($rating = 5; $rating >= 1; $rating--)
                        @php
                            $count = $laundry->ratings()->where('rating', $rating)->count();
                            $percentage = $totalRatings > 0 ? ($count / $totalRatings) * 100 : 0;
                        @endphp
                        <div class="rating-bar">
                            <span class="rating-label">{{ $rating }} نجوم</span>
                            <div class="rating-progress">
                                <div class="progress-bar" style="width: {{ $percentage }}%"></div>
                            </div>
                            <span class="rating-count">{{ $count }}</span>
                        </div>
                    @endfor
                </div>
            </div>

            <!-- Recent Ratings -->
            <div class="recent-ratings">
                <h4>التقييمات الحديثة</h4>
                <div class="ratings-list">
                    @foreach($laundry->ratings()->with(['customer.user'])->latest()->take(5)->get() as $rating)
                    <div class="rating-item">
                        <div class="rating-header">
                            <div class="customer-info">
                                <div class="customer-avatar">
                                    {{ substr($rating->customer->user->name ?? 'U', 0, 1) }}
                                </div>
                                <div class="customer-details">
                                    <div class="customer-name">{{ $rating->customer->user->name ?? 'غير محدد' }}</div>
                                    <div class="rating-date">{{ $rating->created_at->format('Y-m-d') }}</div>
                                </div>
                            </div>
                            <div class="rating-score">
                                <div class="stars">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star {{ $i <= $rating->rating ? 'filled' : '' }}"></i>
                                    @endfor
                                </div>
                                <div class="score">{{ $rating->rating }}/5</div>
                            </div>
                        </div>
                        <div class="rating-comment">{{ $rating->comment ?? 'لا يوجد تعليق' }}</div>
                        @if($rating->service_type === 'service')
                            <div class="service-type-badge">
                                <i class="fas fa-tshirt"></i>
                                خدمة
                            </div>
                        @elseif($rating->service_type === 'package')
                            <div class="service-type-badge">
                                <i class="fas fa-box"></i>
                                باقة
                            </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="no-data">
                <i class="fas fa-star"></i>
                <p>لا توجد تقييمات</p>
            </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin-view-forms.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin-laundry-view.css') }}">
    
@endpush

@push('scripts')
    <script src="{{ asset('js/admin-view-forms.js') }}"></script>
    
@endpush
