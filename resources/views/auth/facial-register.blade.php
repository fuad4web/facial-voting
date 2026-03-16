<x-guest-layout>
    
            <!-- Logo -->
            <div class="flex justify-center mb-8">
                <h1 class="text-2xl font-bold text-gray-900">Secure Voting System</h1>
            </div>

            <!-- Registration Form -->
            <form method="POST" action="{{ route('facial.register') }}" id="registrationForm">
                @csrf

                <!-- Name -->
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input id="password" type="password" name="password" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div class="mb-4">
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <!-- Facial Recognition Section -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Facial Recognition Setup</label>
                    
                    <!-- Video container for face capture -->
                    <div class="relative bg-gray-900 rounded-lg overflow-hidden" style="height: 300px;">
                        <video id="video" width="100%" height="100%" autoplay muted class="object-cover"></video>
                        <canvas id="canvas" class="absolute top-0 left-0 w-full h-full"></canvas>
                    </div>

                    <!-- Face capture button -->
                    <button type="button" id="captureBtn" 
                            class="mt-3 w-full bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        Capture Face
                    </button>

                    <!-- Hidden inputs for facial data -->
                    <input type="hidden" name="facial_descriptors" id="facial_descriptors">
                    <input type="hidden" name="facial_image" id="facial_image">
                    
                    <div id="faceStatus" class="mt-2 text-sm text-center text-gray-600"></div>
                </div>

                <!-- Submit Button -->
                <div class="flex items-center justify-end mt-4">
                    <a class="text-sm text-gray-600 hover:text-gray-900" href="{{ route('login') }}">
                        Already registered?
                    </a>

                    <button type="submit" id="submitBtn" disabled
                            class="ml-4 bg-gray-400 text-white py-2 px-4 rounded-md cursor-not-allowed">
                        Register
                    </button>
                </div>
            </form>
        <!-- </div>
    </div> -->

    <!-- Face API Script - Replace the existing script section -->
    <!-- Face API Script -->
    <script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const video = document.getElementById('video');
        const canvas = document.getElementById('canvas');
        const captureBtn = document.getElementById('captureBtn');
        const facialDescriptors = document.getElementById('facial_descriptors');
        const facialImage = document.getElementById('facial_image');
        const faceStatus = document.getElementById('faceStatus');
        const submitBtn = document.getElementById('submitBtn');

        let faceDetected = false;
        let faceCaptured = false;
        let detectionInterval = null;
        let modelsLoaded = false;

        // Load face-api models
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
                console.error('Error loading models:', error);
                faceStatus.textContent = 'Error loading face detection. Please refresh.';
                faceStatus.classList.add('text-red-500');
            }
        }

        // Start video stream
        async function startVideo() {
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ 
                    video: { 
                        width: 640, 
                        height: 480,
                        facingMode: 'user'
                    } 
                });
                video.srcObject = stream;
            } catch (err) {
                faceStatus.textContent = 'Error accessing camera: ' + err.message;
                faceStatus.classList.add('text-red-500');
                captureBtn.disabled = true;
            }
        }

        // Start face detection when video plays
        video.addEventListener('play', () => {
            if (!modelsLoaded) return;
            
            const displaySize = { width: video.width, height: video.height };
            faceapi.matchDimensions(canvas, displaySize);

            detectionInterval = setInterval(async () => {
                const detections = await faceapi.detectAllFaces(video, 
                    new faceapi.TinyFaceDetectorOptions()).withFaceLandmarks().withFaceDescriptors();
                
                const resizedDetections = faceapi.resizeResults(detections, displaySize);
                
                // Clear canvas
                canvas.getContext('2d').clearRect(0, 0, canvas.width, canvas.height);
                
                if (detections.length === 1 && !faceCaptured) {
                    faceDetected = true;
                    
                    // Draw face detection box and landmarks
                    faceapi.draw.drawDetections(canvas, resizedDetections);
                    faceapi.draw.drawFaceLandmarks(canvas, resizedDetections);
                    
                    // Check face quality
                    const detection = detections[0];
                    const box = detection.detection.box;
                    
                    // Calculate face size relative to video
                    const faceSize = (box.width * box.height) / (video.width * video.height);
                    
                    if (faceSize < 0.1) {
                        faceStatus.textContent = 'Please move closer to the camera';
                        faceStatus.classList.add('text-yellow-600');
                        captureBtn.disabled = true;
                    } else if (box.width < 100 || box.height < 100) {
                        faceStatus.textContent = 'Face too small. Please move closer.';
                        faceStatus.classList.add('text-yellow-600');
                        captureBtn.disabled = true;
                    } else {
                        faceStatus.textContent = 'Face detected! Click "Capture Face" to continue.';
                        faceStatus.classList.remove('text-red-500', 'text-yellow-600');
                        faceStatus.classList.add('text-green-600');
                        captureBtn.disabled = false;
                        
                        // Enable capture button
                        captureBtn.classList.remove('bg-gray-400', 'cursor-not-allowed');
                        captureBtn.classList.add('bg-indigo-600', 'hover:bg-indigo-700');
                    }
                    
                } else if (detections.length > 1) {
                    faceDetected = false;
                    faceStatus.textContent = 'Multiple faces detected. Please ensure only one face is visible.';
                    faceStatus.classList.add('text-red-500');
                    captureBtn.disabled = true;
                } else if (!faceCaptured) {
                    faceDetected = false;
                    faceStatus.textContent = 'No face detected. Please position your face in the camera.';
                    faceStatus.classList.add('text-red-500');
                    captureBtn.disabled = true;
                }
                
                // Draw additional guidance
                if (!faceCaptured) {
                    const ctx = canvas.getContext('2d');
                    ctx.strokeStyle = '#4F46E5';
                    ctx.lineWidth = 2;
                    ctx.setLineDash([5, 5]);
                    
                    // Draw oval guide
                    const centerX = canvas.width / 2;
                    const centerY = canvas.height / 2;
                    const radiusX = 120;
                    const radiusY = 150;
                    
                    ctx.beginPath();
                    ctx.ellipse(centerX, centerY, radiusX, radiusY, 0, 0, 2 * Math.PI);
                    ctx.stroke();
                    
                    // Draw text
                    ctx.font = '14px Arial';
                    ctx.fillStyle = '#4F46E5';
                    ctx.fillText('Position face here', centerX - 70, centerY - 80);
                }
            }, 100);
        });

        captureBtn.addEventListener('click', async () => {
            if (!faceDetected) {
                Swal.fire({
                    icon: 'warning',
                    title: 'No Face Detected',
                    text: 'Please ensure your face is clearly visible in the camera.',
                    confirmButtonColor: '#4F46E5'
                });
                return;
            }

            try {
                // Disable capture button during processing
                captureBtn.disabled = true;
                captureBtn.textContent = 'Processing...';
                
                // Detect face with descriptors
                const detections = await faceapi.detectAllFaces(video, 
                    new faceapi.TinyFaceDetectorOptions()).withFaceLandmarks().withFaceDescriptors();
                
                if (detections.length === 1) {
                    // Check face quality again
                    const detection = detections[0];
                    const box = detection.detection.box;
                    
                    if (box.width < 100 || box.height < 100) {
                        throw new Error('Face too small. Please move closer and try again.');
                    }
                    
                    // Store facial descriptors
                    const descriptors = Array.from(detection.descriptor);
                    facialDescriptors.value = JSON.stringify(descriptors);
                    
                    // Capture high-quality image
                    const canvasTemp = document.createElement('canvas');
                    canvasTemp.width = video.videoWidth;
                    canvasTemp.height = video.videoHeight;
                    const tempCtx = canvasTemp.getContext('2d'); // Changed variable name to tempCtx
                    tempCtx.drawImage(video, 0, 0);
                    
                    // Convert to base64 with high quality
                    const imageData = canvasTemp.toDataURL('image/jpeg', 0.95);
                    facialImage.value = imageData;
                    
                    faceCaptured = true;
                    
                    // Show success message
                    Swal.fire({
                        icon: 'success',
                        title: 'Face Captured!',
                        text: 'Your face has been successfully registered.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    
                    faceStatus.textContent = 'Face captured successfully! You can now complete registration.';
                    faceStatus.classList.remove('text-red-500', 'text-yellow-600');
                    faceStatus.classList.add('text-green-600');
                    
                    // Enable submit button
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('bg-gray-400', 'cursor-not-allowed');
                    submitBtn.classList.add('bg-indigo-600', 'hover:bg-indigo-700');
                    
                    // Stop video
                    if (video.srcObject) {
                        video.srcObject.getTracks().forEach(track => track.stop());
                    }
                    clearInterval(detectionInterval);
                    
                    // Hide capture button
                    captureBtn.style.display = 'none';
                    
                    // Draw final captured frame - use a different variable name
                    const finalCtx = canvas.getContext('2d'); // Changed variable name to finalCtx
                    finalCtx.clearRect(0, 0, canvas.width, canvas.height);
                    finalCtx.drawImage(video, 0, 0, canvas.width, canvas.height);
                    finalCtx.strokeStyle = '#10B981';
                    finalCtx.lineWidth = 4;
                    finalCtx.setLineDash([]);
                    finalCtx.strokeRect(10, 10, canvas.width - 20, canvas.height - 20);
                    
                } else {
                    throw new Error('Please ensure only one face is visible.');
                }
            } catch (error) {
                console.error('Error capturing face:', error);
                
                Swal.fire({
                    icon: 'error',
                    title: 'Capture Failed',
                    text: error.message || 'Error capturing face. Please try again.',
                    confirmButtonColor: '#4F46E5'
                });
                
                // Re-enable capture button
                captureBtn.disabled = false;
                captureBtn.textContent = 'Capture Face';
            }
        });

        // Form submission validation
        document.getElementById('registrationForm').addEventListener('submit', function(e) {
            if (!facialDescriptors.value || !facialImage.value) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Face Not Captured',
                    text: 'Please capture your face before registering.',
                    confirmButtonColor: '#4F46E5'
                });
            }
        });

        // Clean up
        window.addEventListener('beforeunload', () => {
            if (video.srcObject) {
                video.srcObject.getTracks().forEach(track => track.stop());
            }
            if (detectionInterval) {
                clearInterval(detectionInterval);
            }
        });

        // Initialize
        loadModels();
    </script>
</x-guest-layout>
