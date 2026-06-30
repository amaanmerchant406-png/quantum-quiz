@extends('layouts.app')

@section('title', 'Testing Session')

@section('styles')
<style>
    .timer-container {
        position: relative;
        width: 100px;
        height: 100px;
        margin: 0 auto 1.5rem auto;
    }
    #timer-canvas {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
    }
    .timer-text {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 1.5rem;
        font-weight: 800;
        color: #fff;
    }
    .hud-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        border-bottom: 1px solid var(--glass-border);
        padding-bottom: 1rem;
    }
    .streak-container {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        background: rgba(236, 72, 153, 0.1);
        border: 1px solid var(--magenta);
        padding: 0.35rem 0.8rem;
        border-radius: 8px;
        font-size: 0.85rem;
        font-weight: 700;
        color: var(--magenta);
        box-shadow: 0 0 10px rgba(236, 72, 153, 0.2);
    }
    .multiplier-badge {
        background: var(--warning);
        color: #000;
        padding: 0.2rem 0.5rem;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 800;
    }
</style>
@endsection

@section('content')
<div class="play-wrapper">
    <!-- Quiz HUD Status bar -->
    <div class="hud-bar">
        <div>
            <span style="color: var(--text-muted); font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px;">Session</span>
            <h2 class="tech-font" style="font-size: 1.15rem; font-weight: 800; color: #fff;">{{ $quiz->title }}</h2>
        </div>
        
        <div style="display: flex; align-items: center; gap: 1.5rem;">
            <div id="streak-indicator" class="streak-container" style="display: none;">
                <span>STREAK: <span id="streak-count">0</span></span>
                <span class="multiplier-badge" id="multiplier-count">1.0x XP</span>
            </div>
            
            <div style="text-align: right;">
                <span style="color: var(--text-muted); font-size: 0.85rem; text-transform: uppercase;">Points</span>
                <div class="tech-font" id="live-points" style="font-size: 1.25rem; font-weight: 800; color: var(--warning);">0</div>
            </div>
        </div>
    </div>

    <!-- Active Question Card Wrapper -->
    <div class="glass-panel play-card" id="question-card" style="box-shadow: 0 10px 40px rgba(6, 182, 212, 0.1);">
        
        <!-- Timer Canvas -->
        <div class="timer-container">
            <canvas id="timer-canvas" width="200" height="200"></canvas>
            <div class="timer-text tech-font" id="timer-label">0</div>
        </div>

        <!-- Question Tracker -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <span class="tech-font" id="question-tracker" style="color: var(--cyan); font-size: 0.85rem; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px;">Question 1 of 5</span>
            <span id="question-difficulty" class="quiz-difficulty diff-medium" style="position: static; font-size: 0.65rem; padding: 0.15rem 0.4rem;">medium</span>
        </div>

        <!-- Question Statement -->
        <h2 id="question-text" style="font-size: 1.35rem; font-weight: 600; line-height: 1.4; margin-bottom: 2rem; min-height: 70px;">
            Initializing node...
        </h2>

        <!-- Answer Options -->
        <div class="answers-grid" id="answers-container">
            <!-- Populated dynamically via JS -->
        </div>

    </div>
</div>
@endsection

@section('scripts')
<script>
    // Load database questions array passed from PHP controller
    const quizData = @json($quiz->questions);
    const quizDefaultTimeLimit = {{ $quiz->time_limit }};
    const submitUrl = "{{ route('quiz.submit', $quiz->id) }}";
    
    let currentIdx = 0;
    let score = 0;
    let streak = 0;
    let answersLog = {}; // question_id => option_id
    let startTime = Date.now();
    
    // Timer tracking
    let questionTimerVal = 0;
    let timerInterval = null;
    let canvasAnimationId = null;
    
    // DOM Elements
    const qCard = document.getElementById('question-card');
    const qTracker = document.getElementById('question-tracker');
    const qDifficulty = document.getElementById('question-difficulty');
    const qText = document.getElementById('question-text');
    const ansContainer = document.getElementById('answers-container');
    const pointsLabel = document.getElementById('live-points');
    const streakContainer = document.getElementById('streak-indicator');
    const streakLabel = document.getElementById('streak-count');
    const multLabel = document.getElementById('multiplier-count');
    const timerLabel = document.getElementById('timer-label');
    const timerCanvas = document.getElementById('timer-canvas');
    const timerCtx = timerCanvas.getContext('2d');
    
    // Canvas Timer particles
    let particles = [];
    
    function initQuestion() {
        if (currentIdx >= quizData.length) {
            endQuizSession();
            return;
        }

        const q = quizData[currentIdx];
        
        // Update trackers
        qTracker.textContent = `Question ${currentIdx + 1} of ${quizData.length}`;
        qText.textContent = q.question_text;
        
        // Calculate limit (prioritize question override limit, fallback to quiz default)
        questionTimerVal = q.time_limit || quizDefaultTimeLimit;
        timerLabel.textContent = questionTimerVal;
        
        // Options build
        ansContainer.innerHTML = '';
        const letters = ['A', 'B', 'C', 'D', 'E', 'F'];
        q.options.forEach((opt, idx) => {
            const btn = document.createElement('div');
            btn.className = 'answer-option';
            btn.innerHTML = `
                <span class="answer-letter">${letters[idx]}</span>
                <span class="answer-text">${escapeHtml(opt.option_text)}</span>
            `;
            btn.addEventListener('click', () => selectAnswer(opt.id, btn, q.options));
            ansContainer.appendChild(btn);
        });

        // Apply 3D Entrance transition
        qCard.classList.remove('slide-out');
        qCard.classList.add('slide-in');
        requestAnimationFrame(() => {
            qCard.classList.remove('slide-in');
        });
        
        // Start timers
        startQuestionTimer();
    }
    
    function selectAnswer(selectedOptId, selectedBtn, optionsList) {
        // Stop timer
        clearInterval(timerInterval);
        cancelAnimationFrame(canvasAnimationId);
        
        // Log selection
        answersLog[quizData[currentIdx].id] = selectedOptId;
        
        // Disable further clicking
        const optionButtons = ansContainer.querySelectorAll('.answer-option');
        optionButtons.forEach(btn => {
            btn.style.pointerEvents = 'none';
        });
        
        // Correct option checking
        const correctOpt = optionsList.find(o => o.is_correct);
        const isCorrect = selectedOptId === correctOpt.id;
        
        // Visual highlights
        optionButtons.forEach((btn, index) => {
            const opt = optionsList[index];
            if (opt.id === correctOpt.id) {
                btn.classList.add('correct');
            } else if (opt.id === selectedOptId && !isCorrect) {
                btn.classList.add('incorrect');
            }
        });
        
        if (isCorrect) {
            // Scoring
            const qPoints = quizData[currentIdx].points || 10;
            const multiplier = Math.min(4, 1 + Math.floor(streak / 3));
            score += qPoints * multiplier;
            pointsLabel.textContent = score;
            
            // Streak
            streak++;
            streakContainer.style.display = 'flex';
            streakLabel.textContent = streak;
            multLabel.textContent = `${(Math.min(4, 1 + Math.floor(streak / 3)))}x XP`;
            
            // Success Confetti
            triggerSuccessConfetti();
        } else {
            // Break streak
            streak = 0;
            streakContainer.style.display = 'none';
        }
        
        // Card exit transition in 1.8 seconds
        setTimeout(() => {
            qCard.classList.add('slide-out');
            setTimeout(() => {
                currentIdx++;
                initQuestion();
            }, 600);
        }, 1800);
    }
    
    function startQuestionTimer() {
        clearInterval(timerInterval);
        cancelAnimationFrame(canvasAnimationId);
        
        const initialTime = questionTimerVal;
        let lastTime = Date.now();
        
        // Initialize timer canvas particles
        particles = [];
        
        timerInterval = setInterval(() => {
            questionTimerVal--;
            timerLabel.textContent = questionTimerVal;
            
            if (questionTimerVal <= 0) {
                // Timeout logic
                clearInterval(timerInterval);
                cancelAnimationFrame(canvasAnimationId);
                
                // Set default wrong answer log
                answersLog[quizData[currentIdx].id] = null;
                streak = 0;
                streakContainer.style.display = 'none';
                
                // Visual timeout alert
                const optionButtons = ansContainer.querySelectorAll('.answer-option');
                optionButtons.forEach(btn => {
                    btn.style.pointerEvents = 'none';
                    btn.classList.add('incorrect');
                });
                
                setTimeout(() => {
                    qCard.classList.add('slide-out');
                    setTimeout(() => {
                        currentIdx++;
                        initQuestion();
                    }, 600);
                }, 1500);
            }
        }, 1000);
        
        // Start canvas animations
        animateCanvasTimer(initialTime);
    }
    
    function animateCanvasTimer(initialTime) {
        const radius = 90;
        const cx = 100;
        const cy = 100;
        
        function drawFrame() {
            timerCtx.clearRect(0, 0, timerCanvas.width, timerCanvas.height);
            
            // Calculate percentage
            // Since JS interval runs separate, we can interpolate slightly or just use division
            const pct = questionTimerVal / initialTime;
            
            // Draw background ring
            timerCtx.beginPath();
            timerCtx.arc(cx, cy, radius, 0, Math.PI * 2);
            timerCtx.strokeStyle = 'rgba(255,255,255,0.05)';
            timerCtx.lineWidth = 10;
            timerCtx.stroke();
            
            // Draw ticking colored ring
            timerCtx.beginPath();
            // Start at top (-PI/2) and draw clockwise based on pct
            const endAngle = (-Math.PI / 2) + (Math.PI * 2 * pct);
            timerCtx.arc(cx, cy, radius, -Math.PI / 2, endAngle, false);
            
            // Gradient determination (primary purple to cyan, alerts warning red under 5 seconds)
            let strokeColor = '#06b6d4';
            if (questionTimerVal <= 5) {
                strokeColor = '#ef4444';
            } else if (questionTimerVal <= (initialTime / 2)) {
                strokeColor = '#8b5cf6';
            }
            
            timerCtx.strokeStyle = strokeColor;
            timerCtx.lineWidth = 10;
            timerCtx.lineCap = 'round';
            timerCtx.stroke();
            
            // Emit particles at leading edge of arc
            if (pct > 0) {
                const angle = endAngle;
                const px = cx + Math.cos(angle) * radius;
                const py = cy + Math.sin(angle) * radius;
                
                if (Math.random() < 0.3) {
                    particles.push({
                        x: px,
                        y: py,
                        vx: (Math.random() - 0.5) * 2,
                        vy: (Math.random() - 0.5) * 2,
                        life: 1.0,
                        color: strokeColor
                    });
                }
            }
            
            // Render particles
            particles.forEach((p, idx) => {
                p.x += p.vx;
                p.y += p.vy;
                p.life -= 0.02;
                
                timerCtx.beginPath();
                timerCtx.arc(p.x, p.y, Math.max(1, p.life * 3), 0, Math.PI * 2);
                timerCtx.fillStyle = p.color;
                timerCtx.globalAlpha = p.life;
                timerCtx.fill();
                timerCtx.globalAlpha = 1.0;
                
                if (p.life <= 0) particles.splice(idx, 1);
            });
            
            canvasAnimationId = requestAnimationFrame(drawFrame);
        }
        
        drawFrame();
    }
    
    function triggerSuccessConfetti() {
        // Minor local confetti simulation on correct answer click
        for (let i = 0; i < 20; i++) {
            particles.push({
                x: 100 + (Math.random() - 0.5) * 40,
                y: 100 + (Math.random() - 0.5) * 40,
                vx: (Math.random() - 0.5) * 6,
                vy: (Math.random() - 0.5) * 6,
                life: 1.0,
                color: '#10b981'
            });
        }
    }
    
    function endQuizSession() {
        clearInterval(timerInterval);
        cancelAnimationFrame(canvasAnimationId);
        
        const totalDuration = Math.round((Date.now() - startTime) / 1000);
        
        qText.textContent = "Transmitting response data...";
        ansContainer.innerHTML = `
            <div style="grid-column: 1/-1; text-align: center; padding: 2rem;">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="var(--cyan)" stroke-width="2" class="glow-cyan" style="animation: spin 1s infinite linear;">
                    <path d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 8H17" />
                </svg>
            </div>
        `;
        
        // POST to backend
        fetch(submitUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                answers: answersLog,
                time_taken: totalDuration
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // If level up occurred, store flag in localStorage so target view can trigger the confetti event
                if (data.level_up) {
                    localStorage.setItem('level_up_occurred', 'true');
                }
                window.location.href = data.redirect_url;
            } else {
                alert('Submission failed. Returning to dashboard.');
                window.location.href = '/';
            }
        })
        .catch(err => {
            console.error(err);
            alert('Network connection error. Redirecting.');
            window.location.href = '/';
        });
    }

    function escapeHtml(text) {
        return text
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    // Initialize first question on load
    initQuestion();
</script>
<style>
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>
@endsection
