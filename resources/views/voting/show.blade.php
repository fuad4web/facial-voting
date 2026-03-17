@extends('layouts.voting-app')

@section('title', 'Vote for ' . $category->name)

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="mb-6">
                    <h2 class="text-2xl font-bold">{{ $category->name }}</h2>
                    <p class="text-gray-600">{{ $category->description }}</p>
                </div>

                <!-- Face Verification Step -->
                <div id="faceVerificationSection" class="mb-8 p-6 bg-gray-50 rounded-lg">
                    <h3 class="text-lg font-semibold mb-4">Step 1: Face Verification</h3>
                    <p class="text-sm text-gray-600 mb-4">Please look at the camera to verify your identity before voting.</p>
                    
                    <div class="relative bg-gray-900 rounded-lg overflow-hidden" style="height: 300px; max-width: 500px;">
                        <video id="video" width="100%" height="100%" autoplay muted class="object-cover"></video>
                        <canvas id="canvas" class="absolute top-0 left-0 w-full h-full"></canvas>
                    </div>
                    
                    <div id="faceStatus" class="mt-3 text-sm"></div>
                    
                    <button type="button" id="verifyFaceBtn" 
                            class="mt-4 bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed"
                            disabled>
                        Verify Face
                    </button>
                    
                    <input type="hidden" id="facial_descriptors" name="facial_descriptors">
                </div>

                <!-- Candidates Section (initially hidden) -->
                <div id="candidatesSection" style="display: none;">
                    <h3 class="text-lg font-semibold mb-4">Step 2: Select Your Candidate</h3>
                    
                    <form id="voteForm">
                        @csrf
                        <input type="hidden" name="candidate_id" id="selected_candidate">
                        <input type="hidden" name="facial_descriptors" id="vote_facial_descriptors">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @foreach($candidates as $candidate)
                                <div class="border rounded-lg p-4 cursor-pointer candidate-card hover:border-indigo-500 transition"
                                     data-id="{{ $candidate->id }}">
                                    @if($candidate->photo)
                                        <img src="{{ asset('storage/' . $candidate->photo) }}" class="w-32 h-32 object-cover rounded-full mx-auto mb-3">
                                    @else
                                        <div class="w-32 h-32 bg-gray-200 rounded-full mx-auto mb-3 flex items-center justify-center text-gray-500">No Photo</div>
                                    @endif
                                    <h4 class="text-xl font-semibold text-center">{{ $candidate->name }}</h4>
                                    @if($candidate->party)
                                        <p class="text-gray-600 text-center">{{ $candidate->party }}</p>
                                    @endif
                                    <p class="text-sm text-gray-500 mt-2">{{ Str::limit($candidate->bio, 100) }}</p>
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="mt-6 flex justify-end">
                            <button type="submit" id="submitVoteBtn" 
                                    class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed"
                                    disabled>
                                Cast Vote
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Face-api and JS -->
<script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // DOM elements
    const video = document.getElementById('video');
    const canvas = document.getElementById('canvas');
    const verifyBtn = document.getElementById('verifyFaceBtn');
    const faceStatus = document.getElementById('faceStatus');
    const facialDescriptors = document.getElementById('facial_descriptors');
    const voteFacialDescriptors = document.getElementById('vote_facial_descriptors');
    const candidatesSection = document.getElementById('candidatesSection');
    const faceVerificationSection = document.getElementById('faceVerificationSection');
    const candidateCards = document.querySelectorAll('.candidate-card');
    const selectedCandidateInput = document.getElementById('selected_candidate');
    const submitVoteBtn = document.getElementById('submitVoteBtn');
    const voteForm = document.getElementById('voteForm');

    let faceDetected = false;
    let faceVerified = false;
    let detectionInterval = null;
    let modelsLoaded = false;

    // Load models
    async function loadModels() {
        faceStatus.textContent = 'Loading face detection models...';
        try {
            await Promise.all([
                faceapi.nets.tinyFaceDetector.loadFromUri('/models'),
                faceapi.nets.faceLandmark68Net.loadFromUri('/models'),
                faceapi.nets.faceRecognitionNet.loadFromUri('/models')
            ]);
            modelsLoaded = true;
            faceStatus.textContent = 'Camera initializing...';
            startVideo();
        } catch (error) {
            faceStatus.textContent = 'Error loading models.';
            faceStatus.classList.add('text-red-500');
        }
    }

    // Start video
    async function startVideo() {
        try {
            const stream = await navigator.mediaDevices.getUserMedia({ video: true });
            video.srcObject = stream;
        } catch (err) {
            faceStatus.textContent = 'Camera access denied.';
            faceStatus.classList.add('text-red-500');
        }
    }

    // Face detection
    video.addEventListener('play', () => {
        if (!modelsLoaded) return;
        
        const displaySize = { width: video.width, height: video.height };
        faceapi.matchDimensions(canvas, displaySize);

        detectionInterval = setInterval(async () => {
            const detections = await faceapi.detectAllFaces(video, 
                new faceapi.TinyFaceDetectorOptions()).withFaceLandmarks().withFaceDescriptors();
            
            const resizedDetections = faceapi.resizeResults(detections, displaySize);
            canvas.getContext('2d').clearRect(0, 0, canvas.width, canvas.height);
            
            if (detections.length === 1 && !faceVerified) {
                faceDetected = true;
                faceapi.draw.drawDetections(canvas, resizedDetections);
                faceStatus.textContent = 'Face detected. Click "Verify Face" to continue.';
                faceStatus.classList.remove('text-red-500');
                faceStatus.classList.add('text-green-600');
                verifyBtn.disabled = false;
            } else if (detections.length > 1) {
                faceDetected = false;
                faceStatus.textContent = 'Multiple faces detected.';
                faceStatus.classList.add('text-red-500');
                verifyBtn.disabled = true;
            } else if (!faceVerified) {
                faceDetected = false;
                faceStatus.textContent = 'No face detected.';
                faceStatus.classList.add('text-red-500');
                verifyBtn.disabled = true;
            }
        }, 100);
    });

    // Verify face
    verifyBtn.addEventListener('click', async () => {
        if (!faceDetected) {
            Swal.fire('No face detected', 'Please position your face in the camera.', 'warning');
            return;
        }

        verifyBtn.disabled = true;
        verifyBtn.textContent = 'Verifying...';

        try {
            const detections = await faceapi.detectAllFaces(video, 
                new faceapi.TinyFaceDetectorOptions()).withFaceLandmarks().withFaceDescriptors();
            
            if (detections.length === 1) {
                const descriptor = detections[0].descriptor;
                facialDescriptors.value = JSON.stringify(Array.from(descriptor));
                voteFacialDescriptors.value = facialDescriptors.value;

                // We'll verify on server when submitting vote, but we can also do a quick check?
                // For UX, we can assume it's good and show candidates.
                faceVerified = true;
                
                // Stop video
                if (video.srcObject) {
                    video.srcObject.getTracks().forEach(track => track.stop());
                }
                clearInterval(detectionInterval);
                
                // Hide verification section, show candidates
                faceVerificationSection.style.display = 'none';
                candidatesSection.style.display = 'block';
                
                Swal.fire('Face Verified', 'You can now select your candidate and vote.', 'success');
            } else {
                throw new Error('Face detection failed.');
            }
        } catch (error) {
            Swal.fire('Verification Failed', error.message, 'error');
            verifyBtn.disabled = false;
            verifyBtn.textContent = 'Verify Face';
        }
    });

    // Candidate selection
    candidateCards.forEach(card => {
        card.addEventListener('click', function() {
            // Remove selected class from all
            candidateCards.forEach(c => c.classList.remove('border-indigo-600', 'bg-indigo-50'));
            // Add selected class
            this.classList.add('border-indigo-600', 'bg-indigo-50');
            
            const candidateId = this.dataset.id;
            selectedCandidateInput.value = candidateId;
            submitVoteBtn.disabled = false;
        });
    });

    // Submit vote
    voteForm.addEventListener('submit', async function(e) {
        e.preventDefault();

        if (!selectedCandidateInput.value) {
            Swal.fire('No candidate selected', 'Please select a candidate.', 'warning');
            return;
        }

        submitVoteBtn.disabled = true;
        submitVoteBtn.textContent = 'Casting Vote...';

        const formData = new FormData();
        formData.append('candidate_id', selectedCandidateInput.value);
        formData.append('facial_descriptors', voteFacialDescriptors.value);

        try {
            const response = await fetch('{{ route("voting.store", $category) }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json',
                },
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Vote Cast!',
                    text: data.message,
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = data.redirect;
                });
            } else {
                Swal.fire('Error', data.message, 'error');
                submitVoteBtn.disabled = false;
                submitVoteBtn.textContent = 'Cast Vote';
            }
        } catch (error) {
            Swal.fire('Error', 'Something went wrong.', 'error');
            submitVoteBtn.disabled = false;
            submitVoteBtn.textContent = 'Cast Vote';
        }
    });

    // Initialize
    loadModels();

    // Cleanup
    window.addEventListener('beforeunload', () => {
        if (video.srcObject) {
            video.srcObject.getTracks().forEach(track => track.stop());
        }
        if (detectionInterval) {
            clearInterval(detectionInterval);
        }
    });
</script>
@endsection
