@extends('layouts.app')

@section('title', 'Manage Quiz')

@section('styles')
<style>
    .editor-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 2.5rem;
        align-items: start;
        margin-top: 2rem;
    }
    @media (min-width: 950px) {
        .editor-grid {
            grid-template-columns: 1fr 400px;
        }
    }
    .question-list-item {
        background: rgba(255, 255, 255, 0.01);
        border: 1px solid var(--glass-border);
        border-radius: 12px;
        margin-bottom: 1rem;
        padding: 1.25rem;
    }
    .option-row {
        display: flex;
        gap: 0.75rem;
        align-items: center;
        margin-bottom: 0.75rem;
    }
    .option-correct-indicator {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: var(--success);
        box-shadow: 0 0 8px var(--success-glow);
    }
</style>
@endsection

@section('content')
<div style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center;">
    <a href="{{ route('creator.index') }}" style="color: var(--cyan); text-decoration: none; font-size: 0.9rem; display: flex; align-items: center; gap: 0.25rem;">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Back to Studio
    </a>

    <span class="tech-font" style="font-weight: 800; color: var(--text-muted); font-size: 0.95rem;">
        COMPILING: <span class="glow-cyan">{{ strtoupper($quiz->title) }}</span>
    </span>
</div>

<div class="editor-grid">
    <!-- Left Column: Bulk Excel Import & Manual Question List -->
    <div style="display: flex; flex-direction: column; gap: 2rem;">
        
        <!-- Bulk Excel Import Panel -->
        <div class="glass-panel" style="border-color: var(--primary);">
            <h3 class="tech-font glow-primary" style="font-size: 1.25rem; font-weight: 800; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem;">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                BULK EXCEL IMPORT
            </h3>
            <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 1.5rem;">
                Quickly import hundreds of questions using our custom Excel structure. 
                <a href="{{ route('creator.template.download') }}" style="color: var(--cyan); font-weight: 600; text-decoration: none;" class="glow-cyan">Download spreadsheet template</a>.
            </p>

            <form action="{{ route('creator.import', $quiz->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="upload-dropzone" id="excel-dropzone">
                    <svg class="upload-icon" width="40" height="40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                    <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                        <span style="font-weight: 600;">Drag spreadsheet file here</span>
                        <span style="font-size: 0.8rem; color: var(--text-muted);">Supports .xlsx, .csv (Max 4MB)</span>
                    </div>
                    <input type="file" name="excel_file" id="excel-file-input" style="display: none;" accept=".xlsx,.xls,.csv" required>
                    <button type="button" class="btn-glass" id="select-file-btn" style="padding: 0.4rem 1rem; font-size: 0.85rem; border-radius: 8px;">Select File</button>
                    <div id="file-details" style="font-size: 0.85rem; color: var(--cyan); display: none; font-weight: 600;"></div>
                </div>

                <div style="margin-top: 1.5rem; text-align: right;">
                    <button type="submit" class="btn-neon tech-font" style="padding: 0.6rem 1.5rem; font-size: 0.85rem;">
                        PROCESS SPREADSHEET
                    </button>
                </div>
            </form>
        </div>

        <!-- Question List View -->
        <div class="glass-panel">
            <h3 class="tech-font" style="font-size: 1.25rem; font-weight: 800; margin-bottom: 1.5rem; border-bottom: 1px solid var(--glass-border); padding-bottom: 0.75rem;">
                COMPILED QUESTIONS ({{ $quiz->questions->count() }})
            </h3>

            @if($quiz->questions->isEmpty())
                <div style="text-align: center; padding: 4rem 2rem; border: 1px dashed var(--glass-border); border-radius: 12px;">
                    <p style="color: var(--text-muted); font-size: 0.95rem; margin-bottom: 1rem;">No questions generated yet.</p>
                    <p style="font-size: 0.85rem; color: var(--text-muted);">Use bulk Excel upload above, or use the Question Constructor on the right.</p>
                </div>
            @else
                <div style="display: flex; flex-direction: column;">
                    @foreach($quiz->questions as $index => $question)
                        <div class="question-list-item">
                            <div style="display: flex; justify-content: space-between; align-items: start; gap: 1rem; margin-bottom: 1rem;">
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <span class="tech-font" style="font-weight: 800; color: var(--cyan); font-size: 0.9rem;">Q{{ $index + 1 }}</span>
                                    <span style="font-size: 0.8rem; background: rgba(255, 255, 255, 0.05); padding: 0.2rem 0.5rem; border-radius: 4px; color: var(--warning); font-weight: 600;">{{ $question->points }} pts</span>
                                </div>
                                <form action="{{ route('creator.question.delete', $question->id) }}" method="POST" style="margin: 0;">
                                    @csrf
                                    <button type="submit" style="background: none; border: none; color: var(--danger); cursor: pointer; padding: 0.2rem;" title="Delete Question">
                                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                            
                            <h4 style="font-size: 1.05rem; font-weight: 500; color: #fff; line-height: 1.4; margin-bottom: 1rem;">{{ $question->question_text }}</h4>
                            
                            <div style="display: grid; grid-template-columns: 1fr; gap: 0.5rem;">
                                @foreach($question->options as $opt)
                                    <div class="option-row" style="opacity: {{ $opt->is_correct ? '1.0' : '0.6' }};">
                                        @if($opt->is_correct)
                                            <div class="option-correct-indicator"></div>
                                        @else
                                            <div style="width: 12px; height: 12px; border-radius: 50%; border: 1.5px solid var(--text-muted);"></div>
                                        @endif
                                        <span style="font-size: 0.9rem; color: {{ $opt->is_correct ? '#fff' : 'var(--text-muted)' }}; font-weight: {{ $opt->is_correct ? '600' : '400' }};">{{ $opt->option_text }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>

    <!-- Right Column: Manual Question Form & Meta details -->
    <div style="display: flex; flex-direction: column; gap: 2rem;">
        
        <!-- Question Constructor Form -->
        <div class="glass-panel" style="box-shadow: 0 0 20px rgba(6, 182, 212, 0.05);">
            <h3 class="tech-font glow-cyan" style="font-size: 1.25rem; font-weight: 800; margin-bottom: 1.5rem;">
                CONSTRUCTOR
            </h3>

            <form action="{{ route('creator.question.store', $quiz->id) }}" method="POST">
                @csrf

                <div class="form-group">
                    <label class="form-label">Question Text</label>
                    <textarea name="question_text" class="form-input" rows="3" placeholder="What is..." required style="resize: vertical;"></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Points Value</label>
                    <input type="number" name="points" class="form-input" value="10" min="1" max="100" required>
                </div>

                <div style="border-top: 1px solid var(--glass-border); padding-top: 1.5rem; margin-top: 1.5rem;">
                    <label class="form-label" style="margin-bottom: 1rem;">Answer Choices & Correct Target</label>
                    
                    @foreach(range(0, 3) as $i)
                        <div class="form-group" style="margin-bottom: 1rem;">
                            <div style="display: flex; gap: 0.75rem; align-items: center;">
                                <input type="radio" name="correct_option" value="{{ $i }}" {{ $i == 0 ? 'checked' : '' }} style="accent-color: var(--success); cursor: pointer; width: 18px; height: 18px;">
                                <input type="text" name="options[]" class="form-input" placeholder="Option {{ chr(65 + $i) }}" required style="padding: 0.5rem 0.8rem; font-size: 0.9rem;">
                            </div>
                        </div>
                    @endforeach
                </div>

                <button type="submit" class="btn-neon tech-font" style="width: 100%; justify-content: center; margin-top: 1rem;">
                    COMPILE QUESTION
                </button>
            </form>
        </div>

        <!-- Quiz settings Meta Update -->
        <div class="glass-panel">
            <h3 class="tech-font" style="font-size: 1.15rem; font-weight: 800; margin-bottom: 1.25rem;">METADATA</h3>
            
            <form action="{{ route('creator.update', $quiz->id) }}" method="POST">
                @csrf
                
                <div class="form-group">
                    <label class="form-label">Title</label>
                    <input type="text" name="title" class="form-input" value="{{ $quiz->title }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Category</label>
                    <input type="text" name="category" class="form-input" value="{{ $quiz->category }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Difficulty</label>
                    <select name="difficulty" class="form-input" required>
                        <option value="easy" {{ $quiz->difficulty == 'easy' ? 'selected' : '' }}>Easy</option>
                        <option value="medium" {{ $quiz->difficulty == 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="hard" {{ $quiz->difficulty == 'hard' ? 'selected' : '' }}>Hard</option>
                    </select>
                </div>

                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label class="form-label">Limit (seconds)</label>
                    <input type="number" name="time_limit" class="form-input" value="{{ $quiz->time_limit }}" required>
                </div>

                <button type="submit" class="btn-glass" style="width: 100%; justify-content: center;">
                    UPDATE CORE CONFIG
                </button>
            </form>
        </div>

    </div>
</div>
@endsection

@section('scripts')
<script>
    // Drag and Drop files JS
    const dropzone = document.getElementById('excel-dropzone');
    const fileInput = document.getElementById('excel-file-input');
    const selectBtn = document.getElementById('select-file-btn');
    const fileDetails = document.getElementById('file-details');

    selectBtn.addEventListener('click', () => {
        fileInput.click();
    });

    fileInput.addEventListener('change', () => {
        showFile(fileInput.files[0]);
    });

    dropzone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropzone.classList.add('dragover');
    });

    dropzone.addEventListener('dragleave', () => {
        dropzone.classList.remove('dragover');
    });

    dropzone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropzone.classList.remove('dragover');
        if (e.dataTransfer.files.length) {
            fileInput.files = e.dataTransfer.files;
            showFile(e.dataTransfer.files[0]);
        }
    });

    function showFile(file) {
        if (file) {
            fileDetails.textContent = `Selected: ${file.name} (${(file.size / 1024).toFixed(1)} KB)`;
            fileDetails.style.display = 'block';
        } else {
            fileDetails.style.display = 'none';
        }
    }
</script>
@endsection
