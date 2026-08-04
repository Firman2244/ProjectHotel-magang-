<div class="modal-content bg-dark text-white p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5>{{ $report->employee->name ?? 'Karyawan' }}</h5>
        <span class="badge bg-success">STATUS: {{ strtoupper($report->status ?? 'COMPLETED') }}</span>
    </div>

    <h6 class="text-muted">Daftar Pekerjaan</h6>

    @forelse($report->tasks as $task)
        <div class="card bg-secondary text-white p-3 mb-3">
            <p class="fw-bold mb-2">{{ $task->task_title ?? $task->name ?? 'Pekerjaan' }}</p>

            <div class="row">
                <div class="col-md-6">
                    <span class="d-block text-muted mb-1">SEBELUM</span>
                    <div class="image-box bg-dark p-2 rounded text-center">
                        <img src="{{ getImageUrl($task->image_before ?? $task->sebelum) }}"
                             alt="Sebelum"
                             class="img-fluid rounded"
                             style="max-height: 180px; object-fit: cover;"
                             onerror="this.onerror=null; this.src='{{ asset('images/fallback.png') }}';">
                    </div>
                </div>

                <div class="col-md-6">
                    <span class="d-block text-muted mb-1">SESUDAH</span>
                    <div class="image-box bg-dark p-2 rounded text-center d-flex align-items-center justify-content-center" style="min-height: 100px;">
                        @php
                            $afterImage = $task->image_after ?? $task->sesudah ?? null;
                        @endphp

                        @if($afterImage)
                            <img src="{{ getImageUrl($afterImage) }}"
                                 alt="Sesudah"
                                 class="img-fluid rounded"
                                 style="max-height: 180px; object-fit: cover;"
                                 onerror="this.onerror=null; this.src='{{ asset('images/fallback.png') }}';">
                        @else
                            <span class="text-muted">Kosong</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="alert alert-secondary text-center">
            <span class="text-muted">Belum ada daftar pekerjaan.</span>
        </div>
    @endforelse
</div>
