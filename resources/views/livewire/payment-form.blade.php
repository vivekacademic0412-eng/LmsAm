<div class="pay-page">

    {{-- ══ LEFT HERO ══ --}}
    <div class="pay-hero">
        <div class="pay-hero-deco"></div>

        <div class="pay-logo">

            <span>Live Skills Traning Program</span>
        </div>

        <div class="pay-hero-body">
            <div class="pay-hero-tag">
                <i class="fas fa-lock"></i> Secure Enrollment
            </div>

            <h1>Start Your <em>Learning Journey</em> Today</h1>

            <p>Get instant access to our LMS platform, premium study material, recorded classes, assignments,
                assessments and expert mentor support.</p>

            <div class="feat-grid">
                <div class="feat-box">
                    <div class="feat-icon"><i class="fas fa-play-circle"></i></div>
                    <div>
                        <h4>Recorded Classes</h4>
                        <p>Learn anytime with lifetime access.</p>
                    </div>
                </div>
                <div class="feat-box">
                    <div class="feat-icon"><i class="fas fa-certificate"></i></div>
                    <div>
                        <h4>Certification</h4>
                        <p>Industry recognised certificate.</p>
                    </div>
                </div>
                <div class="feat-box">
                    <div class="feat-icon"><i class="fas fa-file-alt"></i></div>
                    <div>
                        <h4>Assignments</h4>
                        <p>Practical learning activities.</p>
                    </div>
                </div>
                <div class="feat-box">
                    <div class="feat-icon"><i class="fas fa-headset"></i></div>
                    <div>
                        <h4>Mentor Support</h4>
                        <p>Direct trainer guidance.</p>
                    </div>
                </div>
            </div>

            <div class="hero-testimonial">
                <p>"This platform completely changed how I approach learning. The structured path and mentor support
                    made all the difference!"</p>
                <div class="testi-author">
                    <div class="testi-avatar">RK</div>
                    <div>
                        <div class="testi-name">Rahul Kumar</div>
                        <div class="testi-role">Full Stack Developer</div>
                    </div>
                    <div class="testi-stars">★★★★★</div>
                </div>
            </div>
        </div>

        <div class="pay-hero-footer">
            <div class="hero-pills">
                <div class="hero-pill"><i class="fas fa-shield-alt"></i> SSL Protected</div>
                <div class="hero-pill"><i class="fas fa-bolt"></i> Instant Access</div>
                <div class="hero-pill"><i class="fas fa-undo"></i> Easy Refund</div>
            </div>
        </div>
    </div>

    {{-- ══ RIGHT FORM ══ --}}
    <div class="pay-form-panel">
        <img src="{{ asset('theme/images/am21.png') }}" alt="Mentor" title="Menntor" class="image-back-mentor3 shape">
        <div class="pf-header">
            <h2>Enrollment Checkout</h2>
            <p>Complete your payment to activate your LMS account instantly.</p>
        </div>

        <div class="price-banner">
            <div class="price-banner-left">
                <span>One-Time Enrollment Fee</span>
                <strong>₹{{ number_format($amount) }}</strong>
            </div>
            <div class="price-banner-right">
                <div class="badge-access">
                    <i class="fas fa-bolt"></i> Instant LMS Access
                </div>
            </div>
        </div>

        <form wire:submit="save">

            <div class="field-group">
                <label>Full Name</label>
                <input type="text" wire:model.blur="name" class="custom-input" placeholder="Enter your full name">
                @error('name')
                    <small class="error-text">{{ $message }}</small>
                @enderror
            </div>

            <div class="field-group">
                <label>Email Address</label>
                <input type="email" wire:model.blur="email" class="custom-input"
                    placeholder="Enter your email address">
                @error('email')
                    <small class="error-text">{{ $message }}</small>
                @enderror
            </div>

            <div class="field-group">
                <label>Mobile Number</label>
                <input type="text" wire:model.blur="phone" class="custom-input"
                    placeholder="Enter your mobile number">
                @error('phone')
                    <small class="error-text">{{ $message }}</small>
                @enderror
            </div>

            <div class="field-row">
                <div class="field-group">
                    <label>State</label>
                    <select wire:model.live="state_id" class="custom-input">
                        <option value="">Select State</option>
                        @foreach ($states as $state)
                            <option value="{{ $state->id }}">{{ $state->name }}</option>
                        @endforeach
                    </select>
                    @error('state_id')
                        <small class="error-text">{{ $message }}</small>
                    @enderror
                </div>
                <div class="field-group">
                    <label>City</label>
                    <select wire:model="city_id" class="custom-input">
                        <option value="">Select City</option>
                        @foreach ($cities as $city)
                            <option value="{{ $city->id }}">{{ $city->name }}</option>
                        @endforeach
                    </select>
                    @error('city_id')
                        <small class="error-text">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <div class="benefit-strip">
                <div class="benefit-item"><i class="fas fa-check-circle"></i> Instant Enrollment</div>
                <div class="benefit-item"><i class="fas fa-check-circle"></i> LMS Dashboard Access</div>
                <div class="benefit-item"><i class="fas fa-check-circle"></i> Recorded Classes</div>
                <div class="benefit-item"><i class="fas fa-check-circle"></i> Secure Payment Gateway</div>
            </div>

            <button type="submit" class="pay-btn" wire:loading.attr="disabled">
                <span wire:loading.remove>
                    <i class="fas fa-lock"></i>
                    Pay ₹{{ number_format($amount) }} Securely
                </span>
                <span wire:loading>
                    <i class="fas fa-spinner fa-spin"></i>
                    Processing...
                </span>
            </button>

            <div class="trust-row">
                <span><i class="fas fa-shield-alt"></i> SSL Secure</span>
                <span><i class="fas fa-bolt"></i> Instant Access</span>
                <span><i class="fas fa-check-circle"></i> Trusted Platform</span>
                <span><i class="fas fa-undo"></i> Easy Refund</span>
            </div>

        </form>

    </div>

    {{-- ══ SUCCESS OVERLAY ══ --}}
    @if ($showSuccess)
        <div class="success-overlay">
            <div class="success-card">
                <div class="success-icon-wrap">
                    <i class="fas fa-check"></i>
                </div>
                <h2>Payment Successful!</h2>
                <p>
                    Congratulations! Your enrollment is complete. We've sent your demo access link to your registered
                    email address.
                </p>

                <div class="progress-track">
                    <div class="progress-fill"></div>
                </div>

                <p class="redirect-txt">
                    Taking you to your LMS Dashboard...
                </p>
            </div>
        </div>
    @endif

    @script
    <script>
        $wire.on('payment-success', () => {
            // Swal.fire({
            //     icon: 'success',
            //     title: 'Payment Successful!',
            //     text: 'Redirecting to LMS Dashboard...',
            //     timer: 3000,
            //     timerProgressBar: true,
            //     showConfirmButton: false
            // });
            setTimeout(() => {
                window.location.href = "{{ route('dashboard') }}";
            }, 3000);
        });
    </script>
    @endscript

</div>
