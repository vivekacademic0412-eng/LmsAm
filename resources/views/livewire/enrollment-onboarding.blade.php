<div class="onb">
    <style>
        /* =====================================================
           ONBOARDING WIZARD — self-contained, no Bootstrap classes.
           Runs on the app's existing theme tokens for light/dark.
        ===================================================== */
        .onb * { box-sizing: border-box; }
        .onb { color: var(--text); margin: 0 auto; }

        .onb-shell {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow-card);
            overflow: hidden;
        }

        /* ── Stepper header ── */
        .onb-tabs {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            border-bottom: 1px solid var(--border);
            background: var(--bg2);
        }

        .onb-tab {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            padding: 16px 8px;
            text-align: center;
            position: relative;
            color: var(--text-muted);
        }

        .onb-tab-icon {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            border: 2px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: .78rem;
            font-weight: 700;
            background: var(--bg-card);
            color: var(--text-muted);
            transition: .2s ease;
        }

        .onb-tab-label { font-size: .78rem; font-weight: 600; }

        .onb-tab.is-active .onb-tab-icon {
            border-color: var(--brand-primary);
            background: var(--brand-primary);
            color: #fff;
        }
        .onb-tab.is-active .onb-tab-label { color: var(--text); }

        .onb-tab.is-done .onb-tab-icon {
            border-color: var(--success);
            background: var(--success);
            color: #fff;
        }

        .onb-tabs::after { content: none; }

        @media (max-width: 640px) {
            .onb-tab-label { display: none; }
        }

        /* ── Progress bar ── */
        .onb-progress-track {
            height: 4px;
            background: var(--border);
        }
        .onb-progress-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--brand-primary), var(--brand-secondary));
            transition: width .3s ease;
        }

        /* ── Body ── */
        .onb-body { padding: 28px 30px; }

        @media (max-width: 640px) {
            .onb-body { padding: 20px; }
        }

        .onb-section-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 22px;
        }

        .onb-section-title {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--text);
            margin: 0;
        }

        .onb-section-title i { color: var(--brand-primary); }

        .onb-phase-badge {
            font-size: .72rem;
            font-weight: 700;
            padding: 5px 12px;
            border-radius: 999px;
            background: var(--primary-glow);
            color: var(--brand-primary);
            white-space: nowrap;
        }

        /* ── Form grid ── */
        .onb-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
        }
        .onb-grid .span-2 { grid-column: 1 / -1; }

        @media (max-width: 640px) {
            .onb-grid { grid-template-columns: 1fr; }
        }

        .onb-field label {
            display: block;
            font-size: .85rem;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 7px;
        }

        .onb-field .req { color: var(--danger); }

        .onb-input, .onb-select, .onb-textarea {
            width: 100%;
            background: var(--input-bg);
            border: 1px solid var(--input-border);
            color: var(--text);
            border-radius: var(--radius-sm);
            padding: 11px 14px;
            font-size: .9rem;
            font-family: inherit;
            transition: border-color .2s ease, box-shadow .2s ease;
        }

        .onb-input:focus, .onb-select:focus, .onb-textarea:focus {
            outline: none;
            border-color: var(--input-focus);
            box-shadow: 0 0 0 3px var(--primary-glow);
        }

        .onb-input.has-error, .onb-select.has-error, .onb-textarea.has-error {
            border-color: var(--danger);
        }

        .onb-error {
            color: var(--danger);
            font-size: .78rem;
            margin-top: 6px;
            display: block;
        }

        .onb-hint {
            color: var(--text-muted);
            font-size: .78rem;
            margin-top: 6px;
        }

        /* ── Segmented control (experience level) ── */
        .onb-segment {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .onb-segment input { position: absolute; opacity: 0; width: 1px; height: 1px; }

        .onb-segment label {
            margin: 0;
            padding: 9px 16px;
            border: 1px solid var(--border);
            border-radius: 999px;
            font-size: .8rem;
            font-weight: 600;
            color: var(--text-muted);
            cursor: pointer;
            transition: .2s ease;
        }

        .onb-segment input:checked + label {
            border-color: var(--brand-primary);
            background: var(--primary-glow);
            color: var(--brand-primary);
        }

        /* ── Pill choice (how did you hear) ── */
        .onb-pill-group {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .onb-pill {
            padding: 9px 16px;
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            font-size: .8rem;
            font-weight: 600;
            color: var(--text-muted);
            background: var(--bg);
            cursor: pointer;
            transition: .2s ease;
        }

        .onb-pill.active {
            border-color: var(--brand-primary);
            background: var(--primary-glow);
            color: var(--brand-primary);
        }

        /* ── Document upload tiles ── */
        .onb-doc-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 16px;
        }

        .onb-doc-tile {
            border: 2px dashed var(--input-border);
            border-radius: var(--radius-sm);
            background: var(--input-bg);
            padding: 20px 16px;
            text-align: center;
            cursor: pointer;
            transition: border-color .2s ease, background .2s ease;
            position: relative;
        }

        .onb-doc-tile:hover { border-color: var(--brand-primary); background: var(--primary-glow); }
        .onb-doc-tile.filled { border-style: solid; border-color: var(--success); }
        .onb-doc-tile input { position: absolute; inset: 0; opacity: 0; cursor: pointer; }

        .onb-doc-tile i { font-size: 22px; color: var(--brand-primary); margin-bottom: 8px; display: block; }
        .onb-doc-tile.filled i { color: var(--success); }
        .onb-doc-tile h6 { font-size: .84rem; font-weight: 600; color: var(--text); margin: 0 0 3px; }
        .onb-doc-tile p { font-size: .72rem; color: var(--text-muted); margin: 0; }
        .onb-doc-file { font-size: .72rem; color: var(--success); margin-top: 6px; word-break: break-all; }

        /* ── Policy accordion ── */
        .onb-policy-box {
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            max-height: 340px;
            overflow-y: auto;
            background: var(--bg);
        }

        .onb-policy-section {
            border-bottom: 1px solid var(--border);
        }
        .onb-policy-section:last-child { border-bottom: none; }

        .onb-policy-summary {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding: 14px 16px;
            cursor: pointer;
            font-weight: 600;
            font-size: .86rem;
            color: var(--text);
        }

        .onb-policy-summary i { color: var(--text-muted); transition: transform .2s ease; }

        .onb-policy-body {
            padding: 0 16px 16px;
            font-size: .82rem;
            line-height: 1.6;
            color: var(--text-muted);
        }

        .onb-policy-read-tag {
            font-size: .68rem;
            color: var(--success);
            font-weight: 700;
        }

        .onb-scroll-note {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: .78rem;
            color: var(--text-muted);
            margin-top: 10px;
        }

        .onb-scroll-note.done { color: var(--success); }

        /* ── Consent checkboxes ── */
        .onb-consent {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 14px 16px;
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            margin-top: 14px;
            background: var(--bg);
        }

        .onb-consent input {
            margin-top: 3px;
            width: 17px;
            height: 17px;
            accent-color: var(--brand-primary);
            flex-shrink: 0;
        }

        .onb-consent label {
            font-size: .84rem;
            color: var(--text);
            line-height: 1.5;
        }

        .onb-consent.locked { opacity: .55; }

        /* ── Footer nav ── */
        .onb-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 18px 30px;
            border-top: 1px solid var(--border);
            background: var(--bg2);
        }

        @media (max-width: 640px) {
            .onb-footer { padding: 16px 20px; }
        }

        .onb-btn {
            border: none;
            border-radius: var(--radius-sm);
            font-weight: 600;
            font-size: .85rem;
            padding: 11px 22px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            transition: transform .15s ease, opacity .15s ease;
        }

        .onb-btn.primary {
            background: linear-gradient(135deg, var(--brand-primary), var(--brand-secondary));
            color: #fff;
        }

        .onb-btn.ghost {
            background: transparent;
            border: 1px solid var(--border);
            color: var(--text);
        }

        .onb-btn:hover { transform: translateY(-1px); opacity: .92; }
        .onb-btn:disabled { opacity: .5; cursor: not-allowed; transform: none; }

        .onb-btn:focus-visible, .onb-doc-tile:focus-within, .onb-pill:focus-visible {
            outline: 2px solid var(--brand-primary);
            outline-offset: 2px;
        }

        @media (prefers-reduced-motion: reduce) {
            .onb-btn, .onb-doc-tile, .onb-pill, .onb-tab-icon, .onb-progress-fill { transition: none; }
        }
    </style>

    <div class="onb-shell">

        {{-- Stepper --}}
        <div class="onb-tabs">
            @foreach (['Personal' => 'fa-id-card', 'Academic' => 'fa-graduation-cap', 'Program' => 'fa-layer-group', 'Declaration' => 'fa-file-signature'] as $label => $icon)
                @php $i = $loop->iteration; @endphp
                <div class="onb-tab {{ $step === $i ? 'is-active' : '' }} {{ $step > $i ? 'is-done' : '' }}">
                    <span class="onb-tab-icon">
                        @if ($step > $i) <i class="fa-solid fa-check"></i> @else {{ $i }} @endif
                    </span>
                    <span class="onb-tab-label">{{ $label }}</span>
                </div>
            @endforeach
        </div>
        <div class="onb-progress-track">
            <div class="onb-progress-fill" style="width: {{ ($step / $totalSteps) * 100 }}%"></div>
        </div>

        <div class="onb-body">

            {{-- ── STEP 1: Personal details ── --}}
            @if ($step === 1)
                <div class="onb-section-head">
                    <h3 class="onb-section-title"><i class="fa-solid fa-id-card"></i> Personal details</h3>
                    <span class="onb-phase-badge">Phase 1 · Setup</span>
                </div>

                <div class="onb-grid">
                    <div class="onb-field">
                        <label>First name <span class="req">*</span></label>
                        <input type="text" wire:model="first_name" class="onb-input @error('first_name') has-error @enderror" placeholder="As per ID proof">
                        @error('first_name') <span class="onb-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="onb-field">
                        <label>Last name <span class="req">*</span></label>
                        <input type="text" wire:model="last_name" class="onb-input @error('last_name') has-error @enderror" placeholder="As per ID proof">
                        @error('last_name') <span class="onb-error">{{ $message }}</span> @enderror
                    </div>

                    <div class="onb-field">
                        <label>Date of birth <span class="req">*</span></label>
                        <input type="date" wire:model="dob" class="onb-input @error('dob') has-error @enderror">
                        @error('dob') <span class="onb-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="onb-field">
                        <label>Gender <span class="req">*</span></label>
                        <select wire:model="gender" class="onb-select @error('gender') has-error @enderror">
                            <option value="">Select</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                            <option value="prefer_not_to_say">Prefer not to say</option>
                        </select>
                        @error('gender') <span class="onb-error">{{ $message }}</span> @enderror
                    </div>

                    <div class="onb-field">
                        <label>Category</label>
                        <select wire:model="category" class="onb-select">
                            <option value="">Select</option>
                            <option value="general">General</option>
                            <option value="obc">OBC</option>
                            <option value="sc">SC</option>
                            <option value="st">ST</option>
                            <option value="ews">EWS</option>
                        </select>
                    </div>
                    <div></div>

                    <div class="onb-field">
                        <label>Mobile number <span class="req">*</span></label>
                        <input type="text" wire:model="mobile_number" maxlength="10" class="onb-input @error('mobile_number') has-error @enderror" placeholder="+91 XXXXX XXXXX">
                        @error('mobile_number') <span class="onb-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="onb-field">
                        <label>Email address <span class="req">*</span></label>
                        <input type="email" wire:model="email" class="onb-input @error('email') has-error @enderror" placeholder="student@email.com">
                        @error('email') <span class="onb-error">{{ $message }}</span> @enderror
                    </div>

                    <div class="onb-field">
                        <label>WhatsApp number</label>
                        <input type="text" wire:model="whatsapp_number" maxlength="10" class="onb-input" placeholder="If different from mobile">
                    </div>
                    <div class="onb-field">
                        <label>City / district <span class="req">*</span></label>
                        <input type="text" wire:model="city_district" class="onb-input @error('city_district') has-error @enderror" placeholder="e.g. Ludhiana">
                        @error('city_district') <span class="onb-error">{{ $message }}</span> @enderror
                    </div>

                    <div class="onb-field span-2">
                        <label>Residential address <span class="req">*</span></label>
                        <textarea wire:model="residential_address" rows="2" class="onb-textarea @error('residential_address') has-error @enderror" placeholder="House no., street, locality, city, state, PIN"></textarea>
                        @error('residential_address') <span class="onb-error">{{ $message }}</span> @enderror
                    </div>

                    <div class="onb-field">
                        <label>ID proof type <span class="req">*</span></label>
                        <select wire:model="id_proof_type" class="onb-select @error('id_proof_type') has-error @enderror">
                            <option value="">Select</option>
                            <option value="aadhaar">Aadhaar</option>
                            <option value="passport">Passport</option>
                            <option value="pan">PAN</option>
                            <option value="voter_id">Voter ID</option>
                            <option value="driving_licence">Driving licence</option>
                        </select>
                        @error('id_proof_type') <span class="onb-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="onb-field">
                        <label>ID number <span class="req">*</span></label>
                        <input type="text" wire:model="id_number" class="onb-input @error('id_number') has-error @enderror" placeholder="Enter ID number">
                        @error('id_number') <span class="onb-error">{{ $message }}</span> @enderror
                    </div>
                </div>
            @endif

            {{-- ── STEP 2: Academic background ── --}}
            @if ($step === 2)
                <div class="onb-section-head">
                    <h3 class="onb-section-title"><i class="fa-solid fa-graduation-cap"></i> Academic background</h3>
                    <span class="onb-phase-badge">Phase 2 · Enrolment</span>
                </div>

                <div class="onb-grid">
                    <div class="onb-field">
                        <label>Highest qualification <span class="req">*</span></label>
                        <input type="text" wire:model="highest_qualification" class="onb-input @error('highest_qualification') has-error @enderror" placeholder="e.g. B.Tech CSE">
                        @error('highest_qualification') <span class="onb-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="onb-field">
                        <label>Percentage / CGPA <span class="req">*</span></label>
                        <input type="text" wire:model="percentage_cgpa" class="onb-input @error('percentage_cgpa') has-error @enderror" placeholder="e.g. 72% or 7.8 CGPA">
                        @error('percentage_cgpa') <span class="onb-error">{{ $message }}</span> @enderror
                    </div>

                    <div class="onb-field">
                        <label>Institution / college name <span class="req">*</span></label>
                        <input type="text" wire:model="institution_name" class="onb-input @error('institution_name') has-error @enderror" placeholder="Name of school / college">
                        @error('institution_name') <span class="onb-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="onb-field">
                        <label>Year of passing <span class="req">*</span></label>
                        <input type="text" wire:model="year_of_passing" maxlength="4" class="onb-input @error('year_of_passing') has-error @enderror" placeholder="e.g. 2023">
                        @error('year_of_passing') <span class="onb-error">{{ $message }}</span> @enderror
                    </div>

                    <div class="onb-field span-2">
                        <label>Work experience (if any) <span class="req">*</span></label>
                        <div class="onb-segment">
                            @foreach (['fresher' => 'Fresher (no experience)', '0-1' => '0–1 year', '1-2' => '1–2 years', '2+' => '2+ years'] as $val => $lbl)
                                <input type="radio" id="exp-{{ $val }}" value="{{ $val }}" wire:model="experience_level">
                                <label for="exp-{{ $val }}">{{ $lbl }}</label>
                            @endforeach
                        </div>
                        @error('experience_level') <span class="onb-error">{{ $message }}</span> @enderror
                    </div>

                    <div class="onb-field">
                        <label>Parent / guardian name <span class="req">*</span></label>
                        <input type="text" wire:model="guardian_name" class="onb-input @error('guardian_name') has-error @enderror" placeholder="Full name">
                        @error('guardian_name') <span class="onb-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="onb-field">
                        <label>Parent / guardian mobile <span class="req">*</span></label>
                        <input type="text" wire:model="guardian_mobile" maxlength="10" class="onb-input @error('guardian_mobile') has-error @enderror" placeholder="+91 XXXXX XXXXX">
                        @error('guardian_mobile') <span class="onb-error">{{ $message }}</span> @enderror
                    </div>
                </div>
            @endif

            {{-- ── STEP 3: Program selection + Documents ── --}}
            @if ($step === 3)
                <div class="onb-section-head">
                    <h3 class="onb-section-title"><i class="fa-solid fa-layer-group"></i> Program selection</h3>
                    <span class="onb-phase-badge">Phase 3 · Mapping</span>
                </div>

                <div class="onb-grid">
                    <div class="onb-field">
                        <label>Program category <span class="req">*</span></label>
                        <select wire:model.live="program_category_id" class="onb-select @error('program_category_id') has-error @enderror">
                            <option value="">Select category</option>
                            @foreach ($programCategories ?? [] as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        @error('program_category_id') <span class="onb-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="onb-field">
                        <label>Program name <span class="req">*</span></label>
                        <select wire:model.live="program_id" class="onb-select @error('program_id') has-error @enderror">
                            <option value="">Select program first</option>
                            @foreach ($programs ?? [] as $prog)
                                <option value="{{ $prog->id }}">{{ $prog->title }}</option>
                            @endforeach
                        </select>
                        @error('program_id') <span class="onb-error">{{ $message }}</span> @enderror
                    </div>

                    <div class="onb-field">
                        <label>Preferred batch</label>
                        <select wire:model="batch_id" class="onb-select">
                            <option value="">Select</option>
                            @foreach ($batches ?? [] as $batch)
                                <option value="{{ $batch->id }}">{{ $batch->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="onb-field">
                        <label>Mode of learning <span class="req">*</span></label>
                        <select wire:model="mode_of_learning" class="onb-select @error('mode_of_learning') has-error @enderror">
                            <option value="">Select</option>
                            <option value="online">Online</option>
                            <option value="offline">Offline</option>
                            <option value="hybrid">Hybrid</option>
                        </select>
                        @error('mode_of_learning') <span class="onb-error">{{ $message }}</span> @enderror
                    </div>

                    <div class="onb-field span-2">
                        <label>Preferred start date <span class="req">*</span></label>
                        <input type="date" wire:model="preferred_start_date" class="onb-input @error('preferred_start_date') has-error @enderror">
                        @error('preferred_start_date') <span class="onb-error">{{ $message }}</span> @enderror
                    </div>

                    <div class="onb-field span-2">
                        <label>How did you hear about us?</label>
                        <div class="onb-pill-group">
                            @foreach (['Google' => 'google', 'Social media' => 'social', 'Friend / referral' => 'referral', 'College fair' => 'college_fair', 'Counsellor' => 'counsellor', 'Walk-in' => 'walk_in'] as $lbl => $val)
                                <span wire:click="$set('referral_source', '{{ $val }}')" class="onb-pill {{ $referral_source === $val ? 'active' : '' }}">{{ $lbl }}</span>
                            @endforeach
                        </div>
                    </div>

                    <div class="onb-field span-2">
                        <label>Career goal / reason for enrolling</label>
                        <textarea wire:model="career_goal" rows="3" class="onb-textarea" placeholder="Briefly describe what you hope to achieve from this program"></textarea>
                    </div>
                </div>

                <div class="onb-section-head" style="margin-top: 28px;">
                    <h3 class="onb-section-title"><i class="fa-solid fa-file-arrow-up"></i> Documents & photos</h3>
                    <span class="onb-phase-badge">Phase 3 · Mapping</span>
                </div>

                <div class="onb-doc-grid">
                    <label class="onb-doc-tile {{ $photo ? 'filled' : '' }}">
                        <input type="file" wire:model="photo" accept="image/*">
                        <i class="fa-solid fa-camera"></i>
                        <h6>Passport-size photo</h6>
                        <p>JPG/PNG, max 2 MB</p>
                        @if ($photo) <div class="onb-doc-file">{{ $photo->getClientOriginalName() }}</div> @endif
                        @error('photo') <span class="onb-error">{{ $message }}</span> @enderror
                    </label>

                    <label class="onb-doc-tile {{ $id_proof_file ? 'filled' : '' }}">
                        <input type="file" wire:model="id_proof_file" accept="image/*,.pdf">
                        <i class="fa-solid fa-id-badge"></i>
                        <h6>ID proof (Aadhaar / PAN, etc.)</h6>
                        <p>JPG/PNG/PDF, max 2 MB</p>
                        @if ($id_proof_file) <div class="onb-doc-file">{{ $id_proof_file->getClientOriginalName() }}</div> @endif
                        @error('id_proof_file') <span class="onb-error">{{ $message }}</span> @enderror
                    </label>

                    <label class="onb-doc-tile {{ $marksheet_certificate ? 'filled' : '' }}">
                        <input type="file" wire:model="marksheet_certificate" accept="image/*,.pdf">
                        <i class="fa-solid fa-award"></i>
                        <h6>Marksheet / degree certificate</h6>
                        <p>JPG/PNG/PDF, max 2 MB</p>
                        @if ($marksheet_certificate) <div class="onb-doc-file">{{ $marksheet_certificate->getClientOriginalName() }}</div> @endif
                        @error('marksheet_certificate') <span class="onb-error">{{ $message }}</span> @enderror
                    </label>

                    <label class="onb-doc-tile {{ $experience_letter ? 'filled' : '' }}">
                        <input type="file" wire:model="experience_letter" accept="image/*,.pdf">
                        <i class="fa-solid fa-briefcase"></i>
                        <h6>Experience letter (optional)</h6>
                        <p>JPG/PNG/PDF, max 2 MB</p>
                        @if ($experience_letter) <div class="onb-doc-file">{{ $experience_letter->getClientOriginalName() }}</div> @endif
                    </label>
                </div>
            @endif

            {{-- ── STEP 4: Declaration & Policy ── --}}
            @if ($step === 4)
                <div class="onb-section-head">
                    <h3 class="onb-section-title"><i class="fa-solid fa-file-signature"></i> Declaration</h3>
                    <span class="onb-phase-badge">Phase 4 · Confirm & submit</span>
                </div>

                <p class="onb-hint" style="margin-bottom: 14px;">
                    This is a legally binding step. Please read every section below before agreeing —
                    the checkbox unlocks once you've scrolled through the full policy.
                </p>

                <div class="onb-policy-box" id="policyScrollBox" wire:ignore.self
                    x-data
                    x-on:scroll="if ($el.scrollTop + $el.clientHeight >= $el.scrollHeight - 12) $wire.markPolicyScrolled()">
                    @foreach (($policy->sections ?? []) as $section)
                        <details class="onb-policy-section" wire:key="policy-{{ $section->id }}" ontoggle="if(this.open) @this.toggleSection('{{ $section->section_key }}')">
                            <summary class="onb-policy-summary">
                                <span>{{ $section->title }}</span>
                                <i class="fa-solid fa-chevron-down"></i>
                            </summary>
                            <div class="onb-policy-body">{!! nl2br(e($section->body)) !!}</div>
                        </details>
                    @endforeach
                </div>

                <div class="onb-scroll-note {{ $hasScrolledPolicy ? 'done' : '' }}">
                    <i class="fa-solid {{ $hasScrolledPolicy ? 'fa-circle-check' : 'fa-circle-info' }}"></i>
                    {{ $hasScrolledPolicy ? "You've read the full policy." : 'Scroll to the end of the policy box to continue.' }}
                </div>

                <div class="onb-consent {{ ! $hasScrolledPolicy ? 'locked' : '' }}">
                    <input type="checkbox" id="declaration_confirmed" wire:model="declaration_confirmed" @disabled(! $hasScrolledPolicy)>
                    <label for="declaration_confirmed">
                        I hereby declare that the information provided in this enrolment form is true and correct to
                        the best of my knowledge. I understand that any misrepresentation may result in cancellation of
                        my enrolment.
                    </label>
                </div>
                @error('declaration_confirmed') <span class="onb-error">{{ $message }}</span> @enderror

                <div class="onb-consent {{ ! $hasScrolledPolicy ? 'locked' : '' }}">
                    <input type="checkbox" id="terms_agreed" wire:model="terms_agreed" @disabled(! $hasScrolledPolicy)>
                    <label for="terms_agreed">
                        I have read and agree to the eligibility, fee, attendance, code of conduct, intellectual
                        property, examination, and data privacy terms above
                        (Policy {{ $policy->version ?? '' }}). <span class="req">*</span>
                    </label>
                </div>
                @error('terms_agreed') <span class="onb-error">{{ $message }}</span> @enderror

                <div class="onb-consent">
                    <input type="checkbox" id="marketing_opt_in" wire:model="marketing_opt_in">
                    <label for="marketing_opt_in">
                        I agree to receive updates and communications via SMS / WhatsApp / email.
                    </label>
                </div>
            @endif

        </div>

        {{-- Footer nav --}}
        <div class="onb-footer">
            <button type="button" wire:click="prevStep" class="onb-btn ghost" @if ($step === 1) disabled @endif>
                <i class="fa-solid fa-arrow-left"></i> Back
            </button>

            @if ($step < $totalSteps)
                <button type="button" wire:click="nextStep" class="onb-btn primary" wire:loading.attr="disabled" wire:target="nextStep">
                    <span wire:loading.remove wire:target="nextStep">Continue <i class="fa-solid fa-arrow-right"></i></span>
                    <span wire:loading wire:target="nextStep">Saving…</span>
                </button>
            @else
                <button type="button" wire:click="submit" class="onb-btn primary" wire:loading.attr="disabled" wire:target="submit"
                    @disabled(! $declaration_confirmed || ! $terms_agreed)>
                    <span wire:loading.remove wire:target="submit"><i class="fa-solid fa-check"></i> Submit enrolment</span>
                    <span wire:loading wire:target="submit">Submitting…</span>
                </button>
            @endif
        </div>
    </div>

    @script
        <script>
            Livewire.on('onboarding-submitted', () => {
                Swal.fire({
                    icon: 'success',
                    title: 'Enrolment submitted',
                    text: 'Your details and policy consent have been recorded. Redirecting to your dashboard…',
                    timer: 2200,
                    showConfirmButton: false,
                }).then(() => { window.location.href = '{{ route('dashboard') }}'; });
            });

            Livewire.on('onboarding-error', (event) => {
                Swal.fire({
                    icon: 'error',
                    title: 'Something went wrong',
                    text: event.message ?? 'Please try again.',
                    confirmButtonColor: '#dc2626',
                });
            });
        </script>
    @endscript
</div>