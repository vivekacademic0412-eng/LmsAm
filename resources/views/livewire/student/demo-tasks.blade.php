  {{-- Demo Practice Tasks --}}
  <section class="d-card">
      <div class="d-section-head" style="margin-bottom:20px;">
          <div>
              <h2>🎓 Demo Practice Tasks</h2>
              <p>
                  Welcome to your practice workspace. These demo tasks are designed to help you
                  understand how learning materials, assignments, submissions, and file uploads
                  work on the platform before you start real coursework.
              </p>
          </div>
      </div>

      <div class="d-demo-intro">
          <div class="d-demo-icon">🚀</div>
          <div>
              <h3>Getting Started</h3>
              <p>
                  Watch the instructional video, download the provided resources,
                  complete the task, and submit your answer. This is a safe practice
                  environment where you can explore the platform workflow with confidence.
              </p>
          </div>
      </div>

      <div class="d-demo-grid">
          @forelse ($demoAssignments as $row)

              @php
                  $task = $row['task'];
                  $submission = $row['submission'];
                  $assignment = $row['assignment'];

                  $canDownload = !empty($task?->resource_file_path);
                  $hasTools = !empty($task?->ai_video_url);
              @endphp

              <div class="d-demo-task">

                  @if (!empty($task?->task_video_path))
                      <div class="d-task-video">
                          <div class="d-task-video-note">
                              📹 Watch this short guide first, then complete the practice task below.
                          </div>

                          <video controls preload="metadata" controlslist="nodownload" playsinline>
                              <source src="{{ route('demo-tasks.video', $task) }}"
                                  type="{{ $task->task_video_mime ?: 'video/mp4' }}">
                          </video>
                      </div>
                  @endif

                  <strong>
                      {{ $task?->title ?? 'Practice Assignment' }}
                  </strong>

                  <div class="d-demo-task-meta">
                      {{ $task?->description ??
                          'Complete this practice activity to learn how assignment submissions, document uploads, and learning resources work within the platform.' }}
                  </div>

                  @if ($canDownload || $hasTools)
                      <div class="d-task-actions">

                          @if ($canDownload)
                              {{-- <a class="d-btn d-btn-ghost d-btn-sm"
                                href="{{ route('demo-tasks.download', $task) }}">
                                📥 Download Resource
                            </a> --}}
                              <a class="d-btn d-btn-ghost d-btn-sm" href="{{ route('demo-tasks.download', $task) }}"
                                  onclick="return confirmDownload(event, this)">
                                  📥 Download Resource
                              </a>
                          @endif

                          @if ($hasTools)
                              <a class="d-btn d-btn-ghost d-btn-sm" href="{{ $task->ai_video_url }}" target="_blank"
                                  rel="noopener">
                                  🛠 Learning Tools
                              </a>
                          @endif

                      </div>
                  @endif

                  @if ($assignment)
                      <form method="POST" action="{{ route('demo-assignments.submit', $assignment) }}"
                          enctype="multipart/form-data" class="d-submit-panel">
                      {{-- <form class="d-submit-panel"> --}}
                          @csrf

                          <div class="d-submit-block">
                              <h4>✍️ Your Answer</h4>
                              <textarea wire:model.defer="answer_text" rows="4"
                                  placeholder="Write your answer, explanation, observations, or solution here..."></textarea>


                              <div class="d-muted" style="font-size:12px;">
                                  Submit your response just like you would for a real assignment.
                              </div>
                          </div>

                          <div class="d-submit-block">
                              <h4>📎 Upload Supporting File</h4>

                              <input type="file" wire:model="submission_file" accept="*/*">

                              <div class="d-muted" style="font-size:12px;">
                                  Accepted formats: PDF, DOCX, PPT, ZIP, Images, Videos and other supporting files.
                              </div>
                          </div>

                          <div class="d-task-actions">

                              <button type="button" class="d-btn d-btn-primary d-btn-sm" onclick="confirmSubmitTask()">
                                  🚀 Submit Practice Task
                              </button>
                              @if ($submission && $submission->file_path)
                                  <a class="d-btn d-btn-ghost d-btn-sm"
                                      href="{{ route('demo-tasks.submissions.download', $submission) }}"
                                      onclick="return confirmDownload(event, this)">
                                      📥 Download Your File
                                  </a>
                                  {{-- <a class="d-btn d-btn-ghost d-btn-sm" href="{{ route('demo-tasks.download', $task) }}"
                                  onclick="return confirmDownload(event, this)">
                                  📥 Download Resource
                              </a> --}}
                              @endif

                          </div>

                          @if ($submission)
                              <div class="d-submit-preview">

                                  <strong>✅ Your Latest Submission</strong>

                                  @if ($submission->answer_text)
                                      <p>{{ $submission->answer_text }}</p>
                                  @else
                                      <p>No written answer submitted yet.</p>
                                  @endif

                                  <div class="d-submit-file">

                                      <span>
                                          {{ $submission->file_name ?: 'No file uploaded' }}
                                      </span>

                                      @if ($submission->file_path)
                                          <a class="d-btn d-btn-ghost d-btn-sm"
                                              href="{{ route('demo-tasks.submissions.download', $submission) }}"
                                              onclick="return confirmDownload(event, this)">
                                              Download
                                          </a>
                                      @endif

                                  </div>

                                  <span class="d-muted" style="font-size:12px;">
                                      Submitted
                                      {{ optional($submission->submitted_at)->diffForHumans() }}
                                  </span>

                              </div>
                          @endif

                      </form>
                  @else
                      <div class="d-sub-empty">
                          <strong>📚 Practice Resource Available</strong>

                          <p style="margin-top:10px;">
                              This task is currently available for learning and exploration.
                          </p>

                          @if ($canDownload)
                              <p>
                                  Download the provided resource to understand the assignment
                                  structure and workflow.
                              </p>
                          @endif

                          <p>
                              Submission functionality will become available once this task
                              has been assigned to your account.
                          </p>
                      </div>
                  @endif

              </div>

          @empty

              <div class="d-sub-empty">
                  <strong>🎯 No Practice Tasks Available Yet</strong>

                  <p style="margin-top:10px;">
                      Demo tasks will appear here once they are published by the administrator.
                      Please check back later.
                  </p>
              </div>

          @endforelse
      </div>
  </section>

  <script>
      function confirmDownload(event, el) {
          event.preventDefault();

          Swal.fire({
              title: 'Download Confirmation',
              text: "Do you want to download this task resource?",
              icon: 'question',
              showCancelButton: true,
              confirmButtonColor: '#3085d6',
              cancelButtonColor: '#d33',
              confirmButtonText: 'Yes, Download',
              cancelButtonText: 'Cancel'
          }).then((result) => {
              if (result.isConfirmed) {
                  window.location.href = el.href;
              }
          });

          return false;
      }

      function confirmSubmitTask() {
          Swal.fire({
              title: 'Submit Task?',
              text: "Are you sure you want to submit this assignment?",
              icon: 'question',
              showCancelButton: true,
              confirmButtonText: 'Yes, Submit',
              cancelButtonText: 'Cancel'
          }).then((result) => {
              if (result.isConfirmed) {
                  Livewire.dispatch('do-submit-task');
              }
          });
      }


      document.addEventListener('livewire:init', () => {

          Livewire.on('success', (event) => {
              Swal.fire({
                  icon: 'success',
                  title: 'Success',
                  text: event.message,
                  timer: 2000,
                  showConfirmButton: false
              });
          });

          Livewire.on('error', (event) => {
              Swal.fire({
                  icon: 'error',
                  title: 'Error',
                  text: event.message,
              });
          });

      });
  </script>
