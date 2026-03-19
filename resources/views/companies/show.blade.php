@extends('layouts.app')

@section('title', $company->name)

@push('styles')
    @vite('resources/css/pages/company.css')
@endpush

@section('content')
<div class="company-page">
    <div class="container">
        <!-- Banner -->
        <div class="company-banner" @if($company->banner_url) style="background-image: url('{{ $company->banner_url }}')" @endif>
            <div class="company-banner__overlay"></div>
        </div>

        <!-- Company Header -->
        <div class="company-header">
            <div class="company-header__logo">
                @if($company->logo_url)
                    <img src="{{ $company->logo_url }}" alt="{{ $company->name }}">
                @else
                    <div class="company-header__logo-placeholder">
                        {{ strtoupper(substr($company->name, 0, 2)) }}
                    </div>
                @endif
            </div>

            <div class="company-header__info">
                <div class="company-header__title-row">
                    <h1 class="company-header__name">
                        {{ $company->name }}
                        @if($company->is_verified)
                            <span class="company-verified" title="{{ __('company.verified') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="24" height="24">
                                    <path fill-rule="evenodd" d="M8.603 3.799A4.49 4.49 0 0112 2.25c1.357 0 2.573.6 3.397 1.549a4.49 4.49 0 013.498 1.307 4.491 4.491 0 011.307 3.497A4.49 4.49 0 0121.75 12a4.49 4.49 0 01-1.549 3.397 4.491 4.491 0 01-1.307 3.497 4.491 4.491 0 01-3.497 1.307A4.49 4.49 0 0112 21.75a4.49 4.49 0 01-3.397-1.549 4.49 4.49 0 01-3.498-1.306 4.491 4.491 0 01-1.307-3.498A4.49 4.49 0 012.25 12c0-1.357.6-2.573 1.549-3.397a4.49 4.49 0 011.307-3.497 4.49 4.49 0 013.497-1.307zm7.007 6.387a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        @endif
                    </h1>

                    @auth
                        @if(auth()->user()->isSeller() && auth()->user()->company && auth()->user()->company->id === $company->id && ! $company->is_verified)
                            @php $hasPending = \App\Models\VerificationRequest::where('company_id', $company->id)->where('status', 'pending')->exists(); @endphp
                            @if(! $hasPending)
                                <a href="{{ route('verification_requests.create') }}" class="btn btn-outline-primary ml-3">{{ __('company.request_verification') }}</a>
                            @else
                                <span class="badge bg-warning text-dark ml-3">{{ __('company.request_pending') }}</span>
                            @endif
                        @endif

                        @if(auth()->id() !== $company->user_id)
                            <button class="btn {{ $isFollowing ? 'btn-secondary' : 'btn-primary' }} company-follow-btn" 
                                    data-company-slug="{{ $company->slug }}"
                                    data-following="{{ $isFollowing ? 'true' : 'false' }}">
                                <span class="follow-text">{{ $isFollowing ? __('company.unfollow') : __('company.follow') }}</span>
                            </button>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="btn btn-primary">{{ __('company.follow') }}</a>
                    @endauth
                </div>

                <div class="company-header__stats">
                    <div class="company-stat">
                        <span class="company-stat__value">{{ $company->products_count ?? $products->total() }}</span>
                        <span class="company-stat__label">{{ __('company.products') }}</span>
                    </div>
                    <div class="company-stat">
                        <span class="company-stat__value followers-count">{{ $company->followers_count }}</span>
                        <span class="company-stat__label">{{ __('company.followers') }}</span>
                    </div>
                </div>

                @if($company->short_description)
                    <p class="company-header__description">{{ $company->short_description }}</p>
                @endif
            </div>
        </div>

        <!-- Company Details -->
        <div class="company-details">
            <div class="company-details__main">
                @if($company->description)
                    <div class="company-about">
                        <h2>{{ __('company.about') }}</h2>
                        <div class="company-about__text">
                            {!! nl2br(e($company->description)) !!}
                        </div>
                    </div>
                @endif

                <!-- Products -->
                <div class="company-products">
                    <h2>{{ __('company.products_title') }}</h2>
                    
                    @if($products->count() > 0)
                        <div class="products-grid">
                            @foreach($products as $product)
                                <div class="product-card">
                                    <a href="{{ route('products.show', $product) }}" class="product-card__image">
                                        @if($product->getPrimaryImage())
                                            <img src="{{ asset('storage/' . $product->getPrimaryImage()->image_path) }}" 
                                                 alt="{{ $product->name }}">
                                        @else
                                            <div class="product-card__placeholder">
                                                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
                                                    <path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                            </div>
                                        @endif
                                        @if($product->sale_price)
                                            <span class="product-card__badge product-card__badge--sale">
                                                -{{ $product->getDiscountPercentage() }}%
                                            </span>
                                        @endif
                                    </a>
                                    <div class="product-card__body">
                                        <h3 class="product-card__title">
                                            <a href="{{ route('products.show', $product) }}">{{ $product->name }}</a>
                                        </h3>
                                        @if($product->category)
                                            <span class="product-card__category">{{ $product->category->name }}</span>
                                        @endif
                                        <div class="product-card__price">
                                            @if($product->sale_price)
                                                <span class="product-card__price--current">${{ number_format($product->sale_price, 2) }}</span>
                                                <span class="product-card__price--old">${{ number_format($product->price, 2) }}</span>
                                            @else
                                                <span class="product-card__price--current">${{ number_format($product->price, 2) }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="pagination-wrapper">
                            {{ $products->links() }}
                        </div>
                    @else
                        <div class="empty-state">
                            <p>{{ __('company.no_products') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="company-details__sidebar">
                <div class="company-contact-card">
                    <h3>{{ __('company.contact_info') }}</h3>
                    
                    @if($company->email)
                        <div class="contact-item">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="20" height="20">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                            </svg>
                            <a href="mailto:{{ $company->email }}">{{ $company->email }}</a>
                        </div>
                    @endif

                    @if($company->phone)
                        <div class="contact-item">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="20" height="20">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                            </svg>
                            <a href="tel:{{ $company->phone }}">{{ $company->phone }}</a>
                        </div>
                    @endif

                    @if($company->website)
                        <div class="contact-item">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="20" height="20">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0112 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 013 12c0-1.605.42-3.113 1.157-4.418" />
                            </svg>
                            <a href="{{ $company->website }}" target="_blank" rel="noopener">{{ parse_url($company->website, PHP_URL_HOST) }}</a>
                        </div>
                    @endif

                    @if($company->address || $company->city || $company->country)
                        <div class="contact-item">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="20" height="20">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                            </svg>
                            <span>
                                @if($company->address){{ $company->address }}, @endif
                                @if($company->city){{ $company->city }}, @endif
                                {{ $company->country }}
                            </span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const followBtn = document.querySelector('.company-follow-btn');
    if (followBtn) {
        followBtn.addEventListener('click', async function() {
            const companySlug = this.dataset.companySlug;
            const isFollowing = this.dataset.following === 'true';
            
            try {
                const response = await fetch(`/companies/${companySlug}/follow`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    this.dataset.following = data.is_following ? 'true' : 'false';
                    this.classList.toggle('btn-primary', !data.is_following);
                    this.classList.toggle('btn-secondary', data.is_following);
                    this.querySelector('.follow-text').textContent = data.is_following 
                        ? '{{ __('company.unfollow') }}' 
                        : '{{ __('company.follow') }}';
                    
                    document.querySelector('.followers-count').textContent = data.followers_count;
                }
            } catch (error) {
                console.error('Error:', error);
            }
        });
    }
});
</script>
@endpush
@endsection
