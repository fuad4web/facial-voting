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

    <!-- Face API Script -->
    <script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
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
        let currentDescriptors = null;
        let currentImageData = null;
        let detectionInterval = null;

        // Load face-api models
        Promise.all([
            faceapi.nets.tinyFaceDetector.loadFromUri('/models'),
            faceapi.nets.faceLandmark68Net.loadFromUri('/models'),
            faceapi.nets.faceRecognitionNet.loadFromUri('/models')
        ]).then(startVideo);

        function startVideo() {
            navigator.mediaDevices.getUserMedia({ video: true })
                .then(stream => {
                    video.srcObject = stream;
                })
                .catch(err => {
                    faceStatus.textContent = 'Error accessing camera: ' + err.message;
                    faceStatus.classList.add('text-red-500');
                });
        }

        video.addEventListener('play', () => {
            const displaySize = { width: video.width, height: video.height };
            faceapi.matchDimensions(canvas, displaySize);

            detectionInterval = setInterval(async () => {
                const detections = await faceapi.detectAllFaces(video, 
                    new faceapi.TinyFaceDetectorOptions()).withFaceLandmarks().withFaceDescriptors();
                
                const resizedDetections = faceapi.resizeResults(detections, displaySize);
                
                // Clear canvas
                canvas.getContext('2d').clearRect(0, 0, canvas.width, canvas.height);
                
                if (detections.length === 1) {
                    faceDetected = true;
                    
                    // Draw face detection box
                    faceapi.draw.drawDetections(canvas, resizedDetections);
                    
                    if (!faceCaptured) {
                        faceStatus.textContent = 'Face detected! Click "Capture Face" to continue.';
                        faceStatus.classList.remove('text-red-500', 'text-green-600');
                        faceStatus.classList.add('text-green-600');
                    }
                } else if (detections.length > 1) {
                    faceDetected = false;
                    faceStatus.textContent = 'Multiple faces detected. Please ensure only one face is visible.';
                    faceStatus.classList.remove('text-green-600');
                    faceStatus.classList.add('text-red-500');
                } else {
                    faceDetected = false;
                    if (!faceCaptured) {
                        faceStatus.textContent = 'No face detected. Please position your face in the camera.';
                        faceStatus.classList.remove('text-green-600');
                        faceStatus.classList.add('text-red-500');
                    }
                }
            }, 100);
        });

        captureBtn.addEventListener('click', async () => {
            if (!faceDetected) {
                alert('No face detected. Please ensure your face is visible.');
                return;
            }

            try {
                // Detect face with descriptors
                const detections = await faceapi.detectAllFaces(video, 
                    new faceapi.TinyFaceDetectorOptions()).withFaceLandmarks().withFaceDescriptors();
                
                if (detections.length === 1) {
                    // Store facial descriptors
                    const descriptors = Array.from(detections[0].descriptor);
                    facialDescriptors.value = JSON.stringify(descriptors);
                    
                    // Capture image
                    const canvasTemp = document.createElement('canvas');
                    canvasTemp.width = video.videoWidth;
                    canvasTemp.height = video.videoHeight;
                    canvasTemp.getContext('2d').drawImage(video, 0, 0);
                    
                    // Convert to base64
                    const imageData = canvasTemp.toDataURL('image/jpeg', 0.8);
                    facialImage.value = imageData;
                    
                    faceCaptured = true;
                    faceStatus.textContent = 'Face captured successfully! You can now register.';
                    faceStatus.classList.remove('text-red-500');
                    faceStatus.classList.add('text-green-600');
                    
                    // Enable submit button
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('bg-gray-400', 'cursor-not-allowed');
                    submitBtn.classList.add('bg-indigo-600', 'hover:bg-indigo-700');
                    
                    // Stop video
                    video.srcObject.getTracks().forEach(track => track.stop());
                    clearInterval(detectionInterval);
                    
                    // Hide capture button
                    captureBtn.style.display = 'none';
                }
            } catch (error) {
                console.error('Error capturing face:', error);
                alert('Error capturing face. Please try again.');
            }
        });

        // Clean up on form submit
        document.getElementById('registrationForm').addEventListener('submit', function() {
            if (video.srcObject) {
                video.srcObject.getTracks().forEach(track => track.stop());
            }
            if (detectionInterval) {
                clearInterval(detectionInterval);
            }
        });
    </script>
</x-guest-layout>
