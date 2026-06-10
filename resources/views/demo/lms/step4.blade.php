{{-- FILE: resources/views/student/step4.blade.php --}}
@extends('demo.layout')

@section('title', $section->heading ?? 'Demo Submitted! 🎉')
@section('bitmoji-message', $section->bitmoji_message ?? '🎉 AMAZING! You did it! You\'re ready for the next level! 🚀')
@section('bitmoji-emoji', $section->bitmoji_emoji ?? '🥳')



@section('content')

    <canvas id="confettiCanvas"></canvas>

    <div class="card">

        {{-- ── Success Hero ── --}}
        <div style="text-align:center; padding:32px 0 20px">
            <div class="step-badge" style="justify-content:center; margin-bottom:20px">
                <div class="dot-pulse" style="background:var(--brand-green)"></div>
                Step 4 of 5 — Submitted!
            </div>
            <div class="success-icon-wrap">
                <div class="success-icon">🎉</div>
                <div class="success-ring"></div>
            </div>
            <h1 style="font-size:2rem; margin-bottom:10px">
                {{ $section->heading ?? 'Demo Submitted Successfully!' }}
            </h1>
            <p style="color:var(--text-muted); max-width:500px; margin:0 auto; line-height:1.7">
                Excellent work, <strong>{{ session('lms_full_name', auth()->user()->name) }}</strong>!
                {{ $section->subheading ?? "You've completed your demo learning session. Your submission is under review." }}
            </p>
        </div>

        {{-- ── Summary Checks ── --}}
        <div class="summary-grid">
            @foreach ([['Video Uploaded', 'Your demo is saved', '🎬'], ['Description Saved', 'Topic documented', '📝'], ['Course Selected', session('lms_course_label', 'Your Course'), '📚']] as $i => $item)
                <div class="summary-item" style="animation-delay:{{ $i * 0.12 }}s">
                    <div class="summary-check"><i class="fas fa-check"></i></div>
                    <div>
                        <strong style="font-size:.85rem; display:block">{{ $item[0] }}</strong>
                        <span style="font-size:.75rem; color:var(--text-muted)">{{ $item[1] }}</span>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- ── Score ── --}}
        {{-- <div class="score-card">
            <div class="score-number" id="scoreNum">0</div>
            <div style="color:var(--text-muted); font-size:.85rem; margin-top:4px">Demo Completion Score</div>
            <div class="score-bar-bg">
                <div class="score-bar-fill" id="scoreBar"></div>
            </div>
            <p style="font-size:.78rem; color:var(--text-muted); margin-top:8px">
                {{ $section->description ?? '⭐ Score updated after mentor review within 24 hrs' }}
            </p>
        </div> --}}

        {{-- ── What's Next ── --}}
        <p
            style="font-family:'Sora',sans-serif; font-size:.76rem; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:.08em; margin-bottom:12px">
            <i class="fas fa-road" style="color:var(--brand-primary); margin-right:6px"></i> What Happens Next
        </p>
        <div class="next-steps">
            <div class="next-step-item">
                <div class="next-icon" style="background:rgba(108,63,245,.15)">🔍</div>
                <div class="next-text">
                    <strong>Mentor Reviews Your Demo</strong>
                    <span>Our team evaluates your video within 24 hours</span>
                </div>
            </div>
            <div class="next-step-item">
                <div class="next-icon" style="background:rgba(255,217,61,.15)">📧</div>
                <div class="next-text">
                    <strong>You'll Receive Feedback</strong>
                    <span>Personalized review sent to {{ session('lms_email_phone', 'your contact') }}</span>
                </div>
            </div>
            <div class="next-step-item">
                <div class="next-icon" style="background:rgba(107,203,119,.15)">🏆</div>
                <div class="next-text">
                    <strong>Unlock Full Course Access</strong>
                    <span>Get access to complete training modules &amp; certificate</span>
                </div>
            </div>
        </div>

        <div class="tip-box">
            <strong>🤖 Guide says:</strong>
            "{{ $section->bitmoji_message ?? 'Well done! You\'re ready for the next level 🚀' }}"
        </div>

        {{-- ═══════════════════════════════════════════
         FEEDBACK SECTION
    ═══════════════════════════════════════════ --}}
        @if (!$feedback)
            <div class="section-divider">Share Your Experience</div>

            <div class="feedback-section" id="feedbackSection">
                <div class="feedback-heading">
                    💬 How Was Your Demo Experience?
                </div>
                <p class="feedback-sub">Your feedback helps us improve the learning journey for every student.</p>

                {{-- ── Success state (shown after submit) ── --}}
                <div class="feedback-success" id="feedbackSuccess">
                    <div class="icon">🙏</div>
                    <h3>Thank You for Your Feedback!</h3>
                    <p>Your response has been saved. We'll use it to make the experience even better.</p>
                    <div style="margin-top:16px; font-size:1.8rem" id="thankYouEmoji"></div>
                </div>

                <div class="feedback-form-inner" id="feedbackFormInner">

                    {{-- ── Step 1: Emoji Reaction ── --}}
                    <p class="star-section-label" style="margin-bottom:10px">
                        <i class="fas fa-smile" style="color:var(--brand-primary); margin-right:5px"></i>
                        How do you feel about this demo?
                    </p>
                    <div class="emoji-row" id="emojiRow">
                        @php
                            $emojis = [
                                ['😍', 'Loved it!'],
                                ['😊', 'Pretty Good'],
                                ['😐', 'It\'s OK'],
                                ['😞', 'Not Great'],
                                ['😡', 'Frustrated'],
                            ];
                        @endphp
                        @foreach ($emojis as $e)
                            <div class="emoji-btn" data-emoji="{{ $e[0] }}" data-label="{{ $e[1] }}"
                                onclick="selectEmoji(this)">
                                <span class="emoji-face">{{ $e[0] }}</span>
                                <span class="emoji-label">{{ $e[1] }}</span>
                            </div>
                        @endforeach
                    </div>

                    {{-- ── Step 2: Star Ratings ── --}}
                    <div class="star-section">
                        <p class="star-section-label">
                            <i class="fas fa-star" style="color:#FFD93D; margin-right:5px"></i>
                            Rate different aspects
                        </p>
                        <div class="star-row-wrap">
                            @php
                                $ratingAspects = [
                                    ['overall', 'content_rating', '📚', 'Course Content Quality'],
                                    ['clarity', 'clarity_rating', '🔊', 'Explanation Clarity'],
                                    ['support', 'support_rating', '🤝', 'Demo Support & Guidance'],
                                ];
                            @endphp
                            @foreach ($ratingAspects as $aspect)
                                <div class="star-row-item">
                                    <div class="label">{{ $aspect[2] }} {{ $aspect[3] }}</div>
                                    <div class="stars" data-aspect="{{ $aspect[1] }}">
                                        @for ($s = 1; $s <= 5; $s++)
                                            <button type="button" class="star-btn" data-val="{{ $s }}"
                                                onclick="rateStar(this)">★</button>
                                        @endfor
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- ── Step 3: Liked Tags ── --}}
                    <div class="tag-section">
                        <p class="tag-section-label">
                            <i class="fas fa-thumbs-up" style="color:var(--brand-green); margin-right:5px"></i>
                            What did you like? <span
                                style="font-weight:400; font-size:.72rem; color:var(--text-muted)">(select all that
                                apply)</span>
                        </p>
                        <div class="tag-pills" id="likedTags">
                            @foreach (['Easy to follow', 'Great examples', 'Clear explanation', 'Good pacing', 'Helpful bitmoji tips', 'Loved the video', 'Nice UI/UX', 'Step-by-step flow'] as $tag)
                                <div class="tag-pill liked" onclick="toggleTag(this,'liked')">{{ $tag }}</div>
                            @endforeach
                        </div>
                    </div>

                    {{-- ── Step 4: Improve Tags ── --}}
                    <div class="tag-section">
                        <p class="tag-section-label">
                            <i class="fas fa-tools" style="color:#FF9F43; margin-right:5px"></i>
                            What could be improved? <span
                                style="font-weight:400; font-size:.72rem; color:var(--text-muted)">(optional)</span>
                        </p>
                        <div class="tag-pills" id="improveTags">
                            @foreach (['More examples', 'Longer demo video', 'Clearer instructions', 'More course options', 'Faster loading', 'Better mobile view', 'More feedback from mentor', 'Add live sessions'] as $tag)
                                <div class="tag-pill improve" onclick="toggleTag(this,'improve')">{{ $tag }}</div>
                            @endforeach
                        </div>
                    </div>

                    {{-- ── Step 5: Text Message ── --}}
                    <div>
                        <p class="star-section-label">
                            <i class="fas fa-pen" style="color:var(--brand-primary); margin-right:5px"></i>
                            Anything else you'd like to share?
                        </p>
                        <textarea class="feedback-textarea" id="feedbackMsg"
                            placeholder="Write your thoughts here... What helped you most? What would make this better?" maxlength="500"></textarea>
                        <div class="char-count-fb"><span id="fbCharCount">0</span>/500</div>
                    </div>

                    {{-- ── Step 6: Would Recommend ── --}}
                    <div class="recommend-row">
                        <div class="ques">🗣️ Would you recommend this demo to a friend?</div>
                        <div class="toggle-row">
                            <button type="button" class="toggle-btn yes" id="recYes" onclick="setRecommend(true)">👍
                                Yes</button>
                            <button type="button" class="toggle-btn no" id="recNo" onclick="setRecommend(false)">👎
                                No</button>
                        </div>
                    </div>

                    {{-- ── Submit ── --}}
                    <div id="emojiValidation"
                        style="display:none; color:#FF9F43; font-size:.82rem; margin-bottom:12px; padding:8px 12px; background:rgba(255,159,67,.08); border-radius:8px; border:1px solid rgba(255,159,67,.25)">
                        <i class="fas fa-exclamation-circle"></i> Please select an emoji reaction to submit your feedback.
                    </div>
                    <button type="button" class="btn-feedback" id="submitFeedback" onclick="submitFeedback()">
                        <span id="fbBtnText"><i class="fas fa-paper-plane"></i> Submit Feedback</span>
                    </button>
                </div>

            </div>
        @else
            {{-- ALREADY SUBMITTED STATE --}}
            <div class="feedback-success" style="display:block">
                <div class="icon">🙏</div>
                <h3>You already submitted feedback</h3>
                <p>Thanks for your response! You can’t submit again.</p>
                <p>Your rating: {{ $feedback->emoji_reaction }}</p>
                @if ($feedback->message)
                    <p>Message: {{ $feedback->message }}</p>
                @endif
            </div>
        @endif
        {{-- ── CTA Buttons ── --}}
        <div class="btn-group" style="margin-top:24px">
            <a href="{{ route('lms.step5') }}" class="btn-primary">
                <i class="fas fa-compass"></i> Explore More Courses
            </a>
            <a href="{{ route('lms.step1') }}" class="btn-secondary">
                <i class="fas fa-th-large"></i> Go to Dashboard
            </a>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // ─── State ───────────────────────────────
        let selectedEmoji = null;
        let selectedLabel = null;
        let ratings = {
            content_rating: 0,
            clarity_rating: 0,
            support_rating: 0
        };
        let likedTagsList = [];
        let improveTagsList = [];
        let wouldRecommend = null;

        // ─── Confetti ────────────────────────────
        const canvas = document.getElementById('confettiCanvas');
        canvas.style.cssText = 'position:fixed;inset:0;pointer-events:none;z-index:200';
        const ctx = canvas.getContext('2d');
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
        const conf = Array.from({
            length: 130
        }, () => ({
            x: Math.random() * canvas.width,
            y: -20 - Math.random() * 120,
            r: 4 + Math.random() * 8,
            d: Math.random() * 2 + 1,
            color: ['#6C3FF5', '#FF6B6B', '#FFD93D', '#6BCB77', '#A855F7', '#22D3EE'][Math.floor(Math.random() *
                6)],
            tilt: Math.random() * 10 - 5
        }));
        let frame = 0;

        function draw() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            conf.forEach(c => {
                ctx.beginPath();
                ctx.fillStyle = c.color;
                ctx.rect(c.x, c.y, c.r, c.r * .6);
                ctx.fill();
                c.y += c.d;
                c.x += Math.sin(frame * .01 + c.tilt) * 1.2;
                c.tilt += .05;
                if (c.y > canvas.height) {
                    c.y = -20;
                    c.x = Math.random() * canvas.width;
                }
            });
            frame++;
            if (frame < 320) requestAnimationFrame(draw);
            else ctx.clearRect(0, 0, canvas.width, canvas.height);
        }
        setTimeout(draw, 300);

        // ─── Score counter ───────────────────────

        // setTimeout(() => {
        //     const t = {{ $demo->completion_score ?? 0 }};
        //     let cur = 0;

        //     const el = document.getElementById('scoreNum');

        //     document.getElementById('scoreBar').style.width = t + '%';

        //     const ti = setInterval(() => {
        //         cur = Math.min(cur + 2, t);
        //         el.textContent = cur;

        //         if (cur >= t) {
        //             clearInterval(ti);
        //         }
        //     }, 30);
        // }, 800);


        // ─── Emoji Select ────────────────────────
        function selectEmoji(el) {
            document.querySelectorAll('.emoji-btn').forEach(b => b.classList.remove('selected'));
            el.classList.add('selected');
            selectedEmoji = el.dataset.emoji;
            selectedLabel = el.dataset.label;
            document.getElementById('emojiValidation').style.display = 'none';

            // Update bitmoji guide message
            const msgs = {
                '😍': '😍 Wow, you loved it! That makes us very happy! 💜',
                '😊': '😊 Glad you had a good experience!',
                '😐': '🤔 Got it — we\'ll work on making it better!',
                '😞': '😔 Sorry to hear that. Please share what can improve!',
                '😡': '😮 We\'re sorry! Please tell us what went wrong.',
            };
            const msgEl = document.getElementById('bitmojiMsg');
            if (msgEl) {
                msgEl.textContent = msgs[selectedEmoji] || '';
                msgEl.style.display = 'block';
            }
        }

        // ─── Star Rating ─────────────────────────
        function rateStar(btn) {
            const container = btn.closest('.stars');
            const val = parseInt(btn.dataset.val);
            const aspect = container.dataset.aspect;
            ratings[aspect] = val;

            container.querySelectorAll('.star-btn').forEach((s, i) => {
                s.classList.toggle('lit', i < val);
            });
        }

        // ─── Tag Toggle ──────────────────────────
        function toggleTag(el, type) {
            el.classList.toggle('selected');
            const list = type === 'liked' ? likedTagsList : improveTagsList;
            const idx = list.indexOf(el.textContent);
            if (el.classList.contains('selected')) {
                if (idx < 0) list.push(el.textContent);
            } else {
                if (idx >= 0) list.splice(idx, 1);
            }
        }

        // ─── Recommend ───────────────────────────
        function setRecommend(val) {
            wouldRecommend = val;
            document.getElementById('recYes').classList.toggle('active', val === true);
            document.getElementById('recNo').classList.toggle('active', val === false);
        }

        // ─── Char count ──────────────────────────
        document.getElementById('feedbackMsg').addEventListener('input', function() {
            document.getElementById('fbCharCount').textContent = this.value.length;
        });

        // ─── Submit ──────────────────────────────
        function submitFeedback() {
            if (!selectedEmoji) {
                document.getElementById('emojiValidation').style.display = 'block';
                document.getElementById('emojiRow').scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
                return;
            }

            const btn = document.getElementById('submitFeedback');
            btn.disabled = true;
            document.getElementById('fbBtnText').innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';

            const payload = {
                _token: document.querySelector('meta[name=csrf-token]')?.content || '',
                emoji_reaction: selectedEmoji,
                emoji_label: selectedLabel,
                rating: Math.max(...Object.values(ratings)) || null,
                content_rating: ratings.content_rating || null,
                clarity_rating: ratings.clarity_rating || null,
                support_rating: ratings.support_rating || null,
                message: document.getElementById('feedbackMsg').value,
                liked_tags: likedTagsList,
                improve_tags: improveTagsList,
                would_recommend: wouldRecommend,
            };

            fetch('{{ route('lms.feedback.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': payload._token
                    },
                    body: JSON.stringify(payload)
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        // Show thank-you state
                        document.getElementById('feedbackFormInner').classList.add('hidden');
                        document.getElementById('feedbackSuccess').style.display = 'block';
                        document.getElementById('thankYouEmoji').textContent = selectedEmoji;
                        document.getElementById('feedbackSection').classList.add('submitted-state');

                        // Mini confetti burst
                        frame = 0;
                        setTimeout(draw, 100);

                        // Update bitmoji
                        const msgEl = document.getElementById('bitmojiMsg');
                        if (msgEl) {
                            msgEl.textContent = '🙏 Thank you for your feedback! You\'re amazing! 💜';
                            msgEl.style.display = 'block';
                        }
                    }
                })
                .catch(() => {
                    // Fallback: show success anyway (demo mode)
                    document.getElementById('feedbackFormInner').classList.add('hidden');
                    document.getElementById('feedbackSuccess').style.display = 'block';
                    document.getElementById('thankYouEmoji').textContent = selectedEmoji || '🙏';
                    document.getElementById('feedbackSection').classList.add('submitted-state');
                    frame = 0;
                    setTimeout(draw, 100);
                });
        }

        // ─── Show bitmoji on load ─────────────────
        setTimeout(() => {
            const el = document.getElementById('bitmojiMsg');
            if (el) {
                el.style.display = 'block';
            }
        }, 1200);
    </script>
@endsection
